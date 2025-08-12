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

use App\Models\Routes;
use App\Models\Masterlist;
use App\Models\RapidXUser;
use App\Models\User;
use App\Models\Allocations;
// use App\Models\SystemOneHRIS;
// use App\Models\SystemOneSubcon;


use App\Models\SystemOneDivision;
use App\Models\SystemOneDepartment;
use App\Models\SystemOneSection;

class AllocationController extends Controller
{
    public function viewAllocations(Request $request){
        $userData = User::where('rapidx_user_id', $request->rapidXUserId)->value('user_role_id');
        if($userData == 1){ // 1-Admin, 2-PIC, 3-Superior
            $allocationData = Allocations::with([
                'requeste_ml_info',
                'requeste_ml_info.hris_info' => function($q){
                                                $q->where('EmpStatus', 1);
                },
                'requeste_ml_info.rapidx_user_info',
                'requeste_ml_info.routes_info',
                'alloc_route_info',
                'requestor_user_info',
            ])
            ->where('is_deleted', 0)
            ->get();
        }else{
            $allocationData = Allocations::with([
                'requeste_ml_info',
                'requeste_ml_info.hris_info' => function($q){
                                                $q->where('EmpStatus', 1);
                },
                'requeste_ml_info.rapidx_user_info',
                'requeste_ml_info.routes_info',
                'alloc_route_info',
                'requestor_user_info',
            ])
            ->where('is_deleted', 0)
            ->where('created_by', $request->rapidXUserId)
            ->get();
        }

        return DataTables::of($allocationData)
            ->addColumn('action', function($row){
                date_default_timezone_set('Asia/Manila');
                $disabled = '';
                if($row->masterlist_status == 1){
                    $result =   '<center>';
                    $result .=  '</center>';
                }else{
                    $result =   '<center>';
                    $result .=  '</center>';
                }
                return $result;
            })
            ->addColumn('request_status', function($row){
                $result = "";
                if($row->request_status == 0){
                    $result .= '<center><span class="badge badge-pill badge-success">Active</span></center>';
                }else{
                    $result .= '<center><span class="badge badge-pill text-secondary" style="background-color: #E6E6E6">Inactive</span></center>';
                }
                return $result;
            })
            ->addColumn('allocation_date', function($row){
                $result = "";
                $result .= '<center><span>'.$row->alloc_date_start .'-'. $row->alloc_date_end.'</span></center>';
                return $result;
            })
            ->addColumn('masterlist_name', function($row){
                $result = "";
                if($row->requeste_ml_info->hris_info != null){ // For Pricon
                    $result .= '<center><span>'.$row->requeste_ml_info->hris_info->FirstName .' '. $row->requeste_ml_info->hris_info->LastName.'</span></center>';
                }else if($row->requeste_ml_info->subcon_info != null){ // For Subcon
                    $result .= '<center><span>'.$row->requeste_ml_info->subcon_info->FirstName .' '. $row->requeste_ml_info->subcon_info->LastName.'</span></center>';
                }else{
                    $result .= '<center><span>Resigned</span></center>';
                }
                return $result;
            })
        ->rawColumns([
            'action',
            'request_status',
            'allocation_date',
            'masterlist_name',
            ])
        ->make(true);
    }

