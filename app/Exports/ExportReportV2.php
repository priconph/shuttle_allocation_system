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

use App\Exports\Sheet\OverAllSheet;
use App\Exports\Sheet\Allocation;

class ExportReportV2 implements  WithMultipleSheets
{
    use Exportable;
    // protected $masterlist;
    protected $mergedLists;
    protected $routeNameCounts;
    protected $routeDestinationFinalCount;
    protected $factory;
    protected $incoming;
    protected $outgoing;
    protected $from;
    protected $to;
    protected $route_code;

    function __construct(
        // $masterlist,
        $mergedLists,
        $routeNameCounts,
        $routeDestinationFinalCount,
        $factory,
        $incoming,
        $outgoing,
        $from,
        $to,
        $route_code
    ){
        // $this->masterlist   = $masterlist;
        $this->mergedLists                  = $mergedLists;
        $this->routeNameCounts              = $routeNameCounts;
        $this->routeDestinationFinalCount   = $routeDestinationFinalCount;
        $this->factory                      = $factory;
        $this->incoming                     = $incoming;
        $this->outgoing                     = $outgoing;
        $this->from                         = $from;
        $this->to                           = $to;
        $this->route_code                   = $route_code;
    }

    public function sheets(): array {
        $sheets = [];
        // $sheets[] = new OverAllSheet($this->masterlist);
        $sheets[] = new OverAllSheet($this->mergedLists);
        $sheets[] = new Allocation(
                            $this->routeNameCounts,
                            $this->routeDestinationFinalCount,
                            $this->factory,
                            $this->incoming,
                            $this->outgoing,
                            $this->from,
                            $this->to,
                            $this->route_code
                        );
        return $sheets;
    }
}
