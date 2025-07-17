<?php

namespace App\Exports\ExportTransportReport;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
Use \Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithCharts;
use PhpOffice\PhpSpreadsheet\Chart\Chart as Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExportAllocation implements  FromView, WithTitle, WithEvents{
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

    public function view(): View {
        return view('exports.export_allocation', ['date' => $this->date]);
    }

    public function title(): string{
        return 'Allocation';
    }


    public function registerEvents(): array{
        /**
         * Alignment
         */
        $alignmentAllCenter = array(
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrap' => TRUE
            ]
        );
        $alignmentHorizontalRight = array(
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'wrap' => TRUE
            ]
        );
        $alignmentHorizontalLeft = array(
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'wrap' => TRUE
            ]
        );
        $aligmentVerticalTop = array(
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
            ]
        );
        $aligmentVerticalBottom = array(
            'alignment' => [
                'vertical' => Alignment::VERTICAL_BOTTOM,
            ]
        );

        /**
         * Border
         */
        $borderStyleAllThin = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $borderStyleTopThin= [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $borderStyleBottomThin= [
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $borderStyleLeftThin= [
            'borders' => [
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $borderStyleRightThin= [
            'borders' => [
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        /**
         * Font
         */
        $fontStyleArial8 = array(
            'font' => array(
                'name'      =>  'Arial',
                'size'      =>  8,
            )
        );
        $fontStyleArial10 = array(
            'font' => array(
                'name'      =>  'Arial',
                'size'      =>  10,
                // 'color'      =>  'red',
                // 'italic'      =>  true
            )
        );
        $fontStyleArialBold10 = array(
            'font' => array(
                'name'      =>  'Arial',
                'size'      =>  10,
                'bold'      =>  true,
                // 'color'      =>  'red',
                // 'italic'      =>  true
            )
        );
        $fontStyleArialBold12 = array(
            'font' => array(
                'name'      =>  'Arial',
                'size'      =>  12,
                'bold'      =>  true,
                // 'italic'      =>  true
            )
        );
        $fontStyleArial12 = array(
            'font' => array(
                'name'      =>  'Arial',
                'size'      =>  12,
                // 'bold'      =>  true,
                // 'italic'      =>  true,
            )
        );
        $fontStyleArialBold14 = array(
            'font' => array(
                'name'      =>  'Arial',
                'size'      =>  14,
                'bold'      =>  true,
                // 'italic'      =>  true
            )
        );
        $fontStyleArialBold16 = array(
            'font' => array(
                'name'      =>  'Arial',
                'size'      =>  16,
                'bold'      =>  true,
                // 'italic'      =>  true
            )
        );
        $fontStyleArialBold20 = array(
            'font' => array(
                'name'      =>  'Arial',
                'size'      =>  20,
                'bold'      =>  true,
                // 'italic'      =>  true
            )
        );
        $fontStyleArialUnderline10 = array(
            'font' => array(
                'name'      =>  'Arial',
                'size'      =>  10,
                'underline'      =>  true,
                'bold'      =>  true,
            )
        );
        $fontStyleArialUnderline12 = array(
            'font' => array(
                'name'      =>  'Arial',
                'size'      =>  12,
                'underline'      =>  true,
                'bold'      =>  true,
            )
        );
        $fontStyleCalibri10 = array(
            'font' => array(
                'name'      =>  'Calibri',
                'size'      =>  10,
                'bold'      =>  true,
                'underline'      =>  true,
            )
        );
        
        return [
            AfterSheet::class => function(AfterSheet $event) use (
                $alignmentAllCenter,
                $alignmentHorizontalRight,
                $alignmentHorizontalLeft,
                $aligmentVerticalTop,
                $aligmentVerticalBottom,

                $borderStyleAllThin,
                $borderStyleTopThin,
                $borderStyleBottomThin,
                $borderStyleLeftThin,
                $borderStyleRightThin,

                $fontStyleArial8,
                $fontStyleArial10,
                $fontStyleArialBold10,
                $fontStyleArialBold12,
                $fontStyleArial12,
                $fontStyleArialBold14,
                $fontStyleArialBold16,
                $fontStyleArialBold20,
                $fontStyleArialUnderline10,
                $fontStyleArialUnderline12,
                $fontStyleCalibri10
            ) {
                /**
                 * Incoming Morning
                 */
                // Static values
                $event->sheet->setCellValue('A1', Carbon::parse($this->date)->addDays(1)->format('j-F') . ' Incoming 7:30AM');
                $event->sheet->getDelegate()->getStyle('A1')->applyFromArray($fontStyleArialBold20);
                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(30);

                $event->sheet->setCellValue('A2','ITEM');
                $event->sheet->setCellValue('B2','ROUTE');
                $event->sheet->setCellValue('C2','PICKUP TIME');
                $event->sheet->setCellValue('D2','PLAN MANPOWER');
                $event->sheet->setCellValue('E2','SHUTTLE PROVIDER');
                $event->sheet->setCellValue('F2','SHUTTLE ALLOCATION');
                $event->sheet->getDelegate()->getRowDimension('2')->setRowHeight(45);
                $event->sheet->getDelegate()->getStyle('A2:F2')->applyFromArray($alignmentAllCenter);
                $event->sheet->getColumnDimension('A')->setWidth(15);
                $event->sheet->getColumnDimension('B')->setWidth(57);
                $event->sheet->getColumnDimension('C')->setWidth(22);
                $event->sheet->getColumnDimension('D')->setWidth(22);
                $event->sheet->getColumnDimension('E')->setWidth(52);
                $event->sheet->getColumnDimension('F')->setWidth(55);
                $event->sheet->getDelegate()->getStyle('A2:F2')->applyFromArray($fontStyleArialBold16);
                $event->sheet->getDelegate()->getStyle('A2:F2')->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle('A2:F2')->applyFromArray($borderStyleAllThin);

                // Dynamic Values
                $totalCountForIncomingMorningArray = [];
                $counterForIncomingMorningStartWithZero = 0;
                $counterForIncomingMorningStartWithRow3 = 3;
                $totalManpowerForIncomingMorningArray = ['total'=> 0];
                $totalManpowerForIncomingEveningArray = ['total'=> 0];
                $totalManpowerForOutgoingAfternoonArray = ['total'=> 0];
                $totalManpowerForOutgoingEveningArray = ['total'=> 0];
                $totalManpowerForOutgoingMorningArray = ['total'=> 0];
                $alphabet = range("A","Z");

                for ($i = 0; $i < count($this->routesData); $i++) {
                    $temporaryTotalForEveryTimeSchedArray = ['routes_id'=>'','routes_name'=>'','incoming'=>'','outgoing'=>''];
                    // Alphabet(Column A)
                    $event->sheet->setCellValue('A'.$counterForIncomingMorningStartWithRow3, $alphabet[$counterForIncomingMorningStartWithZero]);
                    // $event->sheet->getDelegate()->getStyle('A3:A3'.$counterForIncomingMorningStartWithRow3)->getAlignment()->setWrapText(true);
                    $event->sheet->getDelegate()->getStyle('A'.$counterForIncomingMorningStartWithRow3.':A'.$counterForIncomingMorningStartWithRow3)->applyFromArray($alignmentAllCenter);
                    $event->sheet->getDelegate()->getStyle('A'.$counterForIncomingMorningStartWithRow3.':A'.$counterForIncomingMorningStartWithRow3)->applyFromArray($fontStyleArial12);

                    // Routes(Column B)
                    $event->sheet->setCellValue('B'.$counterForIncomingMorningStartWithRow3, $this->routesData[$i]->routes_name);
                    $event->sheet->getDelegate()->getStyle('B'.$counterForIncomingMorningStartWithRow3.':B'.$counterForIncomingMorningStartWithRow3)->getAlignment()->setWrapText(true);
                    $event->sheet->getDelegate()->getStyle('B'.$counterForIncomingMorningStartWithRow3.':B'.$counterForIncomingMorningStartWithRow3)->applyFromArray($fontStyleArial12);
                    $event->sheet->getDelegate()->getStyle('B'.$counterForIncomingMorningStartWithRow3.':B'.$counterForIncomingMorningStartWithRow3)->applyFromArray($alignmentAllCenter);

                    // Pickup Time(Column C)
                    $event->sheet->setCellValue('C'.$counterForIncomingMorningStartWithRow3, Carbon::parse($this->routesData[$i]->pickup_time_info->pickup_time)->format('h:i a'));
                    $event->sheet->getDelegate()->getStyle('C'.$counterForIncomingMorningStartWithRow3.':C'.$counterForIncomingMorningStartWithRow3)->applyFromArray($fontStyleArialBold16);
                    $event->sheet->getDelegate()->getStyle('C'.$counterForIncomingMorningStartWithRow3.':C'.$counterForIncomingMorningStartWithRow3)->applyFromArray($alignmentAllCenter);

                    for ($j = 0; $j < count($this->masterlistData); $j++) {
                        /**
                         * Check if routes(table) id is equal to masterlists(table) routes_id to collect all masterlistData
                         * to be used for computation of each routes.
                         * Check Ids for comparison
                         * - $event->sheet->setCellValue('C6', 'id '. $this->routesData[0]->id);
                         * - $event->sheet->setCellValue('C7', 'id '. $this->masterlistData[1]->routes_id);
                         */
                        if($this->routesData[$i]->id == $this->masterlistData[$j]->routes_id){
                            $temporaryTotalForEveryTimeSchedArray['routes_id']     = $this->routesData[$i]->id;
                            $temporaryTotalForEveryTimeSchedArray['routes_name']   = $this->routesData[$i]->routes_description;
                            $temporaryTotalForEveryTimeSchedArray['incoming']      = $this->masterlistData[$j]->masterlist_incoming;
                            $temporaryTotalForEveryTimeSchedArray['outgoing']      = $this->masterlistData[$j]->masterlist_outgoing;
                            $totalCountForIncomingMorningArray[] = $temporaryTotalForEveryTimeSchedArray;
                        }
                    }

                    // dd($totalCountForIncomingMorningArray);
                    $totalSumManpowerArray = ['total_sum'=> 0];
                    for ($l=0; $l < count($totalCountForIncomingMorningArray); $l++) {
                        $temporaryTimeScheduleOfEachEmployee = $totalCountForIncomingMorningArray[$l]['incoming'];
                        if($this->routesData[$i]->id == $totalCountForIncomingMorningArray[$l]['routes_id']){
                            if($temporaryTimeScheduleOfEachEmployee == "7:30AM"){
                                /**
                                 * Plan Manpower(Column D)
                                 */
                                $totalSumManpowerArray['total_sum']++;
                                $totalManpowerForIncomingMorningArray['total']++;
                                $event->sheet->setCellValue('D'.$counterForIncomingMorningStartWithRow3, $totalSumManpowerArray['total_sum']);
                                $event->sheet->getDelegate()->getStyle('D'.$counterForIncomingMorningStartWithRow3)->applyFromArray($fontStyleArialBold20);
                                $event->sheet->getDelegate()->getStyle('D'.$counterForIncomingMorningStartWithRow3)->applyFromArray($alignmentAllCenter);
    
                                /**
                                 * Commented on 06-06-2023
                                 */
                                // Shuttle Provider(Column E)
                                // if($this->routesData[$i]->shuttle_provider_info->shuttle_provider_name == 'Euroworld'){
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForIncomingMorningStartWithRow3.':F'.$counterForIncomingMorningStartWithRow3)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('4F6228');
                                // }else if($this->routesData[$i]->shuttle_provider_info->shuttle_provider_name == 'Hero Autobot'){
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForIncomingMorningStartWithRow3.':F'.$counterForIncomingMorningStartWithRow3)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('1F497D');
                                // }else if($this->routesData[$i]->shuttle_provider_info->shuttle_provider_name == 'NDPSS'){
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForIncomingMorningStartWithRow3.':F'.$counterForIncomingMorningStartWithRow3)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('f79646');
                                // }else{
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForIncomingMorningStartWithRow3.':F'.$counterForIncomingMorningStartWithRow3)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('000000');
                                // }
                                $event->sheet->setCellValue('E'.$counterForIncomingMorningStartWithRow3, $this->routesData[$i]->shuttle_provider_info->shuttle_provider_name);
                                $event->sheet->getDelegate()->getStyle('E'.$counterForIncomingMorningStartWithRow3.':E'.$counterForIncomingMorningStartWithRow3)->applyFromArray($fontStyleArialBold14);
                                $event->sheet->getDelegate()->getStyle('E'.$counterForIncomingMorningStartWithRow3.':E'.$counterForIncomingMorningStartWithRow3)->applyFromArray($alignmentAllCenter);
                                
                                /**
                                 * Shuttle Allocation(Column F)
                                 */
                                // dd(ceil($totalSumManpowerArray['total_sum'] / $this->routesData[$i]->shuttle_provider_info->shuttle_provider_capacity));
                                $shuttleAllocationBuses = ceil($totalSumManpowerArray['total_sum'] / $this->routesData[$i]->shuttle_provider_info->shuttle_provider_capacity) . " Shuttle Bus";
                                $event->sheet->setCellValue('F'.$counterForIncomingMorningStartWithRow3, $shuttleAllocationBuses);
                                $event->sheet->getDelegate()->getStyle('F'.$counterForIncomingMorningStartWithRow3)->applyFromArray($fontStyleArialBold20);
                                $event->sheet->getDelegate()->getStyle('F'.$counterForIncomingMorningStartWithRow3)->applyFromArray($alignmentAllCenter);
                            }
                        }else{
                            /**
                             * Plan Manpower(Column D)
                             */
                            $event->sheet->setCellValue('D'.$counterForIncomingMorningStartWithRow3, $totalSumManpowerArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle('D'.$counterForIncomingMorningStartWithRow3)->applyFromArray($fontStyleArialBold20);
                            $event->sheet->getDelegate()->getStyle('D'.$counterForIncomingMorningStartWithRow3)->applyFromArray($alignmentAllCenter);

                            /**
                             * Shuttle Allocation(Column F)
                             */
                            $shuttleAllocationBuses = "No Shuttle Allocation";
                            $event->sheet->setCellValue('F'.$counterForIncomingMorningStartWithRow3, $shuttleAllocationBuses);
                            $event->sheet->getDelegate()->getStyle('F'.$counterForIncomingMorningStartWithRow3)->applyFromArray($fontStyleArialBold20);
                            $event->sheet->getDelegate()->getStyle('F'.$counterForIncomingMorningStartWithRow3)->applyFromArray($alignmentAllCenter);
                            
                            /**
                             * Commented on 06-06-2023
                             */
                            // $event->sheet->getDelegate()->getStyle('F'.$counterForIncomingMorningStartWithRow3)
                            //     ->getFont()
                            //     ->getColor()
                            //     ->setARGB('FF0000');
                        }
                    }
                    
                    $event->sheet->getDelegate()->getStyle('A'.$counterForIncomingMorningStartWithRow3.':F'.$counterForIncomingMorningStartWithRow3)->applyFromArray($borderStyleAllThin); // Border All
                    $counterForIncomingMorningStartWithZero++;
                    $counterForIncomingMorningStartWithRow3++;
                }
                /**
                 * Grand Total
                 */
                $event->sheet->setCellValue('B'.$counterForIncomingMorningStartWithRow3, 'Grand Total');
                $event->sheet->getDelegate()->getStyle('B'.$counterForIncomingMorningStartWithRow3)->applyFromArray($fontStyleArialBold20);
                $event->sheet->getDelegate()->getStyle('B'.$counterForIncomingMorningStartWithRow3)->applyFromArray($alignmentAllCenter);
                $event->sheet->setCellValue('D'.$counterForIncomingMorningStartWithRow3, $totalManpowerForIncomingMorningArray['total']);
                $event->sheet->getDelegate()->getStyle('D'.$counterForIncomingMorningStartWithRow3)->applyFromArray($fontStyleArialBold20);
                $event->sheet->getDelegate()->getStyle('D'.$counterForIncomingMorningStartWithRow3)->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('B'.$counterForIncomingMorningStartWithRow3.':D'.$counterForIncomingMorningStartWithRow3)->applyFromArray($borderStyleAllThin); // Border All


                /**
                 * This will be used to target the row for every Time Schedules
                 * in Allocation(Sheet)
                 */
                $totalCountOfActiveRoutes = 0;
                $totalCountOfStaticRows = 5;
                for ($routesIndex = 0; $routesIndex < count($this->routesData); $routesIndex++) {
                    $totalCountOfActiveRoutes++;
                }

                /**
                 * Incoming Evening
                 */
                // Static values
                $event->sheet->setCellValue('A'.($totalCountOfActiveRoutes+$totalCountOfStaticRows+1), Carbon::parse($this->date)->addDays(1)->format('j-F') . ' Incoming 7:30PM');
                $event->sheet->getDelegate()->getStyle('A'.($totalCountOfActiveRoutes+$totalCountOfStaticRows+1))->applyFromArray($fontStyleArialBold20);
                $event->sheet->getDelegate()->getRowDimension($totalCountOfActiveRoutes+$totalCountOfStaticRows+1)->setRowHeight(30);
                $event->sheet->getDelegate()->getStyle('A'.($totalCountOfActiveRoutes+$totalCountOfStaticRows+1))->getAlignment()->setWrapText(false);

                $event->sheet->setCellValue('A'.($totalCountOfActiveRoutes+$totalCountOfStaticRows+2),'ITEM');
                $event->sheet->setCellValue('B'.($totalCountOfActiveRoutes+$totalCountOfStaticRows+2),'ROUTE');
                $event->sheet->setCellValue('C'.($totalCountOfActiveRoutes+$totalCountOfStaticRows+2),'PICKUP TIME');
                $event->sheet->setCellValue('D'.($totalCountOfActiveRoutes+$totalCountOfStaticRows+2),'PLAN MANPOWER');
                $event->sheet->setCellValue('E'.($totalCountOfActiveRoutes+$totalCountOfStaticRows+2),'SHUTTLE PROVIDER');
                $event->sheet->setCellValue('F'.($totalCountOfActiveRoutes+$totalCountOfStaticRows+2),'SHUTTLE ALLOCATION');
                $event->sheet->getDelegate()->getRowDimension($totalCountOfActiveRoutes+$totalCountOfStaticRows+2)->setRowHeight(45);
                $event->sheet->getDelegate()->getStyle('A'.($totalCountOfActiveRoutes+$totalCountOfStaticRows+2).':F'.($totalCountOfActiveRoutes+$totalCountOfStaticRows+2))->applyFromArray($alignmentAllCenter);
                $event->sheet->getColumnDimension('A')->setWidth(15);
                $event->sheet->getColumnDimension('B')->setWidth(57);
                $event->sheet->getColumnDimension('C')->setWidth(22);
                $event->sheet->getColumnDimension('D')->setWidth(22);
                $event->sheet->getColumnDimension('E')->setWidth(52);
                $event->sheet->getColumnDimension('F')->setWidth(55);
                $event->sheet->getDelegate()->getStyle('A'.($totalCountOfActiveRoutes+$totalCountOfStaticRows+2).':F'.($totalCountOfActiveRoutes+$totalCountOfStaticRows+2))->applyFromArray($fontStyleArialBold16);
                $event->sheet->getDelegate()->getStyle('A'.($totalCountOfActiveRoutes+$totalCountOfStaticRows+2).':F'.($totalCountOfActiveRoutes+$totalCountOfStaticRows+2))->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle('A'.($totalCountOfActiveRoutes+$totalCountOfStaticRows+2).':F'.($totalCountOfActiveRoutes+$totalCountOfStaticRows+2))->applyFromArray($borderStyleAllThin);

                // Dynamic Values
                $totalCountForIncomingEveningArray = [];
                $counterForIncomingEveningStartWithZero = 0;
                $counterForIncomingEveningStartWithRow = $totalCountOfActiveRoutes+$totalCountOfStaticRows+3;
                
                $alphabet = range("A","Z");
                for ($i = 0; $i < count($this->routesData); $i++) {
                    $temporaryTotalForEveryTimeSchedArray = ['routes_id'=>'','routes_name'=>'','incoming'=>'','outgoing'=>''];
                    // Alphabet(Column A)
                    $event->sheet->setCellValue('A'.$counterForIncomingEveningStartWithRow, $alphabet[$counterForIncomingEveningStartWithZero]);
                    $event->sheet->getDelegate()->getStyle('A'.$counterForIncomingEveningStartWithRow.':A'.$counterForIncomingEveningStartWithRow)->applyFromArray($alignmentAllCenter);
                    $event->sheet->getDelegate()->getStyle('A'.$counterForIncomingEveningStartWithRow.':A'.$counterForIncomingEveningStartWithRow)->applyFromArray($fontStyleArial12);

                    // Routes(Column B)
                    $event->sheet->setCellValue('B'.$counterForIncomingEveningStartWithRow, $this->routesData[$i]->routes_name);
                    $event->sheet->getDelegate()->getStyle('B'.$counterForIncomingEveningStartWithRow.':B'.$counterForIncomingEveningStartWithRow)->getAlignment()->setWrapText(true);
                    $event->sheet->getDelegate()->getStyle('B'.$counterForIncomingEveningStartWithRow.':B'.$counterForIncomingEveningStartWithRow)->applyFromArray($fontStyleArial12);
                    $event->sheet->getDelegate()->getStyle('B'.$counterForIncomingEveningStartWithRow.':B'.$counterForIncomingEveningStartWithRow)->applyFromArray($alignmentAllCenter);

                    // Pickup Time(Column C)
                    $event->sheet->setCellValue('C'.$counterForIncomingEveningStartWithRow, Carbon::parse($this->routesData[$i]->pickup_time_info->pickup_time)->format('h:i a'));
                    $event->sheet->getDelegate()->getStyle('C'.$counterForIncomingEveningStartWithRow.':C'.$counterForIncomingEveningStartWithRow)->applyFromArray($fontStyleArialBold16);
                    $event->sheet->getDelegate()->getStyle('C'.$counterForIncomingEveningStartWithRow.':C'.$counterForIncomingEveningStartWithRow)->applyFromArray($alignmentAllCenter);

                    for ($j = 0; $j < count($this->masterlistData); $j++) {
                        /**
                         * Check if routes(table) id is equal to masterlists(table) routes_id to collect all masterlistData
                         * to be used for computation of each routes.
                         * Check Ids for comparison
                         * - $event->sheet->setCellValue('C6', 'id '. $this->routesData[0]->id);
                         * - $event->sheet->setCellValue('C7', 'id '. $this->masterlistData[1]->routes_id);
                         */
                        if($this->routesData[$i]->id == $this->masterlistData[$j]->routes_id){
                            $temporaryTotalForEveryTimeSchedArray['routes_id']     = $this->routesData[$i]->id;
                            $temporaryTotalForEveryTimeSchedArray['routes_name']   = $this->routesData[$i]->routes_description;
                            $temporaryTotalForEveryTimeSchedArray['incoming']      = $this->masterlistData[$j]->masterlist_incoming;
                            $temporaryTotalForEveryTimeSchedArray['outgoing']      = $this->masterlistData[$j]->masterlist_outgoing;
                            $totalCountForIncomingEveningArray[] = $temporaryTotalForEveryTimeSchedArray;
                        }
                    }

                    // dd($totalCountForIncomingEveningArray);
                    $totalSumManpowerArray = ['total_sum'=> 0];
                    for ($l=0; $l < count($totalCountForIncomingEveningArray); $l++) {
                        $temporaryTimeScheduleOfEachEmployee = $totalCountForIncomingEveningArray[$l]['incoming'];
                        if($this->routesData[$i]->id == $totalCountForIncomingEveningArray[$l]['routes_id']){
                            if($temporaryTimeScheduleOfEachEmployee == "7:30PM"){
                                /**
                                 * Plan Manpower(Column F)
                                 */
                                $totalSumManpowerArray['total_sum']++;
                                $totalManpowerForIncomingEveningArray['total']++;
                                $event->sheet->setCellValue('D'.$counterForIncomingEveningStartWithRow, $totalSumManpowerArray['total_sum']);
                                $event->sheet->getDelegate()->getStyle('D'.$counterForIncomingEveningStartWithRow)->applyFromArray($fontStyleArialBold20);
                                $event->sheet->getDelegate()->getStyle('D'.$counterForIncomingEveningStartWithRow)->applyFromArray($alignmentAllCenter);
    
                                /**
                                 * Commented on 06-06-2023
                                 */
                                // Shuttle Provider(Column E)
                                // if($this->routesData[$i]->shuttle_provider_info->shuttle_provider_name == 'Euroworld'){
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForIncomingEveningStartWithRow.':F'.$counterForIncomingEveningStartWithRow)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('4F6228');
                                // }else if($this->routesData[$i]->shuttle_provider_info->shuttle_provider_name == 'Hero Autobot'){
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForIncomingEveningStartWithRow.':F'.$counterForIncomingEveningStartWithRow)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('1F497D');
                                // }else if($this->routesData[$i]->shuttle_provider_info->shuttle_provider_name == 'NDPSS'){
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForIncomingEveningStartWithRow.':F'.$counterForIncomingEveningStartWithRow)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('f79646');
                                // }else{
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForIncomingEveningStartWithRow.':F'.$counterForIncomingEveningStartWithRow)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('000000');
                                // }
                                $event->sheet->setCellValue('E'.$counterForIncomingEveningStartWithRow, $this->routesData[$i]->shuttle_provider_info->shuttle_provider_name);
                                $event->sheet->getDelegate()->getStyle('E'.$counterForIncomingEveningStartWithRow.':E'.$counterForIncomingEveningStartWithRow)->applyFromArray($fontStyleArialBold14);
                                $event->sheet->getDelegate()->getStyle('E'.$counterForIncomingEveningStartWithRow.':E'.$counterForIncomingEveningStartWithRow)->applyFromArray($alignmentAllCenter);

                                /**
                                 * Shuttle Allocation
                                 */
                                // dd(ceil($totalSumManpowerArray['total_sum'] / $this->routesData[$i]->shuttle_provider_info->shuttle_provider_capacity));
                                $shuttleAllocationBuses = ceil($totalSumManpowerArray['total_sum'] / $this->routesData[$i]->shuttle_provider_info->shuttle_provider_capacity) . " Shuttle Bus";
                                $event->sheet->setCellValue('F'.$counterForIncomingEveningStartWithRow, $shuttleAllocationBuses);
                                $event->sheet->getDelegate()->getStyle('F'.$counterForIncomingEveningStartWithRow)->applyFromArray($fontStyleArialBold20);
                                $event->sheet->getDelegate()->getStyle('F'.$counterForIncomingEveningStartWithRow)->applyFromArray($alignmentAllCenter);
                            }
                        }else{
                            /**
                             * Plan Manpower(Column F)
                             */
                            $event->sheet->setCellValue('D'.$counterForIncomingEveningStartWithRow, $totalSumManpowerArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle('D'.$counterForIncomingEveningStartWithRow)->applyFromArray($fontStyleArialBold20);
                            $event->sheet->getDelegate()->getStyle('D'.$counterForIncomingEveningStartWithRow)->applyFromArray($alignmentAllCenter);

                            /**
                             * Shuttle Allocation(Column F)
                             */
                            $shuttleAllocationBuses = "No Shuttle Allocation";
                            $event->sheet->setCellValue('F'.$counterForIncomingEveningStartWithRow, $shuttleAllocationBuses);
                            $event->sheet->getDelegate()->getStyle('F'.$counterForIncomingEveningStartWithRow)->applyFromArray($fontStyleArialBold20);
                            $event->sheet->getDelegate()->getStyle('F'.$counterForIncomingEveningStartWithRow)->applyFromArray($alignmentAllCenter);
                            /**
                             * Commented on 06-06-2023
                             */
                            // $event->sheet->getDelegate()->getStyle('F'.$counterForIncomingEveningStartWithRow)
                            //     ->getFont()
                            //     ->getColor()
                            //     ->setARGB('FF0000');
                        }
                        
                    }
                    
                    $event->sheet->getDelegate()->getStyle('A'.$counterForIncomingEveningStartWithRow.':F'.$counterForIncomingEveningStartWithRow)->applyFromArray($borderStyleAllThin); // Border All
                    $counterForIncomingEveningStartWithZero++;
                    $counterForIncomingEveningStartWithRow++;
                }
                /**
                 * Grand Total
                 */
                $event->sheet->setCellValue('B'.$counterForIncomingEveningStartWithRow, 'Grand Total');
                $event->sheet->getDelegate()->getStyle('B'.$counterForIncomingEveningStartWithRow)->applyFromArray($fontStyleArialBold20);
                $event->sheet->getDelegate()->getStyle('B'.$counterForIncomingEveningStartWithRow)->applyFromArray($alignmentAllCenter);
                $event->sheet->setCellValue('D'.$counterForIncomingEveningStartWithRow, $totalManpowerForIncomingEveningArray['total']);
                $event->sheet->getDelegate()->getStyle('D'.$counterForIncomingEveningStartWithRow)->applyFromArray($fontStyleArialBold20);
                $event->sheet->getDelegate()->getStyle('D'.$counterForIncomingEveningStartWithRow)->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('B'.$counterForIncomingEveningStartWithRow.':D'.$counterForIncomingEveningStartWithRow)->applyFromArray($borderStyleAllThin); // Border All


                /**
                 * Outgoing Afternoon
                 */
                // Static values
                $countForOutgoingActiveRoutes = $totalCountOfActiveRoutes*2;
                $countForOutgoingStaticRows = $totalCountOfStaticRows*2;
                // dd($countForOutgoingActiveRoutes+$countForOutgoingStaticRows);
                $event->sheet->setCellValue('A'.($countForOutgoingActiveRoutes+$countForOutgoingStaticRows+1), Carbon::parse($this->date)->addDays(1)->format('j-F') . ' Outgoing 4:30PM');
                $event->sheet->getDelegate()->getStyle('A'.($countForOutgoingActiveRoutes+$countForOutgoingStaticRows+1))->applyFromArray($fontStyleArialBold20);
                $event->sheet->getDelegate()->getRowDimension(($countForOutgoingActiveRoutes+$countForOutgoingStaticRows+1))->setRowHeight(30);
                $event->sheet->getDelegate()->getStyle('A'.($countForOutgoingActiveRoutes+$countForOutgoingStaticRows+1))->getAlignment()->setWrapText(false);

                $event->sheet->setCellValue('A'.($countForOutgoingActiveRoutes+$countForOutgoingStaticRows+2),'ITEM');
                $event->sheet->setCellValue('B'.($countForOutgoingActiveRoutes+$countForOutgoingStaticRows+2),'ROUTE');
                $event->sheet->setCellValue('C'.($countForOutgoingActiveRoutes+$countForOutgoingStaticRows+2),'PICKUP TIME');
                $event->sheet->setCellValue('D'.($countForOutgoingActiveRoutes+$countForOutgoingStaticRows+2),'PLAN MANPOWER');
                $event->sheet->setCellValue('E'.($countForOutgoingActiveRoutes+$countForOutgoingStaticRows+2),'SHUTTLE PROVIDER');
                $event->sheet->setCellValue('F'.($countForOutgoingActiveRoutes+$countForOutgoingStaticRows+2),'SHUTTLE ALLOCATION');
                $event->sheet->getDelegate()->getRowDimension($countForOutgoingActiveRoutes+$countForOutgoingStaticRows+2)->setRowHeight(45);
                $event->sheet->getDelegate()->getStyle('A'.($countForOutgoingActiveRoutes+$countForOutgoingStaticRows+2).':F'.($countForOutgoingActiveRoutes+$countForOutgoingStaticRows+2))->applyFromArray($alignmentAllCenter);
                $event->sheet->getColumnDimension('A')->setWidth(15);
                $event->sheet->getColumnDimension('B')->setWidth(57);
                $event->sheet->getColumnDimension('C')->setWidth(22);
                $event->sheet->getColumnDimension('D')->setWidth(22);
                $event->sheet->getColumnDimension('E')->setWidth(52);
                $event->sheet->getColumnDimension('F')->setWidth(55);
                $event->sheet->getDelegate()->getStyle('A'.($countForOutgoingActiveRoutes+$countForOutgoingStaticRows+2).':F'.($countForOutgoingActiveRoutes+$countForOutgoingStaticRows+2))->applyFromArray($fontStyleArialBold16);
                $event->sheet->getDelegate()->getStyle('A'.($countForOutgoingActiveRoutes+$countForOutgoingStaticRows+2).':F'.($countForOutgoingActiveRoutes+$countForOutgoingStaticRows+2))->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle('A'.($countForOutgoingActiveRoutes+$countForOutgoingStaticRows+2).':F'.($countForOutgoingActiveRoutes+$countForOutgoingStaticRows+2))->applyFromArray($borderStyleAllThin);
                
                // Dynamic Values
                $totalCountForOutgoingAfternoonArray = [];
                $counterForOutgoingAfternoonStartWithZero = 0;
                $counterForOutgoingAfternoonStartWithRow = $countForOutgoingActiveRoutes+$countForOutgoingStaticRows+3;
                $alphabet = range("A","Z");
                for ($i = 0; $i < count($this->routesData); $i++) {
                    $temporaryTotalForEveryTimeSchedArray = ['routes_id'=>'','routes_name'=>'','incoming'=>'','outgoing'=>''];
                    // Alphabet(Column A)
                    $event->sheet->setCellValue('A'.$counterForOutgoingAfternoonStartWithRow, $alphabet[$counterForOutgoingAfternoonStartWithZero]);
                    $event->sheet->getDelegate()->getStyle('A'.$counterForOutgoingAfternoonStartWithRow.':A'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($alignmentAllCenter);
                    $event->sheet->getDelegate()->getStyle('A'.$counterForOutgoingAfternoonStartWithRow.':A'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($fontStyleArial12);

                    // Routes(Column B)
                    $event->sheet->setCellValue('B'.$counterForOutgoingAfternoonStartWithRow, $this->routesData[$i]->routes_name);
                    $event->sheet->getDelegate()->getStyle('B'.$counterForOutgoingAfternoonStartWithRow.':B'.$counterForOutgoingAfternoonStartWithRow)->getAlignment()->setWrapText(true);
                    $event->sheet->getDelegate()->getStyle('B'.$counterForOutgoingAfternoonStartWithRow.':B'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($fontStyleArial12);
                    $event->sheet->getDelegate()->getStyle('B'.$counterForOutgoingAfternoonStartWithRow.':B'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($alignmentAllCenter);

                    // Pickup Time(Column C)
                    $event->sheet->setCellValue('C'.$counterForOutgoingAfternoonStartWithRow, Carbon::parse($this->routesData[$i]->pickup_time_info->pickup_time)->format('h:i a'));
                    $event->sheet->getDelegate()->getStyle('C'.$counterForOutgoingAfternoonStartWithRow.':C'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($fontStyleArialBold16);
                    $event->sheet->getDelegate()->getStyle('C'.$counterForOutgoingAfternoonStartWithRow.':C'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($alignmentAllCenter);

                    for ($j = 0; $j < count($this->masterlistData); $j++) {
                        /**
                         * Check if routes(table) id is equal to masterlists(table) routes_id to collect all masterlistData
                         * to be used for computation of each routes.
                         * Check Ids for comparison
                         * - $event->sheet->setCellValue('C6', 'id '. $this->routesData[0]->id);
                         * - $event->sheet->setCellValue('C7', 'id '. $this->masterlistData[1]->routes_id);
                         */
                        if($this->routesData[$i]->id == $this->masterlistData[$j]->routes_id){
                            $temporaryTotalForEveryTimeSchedArray['routes_id']     = $this->routesData[$i]->id;
                            $temporaryTotalForEveryTimeSchedArray['routes_name']   = $this->routesData[$i]->routes_description;
                            $temporaryTotalForEveryTimeSchedArray['incoming']      = $this->masterlistData[$j]->masterlist_incoming;
                            $temporaryTotalForEveryTimeSchedArray['outgoing']      = $this->masterlistData[$j]->masterlist_outgoing;
                            $totalCountForOutgoingAfternoonArray[] = $temporaryTotalForEveryTimeSchedArray;
                        }
                    }

                    // dd($totalCountForOutgoingAfternoonArray);
                    $totalSumManpowerArray = ['total_sum'=> 0];
                    for ($l=0; $l < count($totalCountForOutgoingAfternoonArray); $l++) {
                        $temporaryTimeScheduleOfEachEmployee = $totalCountForOutgoingAfternoonArray[$l]['outgoing'];
                        if($this->routesData[$i]->id == $totalCountForOutgoingAfternoonArray[$l]['routes_id']){
                            if($temporaryTimeScheduleOfEachEmployee == "4:30PM"){
                                /**
                                 * Plan Manpower(Column F)
                                 */
                                $totalSumManpowerArray['total_sum']++;
                                $totalManpowerForOutgoingAfternoonArray['total']++;
                                $event->sheet->setCellValue('D'.$counterForOutgoingAfternoonStartWithRow, $totalSumManpowerArray['total_sum']);
                                $event->sheet->getDelegate()->getStyle('D'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($fontStyleArialBold20);
                                $event->sheet->getDelegate()->getStyle('D'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($alignmentAllCenter);
    
                                /**
                                 * Commented on 06-06-2023
                                 */
                                // Shuttle Provider(Column E)
                                // if($this->routesData[$i]->shuttle_provider_info->shuttle_provider_name == 'Euroworld'){
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForOutgoingAfternoonStartWithRow.':F'.$counterForOutgoingAfternoonStartWithRow)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('4F6228');
                                // }else if($this->routesData[$i]->shuttle_provider_info->shuttle_provider_name == 'Hero Autobot'){
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForOutgoingAfternoonStartWithRow.':F'.$counterForOutgoingAfternoonStartWithRow)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('1F497D');
                                // }else if($this->routesData[$i]->shuttle_provider_info->shuttle_provider_name == 'NDPSS'){
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForOutgoingAfternoonStartWithRow.':F'.$counterForOutgoingAfternoonStartWithRow)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('f79646');
                                // }else{
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForOutgoingAfternoonStartWithRow.':F'.$counterForOutgoingAfternoonStartWithRow)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('000000');
                                // }
                                $event->sheet->setCellValue('E'.$counterForOutgoingAfternoonStartWithRow, $this->routesData[$i]->shuttle_provider_info->shuttle_provider_name);
                                $event->sheet->getDelegate()->getStyle('E'.$counterForOutgoingAfternoonStartWithRow.':E'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($fontStyleArialBold14);
                                $event->sheet->getDelegate()->getStyle('E'.$counterForOutgoingAfternoonStartWithRow.':E'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($alignmentAllCenter);

                                /**
                                 * Shuttle Allocation
                                 */
                                // dd(ceil($totalSumManpowerArray['total_sum'] / $this->routesData[$i]->shuttle_provider_info->shuttle_provider_capacity));
                                $shuttleAllocationBuses = ceil($totalSumManpowerArray['total_sum'] / $this->routesData[$i]->shuttle_provider_info->shuttle_provider_capacity) . " Shuttle Bus";
                                $event->sheet->setCellValue('F'.$counterForOutgoingAfternoonStartWithRow, $shuttleAllocationBuses);
                                $event->sheet->getDelegate()->getStyle('F'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($fontStyleArialBold20);
                                $event->sheet->getDelegate()->getStyle('F'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($alignmentAllCenter);
                            }
                        }else{
                            /**
                             * Plan Manpower(Column F)
                             */
                            $event->sheet->setCellValue('D'.$counterForOutgoingAfternoonStartWithRow, $totalSumManpowerArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle('D'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($fontStyleArialBold20);
                            $event->sheet->getDelegate()->getStyle('D'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($alignmentAllCenter);

                            /**
                             * Shuttle Allocation(Column F)
                             */
                            $shuttleAllocationBuses = "No Shuttle Allocation";
                            $event->sheet->setCellValue('F'.$counterForOutgoingAfternoonStartWithRow, $shuttleAllocationBuses);
                            $event->sheet->getDelegate()->getStyle('F'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($fontStyleArialBold20);
                            $event->sheet->getDelegate()->getStyle('F'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($alignmentAllCenter);
                            /**
                             * Commented on 06-06-2023
                             */
                            // $event->sheet->getDelegate()->getStyle('F'.$counterForOutgoingAfternoonStartWithRow)
                            //     ->getFont()
                            //     ->getColor()
                            //     ->setARGB('FF0000');
                        }
                        
                    }
                    
                    $event->sheet->getDelegate()->getStyle('A'.$counterForOutgoingAfternoonStartWithRow.':F'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($borderStyleAllThin); // Border All
                    $counterForOutgoingAfternoonStartWithZero++;
                    $counterForOutgoingAfternoonStartWithRow++;
                }
                /**
                 * Grand Total
                 */
                $event->sheet->setCellValue('B'.$counterForOutgoingAfternoonStartWithRow, 'Grand Total');
                $event->sheet->getDelegate()->getStyle('B'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($fontStyleArialBold20);
                $event->sheet->getDelegate()->getStyle('B'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($alignmentAllCenter);
                $event->sheet->setCellValue('D'.$counterForOutgoingAfternoonStartWithRow, $totalManpowerForOutgoingAfternoonArray['total']);
                $event->sheet->getDelegate()->getStyle('D'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($fontStyleArialBold20);
                $event->sheet->getDelegate()->getStyle('D'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('B'.$counterForOutgoingAfternoonStartWithRow.':D'.$counterForOutgoingAfternoonStartWithRow)->applyFromArray($borderStyleAllThin); // Border All


                /**
                 * Outgoing Evening
                 */
                // Static values
                $countForOutgoingEveningActiveRoutes = $totalCountOfActiveRoutes*3;
                $countForOutgoingEveningStaticRows = $totalCountOfStaticRows*3;
                // dd($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows);
                $event->sheet->setCellValue('A'.($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+1), Carbon::parse($this->date)->addDays(1)->format('j-F') . ' Outgoing 7:30PM');
                $event->sheet->getDelegate()->getStyle('A'.($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+1))->applyFromArray($fontStyleArialBold20);
                $event->sheet->getDelegate()->getRowDimension($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+1)->setRowHeight(30);
                $event->sheet->getDelegate()->getStyle('A'.($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+1))->getAlignment()->setWrapText(false);

                $event->sheet->setCellValue('A'.($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+2),'ITEM');
                $event->sheet->setCellValue('B'.($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+2),'ROUTE');
                $event->sheet->setCellValue('C'.($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+2),'PICKUP TIME');
                $event->sheet->setCellValue('D'.($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+2),'PLAN MANPOWER');
                $event->sheet->setCellValue('E'.($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+2),'SHUTTLE PROVIDER');
                $event->sheet->setCellValue('F'.($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+2),'SHUTTLE ALLOCATION');
                $event->sheet->getDelegate()->getRowDimension($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+2)->setRowHeight(45);
                $event->sheet->getDelegate()->getStyle('A'.($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+2).':F'.($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+2))->applyFromArray($alignmentAllCenter);
                $event->sheet->getColumnDimension('A')->setWidth(15);
                $event->sheet->getColumnDimension('B')->setWidth(57);
                $event->sheet->getColumnDimension('C')->setWidth(22);
                $event->sheet->getColumnDimension('D')->setWidth(22);
                $event->sheet->getColumnDimension('E')->setWidth(52);
                $event->sheet->getColumnDimension('F')->setWidth(55);
                $event->sheet->getDelegate()->getStyle('A'.($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+2).':F'.($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+2))->applyFromArray($fontStyleArialBold16);
                $event->sheet->getDelegate()->getStyle('A'.($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+2).':F'.($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+2))->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle('A'.($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+2).':F'.($countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+2))->applyFromArray($borderStyleAllThin);

                // Dynamic Values
                $totalCountForOutgoingEveningArray = [];
                $counterForOutgoingEveningStartWithZero = 0;
                $counterForOutgoingEveningStartWithRow = $countForOutgoingEveningActiveRoutes+$countForOutgoingEveningStaticRows+3;
                $alphabet = range("A","Z");
                for ($i = 0; $i < count($this->routesData); $i++) {
                    $temporaryTotalForEveryTimeSchedArray = ['routes_id'=>'','routes_name'=>'','incoming'=>'','outgoing'=>''];
                    // Alphabet(Column A)
                    $event->sheet->setCellValue('A'.$counterForOutgoingEveningStartWithRow, $alphabet[$counterForOutgoingEveningStartWithZero]);
                    $event->sheet->getDelegate()->getStyle('A'.$counterForOutgoingEveningStartWithRow.':A'.$counterForOutgoingEveningStartWithRow)->applyFromArray($alignmentAllCenter);
                    $event->sheet->getDelegate()->getStyle('A'.$counterForOutgoingEveningStartWithRow.':A'.$counterForOutgoingEveningStartWithRow)->applyFromArray($fontStyleArial12);

                    // Routes(Column B)
                    $event->sheet->setCellValue('B'.$counterForOutgoingEveningStartWithRow, $this->routesData[$i]->routes_name);
                    $event->sheet->getDelegate()->getStyle('B'.$counterForOutgoingEveningStartWithRow.':B'.$counterForOutgoingEveningStartWithRow)->getAlignment()->setWrapText(true);
                    $event->sheet->getDelegate()->getStyle('B'.$counterForOutgoingEveningStartWithRow.':B'.$counterForOutgoingEveningStartWithRow)->applyFromArray($fontStyleArial12);
                    $event->sheet->getDelegate()->getStyle('B'.$counterForOutgoingEveningStartWithRow.':B'.$counterForOutgoingEveningStartWithRow)->applyFromArray($alignmentAllCenter);

                    // Pickup Time(Column C)
                    $event->sheet->setCellValue('C'.$counterForOutgoingEveningStartWithRow, Carbon::parse($this->routesData[$i]->pickup_time_info->pickup_time)->format('h:i a'));
                    $event->sheet->getDelegate()->getStyle('C'.$counterForOutgoingEveningStartWithRow.':C'.$counterForOutgoingEveningStartWithRow)->applyFromArray($fontStyleArialBold16);
                    $event->sheet->getDelegate()->getStyle('C'.$counterForOutgoingEveningStartWithRow.':C'.$counterForOutgoingEveningStartWithRow)->applyFromArray($alignmentAllCenter);

                    for ($j = 0; $j < count($this->masterlistData); $j++) {
                        /**
                         * Check if routes(table) id is equal to masterlists(table) routes_id to collect all masterlistData
                         * to be used for computation of each routes.
                         * Check Ids for comparison
                         * - $event->sheet->setCellValue('C6', 'id '. $this->routesData[0]->id);
                         * - $event->sheet->setCellValue('C7', 'id '. $this->masterlistData[1]->routes_id);
                         */
                        if($this->routesData[$i]->id == $this->masterlistData[$j]->routes_id){
                            $temporaryTotalForEveryTimeSchedArray['routes_id']     = $this->routesData[$i]->id;
                            $temporaryTotalForEveryTimeSchedArray['routes_name']   = $this->routesData[$i]->routes_description;
                            $temporaryTotalForEveryTimeSchedArray['incoming']      = $this->masterlistData[$j]->masterlist_incoming;
                            $temporaryTotalForEveryTimeSchedArray['outgoing']      = $this->masterlistData[$j]->masterlist_outgoing;
                            $totalCountForOutgoingEveningArray[] = $temporaryTotalForEveryTimeSchedArray;
                        }
                    }

                    // dd($totalCountForOutgoingEveningArray);
                    $totalSumManpowerArray = ['total_sum'=> 0];
                    for ($l=0; $l < count($totalCountForOutgoingEveningArray); $l++) {
                        $temporaryTimeScheduleOfEachEmployee = $totalCountForOutgoingEveningArray[$l]['outgoing'];
                        if($this->routesData[$i]->id == $totalCountForOutgoingEveningArray[$l]['routes_id']){
                            if($temporaryTimeScheduleOfEachEmployee == "7:30PM"){
                                /**
                                 * Plan Manpower(Column F)
                                 */
                                $totalSumManpowerArray['total_sum']++;
                                $totalManpowerForOutgoingEveningArray['total']++;
                                $event->sheet->setCellValue('D'.$counterForOutgoingEveningStartWithRow, $totalSumManpowerArray['total_sum']);
                                $event->sheet->getDelegate()->getStyle('D'.$counterForOutgoingEveningStartWithRow)->applyFromArray($fontStyleArialBold20);
                                $event->sheet->getDelegate()->getStyle('D'.$counterForOutgoingEveningStartWithRow)->applyFromArray($alignmentAllCenter);
    
                                /**
                                 * Commented on 06-06-2023
                                 */
                                // Shuttle Provider(Column E)
                                // if($this->routesData[$i]->shuttle_provider_info->shuttle_provider_name == 'Euroworld'){
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForOutgoingEveningStartWithRow.':F'.$counterForOutgoingEveningStartWithRow)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('4F6228');
                                // }else if($this->routesData[$i]->shuttle_provider_info->shuttle_provider_name == 'Hero Autobot'){
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForOutgoingEveningStartWithRow.':F'.$counterForOutgoingEveningStartWithRow)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('1F497D');
                                // }else if($this->routesData[$i]->shuttle_provider_info->shuttle_provider_name == 'NDPSS'){
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForOutgoingEveningStartWithRow.':F'.$counterForOutgoingEveningStartWithRow)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('f79646');
                                // }else{
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForOutgoingEveningStartWithRow.':F'.$counterForOutgoingEveningStartWithRow)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('000000');
                                // }
                                $event->sheet->setCellValue('E'.$counterForOutgoingEveningStartWithRow, $this->routesData[$i]->shuttle_provider_info->shuttle_provider_name);
                                $event->sheet->getDelegate()->getStyle('E'.$counterForOutgoingEveningStartWithRow.':E'.$counterForOutgoingEveningStartWithRow)->applyFromArray($fontStyleArialBold14);
                                $event->sheet->getDelegate()->getStyle('E'.$counterForOutgoingEveningStartWithRow.':E'.$counterForOutgoingEveningStartWithRow)->applyFromArray($alignmentAllCenter);

                                /**
                                 * Shuttle Allocation
                                 */
                                // dd(ceil($totalSumManpowerArray['total_sum'] / $this->routesData[$i]->shuttle_provider_info->shuttle_provider_capacity));
                                $shuttleAllocationBuses = ceil($totalSumManpowerArray['total_sum'] / $this->routesData[$i]->shuttle_provider_info->shuttle_provider_capacity) . " Shuttle Bus";
                                $event->sheet->setCellValue('F'.$counterForOutgoingEveningStartWithRow, $shuttleAllocationBuses);
                                $event->sheet->getDelegate()->getStyle('F'.$counterForOutgoingEveningStartWithRow)->applyFromArray($fontStyleArialBold20);
                                $event->sheet->getDelegate()->getStyle('F'.$counterForOutgoingEveningStartWithRow)->applyFromArray($alignmentAllCenter);
                            }
                        }else{
                            /**
                             * Plan Manpower(Column F)
                             */
                            $event->sheet->setCellValue('D'.$counterForOutgoingEveningStartWithRow, $totalSumManpowerArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle('D'.$counterForOutgoingEveningStartWithRow)->applyFromArray($fontStyleArialBold20);
                            $event->sheet->getDelegate()->getStyle('D'.$counterForOutgoingEveningStartWithRow)->applyFromArray($alignmentAllCenter);

                            /**
                             * Shuttle Allocation(Column F)
                             */
                            $shuttleAllocationBuses = "No Shuttle Allocation";
                            $event->sheet->setCellValue('F'.$counterForOutgoingEveningStartWithRow, $shuttleAllocationBuses);
                            $event->sheet->getDelegate()->getStyle('F'.$counterForOutgoingEveningStartWithRow)->applyFromArray($fontStyleArialBold20);
                            $event->sheet->getDelegate()->getStyle('F'.$counterForOutgoingEveningStartWithRow)->applyFromArray($alignmentAllCenter);
                            /**
                             * Commented on 06-06-2023
                             */
                            // $event->sheet->getDelegate()->getStyle('F'.$counterForOutgoingEveningStartWithRow)
                            //     ->getFont()
                            //     ->getColor()
                            //     ->setARGB('FF0000');
                        }
                        
                    }
                    
                    $event->sheet->getDelegate()->getStyle('A'.$counterForOutgoingEveningStartWithRow.':F'.$counterForOutgoingEveningStartWithRow)->applyFromArray($borderStyleAllThin); // Border All
                    $counterForOutgoingEveningStartWithZero++;
                    $counterForOutgoingEveningStartWithRow++;
                }
                /**
                 * Grand Total
                 */
                $event->sheet->setCellValue('B'.$counterForOutgoingEveningStartWithRow, 'Grand Total');
                $event->sheet->getDelegate()->getStyle('B'.$counterForOutgoingEveningStartWithRow)->applyFromArray($fontStyleArialBold20);
                $event->sheet->getDelegate()->getStyle('B'.$counterForOutgoingEveningStartWithRow)->applyFromArray($alignmentAllCenter);
                $event->sheet->setCellValue('D'.$counterForOutgoingEveningStartWithRow, $totalManpowerForOutgoingEveningArray['total']);
                $event->sheet->getDelegate()->getStyle('D'.$counterForOutgoingEveningStartWithRow)->applyFromArray($fontStyleArialBold20);
                $event->sheet->getDelegate()->getStyle('D'.$counterForOutgoingEveningStartWithRow)->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('B'.$counterForOutgoingEveningStartWithRow.':D'.$counterForOutgoingEveningStartWithRow)->applyFromArray($borderStyleAllThin); // Border All


                /**
                 * Outgoing Morning
                 */
                // Static values
                $countForOutgoingMorningActiveRoutes = $totalCountOfActiveRoutes*4;
                $countForOutgoingMorningStaticRows = $totalCountOfStaticRows*4;
                // dd($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows);
                $event->sheet->setCellValue('A'.($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+1), Carbon::parse($this->date)->addDays(2)->format('j-F') . ' Outgoing 7:30AM');
                $event->sheet->getDelegate()->getStyle('A'.($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+1))->applyFromArray($fontStyleArialBold20);
                $event->sheet->getDelegate()->getRowDimension($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+1)->setRowHeight(30);
                $event->sheet->getDelegate()->getStyle('A'.($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+1))->getAlignment()->setWrapText(false);

                $event->sheet->setCellValue('A'.($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+2),'ITEM');
                $event->sheet->setCellValue('B'.($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+2),'ROUTE');
                $event->sheet->setCellValue('C'.($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+2),'PICKUP TIME');
                $event->sheet->setCellValue('D'.($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+2),'PLAN MANPOWER');
                $event->sheet->setCellValue('E'.($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+2),'SHUTTLE PROVIDER');
                $event->sheet->setCellValue('F'.($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+2),'SHUTTLE ALLOCATION');
                $event->sheet->getDelegate()->getRowDimension($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+2)->setRowHeight(45);
                $event->sheet->getDelegate()->getStyle('A'.($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+2).':F'.($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+2))->applyFromArray($alignmentAllCenter);
                $event->sheet->getColumnDimension('A')->setWidth(15);
                $event->sheet->getColumnDimension('B')->setWidth(57);
                $event->sheet->getColumnDimension('C')->setWidth(22);
                $event->sheet->getColumnDimension('D')->setWidth(22);
                $event->sheet->getColumnDimension('E')->setWidth(52);
                $event->sheet->getColumnDimension('F')->setWidth(55);
                $event->sheet->getDelegate()->getStyle('A'.($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+2).':F'.($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+2))->applyFromArray($fontStyleArialBold16);
                $event->sheet->getDelegate()->getStyle('A'.($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+2).':F'.($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+2))->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle('A'.($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+2).':F'.($countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+2))->applyFromArray($borderStyleAllThin);

                // Dynamic Values
                $totalCountForOutgoingMorningArray = [];
                $counterForOutgoingMorningStartWithZero = 0;
                $counterForOutgoingMorningStartWithRow = $countForOutgoingMorningActiveRoutes+$countForOutgoingMorningStaticRows+3;
                $alphabet = range("A","Z");
                for ($i = 0; $i < count($this->routesData); $i++) {
                    $temporaryTotalForEveryTimeSchedArray = ['routes_id'=>'','routes_name'=>'','incoming'=>'','outgoing'=>''];
                    // Alphabet(Column A)
                    $event->sheet->setCellValue('A'.$counterForOutgoingMorningStartWithRow, $alphabet[$counterForOutgoingMorningStartWithZero]);
                    $event->sheet->getDelegate()->getStyle('A'.$counterForOutgoingMorningStartWithRow.':A'.$counterForOutgoingMorningStartWithRow)->applyFromArray($alignmentAllCenter);
                    $event->sheet->getDelegate()->getStyle('A'.$counterForOutgoingMorningStartWithRow.':A'.$counterForOutgoingMorningStartWithRow)->applyFromArray($fontStyleArial12);

                    // Routes(Column B)
                    $event->sheet->setCellValue('B'.$counterForOutgoingMorningStartWithRow, $this->routesData[$i]->routes_name);
                    $event->sheet->getDelegate()->getStyle('B'.$counterForOutgoingMorningStartWithRow.':B'.$counterForOutgoingMorningStartWithRow)->getAlignment()->setWrapText(true);
                    $event->sheet->getDelegate()->getStyle('B'.$counterForOutgoingMorningStartWithRow.':B'.$counterForOutgoingMorningStartWithRow)->applyFromArray($fontStyleArial12);
                    $event->sheet->getDelegate()->getStyle('B'.$counterForOutgoingMorningStartWithRow.':B'.$counterForOutgoingMorningStartWithRow)->applyFromArray($alignmentAllCenter);

                    // Pickup Time(Column C)
                    $event->sheet->setCellValue('C'.$counterForOutgoingMorningStartWithRow, Carbon::parse($this->routesData[$i]->pickup_time_info->pickup_time)->format('h:i a'));
                    $event->sheet->getDelegate()->getStyle('C'.$counterForOutgoingMorningStartWithRow.':C'.$counterForOutgoingMorningStartWithRow)->applyFromArray($fontStyleArialBold16);
                    $event->sheet->getDelegate()->getStyle('C'.$counterForOutgoingMorningStartWithRow.':C'.$counterForOutgoingMorningStartWithRow)->applyFromArray($alignmentAllCenter);

                    for ($j = 0; $j < count($this->masterlistData); $j++) {
                        /**
                         * Check if routes(table) id is equal to masterlists(table) routes_id to collect all masterlistData
                         * to be used for computation of each routes.
                         * Check Ids for comparison
                         * - $event->sheet->setCellValue('C6', 'id '. $this->routesData[0]->id);
                         * - $event->sheet->setCellValue('C7', 'id '. $this->masterlistData[1]->routes_id);
                         */
                        if($this->routesData[$i]->id == $this->masterlistData[$j]->routes_id){
                            $temporaryTotalForEveryTimeSchedArray['routes_id']     = $this->routesData[$i]->id;
                            $temporaryTotalForEveryTimeSchedArray['routes_name']   = $this->routesData[$i]->routes_description;
                            $temporaryTotalForEveryTimeSchedArray['incoming']      = $this->masterlistData[$j]->masterlist_incoming;
                            $temporaryTotalForEveryTimeSchedArray['outgoing']      = $this->masterlistData[$j]->masterlist_outgoing;
                            $totalCountForOutgoingMorningArray[] = $temporaryTotalForEveryTimeSchedArray;
                        }
                    }

                    // dd($totalCountForOutgoingMorningArray);
                    $totalSumManpowerArray = ['total_sum'=> 0];
                    for ($l=0; $l < count($totalCountForOutgoingMorningArray); $l++) {
                        $temporaryTimeScheduleOfEachEmployee = $totalCountForOutgoingMorningArray[$l]['outgoing'];
                        if($this->routesData[$i]->id == $totalCountForOutgoingMorningArray[$l]['routes_id']){
                            if($temporaryTimeScheduleOfEachEmployee == "7:30AM"){
                                /**
                                 * Plan Manpower(Column F)
                                 */
                                $totalSumManpowerArray['total_sum']++;
                                $totalManpowerForOutgoingMorningArray['total']++;
                                $event->sheet->setCellValue('D'.$counterForOutgoingMorningStartWithRow, $totalSumManpowerArray['total_sum']);
                                $event->sheet->getDelegate()->getStyle('D'.$counterForOutgoingMorningStartWithRow)->applyFromArray($fontStyleArialBold20);
                                $event->sheet->getDelegate()->getStyle('D'.$counterForOutgoingMorningStartWithRow)->applyFromArray($alignmentAllCenter);
    
                                /**
                                 * Commented on 06-06-2023
                                 */
                                // Shuttle Provider(Column E)
                                // if($this->routesData[$i]->shuttle_provider_info->shuttle_provider_name == 'Euroworld'){
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForOutgoingMorningStartWithRow.':F'.$counterForOutgoingMorningStartWithRow)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('4F6228');
                                // }else if($this->routesData[$i]->shuttle_provider_info->shuttle_provider_name == 'Hero Autobot'){
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForOutgoingMorningStartWithRow.':F'.$counterForOutgoingMorningStartWithRow)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('1F497D');
                                // }else if($this->routesData[$i]->shuttle_provider_info->shuttle_provider_name == 'NDPSS'){
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForOutgoingMorningStartWithRow.':F'.$counterForOutgoingMorningStartWithRow)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('f79646');
                                // }else{
                                //     $event->sheet->getDelegate()->getStyle('E'.$counterForOutgoingMorningStartWithRow.':F'.$counterForOutgoingMorningStartWithRow)
                                //         ->getFont()
                                //         ->getColor()
                                //         ->setARGB('000000');
                                // }
                                $event->sheet->setCellValue('E'.$counterForOutgoingMorningStartWithRow, $this->routesData[$i]->shuttle_provider_info->shuttle_provider_name);
                                $event->sheet->getDelegate()->getStyle('E'.$counterForOutgoingMorningStartWithRow.':E'.$counterForOutgoingMorningStartWithRow)->applyFromArray($fontStyleArialBold14);
                                $event->sheet->getDelegate()->getStyle('E'.$counterForOutgoingMorningStartWithRow.':E'.$counterForOutgoingMorningStartWithRow)->applyFromArray($alignmentAllCenter);

                                /**
                                 * Shuttle Allocation
                                 */
                                // dd(ceil($totalSumManpowerArray['total_sum'] / $this->routesData[$i]->shuttle_provider_info->shuttle_provider_capacity));
                                $shuttleAllocationBuses = ceil($totalSumManpowerArray['total_sum'] / $this->routesData[$i]->shuttle_provider_info->shuttle_provider_capacity) . " Shuttle Bus";
                                $event->sheet->setCellValue('F'.$counterForOutgoingMorningStartWithRow, $shuttleAllocationBuses);
                                $event->sheet->getDelegate()->getStyle('F'.$counterForOutgoingMorningStartWithRow)->applyFromArray($fontStyleArialBold20);
                                $event->sheet->getDelegate()->getStyle('F'.$counterForOutgoingMorningStartWithRow)->applyFromArray($alignmentAllCenter);
                            }
                        }else{
                            /**
                             * Plan Manpower(Column F)
                             */
                            $event->sheet->setCellValue('D'.$counterForOutgoingMorningStartWithRow, $totalSumManpowerArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle('D'.$counterForOutgoingMorningStartWithRow)->applyFromArray($fontStyleArialBold20);
                            $event->sheet->getDelegate()->getStyle('D'.$counterForOutgoingMorningStartWithRow)->applyFromArray($alignmentAllCenter);

                            /**
                             * Shuttle Allocation(Column F)
                             */
                            $shuttleAllocationBuses = "No Shuttle Allocation";
                            $event->sheet->setCellValue('F'.$counterForOutgoingMorningStartWithRow, $shuttleAllocationBuses);
                            $event->sheet->getDelegate()->getStyle('F'.$counterForOutgoingMorningStartWithRow)->applyFromArray($fontStyleArialBold20);
                            $event->sheet->getDelegate()->getStyle('F'.$counterForOutgoingMorningStartWithRow)->applyFromArray($alignmentAllCenter);
                            /**
                             * Commented on 06-06-2023
                             */
                            // $event->sheet->getDelegate()->getStyle('F'.$counterForOutgoingMorningStartWithRow)
                            //     ->getFont()
                            //     ->getColor()
                            //     ->setARGB('FF0000');
                        }
                        
                    }
                    
                    $event->sheet->getDelegate()->getStyle('A'.$counterForOutgoingMorningStartWithRow.':F'.$counterForOutgoingMorningStartWithRow)->applyFromArray($borderStyleAllThin); // Border All
                    $counterForOutgoingMorningStartWithZero++;
                    $counterForOutgoingMorningStartWithRow++;
                }
                /**
                 * Grand Total
                 */
                $event->sheet->setCellValue('B'.$counterForOutgoingMorningStartWithRow, 'Grand Total');
                $event->sheet->getDelegate()->getStyle('B'.$counterForOutgoingMorningStartWithRow)->applyFromArray($fontStyleArialBold20);
                $event->sheet->getDelegate()->getStyle('B'.$counterForOutgoingMorningStartWithRow)->applyFromArray($alignmentAllCenter);
                $event->sheet->setCellValue('D'.$counterForOutgoingMorningStartWithRow, $totalManpowerForOutgoingMorningArray['total']);
                $event->sheet->getDelegate()->getStyle('D'.$counterForOutgoingMorningStartWithRow)->applyFromArray($fontStyleArialBold20);
                $event->sheet->getDelegate()->getStyle('D'.$counterForOutgoingMorningStartWithRow)->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('B'.$counterForOutgoingMorningStartWithRow.':D'.$counterForOutgoingMorningStartWithRow)->applyFromArray($borderStyleAllThin); // Border All
            },
        ];
    }

    }