<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
Use \Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithTitle;
// use Maatwebsite\Excel\Concerns\WithDrawings;
// use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use App\Exports\ExportTransportReport\ExportOperations;
use App\Exports\ExportTransportReport\ExportSupportGroup;
use App\Exports\ExportTransportReport\ExportAllocation;

class ExportReportTest implements  WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    protected $date;

    

    function __construct($date, $routesData, $masterlistData){
        $this->date = $date;
        $this->routesData = $routesData;
        $this->masterlistData = $masterlistData;
    }

    public function sheets(): array {
        $sheets = [];
        $sheets[] = new ExportOperations($this->date, $this->routesData, $this->masterlistData);
        $sheets[] = new ExportSupportGroup($this->date, $this->routesData, $this->masterlistData);
        $sheets[] = new ExportAllocation($this->date, $this->routesData, $this->masterlistData);
        return $sheets;
    }
}
