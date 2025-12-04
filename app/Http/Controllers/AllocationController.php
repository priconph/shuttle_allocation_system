<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
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
use App\Models\CutoffTime;
// use App\Models\SystemOneHRIS;
// use App\Models\SystemOneSubcon;

use App\Models\SystemOneDivision;
use App\Models\SystemOneDepartment;
use App\Models\SystemOneSection;

use App\Services\AllocationService;


class AllocationController extends Controller
{
    public function viewAllocations(Request $request){
        $userData = User::where('rapidx_user_id', $request->rapidXUserId)->value('user_role_id');
        if($userData == 1){ // 1-Admin, 2-PIC, 3-Superior
            $allocationData = Allocations::with([
                'request_ml_info',
                'request_ml_info.hris_info' => function($q){
                                                $q->where('EmpStatus', 1);
                },
                'request_ml_info.rapidx_user_info',
                'request_ml_info.routes_info',
                'alloc_route_info',
                'requestor_user_info',
            ])
            ->where('is_deleted', 0)
            ->when($request->Status != 'ALL', function ($query) use ($request) {
                $query->where('request_status', $request->Status); // or change to ID if needed
            })
            ->when($request->Month != 'ALL', function ($query) use ($request) {
                $query->whereMonth('created_at', $request->Month);
            })
            ->when($request->Year != 'ALL', function ($query) use ($request) {
                $query->whereYear('created_at', $request->Year);
            })
            ->when(!empty($request->RequestType), function ($query) use ($request) {
                $query->where('request_type', $request->RequestType); // or change to ID if needed
            })
            ->when(!empty($request->Factory) && $request->Factory != 'ALL', function ($query) use ($request) {
                $query->where('alloc_factory', $request->Factory); // or change to ID if needed
            })
            ->when(!empty($request->AllocationStartDate) && !empty($request->AllocationEndDate), function ($query) use ($request) {
                $query->whereDate('alloc_date_start', '>=', $request->AllocationStartDate)->whereDate('alloc_date_end', '<=', $request->AllocationEndDate) // or change to ID if needed
                        // ✅ Reversed ranges (start > end) — still include them
                        ->orWhere(function($qq) use ($request) {
                            $qq->whereColumn('alloc_date_start', '>', 'alloc_date_end');
                        });
            })
            ->groupBy('control_number')
            ->orderBy('control_number', 'DESC')
            ->get();
        }else{
            $allocationData = Allocations::with([
                'request_ml_info',
                'request_ml_info.hris_info' => function($q){
                                                $q->where('EmpStatus', 1);
                },
                'request_ml_info.rapidx_user_info',
                'request_ml_info.routes_info',
                'alloc_route_info',
                'requestor_user_info',
            ])
            ->where('is_deleted', 0)
            ->where('created_by', $request->rapidXUserId)
            ->when($request->Status != 'ALL', function ($query) use ($request) {
                $query->where('request_status', $request->Status); // or change to ID if needed
            })
            ->when(!empty($request->RequestType), function ($query) use ($request) {
                $query->where('request_type', $request->RequestType); // or change to ID if needed
            })
            ->when(!empty($request->Factory) && $request->Factory != 'ALL', function ($query) use ($request) {
                $query->where('alloc_factory', $request->Factory); // or change to ID if needed
            })
            ->when(!empty($request->AllocationStartDate) && !empty($request->AllocationEndDate), function ($query) use ($request) {
                $query->whereDate('alloc_date_start', '>=', $request->AllocationStartDate)->whereDate('alloc_date_end', '<=', $request->AllocationEndDate); // or change to ID if needed
            })
            ->groupBy('control_number')
            ->orderBy('control_number', 'DESC')
            ->get();
        }

        // --- 🔹 Load all cutoffs once (avoid repetitive DB calls) ---
        $cutoffTimes = CutoffTime::where('is_deleted', 0)->get();
        // $today = Carbon::today();

        // --- 🔹 Pass cutoffTimes and today into DataTables closure
        return DataTables::of($allocationData)
            // ->addColumn('action', function($row) use ($userData, $cutoffTimes, $today){
            //     date_default_timezone_set('Asia/Manila');
            //     $disabled = '';

            //     $allocDate = Carbon::parse($allocationDate)->startOfDay();

            //     // --- Cutoff Lock Logic Start ---
            //     $allocStart = Carbon::parse($row->alloc_date_start)->startOfDay();
            //     $allocEnd   = Carbon::parse($row->alloc_date_end)->endOfDay();

            //     // Normalize factory and schedule
            //     $allocFactory  = preg_replace('/\D/', '', (string) $row->alloc_factory);
            //     $allocSchedule = strtoupper(trim((string) $row->alloc_outgoing));

            //     $isLocked = false;

            //     // ✅ CASE 1: alloc_outgoing has a schedule (NOT "N/A" or null)
            //     if (!empty($allocSchedule) && $allocSchedule !== 'N/A') {

            //         // Find cutoff record for same factory + schedule
            //         $cutoffMatch = $cutoffTimes->first(function($cutoff) use ($allocFactory, $allocSchedule) {
            //             $cutoffFactory  = preg_replace('/\D/', '', (string) $cutoff->factory);
            //             $cutoffSchedule = strtoupper(trim((string) $cutoff->schedule));
            //             return $cutoffFactory == $allocFactory && $cutoffSchedule == $allocSchedule;
            //         });

            //         if ($cutoffMatch && (int) $cutoffMatch->status === 0){

            //             // 🔹 Rule 1: lock today's allocations
            //             if ($today->between($allocStart, $allocEnd)) {
            //                 $isLocked = true;
            //             }

            //             // 🔹 Rule 2: 7:30AM — lock future allocations (tomorrow and onward)
            //             if ($allocStart->gt($today)) {
            //                 $isLocked = true;
            //             }
            //         }
            //     }
            //     // ✅ CASE 2: today is between the allocation date AND alloc_outgoing is "N/A"
            //     else {
            //         // CASE 2.1: today is between the allocation date AND alloc_outgoing is "N/A"
            //         if ($today->between($allocStart, $allocEnd) && $allocSchedule == 'N/A') {
            //             // Check if any cutoff for this factory (today) is locked, except 7:30AM
            //             $hasLockedCutoff = $cutoffTimes->contains(function($cutoff) use ($allocFactory) {
            //                 $cutoffFactory  = preg_replace('/\D/', '', (string) $cutoff->factory);
            //                 $cutoffSchedule = strtoupper(trim((string) $cutoff->schedule));
            //                 $isLocked       = ((int) $cutoff->status === 0);

            //                 return $cutoffFactory == $allocFactory && $isLocked && $cutoffSchedule !== '7:30AM';
            //             });

            //             if ($hasLockedCutoff) {
            //                 $isLocked = true;
            //             }
            //         }// ✅ CASE 2.2: today is between the allocation date AND alloc_outgoing is empty/null
            //         else if ($today->between($allocStart, $allocEnd) && empty($allocSchedule)){
            //             // Check if any cutoff for today is locked, except 7:30AM
            //             $hasLockedCutoff = $cutoffTimes->contains(function($cutoff) {
            //                 $cutoffSchedule = strtoupper(trim((string) $cutoff->schedule));
            //                 $isLocked       = ((int) $cutoff->status === 0);

            //                 return $isLocked && $cutoffSchedule !== '7:30AM';
            //             });

            //             if ($hasLockedCutoff) {
            //                 $isLocked = true;
            //             }
            //         }
            //         // ✅ CASE 2.3: allocation date is in the future (start > today)
            //         // else if ($allocStart->gte($tomorrow) && (empty($allocSchedule) || $allocSchedule == 'N/A')) {
            //         else if ($allocStart->gt($today) && (empty($allocSchedule) || $allocSchedule == 'N/A')) {
            //             // Check if any cutoff for this factory (future lock) is locked, except 7:30AM
            //             $hasLockedCutoff = $cutoffTimes->contains(function($cutoff) use ($allocFactory) {
            //                 $cutoffFactory  = preg_replace('/\D/', '', (string) $cutoff->factory);
            //                 $cutoffSchedule = strtoupper(trim((string) $cutoff->schedule));
            //                 $isLocked       = ((int) $cutoff->status === 0);

            //                 return $cutoffFactory == $allocFactory && $isLocked && $cutoffSchedule !== '7:30AM';
            //             });

            //             if ($hasLockedCutoff) {
            //                 $isLocked = true;
            //             }
            //         }
            //     }

            //     // // --- Find cutoff record for the same factory and schedule ---
            //     // $cutoffMatch = $cutoffTimes->first(function($cutoff) use ($allocFactory, $allocSchedule) {
            //     //     $cutoffFactory  = preg_replace('/\D/', '', (string) $cutoff->factory);
            //     //     $cutoffSchedule = strtoupper(trim((string) $cutoff->schedule));

            //     //     return $cutoffFactory == $allocFactory && $cutoffSchedule == $allocSchedule;
            //     // });
            //     // // return $cutoffMatch;

            //     // if ($cutoffMatch && (int) $cutoffMatch->status === 0) {
            //     //     // 🔹 Rule 1: All schedules except 7:30AM — lock today's allocations
            //     //     if ($allocSchedule !== '7:30AM' && $today->between($allocStart, $allocEnd)) {
            //     //         $isLocked = true;
            //     //     }

            //     //     // 🔹 Rule 2: 7:30AM — lock future allocations (tomorrow and onward)
            //     //     if ($allocSchedule === '7:30AM' && $allocEnd->gte($tomorrow)){
            //     //         $isLocked = true;
            //     //     }
            //     // }

            //     if ($isLocked) {
            //         $disabled = 'disabled';
            //     }

            //     // Admin bypass: admins (userData == 1) are not disabled
            //     // if ($isLocked && $userData != 1) {
            //     // if ($isLocked) {
            //     //     $disabled = 'disabled title="Locked by cutoff"';
            //     // }
            //     // --- Cutoff Lock Logic End ---

            //     // --- Button rendering logic ---
            //     if($row->request_status == 0){
            //         $result =   '<center>';
            //             $result .=      '<button type="button" class="btn btn-primary btn-sm text-center mr-1 editRequest" '.$disabled.' data-control_no="'.$row->control_number.'">';
            //             $result .=          '<i class="fa-solid fa-pen-to-square fa-lg"></i> ';
            //             $result .=      '</button>';

            //             // $cutoffTimes = CutoffTime::where('is_deleted', 0)->where('cutoff_date', '>=', $row->alloc_date_end)->get();

            //             $result .=      '<button type="button" class="btn btn-danger btn-sm text-center mr-1 updateRequestStatus" '.$disabled.' data-control_no="'.$row->control_number.'" data-status="'.$row->request_status.'">';
            //             $result .=          '<i class="fa-solid fa-ban fa-lg"></i>';
            //             $result .=      '</button>';

            //         $result .=  '</center>';
            //     }else if($row->request_status == 1){
            //         $result =   '<center>';
            //             $result .=      '<button type="button" class="btn btn-primary btn-sm text-center mr-1 viewRequest" data-control_no="'.$row->control_number.'">';
            //             $result .=          '<i class="fa-solid fa-eye fa-lg"></i> ';
            //             $result .=      '</button>';

            //             $result .=      '<button type="button" class="btn btn-success btn-sm text-center mr-1 updateRequestStatus" '.$disabled.' data-control_no="'.$row->control_number.'" data-status="'.$row->request_status.'">';
            //             $result .=          '<i class="fa-solid fa-arrow-rotate-right fa-lg"></i>';
            //             $result .=      '</button>';
            //         $result .=  '</center>';
            //     }else{
            //         $result =   '<center>';
            //             $result .=      '<button type="button" class="btn btn-primary btn-sm text-center mr-1 viewRequest" data-control_no="'.$row->control_number.'">';
            //             $result .=          '<i class="fa-solid fa-eye fa-lg"></i> ';
            //             $result .=      '</button>';
            //         $result .=  '</center>';
            //     }
            //     return $result;
            // })
            // ->addColumn('action', function($row) use ($userData, $cutoffTimes, $today){
            ->addColumn('action', function($row) use ($userData, $cutoffTimes){
                date_default_timezone_set('Asia/Manila');

                $today = Carbon::today();
                $allocStart = Carbon::parse($row->alloc_date_start)->startOfDay();
                $allocEnd   = Carbon::parse($row->alloc_date_end)->endOfDay();

                // Determine day type
                if ($today->between($allocStart, $allocEnd)) {
                    $dayType = 'today';
                } else if ($today->lt($allocStart)) {
                    $dayType = 'succeeding';
                } else {
                    $dayType = 'past'; // old allocations always locked
                }
                // // Normalize incoming/outgoing + factory
                // $incoming = strtoupper(trim($row->alloc_incoming ?? ''));
                // $outgoing = strtoupper(trim($row->alloc_outgoing ?? ''));

                // Normalize values (no helper function)
                $incoming = ($row->alloc_incoming === null || trim($row->alloc_incoming) === '' || $row->alloc_incoming === 'N/A') ? 'N/A' : $row->alloc_incoming;
                $outgoing = ($row->alloc_outgoing === null || trim($row->alloc_outgoing) === '' || $row->alloc_outgoing === 'N/A') ? 'N/A' : $row->alloc_outgoing;
                $factory  = preg_replace('/\D/', '', (string)($row->alloc_factory ?? ''));

                // Build schedule list
                if ($incoming !== 'N/A' && $outgoing !== 'N/A') {
                    // $scheduleList = [$incoming, $outgoing];
                    $scheduleList = [$outgoing];
                }else if ($incoming === 'N/A' && $outgoing !== 'N/A') {
                    $scheduleList = [$outgoing];
                }else if ($incoming !== 'N/A' && $outgoing === 'N/A') {
                    $scheduleList = [$incoming];
                }else {
                    // Both N/A — special: disabled only if ANY schedule for same factory is locked
                    $scheduleList = [];
                }
                // return $scheduleList;
                // Lock evaluator
                $isLocked = false;

                if (empty($scheduleList)) {
                    // "Both N/A" → check ANY schedule of same factory
                    $hasLocked = $cutoffTimes->contains(function($c) use ($dayType) {

                        // $cFactory  = preg_replace('/\D/', '', (string)$c->factory);

                        // if ($cFactory != $factory) {
                        //     return false;
                        // }
                        if ($dayType === 'today'     && $c->status_today == 0) return true;
                        if ($dayType === 'succeeding' && $c->status_succeeding == 0) return true;

                        return false;
                    });
                    $isLocked = $hasLocked;
                }else{
                    // Regular (incoming/outgoing have valid schedules)
                    foreach ($scheduleList as $sched){

                        $cutoffMatch = $cutoffTimes->first(function($c) use ($factory, $sched) {

                            $cFactory  = preg_replace('/\D/', '', (string)$c->factory);
                            $cSchedule = strtoupper(trim($c->schedule));

                            return ($cFactory == $factory && $cSchedule == $sched);
                        });
                        // return $dayType;
                        // return $cutoffMatch->status_today;

                        if (!$cutoffMatch) {
                            // No schedule match = safest = locked
                            $isLocked = false;
                            return 'false today';
                            break;
                        }

                        if ($dayType === 'today' && $cutoffMatch->status_today == 0) {
                            $isLocked = true;
                            // return 'true today';
                            break;
                        }

                        if ($dayType === 'succeeding' && $cutoffMatch->status_succeeding == 0) {
                            $isLocked = true;
                            // return 'true succeeding';
                            break;
                        }

                    }
                }

                /* -----------------------------
                Admin Bypass
                ------------------------------*/
                // $isLocked = 'true';
                // $disabled = $isLocked ? "disabled" : "";
                // $disabled = ($isLocked && $userData != 1) ? "disabled" : "";
                /* -----------------------------
                All Action Button Disabled
                ------------------------------*/
                // $disabled = "disabled";
                // $disabled = $isLocked ? "disabled" : "";
                /* -----------------------------
                All Action Button Enabled
                ------------------------------*/
                $disabled = "";
                $isLocked = 0;
                /* -----------------------------
                NEW CONDITION: allocation date range check
                ------------------------------*/
                // if ($allocStart->lt($today)) {
                //     $disabled = "disabled";
                //     $isLocked = true;
                // }

                // --- Button rendering logic ---
                if($row->request_status == 0){
                    $result =   '<center>';
                        // $result .=      '<button type="button" class="btn btn-primary btn-sm text-center mr-1 editRequest" '.$disabled.' data-is-locked="'.$isLocked.'" data-control_no="'.$row->control_number.'">';
                        $result .=      '<button type="button" class="btn btn-primary btn-sm text-center mr-1 editRequest" '.$disabled.' data-is-locked="'.$isLocked.'" data-control_no="'.$row->control_number.'">';
                        $result .=          '<i class="fa-solid fa-pen-to-square fa-lg"></i> ';
                        $result .=      '</button>';

                        $result .=      '<button type="button" class="btn btn-primary btn-sm text-center mr-1 viewRequest" data-control_no="'.$row->control_number.'">';
                        $result .=          '<i class="fa-solid fa-eye fa-lg"></i> ';
                        $result .=      '</button>';

                        // $result .=      '<button type="button" class="btn btn-danger btn-sm text-center mr-1 updateRequestStatus" '.$disabled.' data-is-locked="'.$isLocked.'" data-control_no="'.$row->control_number.'" data-status="'.$row->request_status.'">';
                        $result .=      '<button type="button" class="btn btn-danger btn-sm text-center mr-1 updateRequestStatus" data-control_no="'.$row->control_number.'" data-status="'.$row->request_status.'">';
                        $result .=          '<i class="fa-solid fa-ban fa-lg"></i>';
                        $result .=      '</button>';

                    $result .=  '</center>';
                }else if($row->request_status == 1){
                    $result =   '<center>';
                        $result .=      '<button type="button" class="btn btn-primary btn-sm text-center mr-1 viewRequest" data-control_no="'.$row->control_number.'">';
                        $result .=          '<i class="fa-solid fa-eye fa-lg"></i> ';
                        $result .=      '</button>';

                        // $result .=      '<button type="button" class="btn btn-success btn-sm text-center mr-1 updateRequestStatus" '.$disabled.' data-is-locked="'.$isLocked.'" data-control_no="'.$row->control_number.'" data-status="'.$row->request_status.'">';
                        $result .=      '<button type="button" class="btn btn-success btn-sm text-center mr-1 updateRequestStatus" data-control_no="'.$row->control_number.'" data-status="'.$row->request_status.'">';
                        $result .=          '<i class="fa-solid fa-arrow-rotate-right fa-lg"></i>';
                        $result .=      '</button>';
                    $result .=  '</center>';
                }else{
                    $result =   '<center>';
                        $result .=      '<button type="button" class="btn btn-primary btn-sm text-center mr-1 viewRequest" data-control_no="'.$row->control_number.'">';
                        $result .=          '<i class="fa-solid fa-eye fa-lg"></i> ';
                        $result .=      '</button>';
                    $result .=  '</center>';
                }
                return $result;
            })
            ->addColumn('date_created', function($row){
                $result = "";
                $result .= '<center><span>'.$row->created_at.'</span></center>';
                return $result;
            })
            ->addColumn('request_status', function($row){
                $result = "";
                if($row->request_status == 0){
                    $result .= '<center><span class="badge badge-pill badge-success">ACTIVE</span></center>';
                }else if($row->request_status == 1){
                    $result .= '<center><span class="badge badge-pill" style="background-color: #f05757ff">INACTIVE</span></center>';
                }else if($row->request_status == 2){
                    $result .= '<center><span class="badge badge-pill" style="background-color: #866e6eff">FINISHED</span></center>';
                }else{
                    $result .= '<center><span class="badge badge-pill" style="background-color: #000000ff">N/A</span></center>';
                }
                return $result;
            })
            ->addColumn('request_category', function($row){
                $result = "";
                if($row->request_type == 1){
                    $result .= '<center><span class="badge badge-pill badge-primary">Change Schedule</span></center>';
                }else if($row->request_type == 2){
                    $result .= '<center><span class="badge badge-pill badge-secondary">Not Riding Shuttle</span></center>';
                }else{
                    $result .= '<center><span class="badge badge-pill badge-danger">N/A</span></center>';
                }
                return $result;
            })
            ->addColumn('allocation_date', function($row){
                $result = "";
                $result .= '<center><span>'.$row->alloc_date_start .' - '. $row->alloc_date_end.'</span></center>';
                return $result;
            })
            ->addColumn('allocated_factory', function($row){
                $result = "";
                if($row->request_type == 2){
                    $result .= '<center><span class="badge badge-pill badge-secondary">Not Riding Shuttle</span></center>';
                }else{
                    $result .= '<center><span>'.$row->alloc_factory.'</span></center>';
                }
                return $result;
            })
            ->addColumn('no_of_allocated_emp', function($row){
                $count = Allocations::select('id')->where('is_deleted', 0)->where('control_number', $row->control_number)->count();
                $result = "";
                $result .= '<center><span>'.$count.'</span></center>';
                return $result;
            })
            ->addColumn('masterlist_name', function($row){
                $result = "";
                if($row->request_ml_info->hris_info != null){ // For Pricon
                    $result .= '<center><span>'.$row->request_ml_info->hris_info->FirstName .' '. $row->request_ml_info->hris_info->LastName.'</span></center>';
                }else if($row->request_ml_info->subcon_info != null){ // For Subcon
                    $result .= '<center><span>'.$row->request_ml_info->subcon_info->FirstName .' '. $row->request_ml_info->subcon_info->LastName.'</span></center>';
                }else{
                    $result .= '<center><span>Resigned</span></center>';
                }
                return $result;
            })
        ->rawColumns([
            'action',
            'date_created',
            'request_status',
            'request_category',
            'allocation_date',
            'allocated_factory',
            'no_of_allocated_emp',
            'masterlist_name',
            ])
        ->make(true);
    }

