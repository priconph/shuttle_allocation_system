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
use App\Models\Masterlist;
use App\Models\SystemOneHRIS;
use App\Models\SystemOneSubcon;
use App\Models\CutoffTime;

class MasterlistController extends Controller
{
    public function viewMasterlistTest(Request $request){
        $masterlistData = Masterlist::with([
            'hris_info.position_info',
            'hris_info.division_info',
            'hris_info.department_info',
            'hris_info.section_info',
            'hris_info' => function($q){
                $q->where('EmpStatus', 1);
            },

            'subcon_info.position_info',
            'subcon_info.division_info',
            'subcon_info.department_info',
            'subcon_info.section_info',
            'subcon_info' => function($q){
                $q->where('EmpStatus', 1);
            },
            'routes_info',
            'rapidx_user_info',
        ])
        ->where('is_deleted', 0)
        ->get();

        return $masterlistData;
    }
    public function viewMasterlist(Request $request){
        $userData = User::where('rapidx_user_id', $request->rapidXUserId)->value('user_role_id');
        if($userData == 1){ // 1-Admin, 2-PIC, 3-Superior
            $masterlistData = Masterlist::with([
                'hris_info.position_info',
                'hris_info.division_info',
                'hris_info.department_info',
                'hris_info.section_info',
                'hris_info' => function($q){
                    $q->where('EmpStatus', 1);
                },
    
                'subcon_info.position_info',
                'subcon_info.division_info',
                'subcon_info.department_info',
                'subcon_info.section_info',
                'subcon_info' => function($q){
                    $q->where('EmpStatus', 1);
                },
                'routes_info',
                'rapidx_user_info',
            ])
            ->where('is_deleted', 0)
            ->get();
        }else{
            $masterlistData = Masterlist::with([
                'hris_info.position_info',
                'hris_info.division_info',
                'hris_info.department_info',
                'hris_info.section_info',
                'hris_info' => function($q){
                    $q->where('EmpStatus', 1);
                },
    
                'subcon_info.position_info',
                'subcon_info.division_info',
                'subcon_info.department_info',
                'subcon_info.section_info',
                'subcon_info' => function($q){
                    $q->where('EmpStatus', 1);
                },
                'routes_info',
                'rapidx_user_info',
            ])
            ->where('is_deleted', 0)
            ->where('created_by', $request->rapidXUserId)
            ->get();
        }
        
        return DataTables::of($masterlistData)
            ->addColumn('masterlist_status', function($row){
                $result = "";
                if($row->masterlist_status == 1){
                    $result .= '<center><span class="badge badge-pill badge-success">Active</span></center>';
                }
                else{
                    $result .= '<center><span class="badge badge-pill text-secondary" style="background-color: #E6E6E6">Inactive</span></center>';
                }
                return $result;
            })
            ->addColumn('action', function($row){
                date_default_timezone_set('Asia/Manila');
                /**
                 * Cutoff Time
                 */
                // $cutoffTimeData = CutoffTime::value('cutoff_time');
                // $parsedTime = Carbon::parse($cutoffTimeData)->format('h:i');
                // $dateNow = Carbon::now()->format('h:i');
                // $disabled = 'disabled';
                // if($parsedTime != $dateNow){
                //     $disabled = '';
                // }

                /**
                 * Lock/Unlock Masterlist
                 * to disable editing in Masterlist Module
                 */
                $disabled = '';
                $cutoffTimeData = CutoffTime::value('cutoff_time_status');
                
                if($row->masterlist_status == 1){
                    $result =   '<center>';
                    // $result =   'dates '.$parsedTime . ' & ' . $dateNow;

                    if($cutoffTimeData == 0){
                        $disabled = 'disabled';
                    }
                    $result .=      '<button type="button" class="btn btn-primary btn-xs text-center actionEditMasterlist mr-1" '.$disabled.' masterlist-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modalAddMasterlist" title="Edit Masterlist Details">';
                    $result .=          '<i class="fa fa-xl fa-edit"></i> ';
                    $result .=      '</button>';
                    $result .=      '<button type="button" class="btn btn-warning text-white btn-xs text-center actionEditMasterlistStatus mr-1" masterlist-id="' . $row->id . '" masterlist-status="' . $row->masterlist_status . '" data-bs-toggle="modal" data-bs-target="#modalEditMasterlistStatus" title="Deactivate Masterlist">';
                    $result .=          '<i class="fa-solid fa-xl fa-ban"></i>';
                    $result .=      '</button>';
                    $result .=      '<button type="button" class="btn btn-danger btn-xs text-center actionDeleteMasterlistStatus mr-1" masterlist-id="' . $row->id . '" masterlist-is-deleted="' . $row->is_deleted . '" data-bs-toggle="modal" data-bs-target="#modalDeleteMasterlistStatus" title="Delete Masterlist">';
                    $result .=          '<i class="fa-solid fa-xl fa-trash"></i>';
                    $result .=      '</button>';
                    $result .=  '</center>';
                }
                else{
                    $result =   '<center>';
                    $result .=      '<button type="button" class="btn btn-primary btn-xs text-center actionEditMasterlist mr-1" masterlist-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modalAddMasterlist" title="Edit Masterlist Details">';
                    $result .=          '<i class="fa fa-xl fa-edit"></i>';
                    $result .=      '</button>';
                    $result .=      '<button type="button" class="btn btn-warning btn-xs text-center actionEditMasterlistStatus mr-1" masterlist-id="' . $row->id . '" masterlist-status="' . $row->masterlist_status . '" data-bs-toggle="modal" data-bs-target="#modalEditMasterlistStatus" title="Activate Masterlist">';
                    $result .=          '<i class="fa-solid fa-xl fa-arrow-rotate-right"></i>';
                    $result .=      '</button>';
                    $result .=  '</center>';
                }
                return $result;
            })
            ->addColumn('masterlist_employee_name', function($row){
                $result = "";
                if($row->hris_info != null){ // For Pricon
                    $result .= '<center><span>'.$row->hris_info->FirstName .' '. $row->hris_info->LastName.'</span></center>';
                }
                else if($row->subcon_info != null){ // For Subcon
                    $result .= '<center><span>'.$row->subcon_info->FirstName .' '. $row->subcon_info->LastName.'</span></center>';
                }
                else{
                    $result .= '<center><span>Resigned</span></center>';
                }
                return $result;
            })
            ->addColumn('masterlist_employee_gender', function($row){
                $result = "";
                if($row->hris_info != null){ // For Pricon
                    if($row->hris_info->Gender == 1){
                        $result .= '<center><span>Male</span></center>';
                    }else{
                        $result .= '<center><span>Female</span></center>';
                    }
                    
                }
                else if($row->subcon_info != null){ // For Subcon
                    if($row->subcon_info->Gender == 1){
                        $result .= '<center><span>Male</span></center>';
                    }else{
                        $result .= '<center><span>Female</span></center>';
                    }
                }
                else{
                    $result .= '<center><span>Resigned</span></center>';
                }
                return $result;
            })
            ->addColumn('masterlist_employee_position', function($row){
                $result = "";
                if($row->hris_info != null){ // For Pricon
                    if($row->hris_info->position_info != null){
                        $result .= '<center><span>'.$row->hris_info->position_info->Position .'</span></center>';
                    }else{
                        $result .= '<center><span>-</span></center>';
                    }
                }
                else if($row->subcon_info != null){ // For Subcon
                    if($row->subcon_info->position_info != null){
                        $result .= '<center><span>'.$row->subcon_info->position_info->Position .'</span></center>';
                    }else{
                        $result .= '<center><span>-</span></center>';
                    }
                }
                else{
                    $result .= '<center><span>Resigned</span></center>';
                }
                return $result;
            })
            ->addColumn('masterlist_employee_division', function($row){
                $result = "";
                if($row->hris_info != null){ // For Pricon
                    if($row->hris_info->division_info != null){
                        $result .= '<center><span>'.$row->hris_info->division_info->Division .'</span></center>';
                    }else{
                        $result .= '<center><span>-</span></center>';
                    }
                }
                else if($row->subcon_info != null){ // For Subcon
                    if($row->subcon_info->division_info != null){
                        $result .= '<center><span>'.$row->subcon_info->division_info->Division .'</span></center>';
                    }else{
                        $result .= '<center><span>-</span></center>';
                    }
                }
                else{
                    $result .= '<center><span>Resigned</span></center>';
                }
                return $result;
            })
            ->addColumn('masterlist_employee_department', function($row){
                $result = "";
                if($row->hris_info != null){ // For Pricon
                    if($row->hris_info->department_info != null){
                        $result .= '<center><span>'.$row->hris_info->department_info->Department .'</span></center>';
                    }else{
                        $result .= '<center><span>-</span></center>';
                    }
                }
                else if($row->subcon_info != null){ // For Subcon
                    if($row->subcon_info->department_info != null){
                        $result .= '<center><span>'.$row->subcon_info->department_info->Department .'</span></center>';
                    }else{
                        $result .= '<center><span>-</span></center>';
                    }
                }
                else{
                    $result .= '<center><span>Resigned</span></center>';
                }
                return $result;
            })
            ->addColumn('masterlist_employee_section', function($row){
                $result = "";
                if($row->hris_info != null){ // For Pricon
                    if($row->hris_info->section_info != null){
                        $result .= '<center><span>'.$row->hris_info->section_info->Section .'</span></center>';
                    }else{
                        $result .= '<center><span>-</span></center>';
                    }
                }
                else if($row->subcon_info != null){ // For Subcon
                    if($row->subcon_info->section_info != null){
                        $result .= '<center><span>'.$row->subcon_info->section_info->Section .'</span></center>';
                    }else{
                        $result .= '<center><span>-</span></center>';
                    }
                }
                else{
                    $result .= '<center><span>Resigned</span></center>';
                }
                return $result;
            })
        ->rawColumns([
            'masterlist_status', 
            'action', 
            'masterlist_employee_name', 
            'masterlist_employee_gender',
            'masterlist_employee_position',
            'masterlist_employee_division',
            'masterlist_employee_department',
            'masterlist_employee_section',
            ])
        ->make(true);
    }

