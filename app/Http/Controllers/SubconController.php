<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubconAttendance;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportSubconAttendance;
use PhpOffice\PhpSpreadsheet\Shared\Date;

use DataTables;

class SubconController extends Controller
{
    public function importAttendance(Request $request){
        date_default_timezone_set('Asia/Manila');
        session_start();
        DB::beginTransaction();
        try{
            // Excel::import(new ImportSubconAttendance, $request->file('attendance_file'));
            $collections = Excel::toCollection(new ImportSubconAttendance, $request->file('attendance_file'));

            $required = ["EmpNo", "Name", "Date In", "Time In", "Date Out", "Time Out"];
            $collection = $collections[0][0];
            $missing = collect($required)->diff($collection);
            if ($missing->isNotEmpty()) {
                return response()->json([
                    'result' => false,
                    'msg' => 'Import Failed!<br> Please use required template.'
                ],409);
            }

            unset($collections[0][0]);
            $filtered = collect($collections[0])->filter(function ($row) {
                return !is_null($row[1]);
            })->values();
  
            foreach($filtered AS $key => $value){
                if(!is_null($value[4]) || !is_null($value[5])){
                    SubconAttendance::create([
                        'emp_id' => $value[0],
                        'emp_name' => $value[1],
                        'date_in' => Date::excelToDateTimeObject((int)$value[2])->format('Y-m-d'),
                        'time_in' => Date::excelToDateTimeObject($value[3])->format('H:i:s'),
                        'date_out' => Date::excelToDateTimeObject((int)$value[4])->format('Y-m-d'),
                        'time_out' => Date::excelToDateTimeObject($value[5])->format('H:i:s'),
                        'created_by' => $_SESSION['rapidx_user_id']
                    ]);
                }
              
            }
            DB::commit();

            return response()->json([
                'result' => true,
                'msg' => 'Import Success'
            ]);
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }

    public function dtGetSubconAttendance(Request $request){
        $attendance = SubconAttendance::whereNull('deleted_at')->get();

        return DataTables::of($attendance)
        ->make(true);
    }
}
