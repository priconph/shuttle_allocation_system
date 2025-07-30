<?php

namespace App\Imports;

use App\Models\SubconAttendance;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithStartRow;
// use Auth;

class ImportSubconAttendance implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new SubconAttendance([
            'emp_id'   => $row[0],
            'emp_name' => $row[1],
            'date_in'  => Date::excelToDateTimeObject((int)$row[2])->format('Y-m-d'),
            'time_in'  => Date::excelToDateTimeObject($row[3])->format('H:i:s'),
            'date_out' => Date::excelToDateTimeObject((int)$row[4])->format('Y-m-d'),
            'time_out' => Date::excelToDateTimeObject($row[5])->format('H:i:s'),
        ]);
    }

    // public function startRow(): int
    // {
    //     return 2;
    // }
}
