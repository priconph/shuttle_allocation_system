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
use App\Models\SystemOneHRIS;
use App\Models\SystemOneSubcon;

class SystemOneController extends Controller
{
    public function getEmployees(Request $request){
        $databaseModel;
        if($request->employeeType == 1){
            $databaseModel = 'App\Models\SystemOneHRIS';
        }else{
            $databaseModel = 'App\Models\SystemOneSubcon';
        }
        $employeesData = $databaseModel::with([
                'position_info',
                'division_info',
                'department_info',
                'section_info',
            ])
            ->where('EmpStatus', 1)
            ->get();
        return response()->json(['employeesData' => $employeesData]);
    }
}
