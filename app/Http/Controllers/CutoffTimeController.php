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
            // ->addColumn('status', function($row){
            //     $result = "";
            //     if($row->status == 1){
            //         $result .= '<center><span class="badge badge-pill badge-success">UNLOCKED</span></center>';
            //     }else{
            //         $result .= '<center><span class="badge badge-pill" style="background-color: #f10a0aff">LOCKED</span></center>';
            //     }
            //     return $result;
            // })
            ->addColumn('status', function($row) {
                $statuses = [];

                // --- TODAY STATUS ---
                if ($row->status_today == 1) {
                    $statuses[] = '<span class="mb-2"><b>Today:</b> <label class="badge bg-success">UNLOCKED</label></span> <br>';
                } else {
                    $statuses[] = '<span class="mb-2"><b>Today:</b> <label class="badge bg-danger">LOCKED</label></span> <br>';
                }

                // --- SUCCEEDING STATUS (only for category 2) ---
                if ($row->category == 2) {
                    if ($row->status_succeeding == 1) {
                        $statuses[] = '<span class=""><b>Succeeding:</b> <label class="badge bg-success">UNLOCKED</label></span>';
                    } else {
                        $statuses[] = '<span class=""><b>Succeeding:</b> <label class="badge bg-danger">LOCKED</label></span>';
                    }
                }

                // --- Combine badges centered ---
                return '<center>' . implode(' ', $statuses) . '</center>';
            })
            ->addColumn('today_label', function($row) {
                $result = '<center>';
                if($row->status_today == 1){ //UNLOCKED
                    $result .= '<button type="button" class="btn btn-success btn-sm text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->status_today . '" pickup-time-type="Today" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Available">';
                    $result .=   '<span class="badge rounded-pill bg-success"><i class="fa-solid fa-xl fa-lock-open"></i> Available </span>';
                    $result .= '</button>';
                }else{ //LOCKED
                    $result .= '<button type="button" class="btn btn-danger btn-sm text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->status_today . '" pickup-time-type="Today" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Unlock Masterlist">';
                    $result .=    '<span class="badge rounded-pill bg-danger"><i class="fa-solid fa-xl fa-lock"></i> Closed </span>';
                    $result .= '</button>';
                }
                $result .= '</center>';

                return $result;
            })
            ->addColumn('succeeding_label', function($row) {
                $result = '<center>';
                if($row->status_succeeding == 1){
                    $result .=      '<button type="button" class="btn btn-success btn-sm text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->status_succeeding . '" pickup-time-type="Succeeding" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Available">';
                    $result .=   '<span class="badge rounded-pill bg-success"><i class="fa-solid fa-xl fa-lock-open"></i> Available </span>';
                    $result .=      '</button>';
                }else{
                    $result .=      '<button type="button" class="btn btn-danger btn-sm text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->status_succeeding . '" pickup-time-type="Succeeding" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Closed">';
                    $result .=    '<span class="badge rounded-pill bg-danger"><i class="fa-solid fa-xl fa-lock"></i> Closed </span>';
                    $result .=      '</button>';
                }
                $result .= '</center>';

                return $result;
            })
            // ->addColumn('action', function($row){
            //     $result =   '<center>';
            //     // $result .=      '<button type="button" class="btn btn-primary btn-xs text-center actionEditCutoffTime mr-1" pickup-time-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modalAddCutoffTime" title="Edit Shuttle Provider Details">';
            //     // $result .=          '<i class="fa fa-xl fa-edit"></i> ';
            //     // $result .=      '</button>';

            //     //ORIGINAL CODE - TOGGLE LOCK/UNLOCK BUTTON
            //     // if($row->status == 1){
            //     //     $result .=      '<button type="button" class="btn btn-danger btn-xs text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->status . '" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Lock Masterlist">';
            //     //     $result .=          '<i class="fa-solid fa-xl fa-lock"></i> Lock';
            //     //     $result .=      '</button>';
            //     // }else{
            //     //     $result .=      '<button type="button" class="btn btn-success btn-xs text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->status . '" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Unlock Masterlist">';
            //     //     $result .=          '<i class="fa-solid fa-xl fa-unlock"></i> Unlock';
            //     //     $result .=      '</button>';
            //     // }

            //     // Helper that generates the button HTML
            //     $buildButton = function($id, $status, $typeLabel) {

            //         $isUnlocked = ($status == 1);

            //         $btnClass = $isUnlocked ? 'btn-outline-danger' : 'btn-outline-success';
            //         $icon     = $isUnlocked ? 'fa-lock' : 'fa-unlock';
            //         $label    = $isUnlocked ? 'Lock' : 'Unlock';
            //         $title    = $isUnlocked ? "Lock $typeLabel" : "Unlock $typeLabel";

            //         return '
            //             <button type="button"
            //                     class="btn '.$btnClass.' btn-sm text-center actionEditCutoffTimeStatus mr-1 mb-2"
            //                     pickup-time-id="'.$id.'"
            //                     pickup-time-status="'.$status.'"
            //                     pickup-time-type="'.$typeLabel.'"
            //                     data-bs-toggle="modal"
            //                     data-bs-target="#modalEditCutoffTimeStatus"
            //                     title="'.$title.'">
            //                 <i class="fa-solid fa-xl '.$icon.'"></i> '.$label.' '.$typeLabel.'
            //             </button>
            //         ';
            //     };

            //     $result = '<div class="text-center">';

            //     // Always show TODAY button for all categories
            //     $result .= $buildButton($row->id, $row->status_today, "Today");

            //     // Only category 2 gets a SUCCEEDING button
            //     if ($row->category == 2) {
            //         $result .= '<br>';
            //         $result .= $buildButton($row->id, $row->status_succeeding, "Succeeding");
            //     }

            //     $result .= '</div>';

            //     // if($row->status_today == 1){
            //     //     $result .= '<button type="button" class="btn btn-danger btn-xs text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->status . '" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Lock Masterlist">';
            //     //     $result .=   '<i class="fa-solid fa-xl fa-lock"></i> Lock Today';
            //     //     $result .= '</button>';
            //     // }else{
            //     //     $result .= '<button type="button" class="btn btn-success btn-xs text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->status . '" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Unlock Masterlist">';
            //     //     $result .=    '<i class="fa-solid fa-xl fa-unlock"></i> Unlock Today';
            //     //     $result .= '</button>';
            //     // }

            //     // if($row->status_succeeding == 1){
            //     //     $result .=      '<button type="button" class="btn btn-danger btn-xs text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->status . '" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Lock Masterlist">';
            //     //     $result .=          '<i class="fa-solid fa-xl fa-lock"></i> Lock Succeeding';
            //     //     $result .=      '</button>';
            //     // }else{
            //     //     $result .=      '<button type="button" class="btn btn-success btn-xs text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->status . '" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Unlock Masterlist">';
            //     //     $result .=          '<i class="fa-solid fa-xl fa-unlock"></i> Unlock Succeeding';
            //     //     $result .=      '</button>';
            //     // }

            //     $result .=  '</center>';
            //     return $result;
            // })
            // ->addColumn('action', function($row){
            //     $result =   '<center>';
            //     // $result .=      '<button type="button" class="btn btn-primary btn-xs text-center actionEditCutoffTime mr-1" pickup-time-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modalAddCutoffTime" title="Edit Shuttle Provider Details">';
            //     // $result .=          '<i class="fa fa-xl fa-edit"></i> ';
            //     // $result .=      '</button>';

            //     //ORIGINAL CODE - TOGGLE LOCK/UNLOCK BUTTON
            //     // if($row->status == 1){
            //     //     $result .=      '<button type="button" class="btn btn-danger btn-xs text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->status . '" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Lock Masterlist">';
            //     //     $result .=          '<i class="fa-solid fa-xl fa-lock"></i> Lock';
            //     //     $result .=      '</button>';
            //     // }else{
            //     //     $result .=      '<button type="button" class="btn btn-success btn-xs text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->status . '" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Unlock Masterlist">';
            //     //     $result .=          '<i class="fa-solid fa-xl fa-unlock"></i> Unlock';
            //     //     $result .=      '</button>';
            //     // }

            //     // Helper that generates the button HTML
            //     $buildButton = function($id, $status, $typeLabel) {

            //         $isUnlocked = ($status == 1);

            //         $btnClass = $isUnlocked ? 'btn-outline-danger' : 'btn-outline-success';
            //         $icon     = $isUnlocked ? 'fa-lock' : 'fa-unlock';
            //         $label    = $isUnlocked ? 'Lock' : 'Unlock';
            //         $title    = $isUnlocked ? "Lock $typeLabel" : "Unlock $typeLabel";

            //         return '
            //             <button type="button"
            //                     class="btn '.$btnClass.' btn-sm text-center actionEditCutoffTimeStatus mr-1 mb-2"
            //                     pickup-time-id="'.$id.'"
            //                     pickup-time-status="'.$status.'"
            //                     pickup-time-type="'.$typeLabel.'"
            //                     data-bs-toggle="modal"
            //                     data-bs-target="#modalEditCutoffTimeStatus"
            //                     title="'.$title.'">
            //                 <i class="fa-solid fa-xl '.$icon.'"></i> '.$label.' '.$typeLabel.'
            //             </button>
            //         ';
            //     };

            //     $result = '<div class="text-center">';

            //     // Always show TODAY button for all categories
            //     $result .= $buildButton($row->id, $row->status_today, "Today");

            //     // Only category 2 gets a SUCCEEDING button
            //     if ($row->category == 2) {
            //         $result .= '<br>';
            //         $result .= $buildButton($row->id, $row->status_succeeding, "Succeeding");
            //     }

            //     $result .= '</div>';

            //     // if($row->status_today == 1){
            //     //     $result .= '<button type="button" class="btn btn-danger btn-xs text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->status . '" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Lock Masterlist">';
            //     //     $result .=   '<i class="fa-solid fa-xl fa-lock"></i> Lock Today';
            //     //     $result .= '</button>';
            //     // }else{
            //     //     $result .= '<button type="button" class="btn btn-success btn-xs text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->status . '" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Unlock Masterlist">';
            //     //     $result .=    '<i class="fa-solid fa-xl fa-unlock"></i> Unlock Today';
            //     //     $result .= '</button>';
            //     // }

            //     // if($row->status_succeeding == 1){
            //     //     $result .=      '<button type="button" class="btn btn-danger btn-xs text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->status . '" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Lock Masterlist">';
            //     //     $result .=          '<i class="fa-solid fa-xl fa-lock"></i> Lock Succeeding';
            //     //     $result .=      '</button>';
            //     // }else{
            //     //     $result .=      '<button type="button" class="btn btn-success btn-xs text-center actionEditCutoffTimeStatus mr-1" pickup-time-id="' . $row->id . '" pickup-time-status="' . $row->status . '" data-bs-toggle="modal" data-bs-target="#modalEditCutoffTimeStatus" title="Unlock Masterlist">';
            //     //     $result .=          '<i class="fa-solid fa-xl fa-unlock"></i> Unlock Succeeding';
            //     //     $result .=      '</button>';
            //     // }

            //     $result .=  '</center>';
            //     return $result;
            // })
        ->rawColumns(['cutoff_time','status', 'today_label', 'succeeding_label'])
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
                'category' => 'required',
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
                        'category' => $request->category,
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
                'category' => 'required',
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
                        'category' => $request->category,
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

        try {
            // Validate input
            $request->validate([
                'cutoff_time_id'    => 'required|exists:cutoff_times,id',
                'status'            => 'required|in:0,1',
                'cutoff_time_type'  => 'required|in:Today,Succeeding', // type must be passed from modal
            ]);

            $cutoff = CutoffTime::findOrFail($request->cutoff_time_id);

            // Determine which column to update
            $column = $request->cutoff_time_type === 'Today' ? 'status_today' : 'status_succeeding';

            // Toggle status: if current = 1 → 0, else → 1
            $cutoff->{$column}       = $request->status == 1 ? 0 : 1;
            $cutoff->last_updated_by = $_SESSION['rapidx_user_id'];
            $cutoff->updated_at      = now();
            $cutoff->save();

            return response()->json([
                'hasError' => 0,
                'status'   => (int)$cutoff->{$column},
                'type'     => $request->type
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'hasError' => 1,
                'errors'   => $e->errors() // returns an array of validation messages
            ], 422);
        }

        //OLD CODE COMMENTED CLARK 11/20/2025
        // $data = $request->all(); // collect all input fields
        // $validator = Validator::make($data, [
        //     'cutoff_time_id' => 'required',
        //     'status' => 'required',
        // ]);

        // if($validator->passes()){
        //     if($request->status == 1){
        //         CutoffTime::where('id', $request->cutoff_time_id)
        //             ->update([
        //                     'status' => 0,
        //                     'last_updated_by' => $_SESSION['rapidx_user_id'],
        //                     'updated_at' => date('Y-m-d H:i:s'),
        //                 ]
        //             );
        //         $status = CutoffTime::where('id', $request->cutoff_time_id)->value('status');
        //         return response()->json(['hasError' => 0, 'status' => (int)$status]);
        //     }else{
        //         CutoffTime::where('id', $request->cutoff_time_id)
        //             ->update([
        //                     'status' => 1,
        //                     'last_updated_by' => $_SESSION['rapidx_user_id'],
        //                     'updated_at' => date('Y-m-d H:i:s'),
        //                 ]
        //             );
        //         $status = CutoffTime::where('id', $request->cutoff_time_id)->value('status');
        //         return response()->json(['hasError' => 0, 'status' => (int)$status]);
        //     }
        // }else{
        //     return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
        // }
    }
}
