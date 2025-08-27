<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Exports\ExportReportV3;

use App\Models\RouteCode;
use App\Models\Masterlist;
use App\Models\Allocations;

class ExportReportV3Controller extends Controller
{
    public function export_v3($factory, $from, $to){
        date_default_timezone_set('Asia/Manila');

        // 1. Masterlist
        $masterlists = Masterlist::with([
            'routes_info',
            'hris_info.position_info',
            'hris_info.division_info',
            'hris_info.department_info',
            'hris_info.section_info',
            'subcon_info.position_info',
            'subcon_info.division_info',
            'subcon_info.department_info',
            'subcon_info.section_info',
            'rapidx_user_info'
        ])
        ->where('masterlist_status', '1')
        ->where('is_deleted', '0')
        ->where('masterlist_factory', $factory)
        ->get();
        // return $masterlists;

        // 2. Allocations
        $allocationlists = Allocations::with([
            'requestor_user_info',
            'request_ml_info.routes_info',
            'request_ml_info.hris_info.position_info',
            'request_ml_info.hris_info.division_info',
            'request_ml_info.hris_info.department_info',
            'request_ml_info.hris_info.section_info',
            'request_ml_info.subcon_info.position_info',
            'request_ml_info.subcon_info.division_info',
            'request_ml_info.subcon_info.department_info',
            'request_ml_info.subcon_info.section_info',
            'request_ml_info.rapidx_user_info'
        ])
        ->where('alloc_factory', $factory)
        ->where('request_status', 0)
        ->where('request_type', '!=', 2)
        ->whereDate('alloc_date_start', '<=', $to)
        ->whereDate('alloc_date_end', '>=', $from)
        ->get();
        // return $allocationlists;

        // 3. Get Type 2 Allocations to exclude
        $requestType2Allocations = Allocations::where('request_type', 2)
            ->where('request_status', 0)
            ->where('is_deleted', 0)
            ->whereNotNull('requestee_ml_id')
            ->pluck('requestee_ml_id')
            ->unique();

        // 4. Filter masterlists not riding shuttle (request type = 2) and not in this date range
        $masterlists = $masterlists->filter(function ($ml) use ($requestType2Allocations) {
            return !$requestType2Allocations->contains($ml->id);
        });

        // 5. Remove masterlists that were allocated for another time or factory
        $allAllocations = Allocations::select('requestee_ml_id', 'alloc_factory', 'alloc_date_start', 'alloc_date_end', 'request_type', 'request_status', 'is_deleted')
            ->where('request_type', '!=', 2)
            ->where('request_status', 0)
            ->where('is_deleted', 0)
            ->whereNotNull('requestee_ml_id')
            ->get();

        $excludedMasterlistIds = $allAllocations->filter(function ($alloc) use ($factory, $from, $to) {
            return (
                $alloc->alloc_factory != $factory ||
                $alloc->alloc_date_start != $from ||
                $alloc->alloc_date_end != $to
            );
        })->pluck('requestee_ml_id')->unique();

        $filteredMasterlists = $masterlists->filter(function ($ml) use ($excludedMasterlistIds) {
            return !$excludedMasterlistIds->contains($ml->id);
        });

        // 6. Identify masterlist users not in allocation list
        $matchedIds = $allocationlists->pluck('request_ml_info.id')->filter()->unique();
        $unmatchedMasterlists = $filteredMasterlists->filter(function ($item) use ($matchedIds) {
            return !$matchedIds->contains($item->id);
        });

        // 7. Combine both datasets
        $mergedLists = $allocationlists->values()->merge($unmatchedMasterlists->values());
        // return $mergedLists;

        // 8. Route Codes
        $route_code = RouteCode::with(['routes_details.shuttle_provider_info'])
            ->whereNull('deleted_at')
            ->orderBy('routes_code', 'asc')
            ->get();

        // 9. Initialize collections
        $routeNameCounts = collect();
        foreach ($route_code as $route) {
            $destination = strtolower(trim($route->routes_destination));
        
            $matchingDetails = $route->routes_details->filter(function ($detail) use ($destination) {
                return strtolower(trim($detail->routes_description)) === $destination;
            });

            foreach ($matchingDetails as $detail) {
                $routeName = $detail->routes_name;
                if (!$routeName) continue;

                $incomingCounts = [];
                $outgoingCounts = [];

                foreach ($mergedLists as $item) {
                    $routesInfo = optional($item->routes_info);
                    $fallbackRoutesInfo = optional(optional($item->request_ml_info)->routes_info);
                    $actualRouteName = $routesInfo->routes_name ?? $fallbackRoutesInfo->routes_name;
        
                    if ($actualRouteName !== $routeName) continue;
                
                    // Count alloc_incoming
                    if (!empty($item->alloc_incoming)) {
                        $time = date('h:i A', strtotime($item->alloc_incoming));
                        $incomingCounts[$time] = ($incomingCounts[$time] ?? 0) + 1;
                    }

                    // Count masterlist_incoming
                    if (!empty($item->masterlist_incoming)) {
                        $time = date('h:i A', strtotime($item->masterlist_incoming));
                        $incomingCounts[$time] = ($incomingCounts[$time] ?? 0) + 1;
                    }

                    // Count alloc_outgoing
                    if (!empty($item->alloc_outgoing)) {
                        $time = date('h:i A', strtotime($item->alloc_outgoing));
                        $outgoingCounts[$time] = ($outgoingCounts[$time] ?? 0) + 1;
                    }
        
                    // Count masterlist_outgoing
                    if (!empty($item->masterlist_outgoing)) {
                        $time = date('h:i A', strtotime($item->masterlist_outgoing));
                        $outgoingCounts[$time] = ($outgoingCounts[$time] ?? 0) + 1;
                    }
                }
        
                $routeNameCounts->push([
                    'routes_destination' => $route->routes_destination,
                    'route_code'         => $route->routes_code,
                    'route_name'         => $routeName,
                    'incoming_counts'    => $incomingCounts,
                    'outgoing_counts'    => $outgoingCounts,
                ]);
            }
        }
        // return $routeNameCounts;

        // 10. Final route destination count map (if needed)
        // $routeDestinationCounts = collect();
        // $routeDestinationFinalCount = $routeDestinationCounts->map(function ($count, $destination) {
        //     return [
        //         'routes_destination'    => $destination,
        //         'total_employee_count'  => $count,
        //     ];
        // })->values();

        // 11. Download the report
        if (count($mergedLists) > 0) {
            $factory = str_replace('F', '', $factory);
            return Excel::download(
                new ExportReportV3(
                    $mergedLists,
                    $routeNameCounts,
                    // $routeDestinationFinalCount,
                    $factory,
                    $from,
                    $to,
                    $route_code
                ),
                'F' . $factory . ' - Shuttle Bus Allocation Report for ' . $from . '.xlsx'
            );
        } else {
            return redirect()->back()->with('message', 'There are no data for the chosen date/time.');
        }
    }