    public function addMasterlist(Request $request){
        date_default_timezone_set('Asia/Manila');
        session_start();
        $data = $request->all();

        /* For Insert */
        if(!isset($request->masterlist_id)){
            $validator = Validator::make($data, [
                'employee_type' => 'required',
                'employee_number' => 'required', // When employee number is selected then systemone_id will automatically append values
                'systemone_id' => 'required',
                'masterlist_incoming' => 'required',
                'masterlist_outgoing' => 'required',
                'routes_id' => 'required',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
            } else {

                /**
                 * Validation for existing employee
                 */
                $masterlistData = Masterlist::where('masterlist_employee_number', $request->employee_number)
                ->where('is_deleted', 0)
                ->get();
                if(count($masterlistData) > 0){
                    return response()->json(['hasError' => 1, 'hasExisted' => count($masterlistData)]);
                }

                $insertData = [
                    'masterlist_employee_type' => $request->employee_type,
                    'masterlist_employee_number' => $request->employee_number,
                    'masterlist_incoming' => $request->masterlist_incoming,
                    'masterlist_outgoing' => $request->masterlist_outgoing,
                    'routes_id' => $request->routes_id,
                    'created_by' => $_SESSION['rapidx_user_id'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'is_deleted' => 0
                ];
                if($request->employee_type == 1){
                    $insertData['systemone_hris_id'] = $request->systemone_id;
                }else{
                    $insertData['systemone_subcon_id'] = $request->systemone_id;
                }

                DB::beginTransaction();
                try {
                    Masterlist::insert([
                        $insertData
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
                'masterlist_id' => 'required',
                'systemone_id' => 'required',
                'routes_id' => 'required',
                'masterlist_incoming' => 'required',
                'masterlist_outgoing' => 'required',
                // 'employee_type' => 'required',
                // 'employee_number' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
            } else {
                $updateData = [
                    'masterlist_incoming' => $request->masterlist_incoming,
                    'masterlist_outgoing' => $request->masterlist_outgoing,
                    'routes_id' => $request->routes_id,
                    'last_updated_by' => $_SESSION['rapidx_user_id'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                DB::beginTransaction();
                try {
                    Masterlist::where('id', $request->masterlist_id)->update(
                        $updateData
                    );

                    DB::commit();
                    return response()->json(['hasError' => 0]);
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['hasError' => 1, 'exceptionError' => $e]);
                }
            }
        }
    }

    public function getMasterlistById(Request $request){
        $masterlistData = Masterlist::where('id', $request->masterlistId)->get();
        return response()->json(['masterlistData' => $masterlistData]);
    }

    public function editMasterlistStatus(Request $request){        
        date_default_timezone_set('Asia/Manila');
        session_start();
        
        $data = $request->all(); // collect all input fields
        $validator = Validator::make($data, [
            'masterlist_id' => 'required',
            'masterlist_status' => 'required',
        ]);

        if($validator->passes()){
            if($request->masterlist_status == 1){
                Masterlist::where('id', $request->masterlist_id)
                    ->update([
                            'masterlist_status' => 0,
                            'last_updated_by' => $_SESSION['rapidx_user_id'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                $status = Masterlist::where('id', $request->masterlist_id)->value('masterlist_status');
                return response()->json(['hasError' => 0, 'status' => (int)$status]);
            }else{
                Masterlist::where('id', $request->masterlist_id)
                    ->update([
                            'masterlist_status' => 1,
                            'last_updated_by' => $_SESSION['rapidx_user_id'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                $status = Masterlist::where('id', $request->masterlist_id)->value('masterlist_status');
                return response()->json(['hasError' => 0, 'status' => (int)$status]);
            }
        }
        else{
            return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
        }
    }
    
    public function deleteMasterlist(Request $request){        
        date_default_timezone_set('Asia/Manila');
        session_start();
        
        $data = $request->all(); // collect all input fields
        $validator = Validator::make($data, [
            'masterlist_id' => 'required',
            'masterlist_is_deleted' => 'required',
        ]);

        if($validator->passes()){
            if($request->masterlist_is_deleted == 0){
                Masterlist::where('id', $request->masterlist_id)
                    ->update([
                            'is_deleted' => 1,
                            'last_updated_by' => $_SESSION['rapidx_user_id'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                $status = Masterlist::where('id', $request->masterlist_id)->value('is_deleted');
                return response()->json(['hasError' => 0, 'status' => (int)$status]);
            }
        }
        else{
            return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
        }
    }
}
