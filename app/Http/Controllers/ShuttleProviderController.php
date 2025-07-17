<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Auth; // or use Illuminate\Support\Facades\Auth;
use DataTables;

/**
 * Import Models here
 */
use App\Models\User;
use App\Models\ShuttleProvider;

class ShuttleProviderController extends Controller
{
    public function viewShuttleProvider(){
        $shuttleProviderData = ShuttleProvider::where('is_deleted', 0)->get();
        
        return DataTables::of($shuttleProviderData)
            ->addColumn('shuttle_provider_status', function($row){
                $result = "";
                if($row->shuttle_provider_status == 1){
                    $result .= '<center><span class="badge badge-pill badge-success">Active</span></center>';
                }
                else{
                    $result .= '<center><span class="badge badge-pill text-secondary" style="background-color: #E6E6E6">Inactive</span></center>';
                }
                return $result;
            })
            ->addColumn('action', function($row){
                if($row->shuttle_provider_status == 1){
                    $result =   '<center>';
                    $result .=      '<button type="button" class="btn btn-primary btn-xs text-center actionEditShuttleProvider mr-1" shuttle-provider-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modalAddShuttleProvider" title="Edit Shuttle Provider Details">';
                    $result .=          '<i class="fa fa-xl fa-edit"></i> ';
                    $result .=      '</button>';
                    $result .=      '<button type="button" class="btn btn-danger btn-xs text-center actionEditShuttleProviderStatus mr-1" shuttle-provider-id="' . $row->id . '" shuttle-provider-status="' . $row->shuttle_provider_status . '" data-bs-toggle="modal" data-bs-target="#modalEditShuttleProviderStatus" title="Deactivate Shuttle Provider">';
                    $result .=          '<i class="fa-solid fa-xl fa-ban"></i>';
                    $result .=      '</button>';
                    $result .=  '</center>';
                }
                else{
                    $result =   '<center>';
                    $result .=      '<button type="button" class="btn btn-primary btn-xs text-center actionEditShuttleProvider mr-1" shuttle-provider-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modalAddShuttleProvider" title="Edit ShuttleProvider Details">';
                    $result .=          '<i class="fa fa-xl fa-edit"></i>';
                    $result .=      '</button>';
                    $result .=      '<button type="button" class="btn btn-warning btn-xs text-center actionEditShuttleProviderStatus mr-1" shuttle-provider-id="' . $row->id . '" shuttle-provider-status="' . $row->shuttle_provider_status . '" data-bs-toggle="modal" data-bs-target="#modalEditShuttleProviderStatus" title="Activate Shuttle Provider">';
                    $result .=          '<i class="fa-solid fa-xl fa-arrow-rotate-right"></i>';
                    $result .=      '</button>';
                    $result .=  '</center>';
                }
                return $result;
            })
        ->rawColumns(['shuttle_provider_status', 'action'])
        ->make(true);
    }

    public function addShuttleProvider(Request $request){
        date_default_timezone_set('Asia/Manila');
        session_start();
        $data = $request->all();

        /* For Insert */
        if(!isset($request->shuttle_provider_id)){
            $validator = Validator::make($data, [
                'shuttle_provider_name' => 'required',
                'shuttle_provider_capacity' => 'required',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
            } else {
                DB::beginTransaction();
                try {
                    $shuttleProviderId = ShuttleProvider::insertGetId([
                        'shuttle_provider_name' => $request->shuttle_provider_name,
                        'shuttle_provider_capacity' => $request->shuttle_provider_capacity,
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
                'shuttle_provider_id' => 'required',
                'shuttle_provider_name' => 'required',
                'shuttle_provider_capacity' => 'required',
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
                    ShuttleProvider::where('id', $request->shuttle_provider_id)->update([
                        'shuttle_provider_name' => $request->shuttle_provider_name,
                        'shuttle_provider_capacity' => $request->shuttle_provider_capacity,
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

    public function getShuttleProviderById(Request $request){
        $shuttleProviderData = ShuttleProvider::where('id', $request->shuttleProviderId)->get();
        return response()->json(['shuttleProviderData' => $shuttleProviderData]);
    }

    public function editShuttleProviderStatus(Request $request){        
        date_default_timezone_set('Asia/Manila');
        session_start();
        
        $data = $request->all(); // collect all input fields
        $validator = Validator::make($data, [
            'shuttle_provider_id' => 'required',
            'shuttle_provider_status' => 'required',
        ]);

        if($validator->passes()){
            if($request->shuttle_provider_status == 1){
                ShuttleProvider::where('id', $request->shuttle_provider_id)
                    ->update([
                            'shuttle_provider_status' => 0,
                            'last_updated_by' => $_SESSION['rapidx_user_id'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                $status = ShuttleProvider::where('id', $request->shuttle_provider_id)->value('shuttle_provider_status');
                return response()->json(['hasError' => 0, 'status' => (int)$status]);
            }else{
                ShuttleProvider::where('id', $request->shuttle_provider_id)
                    ->update([
                            'shuttle_provider_status' => 1,
                            'last_updated_by' => $_SESSION['rapidx_user_id'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                $status = ShuttleProvider::where('id', $request->shuttle_provider_id)->value('shuttle_provider_status');
                return response()->json(['hasError' => 0, 'status' => (int)$status]);
            }
        }
        else{
            return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
        }
    }

    // getShuttleProvider
    public function getShuttleProvider(Request $request){
        $shuttleProviderData = ShuttleProvider::where('is_deleted', 0)->where('shuttle_provider_status', 1)->get();
        // return $shuttleProviderData;
        return response()->json(['shuttleProviderData' => $shuttleProviderData]);
    }
}