    // public function viewMasterListForAllocation(Request $request){
    //     $userData = User::where('rapidx_user_id', $request->rapidXUserId)->value('user_role_id');
    //     $requestMlIds = '';
    //     $isViewMode = $request->isViewMode;

    //     if($request->requestControlNo){
    //         $requestMlIds = Allocations::select('requestee_ml_id')->where('is_deleted', 0)->where('control_number', $request->requestControlNo)->get();
    //     }

    //     if($userData == 1){ // 1-Admin, 2-PIC, 3-Superior
    //         $masterlistData = Masterlist::with([
    //             'hris_info' => function ($q) use ($request) {
    //                 $q->where('EmpStatus', 1)
    //                 ->with([
    //                     'position_info',
    //                     'division_info',
    //                     'department_info',
    //                     'section_info',
    //                 ]);
    //             },
    //             'subcon_info' => function ($q) {
    //                 $q->where('EmpStatus', 1)
    //                 ->with([
    //                     'position_info',
    //                     'division_info',
    //                     'department_info',
    //                     'section_info',
    //                 ]);
    //             },
    //             'routes_info',
    //             'rapidx_user_info',
    //         ])
    //         ->when($isViewMode != 2, function ($query){
    //             $query->where('is_deleted', 0);
    //         })
    //         ->when($request->requestControlNo, function ($query) use ($requestMlIds) {
    //             $query->whereIn('id', $requestMlIds);
    //         })
    //         // ->get();
    //         // ->when(!empty($request->department), function ($query) use ($request) {
    //         //     $query->whereHas('hris_info.department_info', function ($q) use ($request) {
    //         //         $q->where('tbl_Department.pkid', $request->department); // or change to ID if needed
    //         //     });
    //         // })
    //         // ->when(!empty($request->section), function ($query) use ($request) {
    //         //     $query->whereHas('hris_info.section_info', function ($q) use ($request) {
    //         //         $q->where('tbl_Section.pkid', $request->section); // or change to ID if needed
    //         //     });
    //         // })
    //         ->get();
    //     }else{
    //         $masterlistData = Masterlist::with([
    //             'hris_info' => function ($q) use ($request) {
    //                 $q->where('EmpStatus', 1)
    //                 ->with([
    //                     'position_info',
    //                     'division_info',
    //                     'department_info',
    //                     'section_info',
    //                 ]);
    //             },
    //             'subcon_info' => function ($q) {
    //                 $q->where('EmpStatus', 1)
    //                 ->with([
    //                     'position_info',
    //                     'division_info',
    //                     'department_info',
    //                     'section_info',
    //                 ]);
    //             },
    //             'routes_info',
    //             'rapidx_user_info',
    //         ])
    //         ->when($isViewMode != 2, function ($query){
    //             $query->where('is_deleted', 0);
    //         })
    //         ->when($request->requestControlNo, function ($query) use ($requestMlIds) {
    //             $query->whereIn('id', $requestMlIds);
    //         })
    //         // ->whereHas('hris_info.department_info', function ($q) use ($request) {
    //         //     $q->where('Department', $request->department); // or 'DepartmentID' if filtering by ID
    //         // })
    //         // ->whereHas('hris_info.section_info', function ($q) use ($request) {
    //         //     $q->where('Section', $request->section); // or 'DepartmentID' if filtering by ID
    //         // })
    //         // ->when(!empty($request->department), function ($query) use ($request) {
    //         //     $query->whereHas('hris_info.department_info', function ($q) use ($request) {
    //         //         $q->where('tbl_Department.pkid', $request->department); // or change to ID if needed
    //         //     });
    //         // })
    //         // ->when(!empty($request->section), function ($query) use ($request) {
    //         //     $query->whereHas('hris_info.section_info', function ($q) use ($request) {
    //         //         $q->where('tbl_Section.pkid', $request->section); // or change to ID if needed
    //         //     });
    //         // })
    //         // ->where('created_by', $request->rapidXUserId)
    //         ->get();
    //     }
    //     return DataTables::of($masterlistData)
    //         ->addColumn('action', function($row) use ($requestMlIds){
    //             $result = "";
    //             if($requestMlIds != ''){
    //                 $result .= "<center>";
    //                     $result .= "<button class='btn btn-md btn-danger btnRemoveEmp' type='button' data-checkbox-id='$row->id'><i class='fa fa-times'></i></button>";
    //                 $result .= "</center>";
    //             }else{
    //                 $result .= "<center>";
    //                     $result .= "<input class='itemCheckbox' type='checkbox' data-checkbox-id='$row->id' style='width: 25px; height: 25px;  text-align: center;' id='checkBoxId' name='checkbox_id[]' value='".$row->id."'>";
    //                 $result .= "</center>";
    //             }

