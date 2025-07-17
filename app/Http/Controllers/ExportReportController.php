<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\RapidXUser;
use App\Exports\ExportReport;
use App\Exports\ExportReportTest;
use App\Models\Routes;
use App\Models\Masterlist;

use App\Exports\ExportTransportReport\ExportAllocation;
use App\Exports\ExportTransportReport\ExportOperations;
use App\Exports\ExportTransportReport\ExportSupportGroup;

class ExportReportController extends Controller
{
    public function export_report(Request $request){
        // session_start();
        // $data = $request->all();
        // $rapidx_user_id = $_SESSION['rapidx_user_id'];
        // $rapidx_username = RapidXUser::where('id', $rapidx_user_id)->get();

        $date = date('M d Y',strtotime(NOW()));
        $routesData = Routes::with([
            'pickup_time_info',
            'shuttle_provider_info',
        ])
        ->where('status', 1)
        ->get();
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
        ])
        ->where('masterlist_status', 1)
        ->where('is_deleted', 0)
        ->get();
        // return $masterlistData;

        return Excel::download(new ExportReport($date, $routesData, $masterlistData), 'Shuttle Allocation Report '.$date.'.xlsx');
    }

    public function export_report_test(Request $request){
        return "export_report_test";
        $date = date('M d Y',strtotime(NOW()));
        $routesData = Routes::with([
            'pickup_time_info',
            'shuttle_provider_info',
        ])
        ->where('status', 1)
        ->get();
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
        ])
        ->where('masterlist_status', 1)
        ->where('is_deleted', 0)
        ->get()->take(100);
        // return $masterlistData;

        return Excel::download(new ExportReportTest($date, $routesData, $masterlistData), 'Shuttle Allocation Report '.$date.'.xlsx');
    }
}
