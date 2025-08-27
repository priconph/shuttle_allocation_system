<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;

use PhpOffice\PhpSpreadsheet\Style\Alignment;

Use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use App\Exports\Sheet\OverAllSheetV3;
use App\Exports\Sheet\AllocationV3;

class ExportReportV3 implements  WithMultipleSheets
{
    use Exportable;
    // protected $masterlist;
    protected $mergedLists;
    protected $routeNameCounts;
    // protected $routeDestinationFinalCount;
    protected $factory;
    protected $from;
    protected $to;
    protected $route_code;

    function __construct(
        // $masterlist,
        $mergedLists,
        $routeNameCounts,
        // $routeDestinationFinalCount,
        $factory,
        $from,
        $to,
        $route_code
    ){
        // $this->masterlist   = $masterlist;
        $this->mergedLists                  = $mergedLists;
        $this->routeNameCounts              = $routeNameCounts;
        //  $this->routeDestinationFinalCount   = $routeDestinationFinalCount;
        $this->factory                      = $factory;
        $this->from                         = $from;
        $this->to                           = $to;
        $this->route_code                   = $route_code;
    }

    public function sheets(): array {
        $sheets = [];
        // $sheets[] = new OverAllSheet($this->masterlist);
        $sheets[] = new OverAllSheetV3($this->mergedLists);
        $sheets[] = new AllocationV3(
                            $this->routeNameCounts,
                            // $this->routeDestinationFinalCount,
                            $this->factory,
                            $this->from,
                            $this->to,
                            $this->route_code
                        );
        return $sheets;
    }
}
