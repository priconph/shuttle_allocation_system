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

            $cleaned = $collections->map(function ($sheet) {
                return $sheet->filter(function ($row) {
                    return $row->filter()->isNotEmpty();
                });
            });
            if(count($collections[0][0]) != 5){
                return response()->json([
                    'result' => false,
                    'msg' => 'Invalid excel format'
                ], 422);
            }

            foreach ($cleaned[0] as $value) {
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
        $date = $request->date;

        // Get all allocations and masterlist in one go, indexed by employee number
        $allocations = Allocations::with([
                'request_ml_info.routes_info'
            ])
            ->where('alloc_date_start', '<=', $date)
            ->where('alloc_date_end', '>=', $date)
            ->where('is_deleted', 0)
            ->get();

        $allocatedEmployeeNumbers = $allocations->pluck('requestee_ml_id')->all();

        $masterlist = Masterlist::with(['routes_info'])
            ->where('masterlist_status', 1)
            ->where('is_deleted', 0)
            ->whereNotIn('id', $allocatedEmployeeNumbers)
            ->get();

        // Build a lookup table for employee number => merged record
        $merged = [];
        foreach ($allocations as $a) {
            $empNo = $a['request_ml_info']['masterlist_employee_number'] ?? null;
            if ($empNo) $merged[$empNo] = $a;
        }
        foreach ($masterlist as $m) {
            $empNo = $m['masterlist_employee_number'] ?? null;
            if ($empNo && !isset($merged[$empNo])) $merged[$empNo] = $m;
        }

        // Get manifests for the date
        $manifests = Manifest::with(['route_details'])
            ->where('date_scanned', $date)
            ->get();

        // Prepare result
        $result = $manifests->map(function ($m) use ($merged) {
            $empNo = $m['emp_no'];
            $match = $merged[$empNo] ?? null;

            $routeDesc = $match['request_ml_info']['routes_info']['routes_description']
                ?? $match['routes_info']['routes_description']
                ?? null;

            $incoming = $match['alloc_incoming']
                ?? $match['masterlist_incoming']
                ?? null;

            $m['expected_routeDesc'] = $routeDesc;
            $m['expected_incoming'] = $incoming;
            return $m;
        })->reject(function ($m) {
            $routeMatch = ($m['route_details']['routes_destination'] ?? null) === ($m['expected_routeDesc'] ?? null);

            $timeScanned = $m['time_scanned'] ?? null;
            $expectedIncoming = $m['expected_incoming'] ?? null;
            $invalidTime = false;

            if ($timeScanned != null) {
                $ts = date('H:i:s', strtotime($timeScanned));
                if ($expectedIncoming === '7:30AM') {
                    $invalidTime = ($ts >= '05:00:00' && $ts <= '09:00:00');
                } elseif ($expectedIncoming === '7:30PM') {
                    $invalidTime = ($ts >= '17:00:00' && $ts <= '21:00:00');
                }
            }

            return $routeMatch && $invalidTime;
        })->values();

        return DataTables::of($result)->make(true);
    }
}
