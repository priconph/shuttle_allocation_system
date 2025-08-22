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
            ->addColumn('status', function($row){
                $result = "";
                if($row->status == 1){
                    $result .= '<center><span class="badge badge-pill badge-success">UNLOCKED</span></center>';
                }
                else{
                    $result .= '<center><span class="badge badge-pill" style="background-color: #f10a0aff">LOCKED</span></center>';
                }
                return $result;
            })
            ->addColumn('action', function($row){
                $result =   '<center>';
                // $result .=      '<button type="button" class="btn btn-primary btn-xs text-center actionEditCutoffTime mr-1" pickup-time-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modalAddCutoffTime" title="Edit Shuttle Provider Details">';
                // $result .=          '<i class="fa fa-xl fa-edit"></i> ';
                // $result .=      '</button>';


                if($row->status == 1){
                    $result .=      '<button type="button" class="btn btn-danger btn-xs text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->status . '" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Lock Masterlist">';
                    $result .=          '<i class="fa-solid fa-xl fa-lock"></i> Lock';
                    $result .=      '</button>';
                }
                else{
                    $result .=      '<button type="button" class="btn btn-success btn-xs text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->status . '" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Unlock Masterlist">';
                    $result .=          '<i class="fa-solid fa-xl fa-unlock"></i> Unlock';
                    $result .=      '</button>';
                }

                $result .=  '</center>';
                return $result;
            })
        ->rawColumns(['cutoff_time','status', 'action'])
        ->make(true);
    }

    public function addCutoffTime(Request $request){
        date_default_timezone_set('Asia/Manila');
        session_start();
        $data = $request->all();

        /* For Insert */
        if(!isset($request->cutoff_time_id)){
            $validator = Validator::make($data, [
                'factory' => 'required',
                'schedule' => 'required',
            ]);

            if ($validator->fails()){
                return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
            }else{

                DB::beginTransaction();
                try {
                    CutoffTime::insert([
                        'factory' => $request->factory,
                        'schedule' => $request->schedule,
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
        }else{ /* For Update */
            $validator = Validator::make($data, [
                'cutoff_time_id' => 'required',
                'factory' => 'required',
                'schedule' => 'required',
            ]);

            // For debugging of session only
            // $sessionChecker = "session not set";
            // if(isset($_SESSION['rapidx_user_id'])){
            //     $sessionChecker = "session set";
            // }

            if ($validator->fails()){
                return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
            }else{
                DB::beginTransaction();
                try{
                    CutoffTime::where('id', $request->cutoff_time_id)->update([
                        'factory' => $request->factory,
                        'schedule' => $request->schedule,
                        'last_updated_by' => $_SESSION['rapidx_user_id'],
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    DB::commit();
                    return response()->json(['hasError' => 0]);
                }catch (\Exception $e) {
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
            'status' => 'required',
        ]);

        if($validator->passes()){
            if($request->status == 1){
                CutoffTime::where('id', $request->cutoff_time_id)
                    ->update([
                            'status' => 0,
                            'last_updated_by' => $_SESSION['rapidx_user_id'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                $status = CutoffTime::where('id', $request->cutoff_time_id)->value('status');
                return response()->json(['hasError' => 0, 'status' => (int)$status]);
            }else{
                CutoffTime::where('id', $request->cutoff_time_id)
                    ->update([
                            'status' => 1,
                            'last_updated_by' => $_SESSION['rapidx_user_id'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                $status = CutoffTime::where('id', $request->cutoff_time_id)->value('status');
                return response()->json(['hasError' => 0, 'status' => (int)$status]);
            }
        }
        else{
            return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
        }
    }
}