    // public function export_v3($factory, $from, $to)
    // {
    //     date_default_timezone_set('Asia/Manila');
    
    //     // Masterlist Query
    //     $masterlists = Masterlist::with([
    //         'routes_info',
    //         'hris_info.position_info',
    //         'hris_info.division_info',
    //         'hris_info.department_info',
    //         'hris_info.section_info',
    //         'subcon_info.position_info',
    //         'subcon_info.division_info',
    //         'subcon_info.department_info',
    //         'subcon_info.section_info',
    //         'rapidx_user_info'
    //     ])
    //     ->where('masterlist_status', '1')
    //     ->where('is_deleted', '0')
    //     ->where('masterlist_factory', $factory)
    //     ->get();
    
    //     $allocationlists = Allocations::with([
    //         'request_ml_info.routes_info',
    //         'request_ml_info.hris_info.position_info',
    //         'request_ml_info.hris_info.division_info',
    //         'request_ml_info.hris_info.department_info',
    //         'request_ml_info.hris_info.section_info',
    //         'request_ml_info.subcon_info.position_info',
    //         'request_ml_info.subcon_info.division_info',
    //         'request_ml_info.subcon_info.department_info',
    //         'request_ml_info.subcon_info.section_info',
    //         'request_ml_info.rapidx_user_info'
    //     ])
    //     ->where('alloc_factory', $factory)
    //     ->where('request_status', '0')
    //     ->where('request_type', '!=', '2')
    //     ->where(function ($query) use ($from, $to) {
    //         $query->whereDate('alloc_date_start', '<=', $to)
    //               ->whereDate('alloc_date_end', '>=', $from);
    //     })
    //     ->get();
    
    //     $allAllocations = Allocations::select(
    //         'requestee_ml_id',
    //         'alloc_factory',
    //         'alloc_date_start',
    //         'alloc_date_end',
    //         'request_type',
    //         'request_status', 
    //         'is_deleted'
    //     )
    //     ->where('request_type', '!=', 2)
    //     ->where('request_status', 0)
    //     ->where('is_deleted', 0)
    //     ->whereNotNull('requestee_ml_id')
    //     ->get();
    
    //     $excludedMasterlistIds = $allAllocations
    //         ->filter(function ($alloc) use ($factory, $from, $to) {
    //             return (
    //                 $alloc->alloc_factory       != $factory ||
    //                 $alloc->alloc_date_start    != $from || 
    //                 $alloc->alloc_date_end      != $to
    //             );
    //         })
    //         ->pluck('requestee_ml_id')
    //         ->unique();
    