    //             return $result;
    //         })
    //         ->addColumn('name', function($row){
    //             $result = "";
    //             if($row->hris_info != null){ // For Pricon
    //                 $result .= '<center><span>'.$row->hris_info->FirstName .' '. $row->hris_info->LastName.'</span></center>';
    //             }else if($row->subcon_info != null){ // For Subcon
    //                 $result .= '<center><span>'.$row->subcon_info->FirstName .' '. $row->subcon_info->LastName.'</span></center>';
    //             }else{
    //                 $result .= '<center><span>Resigned</span></center>';
    //             }
    //             return $result;
    //         })
    //         ->addColumn('factory', function($row){
    //             $result = "";
    //             if($row->masterlist_factory != null){ //Existing Data
    //                 $result .= '<center><span>'.$row->masterlist_factory.'</span></center>';
    //             }else{
    //                 $result .= '<center><span>-</span></center>';
    //             }
    //             return $result;
    //         })
    //         ->addColumn('department', function($row){
    //             $result = "";
    //             if($row->hris_info != null){ // For Pricon
    //                 if($row->hris_info->department_info != null){
    //                     $result .= '<center><span>'.$row->hris_info->department_info->Department .'</span></center>';
    //                 }else{
    //                     $result .= '<center><span>-</span></center>';
    //                 }
    //             }else if($row->subcon_info != null){ // For Subcon
    //                 if($row->subcon_info->department_info != null){
    //                     $result .= '<center><span>'.$row->subcon_info->department_info->Department .'</span></center>';
    //                 }else{
    //                     $result .= '<center><span>-</span></center>';
    //                 }
    //             }else{
    //                 $result .= '<center><span>-</span></center>';
    //             }
    //             return $result;
    //         })
    //         ->addColumn('section', function($row){
    //             $result = "";
    //             if($row->hris_info != null){ // For Pricon
    //                 if($row->hris_info->section_info != null){
    //                     $result .= '<center><span>'.$row->hris_info->section_info->Section .'</span></center>';
    //                 }else{
    //                     $result .= '<center><span>-</span></center>';
    //                 }
    //             }else if($row->subcon_info != null){ // For Subcon
    //                 if($row->subcon_info->section_info != null){
    //                     $result .= '<center><span>'.$row->subcon_info->section_info->Section .'</span></center>';
    //                 }else{
    //                     $result .= '<center><span>-</span></center>';
    //                 }
    //             }else{
    //                 $result .= '<center><span>Resigned</span></center>';
    //             }
    //             return $result;
    //         })
    //     ->rawColumns([
    //         'action',
    //         'name',
    //         'factory',
    //         'department',
    //         'section',
    //         ])
    //     ->make(true);
    // }
    public function viewMasterListForAllocation(Request $request){
        $userData = User::where('rapidx_user_id', $request->rapidXUserId)->value('user_role_id');
        $requestMlIds = '';
        $isViewMode = $request->isViewMode;

        if ($request->requestControlNo) {
            $requestMlIds = Allocations::select('requestee_ml_id')
                ->where('is_deleted', 0)
                ->where('control_number', $request->requestControlNo)
                ->pluck('requestee_ml_id')
                ->toArray();
        }

        if ($userData == 1) { // 1 - Admin
            $masterlistData = Masterlist::with([
                    'hris_info' => function ($q) {
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
                ->when($isViewMode != 2, function ($q) {
                    return $q->where('is_deleted', 0);
                })
                ->when($request->requestControlNo, function ($q) use ($requestMlIds) {
                    return $q->whereIn('id', $requestMlIds);
                })
                ->get();
        } else {
            $masterlistData = Masterlist::with([
                    'hris_info' => function ($q) {
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
                ->when($isViewMode != 2, function ($q) {
                    return $q->where('is_deleted', 0);
                })
                ->when($request->requestControlNo, function ($q) use ($requestMlIds) {
                    return $q->whereIn('id', $requestMlIds);
                })
                ->get();
        }
        // $selectedIds = json_decode($request->selectedIds, true);

        // ✅ Sort so that selected IDs appear first
        // if (!empty($request->selectedIds)) {
        //     $selectedIds = $request->selectedIds;
        //     $masterlistData = $masterlistData->sortByDesc(function ($item) use ($selectedIds) {
        //         return in_array($item->id, $selectedIds) ? 1 : 0;
        //     })->values();
        // }
        if (!empty($request->selectedIds)) {
            // decode JSON string to array
            $selectedIds = json_decode($request->selectedIds, true);

            $masterlistData = $masterlistData->sortByDesc(function ($item) use ($selectedIds) {
                return in_array($item->id, $selectedIds) ? 1 : 0;
            })->values();
        }

        return DataTables::of($masterlistData)
            ->addColumn('action', function ($row) use ($requestMlIds) {
                $result = "";
                if ($requestMlIds != '') {
                    $result .= "<center>";
                    $result .= "<button class='btn btn-md btn-danger btnRemoveEmp' type='button' data-checkbox-id='$row->id'><i class='fa fa-times'></i></button>";
                    $result .= "</center>";
                } else {
                    $result .= "<center>";
                    $result .= "<input class='itemCheckbox' type='checkbox' data-checkbox-id='$row->id' style='width: 25px; height: 25px; text-align: center;' name='checkbox_id[]' value='" . $row->id . "'>";
                    $result .= "</center>";
                }

                return $result;
            })
            ->addColumn('name', function ($row) {
                $result = "";
                if ($row->hris_info != null) { // Pricon
                    $result .= '<center><span>' . $row->hris_info->FirstName . ' ' . $row->hris_info->LastName . '</span></center>';
                } else if ($row->subcon_info != null) { // Subcon
                    $result .= '<center><span>' . $row->subcon_info->FirstName . ' ' . $row->subcon_info->LastName . '</span></center>';
                } else {
                    $result .= '<center><span>Resigned</span></center>';
                }
                return $result;
            })
            ->addColumn('factory', function ($row) {
                return '<center><span>' . ($row->masterlist_factory ?: '-') . '</span></center>';
            })
            ->addColumn('department', function ($row) {
                if ($row->hris_info && $row->hris_info->department_info) {
                    return '<center><span>' . $row->hris_info->department_info->Department . '</span></center>';
                } elseif ($row->subcon_info && $row->subcon_info->department_info) {
                    return '<center><span>' . $row->subcon_info->department_info->Department . '</span></center>';
                }
                return '<center><span>-</span></center>';
            })
            ->addColumn('section', function ($row) {
                if ($row->hris_info && $row->hris_info->section_info) {
                    return '<center><span>' . $row->hris_info->section_info->Section . '</span></center>';
                } elseif ($row->subcon_info && $row->subcon_info->section_info) {
                    return '<center><span>' . $row->subcon_info->section_info->Section . '</span></center>';
                }
                return '<center><span>-</span></center>';
            })
            ->rawColumns(['action', 'name', 'factory', 'department', 'section'])
            ->make(true);
    }

    public function getUserInfo(Request $request){
        $userData = User::with(['rapidx_user_info'])->where('rapidx_user_id', $request->userId)->where('is_deleted', 0)->first();
        return response()->json(['userDetails' => $userData]);
    }

    public function getCutOffTime(Request $request){
        $scheduleData = CutoffTime::where('is_deleted', 0)->where('factory', $request->param_factory)->get();
        return response()->json(['scheduleDetails' => $scheduleData]);
    }

    public function getMasterlistInfoForFilter(Request $request){
        $departmentDetails = Masterlist::where('is_deleted', 0)
                    ->with(['hris_info.department_info' => function ($q) use ($request) {
                        $q->select('pkid', 'Department'); // Adjust fields based on your table
                        },'subcon_info.department_info' => function ($q) {
                            $q->select('pkid', 'Department');
                        }
                    ])
                    // ->when($request->param_factory != 'ALL', function ($query) use ($request) {
                    //     $query->where('masterlist_factory', $request->param_factory); // param_factory
                    // })
                    ->where('masterlist_factory', $request->param_factory) // param_factory
                    ->get()
                    ->flatMap(function ($item) {
                        // Combine both into one array and remove nulls
                        return collect([
                            $item->hris_info->department_info ?? null,
                            $item->subcon_info->department_info ?? null
                        ])->filter();
                    })
                    ->unique('Department') // Avoid duplicates by department name
                    ->values();
                    // ->map(function ($item) {
                    //     // Merge both possible department_info sources into one collection
                    //     return collect([$item->hris_info->department_info, $item->subcon_info->department_info])
                    //         ->filter(); // Remove nulls
                    // })
                    // ->flatten(1) // Flatten one level so we have a flat list of departments
                    // ->pluck('hris_info.department_info') // Get all related departments
                    // ->filter() // Remove nulls in case of missing relationships
                    // ->unique('Department') // Or use 'Department' if that's what you want
                    // ->values(); // Re-index the array

        $sectionDetails = Masterlist::where('is_deleted', 0)
                    ->with(['hris_info.section_info' => function ($q) use ($request) {
                                $q->select('pkid', 'Section')
                                ->when($request->param_department != 'ALL', function ($query) use ($request) {
                                    $query->where('fkDepartment', $request->param_department); // Adjust fields based on your table
                                });
                            },'subcon_info.section_info' => function ($q) use ($request) {
                                $q->select('pkid', 'Section')
                                ->when($request->param_department != 'ALL', function ($query) use ($request) {
                                    $query->where('fkDepartment', $request->param_department); // Adjust fields based on your table
                                });
                            }
                    ])
                    ->where('masterlist_factory', $request->param_factory) // param_factory
                    ->get()
                    ->flatMap(function ($item) {
                        // Combine both into one array and remove nulls
                        return collect([
                            $item->hris_info->section_info ?? null,
                            $item->subcon_info->section_info ?? null
                        ])->filter();
                    })
                    // ->pluck('hris_info.section_info') // Get all related departments
                    // ->filter() // Remove nulls in case of missing relationships
                    ->unique('Section') // Or use 'Department' if that's what you
                    ->values(); // Re-index the array

        return response()->json(['departmentDetails' => $departmentDetails, 'sectionDetails' => $sectionDetails]);
    }

    public function addAllocationData(Request $request){
        date_default_timezone_set('Asia/Manila');
        session_start();
        $data = $request->all();
        $currentHour = date('H'); // 24-hour format
        $userData = User::where('rapidx_user_id', $request->requestor_id)->value('user_role_id');
        // $cutoffTimeData = CutoffTime::where('id', $request->cutoffTimeId)->get();

        // if ($currentHour >= 15 && $userData != 1 && $request->start_date == date('Y-m-d')){
        //     return response()->json(['hasError' => 1, 'result' => 0, 'message' => 'Allocation for TODAY is already closed.']);
        // }

        $validate_array = [
            'requestor_id'    => 'required',
            'type_of_request' => 'required',
            'start_date'      => 'required',
            'end_date'        => 'required',
        ];

        // Add additional rules if type_of_request is 1 change schedule
        if ($request->type_of_request == 1 || $request->type_of_request == 0) {
            $validate_array = array_merge($validate_array, [
                'alloc_factory'  => 'required',
                'alloc_incoming' => 'required',
                'alloc_outgoing' => 'required',
            ]);
        }

        $validator = Validator::make($data, $validate_array);

        if ($validator->fails()){
            return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
        }else{

            /**
             * Validation for existing employee
             */

            if($request->selectedIds[0] != 0){ //default value of selectIds, meaning empty array
                $allConflicts = collect(); // store all conflicts here

                foreach ($request->selectedIds as $key => $value){

                    $conflictingAllocations = Allocations::with(['request_ml_info.hris_info', 'request_ml_info.subcon_info', 'requestor_user_info'])
                                                // ->whereIn('requestee_ml_id', $request->selectedIds)
                                                ->where('requestee_ml_id', $value)
                                                ->where('is_deleted', 0)
                                                ->where('request_status', 0)
                                                ->whereDate('alloc_date_start', '<=', $request->end_date) // starts before new ends
                                                ->whereDate('alloc_date_end', '>=', $request->start_date) // ends after new starts
                                                ->get(['control_number', 'requestee_ml_id', 'alloc_date_start', 'alloc_date_end', 'requested_by']);

                    // ✅ Add only those that actually conflict (not same control number)
                    $filtered = $conflictingAllocations->filter(function ($alloc) use ($request) {
                        return $alloc->control_number != $request->request_control_no;
                    });

                    // ✅ Merge into main collection
                    $allConflicts = $allConflicts->merge($filtered);

                    // old code 10/30/2025
                    // // ✅ Check if there's at least one conflicting allocation that isn't the same control number
                    // $hasConflict = $conflictingAllocations->contains(function ($alloc) use ($request) {
                    //     return $alloc->control_number != $request->request_control_no;
                    // });
                }

                // ✅ After loop, check if we collected any
                // if ($conflictingAllocations->isNotEmpty() && $conflictingAllocations[0]->control_number != $request->request_control_no) {
                // if($hasConflict){
                if($allConflicts->isNotEmpty()){
                    // return response()->json([
                    //     'hasExisted' => count($conflictingAllocations),
                    //     'error' => 'Some people already have allocations in the selected date range.',
                    //     'conflicts' => $conflictingAllocations->map(function($item) {
                    //         if($item->request_ml_info->hris_info != null){
                    //             $requested_emp = $item->request_ml_info->hris_info->FirstName.' '.$item->request_ml_info->hris_info->LastName;
                    //         }else{
                    //             $requested_emp = $item->request_ml_info->subcon_info->FirstName.' '.$item->request_ml_info->subcon_info->LastName;
                    //         }

                    //         return [
                    //             'control_number' => $item->control_number,
                    //             'requestee_ml_id' => $item->requestee_ml_id,
                    //             'start' => $item->alloc_date_start,
                    //             'end' => $item->alloc_date_end,
                    //             'requested_by' => $item->requestor_user_info->name,
                    //             'requested_emp' => $requested_emp
                    //         ];
                    //     })
                    // ]);

                    return response()->json([
                        'hasExisted' => $allConflicts->count(),
                        'error' => 'Some people already have allocations in the selected date range.',
                        'conflicts' => $allConflicts->map(function($item) {
                            if ($item->request_ml_info->hris_info != null) {
                                $requested_emp = $item->request_ml_info->hris_info->FirstName.' '.$item->request_ml_info->hris_info->LastName;
                            } else {
                                $requested_emp = $item->request_ml_info->subcon_info->FirstName.' '.$item->request_ml_info->subcon_info->LastName;
                            }

                            return [
                                'control_number' => $item->control_number,
                                'requestee_ml_id' => $item->requestee_ml_id,
                                'start' => $item->alloc_date_start,
                                'end' => $item->alloc_date_end,
                                'requested_by' => $item->requestor_user_info->name,
                                'requested_emp' => $requested_emp
                            ];
                        })
                    ]);
                }
            }

            DB::beginTransaction();
            try {
                // return 'true';
                if(isset($request->request_control_no)){
                    //🔴 Delete existing data
                    Allocations::where('control_number', $request->request_control_no)->delete();
                }

                //Control No. Generation
                $lastest_control_no = Allocations::where('is_deleted', 0)->latest('id')->first();

                if(is_null($lastest_control_no)){
                    $control_no_counter = 1;
                }else{
                    $control_no = $lastest_control_no->control_number;
                    $control_no_ymd = substr($control_no, 0, 6);
                    $control_no_counter = substr($control_no, 7);

                    // CONDITION TO RESET COUNTER, commented out (Disabled to avoid resetting the counter)
                    if($control_no_ymd == date('ymd')){ //Reset when New Year
                        $control_no_counter++; //increment the 2nd index
                    }else{
                        $control_no_counter = 1;
                    }
                }

                if(strlen($control_no_counter) == 1){
                    $digit_prefix = '000';
                }else if(strlen($control_no_counter) == 2){
                    $digit_prefix = '00';
                }else if(strlen($control_no_counter) == 3){
                    $digit_prefix = '0';
                }

                $control_no_concat_value = date('ymd').'-'.$digit_prefix.$control_no_counter;

                if($request->selectedIds[0] != 0){ //default value of selectIds, meaning empty array

                    foreach ($request->selectedIds as $key => $value) {
                        Allocations::insert([
                            'control_number'   => $control_no_concat_value,
                            'request_type'     => $request->type_of_request,
                            'date_requested'   => $request->date_requested,
                            'alloc_date_start' => $request->start_date,
                            'alloc_date_end'   => $request->end_date,
                            'requestee_ml_id'  => $request->selectedIds[$key],
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
                }else{
                    return response()->json(['hasError' => 1, 'result' => 0, 'message' => 'No Employee Selected']);
                }

                DB::commit();
                return response()->json(['hasError' => 0]);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['hasError' => 1, 'exceptionError' => $e]);
            }
        }
    }

    public function getAllocationData(Request $request){
        $allocationData = Allocations::with([
                            'request_ml_info',
                            'request_ml_info.hris_info' => function($q){
                                                            $q->where('EmpStatus', 1);
                            },
                            'request_ml_info.rapidx_user_info',
                            'request_ml_info.routes_info',
                            'alloc_route_info',
                            'requestor_user_info',
                        ])
                        ->where('is_deleted', 0)
                        ->where('control_number', $request->control_number)
                        ->get();

        $userData = User::with(['rapidx_user_info'])->where('rapidx_user_id', $request->userId)->where('is_deleted', 0)->first();
        return response()->json(['allocationDetails' => $allocationData, 'userDetails' => $userData]);
    }

    public function changeAllocationStatus(Request $request){
        DB::beginTransaction();
        try {

            if($request->delete_request_status == 0){
                $change_status_to = 1;
            }else if($request->delete_request_status == 1){
                $change_status_to = 0;
            }else{ //Run only when setting status to "finished"
                $change_status_to = 2;
            }

            Allocations::where('control_number', $request->delete_control_no)->update(['request_status' => $change_status_to, 'updated_at' => date('Y-m-d H:i:s')]);
            DB::commit();
            return response()->json(['hasError' => 0]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['hasError' => 1, 'exceptionError' => $e]);
        }
    }

    // public function changeAllocationStatus(Request $request, AllocationService $allocationService)
    // {
    //     $result = $allocationService->changeStatus(
    //         $request->delete_control_no,
    //         $request->delete_request_status
    //     );

    //     return response()->json($result);
    // }
}
