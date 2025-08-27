<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Exports\ExportReportV2;

use App\Models\RouteCode;
use App\Models\Masterlist;
use App\Models\Allocations;

class ExportReportV2Controller extends Controller
{
    public function export_v2($factory,$url_route,$incoming,$outgoing,$from,$to){
        date_default_timezone_set('Asia/Manila');

         // Masterlist Query
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
            'rapidx_user_info',
        ])
        ->where('masterlist_status', '1')
        ->where('is_deleted', '0')
        ->where('masterlist_factory', $factory)
        ->where('masterlist_incoming', $incoming)
        ->where('masterlist_outgoing', $outgoing)
        ->get();

        $allocationlists = Allocations::with([
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
        ->where('request_status', '0')
        ->where('request_type', '!=', '2')
        ->where(function ($query) use ($incoming, $outgoing) {
            $query->where(function($q) use ($incoming, $outgoing) {
                $q->where('alloc_incoming', $incoming)
                    ->where('alloc_outgoing', $outgoing);
            })
            ->orWhere(function($q) use ($incoming, $outgoing) {
                $q->where('alloc_incoming', 'N/A')
                    ->where('alloc_outgoing', $outgoing);
            })
            ->orWhere(function($q) use ($incoming, $outgoing) {
                $q->where('alloc_outgoing', 'N/A')
                    ->where('alloc_incoming', $incoming);
            });
        })
        ->where(function ($query) use ($from, $to) {
            $query->whereDate('alloc_date_start', '<=', $to)
                    ->whereDate('alloc_date_end', '>=', $from);
        })
        ->orderByDesc('id')        // order child records desc
        ->where('is_deleted', 0)
        ->get();

        // All Allocations (for filtering masterlists)
        $allAllocations =
            Allocations::select(
                'requestee_ml_id',
                'alloc_factory',
                'alloc_incoming',
                'alloc_outgoing',
                'alloc_date_start',
                'alloc_date_end',
                'request_type',
                'request_status',
                'is_deleted'
            )
            ->where('request_type', '!=', 2)
            ->where('request_status', 0)
            ->where('is_deleted', 0)
            ->whereNotNull('requestee_ml_id')
            ->get();

            // Filter out masterlists that already have allocations in other factory or time
            $excludedMasterlistIds = $allAllocations
            ->filter(function ($alloc) use ($factory, $incoming, $outgoing, $from, $to) {
                return (
                    $alloc->alloc_factory       != $factory ||
                    $alloc->alloc_incoming      != $incoming ||
                    $alloc->alloc_outgoing      != $outgoing ||
                    $alloc->alloc_date_start    != $from ||
                    $alloc->alloc_date_end      != $to
                );
            })
            ->pluck('requestee_ml_id')
            ->unique();

        // CHAN - 08-20-2025
        $type2Allocations = Allocations::where('request_type', 2)
        ->where('request_status', 0)
        ->where('is_deleted', 0)
        ->whereNotNull('requestee_ml_id')
        ->pluck('requestee_ml_id')
        ->unique();

        // CHAN - 08-20-2025
        $masterlists = $masterlists->filter(function ($ml) use ($type2Allocations) {
            return !$type2Allocations->contains($ml->id);
        });

        $filteredMasterlists = $masterlists->filter(function ($ml) use ($excludedMasterlistIds) {
            return !$excludedMasterlistIds->contains($ml->id);
        });

        $matchedIds = $allocationlists->pluck('request_ml_info.id')->filter()->unique();

        $unmatchedMasterlists = $filteredMasterlists->filter(function ($item) use ($matchedIds) {
            return !$matchedIds->contains($item->id);
        });

        $mergedLists = $allocationlists->values()->merge($unmatchedMasterlists->values());
        // return $mergedLists;

        // Route Code Grouping and Counting
        $route_code = RouteCode::with(['routes_details.shuttle_provider_info'])->whereNull('deleted_at')->orderBy('routes_code', 'asc')->get();

        $routeNameCounts = collect();
        $routeDestinationCounts = collect();

        foreach ($route_code as $route) {
            $destination = strtolower(trim($route->routes_destination));

            $matchingDetails = $route->routes_details->filter(function ($detail) use ($destination) {
                return strtolower(trim($detail->routes_description)) === $destination;
            });

            foreach ($matchingDetails as $detail) {
                $routeName = $detail->routes_name;
                if (!$routeName) continue;

                $incomingCount = $mergedLists->filter(function ($item) use ($routeName) {
                    $routesInfo = optional($item->routes_info);
                    $fallbackRoutesInfo = optional(optional($item->request_ml_info)->routes_info);
                    $allocIncoming = $item->alloc_incoming;

                    $actualRouteName = $routesInfo->routes_name ?? $fallbackRoutesInfo->routes_name;

                    return $actualRouteName === $routeName && $allocIncoming !== 'N/A';
                })->count();

                $outgoingCount = $mergedLists->filter(function ($item) use ($routeName) {
                    $routesInfo = optional($item->routes_info);
                    $fallbackRoutesInfo = optional(optional($item->request_ml_info)->routes_info);
                    $allocOutgoing = $item->alloc_outgoing;

                    $actualRouteName = $routesInfo->routes_name ?? $fallbackRoutesInfo->routes_name;

                    return $actualRouteName === $routeName && $allocOutgoing !== 'N/A';
                })->count();


                $routeNameCounts->push([
                    'routes_destination' => $route->routes_destination,
                    'route_code'         => $route->routes_code,
                    'route_name'         => $routeName,
                    'incoming_count'     => $incomingCount,
                    'outgoing_count'     => $outgoingCount,
                ]);
            }
        }
        // return $routeNameCounts;

        $routeDestinationFinalCount = $routeDestinationCounts->map(function ($count, $destination) {
            return [
                'routes_destination'    => $destination,
                'total_employee_count'  => $count,
            ];
        })->values();
        // return $routeDestinationFinalCount;

        if(count($mergedLists) > 0){
            $factory = str_replace('F', '', $factory);
            return Excel::download(
                new ExportReportV2(
                    $mergedLists,
                    $routeNameCounts,
                    $routeDestinationFinalCount,
                    $factory,
                    $incoming,
                    $outgoing,
                    $from,
                    $to,
                    $route_code
                ),
                'F'.$factory.' - Shuttle Bus Allocation Report for '.$from.' from '.$incoming.' - '.$outgoing.'.xlsx'
            );
        }else{
            return redirect()->back()->with('message', 'There are no data for the chosen date/time.');
        }
    }
}