    public function viewMasterListForAllocation(Request $request){
        $userData = User::where('rapidx_user_id', $request->rapidXUserId)->value('user_role_id');
        if($userData == 1){ // 1-Admin, 2-PIC, 3-Superior
            $masterlistData = Masterlist::with([
                'hris_info' => function ($q) use ($request) {
                    $q->where('EmpStatus', 1)
                    ->with([
                        'position_info',
                        'division_info',
                        'department_info',
                        'section_info',
                    ]);
                },
                'subcon_info' => function ($q) {
                    $q->where('EmpStatus', 1)
                    ->with([
                        'position_info',
                        'division_info',
                        'department_info',
                        'section_info',
                    ]);
                },
                'routes_info',
                'rapidx_user_info',
            ])
            ->where('is_deleted', 0)
            // ->when(!empty($request->department), function ($query) use ($request) {
            //     $query->whereHas('hris_info.department_info', function ($q) use ($request) {
            //         $q->where('tbl_Department.pkid', $request->department); // or change to ID if needed
            //     });
            // })
            // ->when(!empty($request->section), function ($query) use ($request) {
            //     $query->whereHas('hris_info.section_info', function ($q) use ($request) {
            //         $q->where('tbl_Section.pkid', $request->section); // or change to ID if needed
            //     });
            // })
            ->get();
        }else{
            $masterlistData = Masterlist::with([
                'hris_info' => function ($q) use ($request) {
                    $q->where('EmpStatus', 1)
                    ->with([
                        'position_info',
                        'division_info',
                        'department_info',
                        'section_info',
                    ]);
                },
                'subcon_info' => function ($q) {
                    $q->where('EmpStatus', 1)
                    ->with([
                        'position_info',
                        'division_info',
                        'department_info',
                        'section_info',
                    ]);
                },
                'routes_info',
                'rapidx_user_info',
            ])
            ->where('is_deleted', 0)
            // ->when(!empty($request->department), function ($query) use ($request) {
            //     $query->whereHas('hris_info.department_info', function ($q) use ($request) {
            //         $q->where('tbl_Department.pkid', $request->department); // or change to ID if needed
            //     });
            // })
            // ->when(!empty($request->section), function ($query) use ($request) {
            //     $query->whereHas('hris_info.section_info', function ($q) use ($request) {
            //         $q->where('tbl_Section.pkid', $request->section); // or change to ID if needed
            //     });
            // })
            ->where('created_by', $request->rapidXUserId)
            ->get();
        }

        return DataTables::of($masterlistData)
            ->addColumn('action', function($row){
                $data = '';
                if($row->id){
                    $data = $row->id;
                }

                $result = "";
                $result .= "<center>";
                    $result .= "<input class='itemCheckbox' type='checkbox' data-checkbox-id='$row->id' style='width: 25px; height: 25px;  text-align: center;' id='checkBoxId' name='checkbox_id[]' value='".$data."'>";
                $result .= "</center>";

                return $result;

                // date_default_timezone_set('Asia/Manila');
                // $disabled = '';

                // if($row->masterlist_status == 1){
                //     $result =   '<center>';
                //     $result .=  '</center>';
                // }else{
                //     $result =   '<center>';
                //     $result .=  '</center>';
                // }
                // return $result;
            })
            ->addColumn('name', function($row){
                $result = "";
                if($row->hris_info != null){ // For Pricon
                    $result .= '<center><span>'.$row->hris_info->FirstName .' '. $row->hris_info->LastName.'</span></center>';
                }else if($row->subcon_info != null){ // For Subcon
                    $result .= '<center><span>'.$row->subcon_info->FirstName .' '. $row->subcon_info->LastName.'</span></center>';
                }else{
                    $result .= '<center><span>Resigned</span></center>';
                }
                return $result;
            })
            ->addColumn('factory', function($row){
                $result = "";
                if($row->masterlist_factory != null){ //Existing Data
                    $result .= '<center><span>'.$row->masterlist_factory.'</span></center>';
                }else{
                    $result .= '<center><span>-</span></center>';
                }
                return $result;
            })
            ->addColumn('department', function($row){
                $result = "";
                if($row->hris_info != null){ // For Pricon
                    if($row->hris_info->department_info != null){
                        $result .= '<center><span>'.$row->hris_info->department_info->Department .'</span></center>';
                    }else{
                        $result .= '<center><span>-</span></center>';
                    }
                }else if($row->subcon_info != null){ // For Subcon
                    if($row->subcon_info->department_info != null){
                        $result .= '<center><span>'.$row->subcon_info->department_info->Department .'</span></center>';
                    }else{
                        $result .= '<center><span>-</span></center>';
                    }
                }else{
                    $result .= '<center><span>Resigned</span></center>';
                }
                return $result;
            })
            ->addColumn('section', function($row){
                $result = "";
                if($row->hris_info != null){ // For Pricon
                    if($row->hris_info->section_info != null){
                        $result .= '<center><span>'.$row->hris_info->section_info->Section .'</span></center>';
                    }else{
                        $result .= '<center><span>-</span></center>';
                    }
                }else if($row->subcon_info != null){ // For Subcon
                    if($row->subcon_info->section_info != null){
                        $result .= '<center><span>'.$row->subcon_info->section_info->Section .'</span></center>';
                    }else{
                        $result .= '<center><span>-</span></center>';
                    }
                }else{
                    $result .= '<center><span>Resigned</span></center>';
                }
                return $result;
            })
        ->rawColumns([
            'action',
            'name',
            'factory',
            'department',
            'section',
            ])
        ->make(true);
    }

