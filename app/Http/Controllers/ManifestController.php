<?php

namespace App\Http\Controllers;

use DataTables;
use App\Models\Manifest;
use App\Models\RouteCode;
use App\Models\Masterlist;
use App\Models\Allocations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportShuttleManifest;

date_default_timezone_set('Asia/Manila');
class ManifestController extends Controller
{
    public function dt_get_manifest(Request $request){
        $manifests = Manifest::with(['route_details'])
        ->where('date_scanned', $request->date_scanned)
        ->where(function($query) use ($request){
            if(!is_null($request->route)){
                $query->where('route', $request->route);
            }
        })
        ->get();
        return DataTables::of($manifests)
        ->make(true);
    }
    
    public function import_manifest(Request $request){
        DB::beginTransaction();
        try{
            $collections = Excel::toCollection(new ImportShuttleManifest, $request->file('manifest'));
            // return count( $collections[0][0] );
            if(count($collections[0][0]) != 5){
                return response()->json([
                    'result' => false,
                    'msg' => 'Invalid excel format'
                ], 422);
            }

            foreach ($collections[0] as $value) {
                Manifest::insert([
                    'emp_no' => $value[0],
                    'date_scanned' => $value[1],
                    'time_scanned' => $value[2],
                    'route' => $value[3],
                    'factory' => $value[4]
                ]);
            }
            DB::commit();
            return response()->json([
                'result' => true,
            ]);
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
      
    }

    public function get_route_code(Request $request){
        return RouteCode::whereNull('deleted_at')->get();
    }

    public function dt_get_inconsistent(Request $request){
        $manifests = Manifest::with(['route_details'])
        ->where('date_scanned', $request->date)
        ->get();


        $allocations = Allocations::with([
            'request_ml_info',
            'request_ml_info.routes_info',
        ])
        ->where('alloc_date_start', '<=', $request->date)
        ->where('alloc_date_end', '>=', $request->date)
        ->where('is_deleted', 0)
        ->get();

        // get all employee numbers that are already allocated
        $allocatedEmployeeNumbers = Allocations::where('alloc_date_start', '<=', $request->date)
        ->where('alloc_date_end', '>=', $request->date)
        ->where('is_deleted', 0)
        ->pluck('requestee_ml_id'); // employee numbers

        // remove them from masterlist
        $masterlist_removed_data = Masterlist::with([
            'routes_info'
        ])
        ->where('masterlist_status', 1)
        ->where('is_deleted', 0)
        ->whereNotIn('id', $allocatedEmployeeNumbers) // compare employee number
        ->get();

        $merged = $allocations->merge($masterlist_removed_data); // merge the allocation and masterlist

        /**
         * Compare manifests against merged records.
         *
         * @var $result contains manifest records that DO NOT have
         *              a matching employee number + route description
         *              in the merged data.
         * 
         * Each record will include an extra field:
         *   - expected_routeDesc → the route description from merged (if any)
         */
        $result = $manifests->map(function ($m) use ($merged) {
            // find first merged record with same emp_no
            $match = $merged->first(function ($mer) use ($m) {
                $empNo = $mer['request_ml_info']['masterlist_employee_number']
                    ?? $mer['masterlist_employee_number']
                    ?? null;
                return $m['emp_no'] == $empNo;
            });
            // expected routeDesc if emp_no matched
            $routeDesc = $match['request_ml_info']['routes_info']['routes_description']
                ?? $match['routes_info']['routes_description']
                ?? null;

            $incoming = $match['alloc_incoming']
                ?? $match['masterlist_incoming']
                ?? null;
            // attach expected_routeDesc to manifest
            $m['expected_routeDesc'] = $routeDesc;
            $m['expected_incoming'] = $incoming;
            return $m;
        })
        ->reject(function ($m) {
            // reject if route matches
            // return ($m['route_details']['routes_destination'] ?? null) === ($m['expected_routeDesc'] ?? null);

            $timeScanned = $m['time_scanned'] ?? null;
            $expectedIncoming = $m['expected_incoming'] ?? null;

            // default is keep
            $invalidTime = false;

            if ($timeScanned != null) {
                // $ts = \Carbon\Carbon::createFromFormat('H:i:s', $timeScanned);

                // if ($expectedIncoming === '7:30AM') {
                //     // valid if 05:00:00–09:00:00
                //     $invalidTime = !$ts->between(
                //         \Carbon\Carbon::createFromTime(5, 0, 0),
                //         \Carbon\Carbon::createFromTime(9, 0, 0)
                //     );
                // } elseif ($expectedIncoming === '7:30PM') {
                //     // valid if 17:00:00–21:00:00
                //     $invalidTime = !$ts->between(
                //         \Carbon\Carbon::createFromTime(17, 0, 0),
                //         \Carbon\Carbon::createFromTime(21, 0, 0)
                //     );
                // }
                $ts = date('H:i:s', strtotime($timeScanned));
                
                if ($expectedIncoming === '7:30AM') {
                    // valid if 05:00:00–09:00:00
                    $invalidTime = ($ts >= '05:00:00' && $ts <= '09:00:00');
                } elseif ($expectedIncoming === '7:30PM') {
                    // valid if 17:00:00–21:00:00
                    $invalidTime = ($ts >= '17:00:00' && $ts <= '21:00:00');
                }
            }

            return 
                // reject if route matches
                (($m['route_details']['routes_destination'] ?? null) === ($m['expected_routeDesc'] ?? null))
                // OR reject if incoming matches time range invalid
                && $invalidTime;
        })
        ->values(); // reindex

        return DataTables::of($result)
        ->make(true);
    }
}
