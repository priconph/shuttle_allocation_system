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
use App\Models\PickupTime;

class PickupTimeController extends Controller
{
    public function viewPickupTime(){
        $pickupTimeData = PickupTime::where('is_deleted', 0)->get();
        
        return DataTables::of($pickupTimeData)
            ->addColumn('pickup_time', function($row){
                $result = "";
                $result .= '<center><span>'. Carbon::parse($row->pickup_time)->format('h:iA') .'</span></center>';
                return $result;
            })
            ->addColumn('pickup_time_status', function($row){
                $result = "";
                if($row->pickup_time_status == 1){
                    $result .= '<center><span class="badge badge-pill badge-success">Active</span></center>';
                }
                else{
                    $result .= '<center><span class="badge badge-pill text-secondary" style="background-color: #E6E6E6">Inactive</span></center>';
                }
                return $result;
            })
            ->addColumn('action', function($row){
                if($row->pickup_time_status == 1){
                    $result =   '<center>';
                    $result .=      '<button type="button" class="btn btn-primary btn-xs text-center actionEditPickupTime mr-1" pickup-time-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modalAddPickupTime" title="Edit Shuttle Provider Details">';
                    $result .=          '<i class="fa fa-xl fa-edit"></i> ';
                    $result .=      '</button>';
                    $result .=      '<button type="button" class="btn btn-danger btn-xs text-center actionEditPickupTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->pickup_time_status . '" data-bs-toggle="modal" data-bs-target="#modalEditPickupTimeStatus" title="Deactivate Shuttle Provider">';
                    $result .=          '<i class="fa-solid fa-xl fa-ban"></i>';
                    $result .=      '</button>';
                    $result .=  '</center>';
                }
                else{
                    $result =   '<center>';
                    $result .=      '<button type="button" class="btn btn-primary btn-xs text-center actionEditPickupTime mr-1" pickup-time-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modalAddPickupTime" title="Edit PickupTime Details">';
                    $result .=          '<i class="fa fa-xl fa-edit"></i>';
                    $result .=      '</button>';
                    $result .=      '<button type="button" class="btn btn-warning btn-xs text-center actionEditPickupTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->pickup_time_status . '" data-bs-toggle="modal" data-bs-target="#modalEditPickupTimeStatus" title="Activate Shuttle Provider">';
                    $result .=          '<i class="fa-solid fa-xl fa-arrow-rotate-right"></i>';
                    $result .=      '</button>';
                    $result .=  '</center>';
                }
                return $result;
            })
        ->rawColumns(['pickup_time','pickup_time_status', 'action'])
        ->make(true);
    }

    public function addPickupTime(Request $request){
        date_default_timezone_set('Asia/Manila');
        session_start();
        $data = $request->all();

        /* For Insert */
        if(!isset($request->pickup_time_id)){
            $validator = Validator::make($data, [
                'pickup_time' => 'required',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
            } else {
                DB::beginTransaction();
                try {
                    $pickupTimeId = PickupTime::insertGetId([
                        'pickup_time' => $request->pickup_time,
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
                'pickup_time_id' => 'required',
                'pickup_time' => 'required',
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
                    PickupTime::where('id', $request->pickup_time_id)->update([
                        'pickup_time' => $request->pickup_time,
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

    public function getPickupTimeById(Request $request){
        $pickupTimeData = PickupTime::where('id', $request->pickupTimeId)->get();
        return response()->json(['pickupTimeData' => $pickupTimeData]);
    }

    public function editPickupTimeStatus(Request $request){        
        date_default_timezone_set('Asia/Manila');
        session_start();
        
        $data = $request->all(); // collect all input fields
        $validator = Validator::make($data, [
            'pickup_time_id' => 'required',
            'pickup_time_status' => 'required',
        ]);

        if($validator->passes()){
            if($request->pickup_time_status == 1){
                PickupTime::where('id', $request->pickup_time_id)
                    ->update([
                            'pickup_time_status' => 0,
                            'last_updated_by' => $_SESSION['rapidx_user_id'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                $status = PickupTime::where('id', $request->pickup_time_id)->value('pickup_time_status');
                return response()->json(['hasError' => 0, 'status' => (int)$status]);
            }else{
                PickupTime::where('id', $request->pickup_time_id)
                    ->update([
                            'pickup_time_status' => 1,
                            'last_updated_by' => $_SESSION['rapidx_user_id'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                $status = PickupTime::where('id', $request->pickup_time_id)->value('pickup_time_status');
                return response()->json(['hasError' => 0, 'status' => (int)$status]);
            }
        }
        else{
            return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
        }
    }

    public function getPickupTime(Request $request){
        $parsedPickupTimeColumn = [];
        $pickupTimeData = PickupTime::where('is_deleted', 0)->where('pickup_time_status', 1)->get();
        for ($i=0; $i < count($pickupTimeData); $i++) { 
            $parsedPickupTimeColumn[] = Carbon::parse($pickupTimeData[$i]->pickup_time)->format('h:iA');
        }
        // return $parsedPickupTimeColumn;
        return response()->json(['pickupTimeData' => $pickupTimeData, 'parsedPickupTimeColumn' => $parsedPickupTimeColumn]);
    }
}
