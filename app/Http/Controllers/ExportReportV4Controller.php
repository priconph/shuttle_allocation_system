<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Exports\ExportReportV4;

use App\Models\RouteCode;
use App\Models\Masterlist;
use App\Models\Allocations;

class ExportReportV4Controller extends Controller
{
    public function export_v4($factory, $from, $to){
        date_default_timezone_set('Asia/Manila');

        $mergedLists = Allocations::with([
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
        ->where('request_status', '!=', 1)
        ->where('request_type', '!=', 2)
        ->whereDate('alloc_date_start', '<=', $to)
        ->whereDate('alloc_date_end', '>=', $from)
        ->get();

        $route_code = RouteCode::with(['routes_details.shuttle_provider_info'])
            ->whereNull('deleted_at')
            ->orderBy('routes_code', 'asc')
            ->get();
        // return $route_code;

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

                    // Count alloc_outgoing
                    if (!empty($item->alloc_outgoing)) {
                        $time = date('h:i A', strtotime($item->alloc_outgoing));
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
        // return $routeNameCounts;

        if (count($mergedLists) > 0) {
            $factory = str_replace('F', '', $factory);
            return Excel::download(
                new ExportReportV4(
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

}
