<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Auth; // or use Illuminate\Support\Facades\Auth;
use DataTables;
use Carbon\Carbon;

/**
 * Import Models here
 */
use App\Models\User;
use App\Models\Routes;

class RoutesController extends Controller
{
    public function viewRoutes(){
        $routesData = Routes::with([
            'pickup_time_info',    
            'shuttle_provider_info',    
        ])
        ->where('is_deleted', 0)->get();
        
        return DataTables::of($routesData)
            ->addColumn('status', function($row){
                $result = "";
                if($row->status == 1){
                    $result .= '<center><span class="badge badge-pill badge-success">Active</span></center>';
                }
                else{
                    $result .= '<center><span class="badge badge-pill text-secondary" style="background-color: #E6E6E6">Inactive</span></center>';
                }
                return $result;
            })
            ->addColumn('action', function($row){
                if($row->status == 1){
                    $result =   '<center>';
                    $result .=      '<button type="button" class="btn btn-primary btn-xs text-center actionEditRoutes mr-1" routes-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modalAddRoutes" title="Edit Routes Details">';
                    $result .=          '<i class="fa fa-xl fa-edit"></i> ';
                    $result .=      '</button>';
                    $result .=      '<button type="button" class="btn btn-danger btn-xs text-center actionEditRoutesStatus mr-1" routes-id="' . $row->id . '" routes-status="' . $row->status . '" data-bs-toggle="modal" data-bs-target="#modalEditRoutesStatus" title="Deactivate Routes">';
                    $result .=          '<i class="fa-solid fa-xl fa-ban"></i>';
                    $result .=      '</button>';
                    $result .=  '</center>';
                }
                else{
                    $result =   '<center>';
                    $result .=      '<button type="button" class="btn btn-primary btn-xs text-center actionEditRoutes mr-1" routes-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modalAddRoutes" title="Edit Routes Details">';
                    $result .=          '<i class="fa fa-xl fa-edit"></i>';
                    $result .=      '</button>';
                    $result .=      '<button type="button" class="btn btn-warning btn-xs text-center actionEditRoutesStatus mr-1" routes-id="' . $row->id . '" routes-status="' . $row->status . '" data-bs-toggle="modal" data-bs-target="#modalEditRoutesStatus" title="Activate Routes">';
                    $result .=          '<i class="fa-solid fa-xl fa-arrow-rotate-right"></i>';
                    $result .=      '</button>';
                    $result .=  '</center>';
                }
                return $result;
            })
            ->addColumn('pickup_time_info', function($row){
                $result = "";
                $result .= '<center><span>'. Carbon::parse($row->pickup_time_info->pickup_time)->format('h:iA') .'</span></center>';
                return $result;
            })
        ->rawColumns(['status', 'action', 'pickup_time_info'])
        ->make(true);
    }

    public function addRoutes(Request $request){
        date_default_timezone_set('Asia/Manila');
        session_start();
        $data = $request->all();

        /* For Insert */
        if(!isset($request->routes_id)){
            $validator = Validator::make($data, [
                'routes_name' => 'required',
                'pickup_time_id' => 'required',
                'shuttle_provider_id' => 'required',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
            } else {
                DB::beginTransaction();
                try {
                    $routesId = Routes::insertGetId([
                        'routes_name' => $request->routes_name,
                        'routes_description' => $request->routes_description,
                        'pickup_time_id' => $request->pickup_time_id,
                        'shuttle_provider_id' => $request->shuttle_provider_id,
                        'created_by' => $_SESSION['rapidx_user_id'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'is_deleted' => 0
                    ]);

                    DB::commit();
                    return response()->json(['hasError' => 0]);
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['hasError' => 1, 'exceptionError' => $e]);
                }
            }
        }
        else{ /* For Update */
            $validator = Validator::make($data, [
                'routes_id' => 'required',
                'routes_name' => 'required',
                'pickup_time_id' => 'required',
                'shuttle_provider_id' => 'required',
            ]);

            // For debugging of session only
            $sessionChecker = "session not set";
            if(isset($_SESSION['rapidx_user_id'])){
                $sessionChecker = "session set";
            }

            if ($validator->fails()) {
                return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
            } else {
                DB::beginTransaction();
                try {
                    Routes::where('id', $request->routes_id)->update([
                        'routes_name' => $request->routes_name,
                        'routes_description' => $request->routes_description,
                        'pickup_time_id' => $request->pickup_time_id,
                        'shuttle_provider_id' => $request->shuttle_provider_id,
                        'last_updated_by' => $_SESSION['rapidx_user_id'],
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    
                    DB::commit();
                    return response()->json(['hasError' => 0]);
                } catch (\Exception $e) {
                    DB::rollback();

                    return response()->json(['hasError' => 1, 'exceptionError' => $e, 'sessionChecker' => $sessionChecker]);
                }
            }
        }
    }

    public function getRoutesById(Request $request){
        $routesData = Routes::where('id', $request->routesId)->get();
        return response()->json(['routesData' => $routesData]);
    }

    public function editRoutesStatus(Request $request){        
        date_default_timezone_set('Asia/Manila');
        session_start();

        $data = $request->all(); // collect all input fields
        $validator = Validator::make($data, [
            'routes_id' => 'required',
            'status' => 'required',
        ]);

        if($validator->passes()){
            if($request->status == 1){
                Routes::where('id', $request->routes_id)
                    ->update([
                            'status' => 0,
                            'last_updated_by' => $_SESSION['rapidx_user_id'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                $status = Routes::where('id', $request->routes_id)->value('status');
                return response()->json(['hasError' => 0, 'status' => (int)$status]);
            }else{
                Routes::where('id', $request->routes_id)
                    ->update([
                            'status' => 1,
                            'last_updated_by' => $_SESSION['rapidx_user_id'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                $status = Routes::where('id', $request->routes_id)->value('status');
                return response()->json(['hasError' => 0, 'status' => (int)$status]);
            }
        }
        else{
            return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
        }
    }

    public function getRoutes(Request $request){
        $routesData = Routes::with([
                'pickup_time_info',
                'shuttle_provider_info',
            ])
            ->where('status', 1)
            ->where('is_deleted', 0)
            ->get();
        return response()->json(['routesData' => $routesData]);
    }
}
