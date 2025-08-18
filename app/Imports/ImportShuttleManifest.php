<?php

namespace App\Imports;

use App\Models\Manifest;
use App\Models\SubconAttendance;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithStartRow;
// use Auth;

class ImportShuttleManifest implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Manifest([
            'emp_no'   => $row[0],
            'date_scanned' => $row[1],
            'time_scanned' => $row[2],
            'route' => $row[3],
            'factory' => $row[4],
        ]);
    }

    // public function startRow(): int
    // {
    //     return 2;
    // }
}
