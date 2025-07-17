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

class ExportOperations implements  FromView, WithTitle, WithEvents
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

    public function view(): View {
        return view('exports.export_operations', ['date' => $this->date, 'routesData' => $this->routesData]);
    }

    public function title(): string{
        return 'Operations';
    }


    public function registerEvents(): array {
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
        $fontStyleArialBold14 = array(
            'font' => array(
                'name'      =>  'Arial',
                'size'      =>  14,
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
                $fontStyleArialBold14,
                $fontStyleArialUnderline10,
                $fontStyleCalibri10
            ) {

                /**
                 * Static values
                 */
                $event->sheet->getDelegate()->getRowDimension('4')->setRowHeight(100);
                $event->sheet->getColumnDimension('A')->setWidth(15);
                $event->sheet->getColumnDimension('B')->setWidth(15);

                // Row 1
                $event->sheet->setCellValue('A1','Operation Group Shuttle Bus Allocation Tally Sheet:');
                $event->sheet->getDelegate()->getStyle('A1')->applyFromArray($fontStyleArialBold10);

                // Row 2
                $event->sheet->setCellValue('A2','as of');
                $event->sheet->setCellValue('B2', Carbon::parse($this->date)->addDays(1)->format('l, j F Y'));
                $event->sheet->getDelegate()->getStyle('B2:C2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFEA00');

                // Row 4
                $event->sheet->setCellValue('A4','INCOMING');
                $event->sheet->setCellValue('B4','OUTGOING');
                $event->sheet->getDelegate()->getStyle('A4:B4')->applyFromArray($fontStyleArialBold12);
                $event->sheet->getDelegate()->getStyle('A4:B4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFEA00'); // Yellow

                /**
                 * Dynamic Values
                 */
                // Row 3 and Row 4
                //  [0] => A [1] => B [2] => C [3] => D [4] => E [5] => F [6] => G [7] => H [8] => I [9] => J [10] => K [11] => L [12] => M [13] => N [14] => O [15] => P [16] => Q [17] => R [18] => S [19] => T [20] => U [21] => V [22] => W [23] => X [24] => Y [25] => Z
                $counterForRoutesDataStartWithZero = 0;
                $counterStartWithColumnC = 2;
                $alphabet = range("A","Z");
                for ($i = 0; $i < count($this->routesData); $i++) {
                    if($counterStartWithColumnC != 0 || $counterStartWithColumnC != 1){
                        // C3 to Up
                        $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'3', $alphabet[$counterForRoutesDataStartWithZero]); // Alphabet letters data Row 3
                        $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'3', $alphabet[$counterForRoutesDataStartWithZero])
                            ->applyFromArray($fontStyleArialBold12); // Alphabet letters font bold Row 3

                        // C4 to Up
                        $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'4', $this->routesData[$i]->routes_name);
                        $event->sheet->getColumnDimension($alphabet[$counterStartWithColumnC])->setWidth(18);
                        $counterForRoutesDataStartWithZero++;
                        $counterStartWithColumnC++;
                    }
                }
                $event->sheet->getDelegate()->getStyle('A4:'.$alphabet[$counterStartWithColumnC].'4')->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle('A4:'.$alphabet[$counterStartWithColumnC].'4')->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('A4:B4')->applyFromArray($borderStyleAllThin);
                $event->sheet->getDelegate()->getStyle('C3:'.$alphabet[$counterStartWithColumnC].'4')->applyFromArray($borderStyleAllThin);
                $event->sheet->getDelegate()->getStyle('C3:'.$alphabet[$counterStartWithColumnC].'4')->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('C3:'.$alphabet[$counterStartWithColumnC].'4')->applyFromArray($fontStyleArial8);

                /**
                 * Total
                 */
                $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'3','Total');
                $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'3','Total');
                $event->sheet->getColumnDimension($alphabet[$counterStartWithColumnC])->setWidth(18);
                $event->sheet->getDelegate()->mergeCells($alphabet[$counterStartWithColumnC].'3:'.$alphabet[$counterStartWithColumnC].'4');
                $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'3:'.$alphabet[$counterStartWithColumnC].'4')
                            ->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('008000');
                /**
                 * For TS
                 */
                // Static Values
                $event->sheet->setCellValue('A5','TS');
                $event->sheet->setCellValue('A6','7:30AM');
                $event->sheet->setCellValue('A7','7:30AM');
                $event->sheet->setCellValue('A8','7:30AM');
                $event->sheet->setCellValue('A9','7:30PM');
                $event->sheet->setCellValue('B6','3:30PM');
                $event->sheet->setCellValue('B7','4:30PM');
                $event->sheet->setCellValue('B8','7:30PM');
                $event->sheet->setCellValue('B9','7:30AM');
                // Dynamic values
                $event->sheet->getDelegate()->getStyle('A6:'.$alphabet[$counterForRoutesDataStartWithZero+2].'9')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('90EE90');
                $event->sheet->getDelegate()->getStyle('A6:'.$alphabet[$counterForRoutesDataStartWithZero+2].'9')->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('A6:'.$alphabet[$counterForRoutesDataStartWithZero+2].'9')->applyFromArray($borderStyleAllThin);
                $event->sheet->getDelegate()->getStyle('A6:'.$alphabet[$counterStartWithColumnC+2].'9')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                $event->sheet->getDelegate()->getStyle('A6:'.'B9')->applyFromArray($fontStyleArialBold10); // Time Schedules Font Arial 10
                $event->sheet->getDelegate()->getStyle('A5')->applyFromArray($fontStyleArialUnderline10); // Division

                /*
                 * For CN
                */
                // Static Values
                $event->sheet->setCellValue('A12','CN');
                $event->sheet->setCellValue('A13','7:30AM');
                $event->sheet->setCellValue('A14','7:30AM');
                $event->sheet->setCellValue('A15','7:30AM');
                $event->sheet->setCellValue('A16','7:30PM');
                $event->sheet->setCellValue('B13','3:30PM');
                $event->sheet->setCellValue('B14','4:30PM');
                $event->sheet->setCellValue('B15','7:30PM');
                $event->sheet->setCellValue('B16','7:30AM');
                // Dynamic Values
                $event->sheet->getDelegate()->getStyle('A13:'.$alphabet[$counterForRoutesDataStartWithZero+2].'16')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('90EE90');
                $event->sheet->getDelegate()->getStyle('A13:'.$alphabet[$counterForRoutesDataStartWithZero+2].'16')->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('A13:'.$alphabet[$counterForRoutesDataStartWithZero+2].'16')->applyFromArray($borderStyleAllThin);
                $event->sheet->getDelegate()->getStyle('A13:'.$alphabet[$counterStartWithColumnC+2].'16')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                $event->sheet->getDelegate()->getStyle('A13:'.'B16')->applyFromArray($fontStyleArialBold10); // Time Schedules Font Arial 10
                $event->sheet->getDelegate()->getStyle('A12')->applyFromArray($fontStyleArialUnderline10); // Division

                /**
                 * For PPS
                 */
                // Static Values
                $event->sheet->setCellValue('A19','PPS');
                $event->sheet->setCellValue('A20','7:30AM');
                $event->sheet->setCellValue('A21','7:30AM');
                $event->sheet->setCellValue('A22','7:30AM');
                $event->sheet->setCellValue('A23','7:30PM');
                $event->sheet->setCellValue('B20','3:30PM');
                $event->sheet->setCellValue('B21','4:30PM');
                $event->sheet->setCellValue('B22','7:30PM');
                $event->sheet->setCellValue('B23','7:30AM');
                // Dynamic Values
                $event->sheet->getDelegate()->getStyle('A20:'.$alphabet[$counterForRoutesDataStartWithZero+2].'23')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('90EE90');
                $event->sheet->getDelegate()->getStyle('A20:'.$alphabet[$counterForRoutesDataStartWithZero+2].'23')->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('A20:'.$alphabet[$counterForRoutesDataStartWithZero+2].'23')->applyFromArray($borderStyleAllThin);
                $event->sheet->getDelegate()->getStyle('A20:'.$alphabet[$counterStartWithColumnC+2].'23')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                $event->sheet->getDelegate()->getStyle('A20:'.'B23')->applyFromArray($fontStyleArialBold10); // Time Schedules Font Arial 10
                $event->sheet->getDelegate()->getStyle('A19')->applyFromArray($fontStyleArialUnderline10); // Division

                /**
                 * For YF
                 */
                // Static Values
                $event->sheet->setCellValue('A26','YF');
                $event->sheet->setCellValue('A27','7:30AM');
                $event->sheet->setCellValue('A28','7:30AM');
                $event->sheet->setCellValue('A29','7:30AM');
                $event->sheet->setCellValue('A30','7:30PM');
                $event->sheet->setCellValue('B27','3:30PM');
                $event->sheet->setCellValue('B28','4:30PM');
                $event->sheet->setCellValue('B29','7:30PM');
                $event->sheet->setCellValue('B30','7:30AM');
                // Dynamic Values
                $event->sheet->getDelegate()->getStyle('A27:'.$alphabet[$counterForRoutesDataStartWithZero+2].'30')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('90EE90');
                $event->sheet->getDelegate()->getStyle('A27:'.$alphabet[$counterForRoutesDataStartWithZero+2].'30')->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('A27:'.$alphabet[$counterForRoutesDataStartWithZero+2].'30')->applyFromArray($borderStyleAllThin);
                $event->sheet->getDelegate()->getStyle('A27:'.$alphabet[$counterStartWithColumnC+2].'30')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                $event->sheet->getDelegate()->getStyle('A27:'.'B30')->applyFromArray($fontStyleArialBold10); // Time Schedules Font Arial 10
                $event->sheet->getDelegate()->getStyle('A26')->applyFromArray($fontStyleArialUnderline10); // Division

                /**
                 * Looping for computation of "Operations"
                 */
                $totalCountForAllRoutesArray = [];
                $counterOfAllDivisionStartWithColumnC = 2;
                // dd($this->masterlistData);
                for ($i = 0; $i < count($this->routesData); $i++) {
                    $temporaryTotalForEveryRoutesArray = ['division'=>'','routes_id'=>'','routes_name'=>'','incoming'=>'','outgoing'=>''];
                    for ($j = 0; $j < count($this->masterlistData); $j++) {
                            /**
                             * Check if routes(table) id is equal to masterlists(table) routes_id to collect all masterlistData
                             * to be used for computation of each routes.
                             * Check Ids for comparison
                             * - $event->sheet->setCellValue('C6', 'id '. $this->routesData[0]->id);
                             * - $event->sheet->setCellValue('C7', 'id '. $this->masterlistData[1]->routes_id);
                             */
                            if($this->routesData[$i]->id == $this->masterlistData[$j]->routes_id){
                                if($this->masterlistData[$j]->masterlist_employee_type == 1){ // 1-Pricon
                                    $temporaryTotalForEveryRoutesArray['division']      = $this->masterlistData[$j]->hris_info->division_info->Division;
                                }else{ // 2-Subcon
                                    $temporaryTotalForEveryRoutesArray['division']      = $this->masterlistData[$j]->subcon_info->division_info->Division;
                                }
                                // dd($this->masterlistData[$j]->subcon_info);
                                $temporaryTotalForEveryRoutesArray['routes_id']     = $this->routesData[$i]->id;
                                $temporaryTotalForEveryRoutesArray['routes_name']   = $this->routesData[$i]->routes_description;
                                $temporaryTotalForEveryRoutesArray['incoming']      = $this->masterlistData[$j]->masterlist_incoming;
                                $temporaryTotalForEveryRoutesArray['outgoing']      = $this->masterlistData[$j]->masterlist_outgoing;
                                $totalCountForAllRoutesArray[] = $temporaryTotalForEveryRoutesArray;
                            }
                    }
                    // dd($temporaryTotalForEveryRoutesArray);

                    /**
                     * First I check the division to separate it in "Operations" in excel
                     * then I check the $routesData(this will hold the "id" in routes table then loop it to be use in matching)
                     * then I will check if match based on the $totalCountForAllRoutesArray(this array will hold key value pair named "routes_id")
                     * Check Ids for comparison to compare
                     * - $this->routesData[$i]->id
                     * - $totalCountForAllRoutesArray[$l]['routes_id']
                     * - dd($totalCountForAllRoutesArray);
                     */
                    $timeSchedulesTSArray = [
                        ['time'=>'7:30AM-3:30PM','count'=> 0, 'row'=>'6', 'total_count'=> 0],
                        ['time'=>'7:30AM-4:30PM','count'=> 0, 'row'=>'7', 'total_count'=> 0],
                        ['time'=>'7:30AM-7:30PM','count'=> 0, 'row'=>'8', 'total_count'=> 0],
                        ['time'=>'7:30PM-7:30AM','count'=> 0, 'row'=>'9', 'total_count'=> 0],
                    ];
                    $totalSumTSArray = ['total_sum'=> 0];

                    $timeSchedulesCNArray = [
                        ['time'=>'7:30AM-3:30PM','count'=> 0, 'row'=>'13', 'total_count'=> 0],
                        ['time'=>'7:30AM-4:30PM','count'=> 0, 'row'=>'14', 'total_count'=> 0],
                        ['time'=>'7:30AM-7:30PM','count'=> 0, 'row'=>'15', 'total_count'=> 0],
                        ['time'=>'7:30PM-7:30AM','count'=> 0, 'row'=>'16', 'total_count'=> 0],
                    ];
                    $totalSumCNArray = ['total_sum'=> 0];

                    $timeSchedulesPPSArray = [
                        ['time'=>'7:30AM-3:30PM','count'=> 0, 'row'=>'20', 'total_count'=> 0],
                        ['time'=>'7:30AM-4:30PM','count'=> 0, 'row'=>'21', 'total_count'=> 0],
                        ['time'=>'7:30AM-7:30PM','count'=> 0, 'row'=>'22', 'total_count'=> 0],
                        ['time'=>'7:30PM-7:30AM','count'=> 0, 'row'=>'23', 'total_count'=> 0],
                    ];
                    $totalSumPPSArray = ['total_sum'=> 0];

                    $timeSchedulesYFArray = [
                        ['time'=>'7:30AM-3:30PM','count'=> 0, 'row'=>'27', 'total_count'=> 0],
                        ['time'=>'7:30AM-4:30PM','count'=> 0, 'row'=>'28', 'total_count'=> 0],
                        ['time'=>'7:30AM-7:30PM','count'=> 0, 'row'=>'29', 'total_count'=> 0],
                        ['time'=>'7:30PM-7:30AM','count'=> 0, 'row'=>'30', 'total_count'=> 0],
                    ];
                    $totalSumYFArray = ['total_sum'=> 0];

                    // dd($totalCountForAllRoutesArray);
                    for ($l=0; $l < count($totalCountForAllRoutesArray); $l++) {
                        if($totalCountForAllRoutesArray[$l]['division'] == "TS"){
                            $temporaryTimeScheduleOfEachRoutesTS = $totalCountForAllRoutesArray[$l]['incoming']."-".$totalCountForAllRoutesArray[$l]['outgoing'];
                            // 7:30am-7:30pm
                            // if($this->routesData[$i]->id == $totalCountForAllRoutesArray[$l]['routes_id']){ // check the routes added in the $totalCountForAllRoutesArray to know which column to be set value
                            //     // dd($temporaryTimeScheduleOfEachRoutesTS);
                            //     for ($m=0; $m < count($timeSchedulesTSArray); $m++) {
                            //         if($temporaryTimeScheduleOfEachRoutesTS == $timeSchedulesTSArray[$m]['time']){
                            //             $timeSchedulesTSArray[$m]['count']++;
                            //             $event->sheet->setCellValue($alphabet[$counterOfAllDivisionStartWithColumnC].$timeSchedulesTSArray[$m]['row'], $timeSchedulesTSArray[$m]['count']);
                            //         }
                            //     }
                            // }
                            for ($m=0; $m < count($timeSchedulesTSArray); $m++) {
                                /**
                                 * Total count computation for each routes & time schedule for TS only
                                 */
                                if($this->routesData[$i]->id == $totalCountForAllRoutesArray[$l]['routes_id']){ // check the routes added in the $totalCountForAllRoutesArray to know which column to be set value
                                    if($temporaryTimeScheduleOfEachRoutesTS == $timeSchedulesTSArray[$m]['time']){
                                        $timeSchedulesTSArray[$m]['count']++;
                                        $event->sheet->setCellValue($alphabet[$counterOfAllDivisionStartWithColumnC].$timeSchedulesTSArray[$m]['row'], $timeSchedulesTSArray[$m]['count']);
                                    }
                                }

                                /**
                                 * Total sum computation for each time schedule for TS only
                                 */
                                if($temporaryTimeScheduleOfEachRoutesTS == $timeSchedulesTSArray[$m]['time']){
                                    $timeSchedulesTSArray[$m]['total_count']++;
                                    $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].$timeSchedulesTSArray[$m]['row'], $timeSchedulesTSArray[$m]['total_count']);
                                }
                            }

                            /**
                             * Total sum computation for TS only
                             */
                            $totalSumTSArray['total_sum']++;
                            $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'10', $totalSumTSArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'10')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'10')->applyFromArray($alignmentAllCenter);
                        }
                        if($totalCountForAllRoutesArray[$l]['division'] == "CN"){
                            $temporaryTimeScheduleOfEachRoutesCN = $totalCountForAllRoutesArray[$l]['incoming']."-".$totalCountForAllRoutesArray[$l]['outgoing'];
                            // if($this->routesData[$i]->id == $totalCountForAllRoutesArray[$l]['routes_id']){ // check the routes added in the $totalCountForAllRoutesArray to know which column to be set value
                            //     // dd($temporaryTimeScheduleOfEachRoutesCN);
                            //     for ($m=0; $m < count($timeSchedulesCNArray); $m++) {
                            //         if($temporaryTimeScheduleOfEachRoutesCN == $timeSchedulesCNArray[$m]['time']){
                            //             $timeSchedulesCNArray[$m]['count']++;
                            //             $event->sheet->setCellValue($alphabet[$counterOfAllDivisionStartWithColumnC].$timeSchedulesCNArray[$m]['row'], $timeSchedulesCNArray[$m]['count']);
                            //         }
                            //     }
                            // }
                            for ($m=0; $m < count($timeSchedulesCNArray); $m++) {
                                /**
                                 * Total count computation for each routes & time schedule for CN only
                                 */
                                if($this->routesData[$i]->id == $totalCountForAllRoutesArray[$l]['routes_id']){ // check the routes added in the $totalCountForAllRoutesArray to know which column to be set value
                                    if($temporaryTimeScheduleOfEachRoutesCN == $timeSchedulesCNArray[$m]['time']){
                                        $timeSchedulesCNArray[$m]['count']++;
                                        $event->sheet->setCellValue($alphabet[$counterOfAllDivisionStartWithColumnC].$timeSchedulesCNArray[$m]['row'], $timeSchedulesCNArray[$m]['count']);
                                    }
                                }

                                /**
                                 * Total sum computation for each time schedule for CN only
                                 */
                                if($temporaryTimeScheduleOfEachRoutesCN == $timeSchedulesCNArray[$m]['time']){
                                    $timeSchedulesCNArray[$m]['total_count']++;
                                    $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].$timeSchedulesCNArray[$m]['row'], $timeSchedulesCNArray[$m]['total_count']);
                                }
                            }

                            /**
                             * Total sum computation for CN only
                             */
                            $totalSumCNArray['total_sum']++;
                            $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'17', $totalSumCNArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'17')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'17')->applyFromArray($alignmentAllCenter);
                        }
                        if($totalCountForAllRoutesArray[$l]['division'] == "PPS"){
                            $temporaryTimeScheduleOfEachRoutesPPS = $totalCountForAllRoutesArray[$l]['incoming']."-".$totalCountForAllRoutesArray[$l]['outgoing'];
                            for ($m=0; $m < count($timeSchedulesPPSArray); $m++) {
                                /**
                                 * Total count computation for each routes & time schedule for PPS only
                                 */
                                if($this->routesData[$i]->id == $totalCountForAllRoutesArray[$l]['routes_id']){ // check the routes added in the $totalCountForAllRoutesArray to know which column to be set value
                                    if($temporaryTimeScheduleOfEachRoutesPPS == $timeSchedulesPPSArray[$m]['time']){
                                        $timeSchedulesPPSArray[$m]['count']++;
                                        $event->sheet->setCellValue($alphabet[$counterOfAllDivisionStartWithColumnC].$timeSchedulesPPSArray[$m]['row'], $timeSchedulesPPSArray[$m]['count']);
                                    }
                                }

                                /**
                                 * Total sum computation for each time schedule for PPS only
                                 */
                                if($temporaryTimeScheduleOfEachRoutesPPS == $timeSchedulesPPSArray[$m]['time']){
                                    $timeSchedulesPPSArray[$m]['total_count']++;
                                    $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].$timeSchedulesPPSArray[$m]['row'], $timeSchedulesPPSArray[$m]['total_count']);
                                }
                            }

                            /**
                             * Total sum computation for PPS only
                             */
                            $totalSumPPSArray['total_sum']++;
                            $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'24', $totalSumPPSArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'24')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'24')->applyFromArray($alignmentAllCenter);
                        }
                        if($totalCountForAllRoutesArray[$l]['division'] == "YF"){
                            $temporaryTimeScheduleOfEachRoutesYF = $totalCountForAllRoutesArray[$l]['incoming']."-".$totalCountForAllRoutesArray[$l]['outgoing'];
                            for ($m=0; $m < count($timeSchedulesYFArray); $m++) {
                                /**
                                 * Total count computation for each routes & time schedule for CN only
                                 */
                                if($this->routesData[$i]->id == $totalCountForAllRoutesArray[$l]['routes_id']){ // check the routes added in the $totalCountForAllRoutesArray to know which column to be set value
                                    if($temporaryTimeScheduleOfEachRoutesYF == $timeSchedulesYFArray[$m]['time']){
                                        $timeSchedulesYFArray[$m]['count']++;
                                        $event->sheet->setCellValue($alphabet[$counterOfAllDivisionStartWithColumnC].$timeSchedulesYFArray[$m]['row'], $timeSchedulesYFArray[$m]['count']);
                                    }
                                }

                                /**
                                 * Total sum computation for each time schedule for CN only
                                 */
                                if($temporaryTimeScheduleOfEachRoutesYF == $timeSchedulesYFArray[$m]['time']){
                                    $timeSchedulesYFArray[$m]['total_count']++;
                                    $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].$timeSchedulesYFArray[$m]['row'], $timeSchedulesYFArray[$m]['total_count']);
                                }
                            }

                            /**
                             * Total sum computation for YF only
                             */
                            $totalSumYFArray['total_sum']++;
                            $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'31', $totalSumYFArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'31')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'31')->applyFromArray($alignmentAllCenter);
                        }
                    }
                    $counterOfAllDivisionStartWithColumnC++;
                }
                // dd($timeSchedulesTSArray);
                // dd($timeSchedulesCNArray);
            },
        ];
    }

    }


