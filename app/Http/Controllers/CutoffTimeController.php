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
use App\Models\CutoffTime;

class CutoffTimeController extends Controller
{
    public function viewCutoffTime(){
        $cutoffTimeData = CutoffTime::where('is_deleted', 0)->get();
        
        return DataTables::of($cutoffTimeData)
            ->addColumn('cutoff_time', function($row){
                $result = "";
                $result .= '<center><span>'. Carbon::parse($row->cutoff_time)->format('h:iA') .'</span></center>';
                return $result;
            })
            ->addColumn('cutoff_time_status', function($row){
                $result = "";
                if($row->cutoff_time_status == 1){
                    $result .= '<center><span class="badge badge-pill badge-success">Active</span></center>';
                }
                else{
                    $result .= '<center><span class="badge badge-pill text-secondary" style="background-color: #E6E6E6">Inactive</span></center>';
                }
                return $result;
            })
            ->addColumn('action', function($row){
                $result =   '<center>';
                // $result .=      '<button type="button" class="btn btn-primary btn-xs text-center actionEditCutoffTime mr-1" pickup-time-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modalAddCutoffTime" title="Edit Shuttle Provider Details">';
                // $result .=          '<i class="fa fa-xl fa-edit"></i> ';
                // $result .=      '</button>';
                

                if($row->cutoff_time_status == 1){
                    $result .=      '<button type="button" class="btn btn-danger btn-xs text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->cutoff_time_status . '" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Lock Masterlist">';
                    $result .=          '<i class="fa-solid fa-xl fa-lock"></i> Lock';
                    $result .=      '</button>';
                }
                else{
                    $result .=      '<button type="button" class="btn btn-success btn-xs text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->cutoff_time_status . '" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Unlock Masterlist">';
                    $result .=          '<i class="fa-solid fa-xl fa-unlock"></i> Unlock';
                    $result .=      '</button>';
                }

                $result .=  '</center>';
                return $result;
            })
        ->rawColumns(['cutoff_time','cutoff_time_status', 'action'])
        ->make(true);
    }

    public function addCutoffTime(Request $request){
        date_default_timezone_set('Asia/Manila');
        session_start();
        $data = $request->all();

        /* For Insert */
        if(!isset($request->cutoff_time_id)){
            $validator = Validator::make($data, [
                'cutoff_time' => 'required',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
            } else {
                DB::beginTransaction();
                try {
                    $pickupTimeId = CutoffTime::insertGetId([
                        'cutoff_time' => $request->cutoff_time,
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
                'cutoff_time_id' => 'required',
                'cutoff_time' => 'required',
            ]);

            // For debugging of session only
            // $sessionChecker = "session not set";
            // if(isset($_SESSION['rapidx_user_id'])){
            //     $sessionChecker = "session set";
            // }

            if ($validator->fails()) {
                return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
            } else {
                DB::beginTransaction();
                try {
                    CutoffTime::where('id', $request->cutoff_time_id)->update([
                        'cutoff_time' => $request->cutoff_time,
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
    
    public function getCutoffTimeById(Request $request){
        $cutoffTimeData = CutoffTime::where('id', $request->cutoffTimeId)->get();
        // return $cutoffTimeData;
        return response()->json(['cutoffTimeData' => $cutoffTimeData]);
    }

    public function editCutoffTimeStatus(Request $request){        
        date_default_timezone_set('Asia/Manila');
        session_start();
        
        $data = $request->all(); // collect all input fields
        $validator = Validator::make($data, [
            'cutoff_time_id' => 'required',
            'cutoff_time_status' => 'required',
        ]);

        if($validator->passes()){
            if($request->cutoff_time_status == 1){
                CutoffTime::where('id', $request->cutoff_time_id)
                    ->update([
                            'cutoff_time_status' => 0,
                            'last_updated_by' => $_SESSION['rapidx_user_id'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                $status = CutoffTime::where('id', $request->cutoff_time_id)->value('cutoff_time_status');
                return response()->json(['hasError' => 0, 'status' => (int)$status]);
            }else{
                CutoffTime::where('id', $request->cutoff_time_id)
                    ->update([
                            'cutoff_time_status' => 1,
                            'last_updated_by' => $_SESSION['rapidx_user_id'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                $status = CutoffTime::where('id', $request->cutoff_time_id)->value('cutoff_time_status');
                return response()->json(['hasError' => 0, 'status' => (int)$status]);
            }
        }
        else{
            return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
        }
    }
}
