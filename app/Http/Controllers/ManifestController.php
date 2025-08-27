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
            'request_ml_info'
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
        $masterlist_removed_data = Masterlist::where('masterlist_status', 1)
            ->where('is_deleted', 0)
            ->whereNotIn('id', $allocatedEmployeeNumbers) // compare employee number
            ->get();

        $merged = $allocations->merge($masterlist_removed_data);

        return response()->json([
            // 'allocatedEmployeeNumbers' => $allocatedEmployeeNumbers,
            // 'masterlist_removed_data' => $masterlist_removed_data,
            'allocations' => $allocations,
            'manifests' => $manifests,
            // 'merged' => $merged,
        ]);

        // return DataTables::of($manifests)
        // ->make(true);
    }
}