    public function getUserInfo(Request $request){
        $userData = User::with(['rapidx_user_info'])->where('rapidx_user_id', $request->userId)->where('is_deleted', 0)->first();

        // return $userData;
        return response()->json(['userDetails' => $userData]);
    }

    public function getMasterlistInfoForFilter(Request $request){
        $departments = Masterlist::where('is_deleted', 0)
                        ->with(['hris_info.department_info' => function ($q) {
                            $q->select('pkid', 'Department'); // Adjust fields based on your table
                        }])
                        ->get()
                        ->pluck('hris_info.department_info') // Get all related departments
                        ->filter() // Remove nulls in case of missing relationships
                        ->unique('Department') // Or use 'Department' if that's what you want
                        ->values(); // Re-index the array

        // return $departments;
        $sectionDetails = Masterlist::where('is_deleted', 0)
                        ->with(['hris_info.section_info' => function ($q) {
                            $q->select('pkid', 'Section'); // Adjust fields based on your table
                        }])
                        ->get()
                        ->pluck('hris_info.section_info') // Get all related departments
                        ->filter() // Remove nulls in case of missing relationships
                        ->unique('Section') // Or use 'Department' if that's what you want
                        ->values(); // Re-index the array

        // $masterlistData = Masterlist::with([
        //                     'hris_info' => function ($q) use ($request) {
        //                         $q->where('EmpStatus', 1)
        //                         ->with([
        //                             'department_info',
        //                             'section_info',
        //                         ]);
        //                     },
        //                     'subcon_info' => function ($q) {
        //                         $q->where('EmpStatus', 1)
        //                         ->with([
        //                             'department_info',
        //                             'section_info',
        //                         ]);
        //                     },
        //                 ])
        //                 ->select('masterlists.*') // specify base table to avoid conflict with joins
        //                 ->where('is_deleted', 0)
        //                 ->distinct()
        //                 ->get();

        // return $masterlistData;
        // $departmentDetails =  SystemOneDepartment::select('pkid', 'Department')->where('isActive', 1)->distinct()->get();
        // $sectionDetails =  SystemOneSection::select('pkid', 'Section')->where('isActive', 1)->distinct()->get();

        return response()->json(['departmentDetails' => $departments, 'sectionDetails' => $sectionDetails]);
    }

