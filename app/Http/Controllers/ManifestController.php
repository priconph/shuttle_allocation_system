<?php

namespace App\Http\Controllers;

use DataTables;
use App\Models\Manifest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportShuttleManifest;

date_default_timezone_set('Asia/Manila');
class ManifestController extends Controller
{
    public function dt_get_manifest(Request $request){
        $manifests = Manifest::all();
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
}