    //     // CHAN - 08-20-2025
    //     $type2Allocations = Allocations::where('request_type', 2)
    //         ->where('request_status', 0)
    //         ->where('is_deleted', 0)
    //         ->whereNotNull('requestee_ml_id')
    //         ->pluck('requestee_ml_id')
    //         ->unique();
    
    //     // Filter out type 2 (Not riding shuttle)
    //     $masterlists = $masterlists->filter(function ($ml) use ($type2Allocations) {
    //         return !$type2Allocations->contains($ml->id);
    //     });
    
    //     // Remove masterlists already allocated elsewhere
    //     $filteredMasterlists = $masterlists->filter(function ($ml) use ($excludedMasterlistIds) {
    //         return !$excludedMasterlistIds->contains($ml->id);
    //     });
    
    //     $matchedIds = $allocationlists->pluck('request_ml_info.id')->filter()->unique();
    
    //     $unmatchedMasterlists = $filteredMasterlists->filter(function ($item) use ($matchedIds) {
    //         return !$matchedIds->contains($item->id);
    //     });
    
    //     // Merge allocations + remaining masterlist users
    //     $mergedLists = $allocationlists->values()->merge($unmatchedMasterlists->values());
    
    //     // Route Code Grouping and Counting
    //     $route_code = RouteCode::with(['routes_details.shuttle_provider_info'])
    //         ->whereNull('deleted_at')
    //         ->orderBy('routes_code', 'asc')
    //         ->get();
    
    //     $routeNameCounts = collect();
    //     $routeDestinationCounts = collect();
    
    //     foreach ($route_code as $route) {
    //         $destination = strtolower(trim($route->routes_destination));
        
    //         $matchingDetails = $route->routes_details->filter(function ($detail) use ($destination) {
    //             return strtolower(trim($detail->routes_description)) === $destination;
    //         });
        
    //         foreach ($matchingDetails as $detail) {
    //             $routeName = $detail->routes_name;
    //             if (!$routeName) continue;
        
    //             $incomingCounts = [];
    //             $outgoingCounts = [];
        
    //             foreach ($mergedLists as $item) {
    //                 $routesInfo = optional($item->routes_info);
    //                 $fallbackRoutesInfo = optional(optional($item->request_ml_info)->routes_info);
    //                 $actualRouteName = $routesInfo->routes_name ?? $fallbackRoutesInfo->routes_name;
        
    //                 if ($actualRouteName !== $routeName) continue;
        
    //                 $incomingTime = trim($item->alloc_incoming ?? '');
    //                 $outgoingTime = trim($item->alloc_outgoing ?? '');
        
    //                 if (!empty($incomingTime)) {
    //                     $incomingTime = date('h:i A', strtotime($incomingTime));
    //                     if (!isset($incomingCounts[$incomingTime])) {
    //                         $incomingCounts[$incomingTime] = 0;
    //                     }
    //                     $incomingCounts[$incomingTime]++;
    //                 }
        
    //                 if (!empty($outgoingTime)) {
    //                     $outgoingTime = date('h:i A', strtotime($outgoingTime));
    //                     if (!isset($outgoingCounts[$outgoingTime])) {
    //                         $outgoingCounts[$outgoingTime] = 0;
    //                     }
    //                     $outgoingCounts[$outgoingTime]++;
    //                 }
    //             }
        
    //             $routeNameCounts->push([
    //                 'routes_destination' => $route->routes_destination,
    //                 'route_code'         => $route->routes_code,
    //                 'route_name'         => $routeName,
    //                 'incoming_counts'    => $incomingCounts,
    //                 'outgoing_counts'    => $outgoingCounts,
    //             ]);
    //         }
    //     }
        
    //     // return $routeNameCounts;
    //     $routeDestinationFinalCount = $routeDestinationCounts->map(function ($count, $destination) {
    //         return [
    //             'routes_destination'    => $destination,
    //             'total_employee_count'  => $count,
    //         ];
    //     })->values();
    
    //     if (count($mergedLists) > 0) {
    //         $factory = str_replace('F', '', $factory);
    //         return Excel::download(
    //             new ExportReportV3(
    //                 $mergedLists,
    //                 $routeNameCounts,
    //                 $routeDestinationFinalCount,
    //                 $factory,
    //                 $from,
    //                 $to,
    //                 $route_code
    //             ),
    //             'F' . $factory . ' - Shuttle Bus Allocation Report for ' . $from . '.xlsx'
    //         );
    //     } else {
    //         return redirect()->back()->with('message', 'There are no data for the chosen date/time.');
    //     }
    // }
    
}