    public function addAllocationData(Request $request){
        date_default_timezone_set('Asia/Manila');
        session_start();
        $data = $request->all();
        // return $data;
        // return $request->checkbox_id[0];
        /* For Insert */
        // if(!isset($request->masterlist_id)){
            // $validator = Validator::make($data, [
            //     'employee_type' => 'required',
            //     'employee_number' => 'required', //When employee number is selected then systemone_id will automatically append values
            //     'systemone_id' => 'required',
            //     'masterlist_incoming' => 'required',
            //     'masterlist_outgoing' => 'required',
            //     'routes_id' => 'required',
            // ]);

            // if ($validator->fails()){
            //     return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
            // }else{

                /**
                 * Validation for existing employee
                 */

                // $masterlistData = Allocations::where('requestee_ml_id', $request->request_emp_no)
                //                 ->where('is_deleted', 0)
                //                 ->get();

                // if(count($masterlistData) > 0){
                //     return response()->json(['hasError' => 1, 'hasExisted' => count($masterlistData)]);
                // }
                DB::beginTransaction();
                try {
                    foreach ($request->checkbox_id as $key => $value) {
                        Allocations::insert([
                            'request_type'     => $request->type_of_request,
                            'date_requested'   => $request->date_requested,
                            'alloc_date_start' => $request->start_date,
                            'alloc_date_end'   => $request->end_date,
                            'requestee_ml_id'  => $request->checkbox_id[$key],
                            'alloc_factory'    => $request->alloc_factory,
                            'alloc_incoming'   => $request->alloc_incoming,
                            'alloc_outgoing'   => $request->alloc_outgoing,
                            'requested_by'     => $request->requestor_id,
                            'created_by'       => $request->requestor_id,
                            'last_updated_by'  => $request->requestor_id,
                            'created_at'       => date('Y-m-d H:i:s'),
                            'updated_at'       => date('Y-m-d H:i:s'),
                        ]);
                    }

                    DB::commit();
                    return response()->json(['hasError' => 0]);
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['hasError' => 1, 'exceptionError' => $e]);
                }

                // $insertData = [
                //     'masterlist_factory' => $request->factory,
                //     'masterlist_employee_type' => $request->employee_type,
                //     'masterlist_employee_number' => $request->employee_number,
                //     'masterlist_incoming' => $request->masterlist_incoming,
                //     'masterlist_outgoing' => $request->masterlist_outgoing,
                //     'routes_id' => $request->routes_id,
                //     'factory' => 'required',
                //     'created_by' => $_SESSION['rapidx_user_id'],
                //     'created_at' => date('Y-m-d H:i:s'),
                //     'is_deleted' => 0
                // ];

                // if($request->employee_type == 1){
                //     $insertData['systemone_hris_id'] = $request->systemone_id;
                // }else{
                //     $insertData['systemone_subcon_id'] = $request->systemone_id;
                // }

                // DB::beginTransaction();
                // try {
                //     Masterlist::insert([
                //         $insertData
                //     ]);

                //     DB::commit();
                //     return response()->json(['hasError' => 0]);
                // } catch (\Exception $e) {
                //     DB::rollback();
                //     return response()->json(['hasError' => 1, 'exceptionError' => $e]);
                // }

            // }
        // }else{ /* For Update */
        //     $validator = Validator::make($data, [
        //         'masterlist_id' => 'required',
        //         'systemone_id' => 'required',
        //         'factory' => 'required',
        //         'routes_id' => $request->routes_id,
        //         'employee_type' => 'required',
        //         'employee_number' => 'required',
        //         // 'masterlist_incoming' => 'required',
        //         // 'masterlist_outgoing' => 'required',
        //     ]);

        //     if ($validator->fails()) {
        //         return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
        //     } else {
        //         $updateData = [
        //             'routes_id' => $request->routes_id,
        //             'masterlist_factory' => $request->factory,
        //             'masterlist_incoming' => $request->masterlist_incoming,
        //             'masterlist_outgoing' => $request->masterlist_outgoing,
        //             'last_updated_by' => $_SESSION['rapidx_user_id'],
        //             'updated_at' => date('Y-m-d H:i:s'),
        //         ];

        //         DB::beginTransaction();
        //         try {
        //            return Masterlist::where('id', $request->masterlist_id)->update(
        //                 $updateData
        //             );
        //             DB::commit();
        //             return response()->json(['hasError' => 0]);
        //         } catch (\Exception $e) {
        //             DB::rollback();
        //             return response()->json(['hasError' => 1, 'exceptionError' => $e]);
        //         }
        //     }
        // }
    }
}
