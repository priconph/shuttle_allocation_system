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

class ExportSupportGroup implements  FromView, WithTitle, WithEvents{
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
        return view('exports.export_support_group', ['date' => $this->date]);
    }

    public function title(): string{
        return 'Support Group';
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
                $fontStyleArialBold14,
                $fontStyleArialUnderline10,
                $fontStyleArialUnderline12,
                $fontStyleCalibri10
            ) {
                /**
                 * Static values
                 */
                $event->sheet->getDelegate()->getRowDimension('4')->setRowHeight(100);
                $event->sheet->getColumnDimension('A')->setWidth(15);
                $event->sheet->getColumnDimension('B')->setWidth(15);

                // Row 1
                $event->sheet->setCellValue('A1','Support Group Shuttle Bus Allocation Tally Sheet:');
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
                        $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'3', $alphabet[$counterForRoutesDataStartWithZero]);
                        $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'3', $alphabet[$counterForRoutesDataStartWithZero])
                            ->applyFromArray($fontStyleArialBold12);

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
                 * HRD
                 * For Row 5 to 9
                 */
                $event->sheet->setCellValue('A5','HRD');
                $event->sheet->setCellValue('A6','7:30AM');
                $event->sheet->setCellValue('A7','7:30AM');
                $event->sheet->setCellValue('A8','7:30AM');
                $event->sheet->setCellValue('A9','7:30PM');
                $event->sheet->setCellValue('B6','3:30PM');
                $event->sheet->setCellValue('B7','4:30PM');
                $event->sheet->setCellValue('B8','7:30PM');
                $event->sheet->setCellValue('B9','7:30AM');
                $event->sheet->getDelegate()->getStyle('A6:'.$alphabet[$counterForRoutesDataStartWithZero+2].'9')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('90EE90'); // Highlight in green color
                $event->sheet->getDelegate()->getStyle('A5')->applyFromArray($fontStyleArialUnderline12); // Division
                $event->sheet->getDelegate()->getStyle('A6:'.$alphabet[$counterForRoutesDataStartWithZero+2].'9')->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('A6:'.$alphabet[$counterForRoutesDataStartWithZero+2].'9')->applyFromArray($borderStyleAllThin);
                $event->sheet->getDelegate()->getStyle('A6:'.$alphabet[$counterStartWithColumnC+2].'9')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                $event->sheet->getDelegate()->getStyle('A6:'.'B9')->applyFromArray($fontStyleArialBold10); // Time Schedules Font Arial 10

                /**
                 * Logistics
                 * For Row 12 to 16
                 */
                $event->sheet->setCellValue('A12','Logistics');
                $event->sheet->setCellValue('A13','7:30AM');
                $event->sheet->setCellValue('A14','7:30AM');
                $event->sheet->setCellValue('A15','7:30AM');
                $event->sheet->setCellValue('A16','7:30PM');
                $event->sheet->setCellValue('B13','3:30PM');
                $event->sheet->setCellValue('B14','4:30PM');
                $event->sheet->setCellValue('B15','7:30PM');
                $event->sheet->setCellValue('B16','7:30AM');
                $event->sheet->getDelegate()->getStyle('A13:'.$alphabet[$counterForRoutesDataStartWithZero+2].'16')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('90EE90');
                $event->sheet->getDelegate()->getStyle('A12')->applyFromArray($fontStyleArialUnderline12); // Division
                $event->sheet->getDelegate()->getStyle('A13:'.$alphabet[$counterForRoutesDataStartWithZero+2].'16')->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('A13:'.$alphabet[$counterForRoutesDataStartWithZero+2].'16')->applyFromArray($borderStyleAllThin);
                $event->sheet->getDelegate()->getStyle('A13:'.$alphabet[$counterStartWithColumnC+2].'16')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                $event->sheet->getDelegate()->getStyle('A13:'.'B16')->applyFromArray($fontStyleArialBold10); // Time Schedules Font Arial 10

                /**
                 * Facility
                 * For Row 19 to 23
                 */
                $event->sheet->setCellValue('A19','Facility');
                $event->sheet->setCellValue('A20','7:30AM');
                $event->sheet->setCellValue('A21','7:30AM');
                $event->sheet->setCellValue('A22','7:30AM');
                $event->sheet->setCellValue('A23','7:30PM');
                $event->sheet->setCellValue('B20','3:30PM');
                $event->sheet->setCellValue('B21','4:30PM');
                $event->sheet->setCellValue('B22','7:30PM');
                $event->sheet->setCellValue('B23','7:30AM');
                $event->sheet->getDelegate()->getStyle('A20:'.$alphabet[$counterForRoutesDataStartWithZero+2].'23')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('90EE90');
                $event->sheet->getDelegate()->getStyle('A19')->applyFromArray($fontStyleArialUnderline12); // Division
                $event->sheet->getDelegate()->getStyle('A20:'.$alphabet[$counterForRoutesDataStartWithZero+2].'23')->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('A20:'.$alphabet[$counterForRoutesDataStartWithZero+2].'23')->applyFromArray($borderStyleAllThin);
                $event->sheet->getDelegate()->getStyle('A20:'.$alphabet[$counterStartWithColumnC+2].'23')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                $event->sheet->getDelegate()->getStyle('A20:'.'B23')->applyFromArray($fontStyleArialBold10); // Time Schedules Font Arial 10

                /**
                 * Subcon
                 * For Row 26 to 30
                 */
                $event->sheet->setCellValue('A26','Subcon');
                $event->sheet->setCellValue('A27','7:30AM');
                $event->sheet->setCellValue('A28','7:30AM');
                $event->sheet->setCellValue('A29','7:30AM');
                $event->sheet->setCellValue('A30','7:30PM');
                $event->sheet->setCellValue('B27','3:30PM');
                $event->sheet->setCellValue('B28','4:30PM');
                $event->sheet->setCellValue('B29','7:30PM');
                $event->sheet->setCellValue('B30','7:30AM');
                $event->sheet->getDelegate()->getStyle('A27:'.$alphabet[$counterForRoutesDataStartWithZero+2].'30')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('90EE90');
                $event->sheet->getDelegate()->getStyle('A26')->applyFromArray($fontStyleArialUnderline12); // Division
                $event->sheet->getDelegate()->getStyle('A27:'.$alphabet[$counterForRoutesDataStartWithZero+2].'30')->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('A27:'.$alphabet[$counterForRoutesDataStartWithZero+2].'30')->applyFromArray($borderStyleAllThin);
                $event->sheet->getDelegate()->getStyle('A27:'.$alphabet[$counterStartWithColumnC+2].'30')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                $event->sheet->getDelegate()->getStyle('A27:'.'B30')->applyFromArray($fontStyleArialBold10); // Time Schedules Font Arial 10

                /**
                 * Finance
                 * For Row 33 to 37
                 */
                $event->sheet->setCellValue('A33','Finance');
                $event->sheet->setCellValue('A34','7:30AM');
                $event->sheet->setCellValue('A35','7:30AM');
                $event->sheet->setCellValue('A36','7:30AM');
                $event->sheet->setCellValue('A37','7:30PM');
                $event->sheet->setCellValue('B34','3:30PM');
                $event->sheet->setCellValue('B35','4:30PM');
                $event->sheet->setCellValue('B36','7:30PM');
                $event->sheet->setCellValue('B37','7:30AM');
                $event->sheet->getDelegate()->getStyle('A34:'.$alphabet[$counterForRoutesDataStartWithZero+2].'37')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('90EE90');
                $event->sheet->getDelegate()->getStyle('A33')->applyFromArray($fontStyleArialUnderline12); // Division
                $event->sheet->getDelegate()->getStyle('A34:'.$alphabet[$counterForRoutesDataStartWithZero+2].'37')->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('A34:'.$alphabet[$counterForRoutesDataStartWithZero+2].'37')->applyFromArray($borderStyleAllThin);
                $event->sheet->getDelegate()->getStyle('A34:'.$alphabet[$counterStartWithColumnC+2].'37')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                $event->sheet->getDelegate()->getStyle('A34:'.'B37')->applyFromArray($fontStyleArialBold10); // Time Schedules Font Arial 10

                /**
                 * Secretariat
                 * For Row 40 to 44
                 */
                $event->sheet->setCellValue('A40','Secretariat');
                $event->sheet->setCellValue('A41','7:30AM');
                $event->sheet->setCellValue('A42','7:30AM');
                $event->sheet->setCellValue('A43','7:30AM');
                $event->sheet->setCellValue('A44','7:30PM');
                $event->sheet->setCellValue('B41','3:30PM');
                $event->sheet->setCellValue('B42','4:30PM');
                $event->sheet->setCellValue('B43','7:30PM');
                $event->sheet->setCellValue('B44','7:30AM');
                $event->sheet->getDelegate()->getStyle('A41:'.$alphabet[$counterForRoutesDataStartWithZero+2].'44')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('90EE90');
                $event->sheet->getDelegate()->getStyle('A40')->applyFromArray($fontStyleArialUnderline12); // Division
                $event->sheet->getDelegate()->getStyle('A41:'.$alphabet[$counterForRoutesDataStartWithZero+2].'44')->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('A41:'.$alphabet[$counterForRoutesDataStartWithZero+2].'44')->applyFromArray($borderStyleAllThin);
                $event->sheet->getDelegate()->getStyle('A41:'.$alphabet[$counterStartWithColumnC+2].'44')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                $event->sheet->getDelegate()->getStyle('A41:'.'B44')->applyFromArray($fontStyleArialBold10); // Time Schedules Font Arial 10

                /**
                 * IAS
                 * For Row 47 to 51
                 */
                $event->sheet->setCellValue('A47','IAS');
                $event->sheet->setCellValue('A48','7:30AM');
                $event->sheet->setCellValue('A49','7:30AM');
                $event->sheet->setCellValue('A50','7:30AM');
                $event->sheet->setCellValue('A51','7:30PM');
                $event->sheet->setCellValue('B48','3:30PM');
                $event->sheet->setCellValue('B49','4:30PM');
                $event->sheet->setCellValue('B50','7:30PM');
                $event->sheet->setCellValue('B51','7:30AM');
                $event->sheet->getDelegate()->getStyle('A48:'.$alphabet[$counterForRoutesDataStartWithZero+2].'51')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('90EE90');
                $event->sheet->getDelegate()->getStyle('A47')->applyFromArray($fontStyleArialUnderline12); // Division
                $event->sheet->getDelegate()->getStyle('A48:'.$alphabet[$counterForRoutesDataStartWithZero+2].'51')->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('A48:'.$alphabet[$counterForRoutesDataStartWithZero+2].'51')->applyFromArray($borderStyleAllThin);
                $event->sheet->getDelegate()->getStyle('A48:'.$alphabet[$counterStartWithColumnC+2].'51')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                $event->sheet->getDelegate()->getStyle('A48:'.'B51')->applyFromArray($fontStyleArialBold10); // Time Schedules Font Arial 10

                /**
                 * QAD
                 * For Row 54 to 58
                 */
                $event->sheet->setCellValue('A54','QAD');
                $event->sheet->setCellValue('A55','7:30AM');
                $event->sheet->setCellValue('A56','7:30AM');
                $event->sheet->setCellValue('A57','7:30AM');
                $event->sheet->setCellValue('A58','7:30PM');
                $event->sheet->setCellValue('B55','3:30PM');
                $event->sheet->setCellValue('B56','4:30PM');
                $event->sheet->setCellValue('B57','7:30PM');
                $event->sheet->setCellValue('B58','7:30AM');
                $event->sheet->getDelegate()->getStyle('A55:'.$alphabet[$counterForRoutesDataStartWithZero+2].'58')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('90EE90');
                $event->sheet->getDelegate()->getStyle('A54')->applyFromArray($fontStyleArialUnderline12); // Division
                $event->sheet->getDelegate()->getStyle('A55:'.$alphabet[$counterForRoutesDataStartWithZero+2].'58')->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('A55:'.$alphabet[$counterForRoutesDataStartWithZero+2].'58')->applyFromArray($borderStyleAllThin);
                $event->sheet->getDelegate()->getStyle('A55:'.$alphabet[$counterStartWithColumnC+2].'58')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                $event->sheet->getDelegate()->getStyle('A55:'.'B58')->applyFromArray($fontStyleArialBold10); // Time Schedules Font Arial 10

                /**
                 * ISS
                 * For Row 61 to 65
                 */
                $event->sheet->setCellValue('A61','ISS');
                $event->sheet->setCellValue('A62','7:30AM');
                $event->sheet->setCellValue('A63','7:30AM');
                $event->sheet->setCellValue('A64','7:30AM');
                $event->sheet->setCellValue('A65','7:30PM');
                $event->sheet->setCellValue('B62','3:30PM');
                $event->sheet->setCellValue('B63','4:30PM');
                $event->sheet->setCellValue('B64','7:30PM');
                $event->sheet->setCellValue('B65','7:30AM');
                $event->sheet->getDelegate()->getStyle('A62:'.$alphabet[$counterForRoutesDataStartWithZero+2].'65')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('90EE90');
                $event->sheet->getDelegate()->getStyle('A61')->applyFromArray($fontStyleArialUnderline12); // Division
                $event->sheet->getDelegate()->getStyle('A62:'.$alphabet[$counterForRoutesDataStartWithZero+2].'65')->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('A62:'.$alphabet[$counterForRoutesDataStartWithZero+2].'65')->applyFromArray($borderStyleAllThin);
                $event->sheet->getDelegate()->getStyle('A62:'.$alphabet[$counterStartWithColumnC+2].'65')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                $event->sheet->getDelegate()->getStyle('A62:'.'B65')->applyFromArray($fontStyleArialBold10); // Time Schedules Font Arial 10

                /**
                 * PPC
                 * For Row 68 to 72
                 */
                $event->sheet->setCellValue('A68','PPC');
                $event->sheet->setCellValue('A69','7:30AM');
                $event->sheet->setCellValue('A70','7:30AM');
                $event->sheet->setCellValue('A71','7:30AM');
                $event->sheet->setCellValue('A72','7:30PM');
                $event->sheet->setCellValue('B69','3:30PM');
                $event->sheet->setCellValue('B70','4:30PM');
                $event->sheet->setCellValue('B71','7:30PM');
                $event->sheet->setCellValue('B72','7:30AM');
                $event->sheet->getDelegate()->getStyle('A69:'.$alphabet[$counterForRoutesDataStartWithZero+2].'72')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('90EE90');
                $event->sheet->getDelegate()->getStyle('A68')->applyFromArray($fontStyleArialUnderline12); // Division
                $event->sheet->getDelegate()->getStyle('A69:'.$alphabet[$counterForRoutesDataStartWithZero+2].'72')->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('A69:'.$alphabet[$counterForRoutesDataStartWithZero+2].'72')->applyFromArray($borderStyleAllThin);
                $event->sheet->getDelegate()->getStyle('A69:'.$alphabet[$counterStartWithColumnC+2].'72')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                $event->sheet->getDelegate()->getStyle('A69:'.'B72')->applyFromArray($fontStyleArialBold10); // Time Schedules Font Arial 10

                /**
                 * Design
                 * For Row 75 to 79
                 */
                $event->sheet->setCellValue('A75','Design');
                $event->sheet->setCellValue('A76','7:30AM');
                $event->sheet->setCellValue('A77','7:30AM');
                $event->sheet->setCellValue('A78','7:30AM');
                $event->sheet->setCellValue('A79','7:30PM');
                $event->sheet->setCellValue('B76','3:30PM');
                $event->sheet->setCellValue('B77','4:30PM');
                $event->sheet->setCellValue('B78','7:30PM');
                $event->sheet->setCellValue('B79','7:30AM');
                $event->sheet->getDelegate()->getStyle('A76:'.$alphabet[$counterForRoutesDataStartWithZero+2].'79')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('90EE90');
                $event->sheet->getDelegate()->getStyle('A75')->applyFromArray($fontStyleArialUnderline12); // Division
                $event->sheet->getDelegate()->getStyle('A76:'.$alphabet[$counterForRoutesDataStartWithZero+2].'79')->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('A76:'.$alphabet[$counterForRoutesDataStartWithZero+2].'79')->applyFromArray($borderStyleAllThin);
                $event->sheet->getDelegate()->getStyle('A76:'.$alphabet[$counterStartWithColumnC+2].'79')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                $event->sheet->getDelegate()->getStyle('A76:'.'B79')->applyFromArray($fontStyleArialBold10); // Time Schedules Font Arial 10

                /**
                 * EMS
                 * For Row 82 to 86
                 */
                $event->sheet->setCellValue('A82','EMS');
                $event->sheet->setCellValue('A83','7:30AM');
                $event->sheet->setCellValue('A84','7:30AM');
                $event->sheet->setCellValue('A85','7:30AM');
                $event->sheet->setCellValue('A86','7:30PM');
                $event->sheet->setCellValue('B83','3:30PM');
                $event->sheet->setCellValue('B84','4:30PM');
                $event->sheet->setCellValue('B85','7:30PM');
                $event->sheet->setCellValue('B86','7:30AM');
                $event->sheet->getDelegate()->getStyle('A83:'.$alphabet[$counterForRoutesDataStartWithZero+2].'86')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('90EE90');
                $event->sheet->getDelegate()->getStyle('A82')->applyFromArray($fontStyleArialUnderline12); // Division
                $event->sheet->getDelegate()->getStyle('A83:'.$alphabet[$counterForRoutesDataStartWithZero+2].'86')->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('A83:'.$alphabet[$counterForRoutesDataStartWithZero+2].'86')->applyFromArray($borderStyleAllThin);
                $event->sheet->getDelegate()->getStyle('A83:'.$alphabet[$counterStartWithColumnC+2].'86')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                $event->sheet->getDelegate()->getStyle('A83:'.'B86')->applyFromArray($fontStyleArialBold10); // Time Schedules Font Arial 10

                /**
                 * ESS
                 * For Row 89 to 93
                 */
                $event->sheet->setCellValue('A89','ESS');
                $event->sheet->setCellValue('A90','7:30AM');
                $event->sheet->setCellValue('A91','7:30AM');
                $event->sheet->setCellValue('A92','7:30AM');
                $event->sheet->setCellValue('A93','7:30PM');
                $event->sheet->setCellValue('B90','3:30PM');
                $event->sheet->setCellValue('B91','4:30PM');
                $event->sheet->setCellValue('B92','7:30PM');
                $event->sheet->setCellValue('B93','7:30AM');
                $event->sheet->getDelegate()->getStyle('A90:'.$alphabet[$counterForRoutesDataStartWithZero+2].'93')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('90EE90');
                $event->sheet->getDelegate()->getStyle('A89')->applyFromArray($fontStyleArialUnderline12); // Division
                $event->sheet->getDelegate()->getStyle('A90:'.$alphabet[$counterForRoutesDataStartWithZero+2].'93')->applyFromArray($alignmentAllCenter);
                $event->sheet->getDelegate()->getStyle('A90:'.$alphabet[$counterForRoutesDataStartWithZero+2].'93')->applyFromArray($borderStyleAllThin);
                $event->sheet->getDelegate()->getStyle('A90:'.$alphabet[$counterStartWithColumnC+2].'93')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                $event->sheet->getDelegate()->getStyle('A90:'.'B93')->applyFromArray($fontStyleArialBold10); // Time Schedules Font Arial 10

                /**
                 * Looping for computation of "Support Group"
                 */
                $totalCountForAllRoutesArray = [];
                $counterOfAllDivisionStartWithColumnC = 2;
                for ($i = 0; $i < count($this->routesData); $i++) {
                    $temporaryTotalForEveryRoutesArray = ['section'=>'','department'=>'','division'=>'','routes_id'=>'','routes_name'=>'','incoming'=>'','outgoing'=>''];
                    for ($j = 0; $j < count($this->masterlistData); $j++) {
                            /**
                             * Check if routes(table) id is equal to masterlists(table) routes_id to collect all masterlistData
                             * to be used for computation of each routes.
                             * Check Ids for comparison
                             * - $event->sheet->setCellValue('C6', 'id '. $this->routesData[0]->id);
                             * - $event->sheet->setCellValue('C7', 'id '. $this->masterlistData[0]->routes_id);
                             */
                            if($this->routesData[$i]->id == $this->masterlistData[$j]->routes_id){
                                if($this->masterlistData[$j]->masterlist_employee_type == 1){ // 1-Pricon
                                    $temporaryTotalForEveryRoutesArray['section']      = $this->masterlistData[$j]->hris_info->section_info->Section;
                                    $temporaryTotalForEveryRoutesArray['department']      = $this->masterlistData[$j]->hris_info->department_info->Department; // For Finance & PPC purpose
                                    $temporaryTotalForEveryRoutesArray['division']      = $this->masterlistData[$j]->hris_info->division_info->Division; // For QAD purpose
                                }else{ // 2-Subcon
                                    // dd($this->masterlistData[$j]->subcon_info); // Check subcon employee section
                                    if($this->masterlistData[$j]->subcon_info->section_info != null){ // Check if fkSection(in db_hris table) is not 0
                                        // $temporaryTotalForEveryRoutesArray['section']      = $this->masterlistData[$j]->subcon_info->section_info->Section;
                                        $temporaryTotalForEveryRoutesArray['section']      = 'Subcon';
                                    }else{
                                        $temporaryTotalForEveryRoutesArray['section']      = 'null';
                                    }
                                }
                                
                                $temporaryTotalForEveryRoutesArray['routes_id']     = $this->routesData[$i]->id;
                                $temporaryTotalForEveryRoutesArray['routes_name']   = $this->routesData[$i]->routes_description;
                                $temporaryTotalForEveryRoutesArray['incoming']      = $this->masterlistData[$j]->masterlist_incoming;
                                $temporaryTotalForEveryRoutesArray['outgoing']      = $this->masterlistData[$j]->masterlist_outgoing;
                                $totalCountForAllRoutesArray[] = $temporaryTotalForEveryRoutesArray;
                            }
                    }

                    /**
                     * First I check the section to separate it in "Operations" in excel
                     * then I check the $routesData(this will hold the "id" in routes table then loop it to be use in matching)
                     * then I will check if match based on the $totalCountForAllRoutesArray(this array will hold key value pair named "routes_id")
                     * Check Ids for comparison to compare
                     * - $this->routesData[$i]->id  
                     * - $totalCountForAllRoutesArray[$l]['routes_id']
                     * - dd($totalCountForAllRoutesArray);
                     */
                    $timeSchedulesHRDArray = [
                        ['time'=>'7:30AM-3:30PM','count'=> 0, 'row'=>'6', 'total_count'=> 0],
                        ['time'=>'7:30AM-4:30PM','count'=> 0, 'row'=>'7', 'total_count'=> 0],
                        ['time'=>'7:30AM-7:30PM','count'=> 0, 'row'=>'8', 'total_count'=> 0],
                        ['time'=>'7:30PM-7:30AM','count'=> 0, 'row'=>'9', 'total_count'=> 0],
                    ];
                    $totalSumHRDArray = ['total_sum'=> 0];

                    $timeSchedulesLogisticsArray = [
                        ['time'=>'7:30AM-3:30PM','count'=> 0, 'row'=>'13', 'total_count'=> 0],
                        ['time'=>'7:30AM-4:30PM','count'=> 0, 'row'=>'14', 'total_count'=> 0],
                        ['time'=>'7:30AM-7:30PM','count'=> 0, 'row'=>'15', 'total_count'=> 0],
                        ['time'=>'7:30PM-7:30AM','count'=> 0, 'row'=>'16', 'total_count'=> 0],
                    ];
                    $totalSumLogisticsArray = ['total_sum'=> 0];

                    $timeSchedulesFacilityArray = [
                        ['time'=>'7:30AM-3:30PM','count'=> 0, 'row'=>'20', 'total_count'=> 0],
                        ['time'=>'7:30AM-4:30PM','count'=> 0, 'row'=>'21', 'total_count'=> 0],
                        ['time'=>'7:30AM-7:30PM','count'=> 0, 'row'=>'22', 'total_count'=> 0],
                        ['time'=>'7:30PM-7:30AM','count'=> 0, 'row'=>'23', 'total_count'=> 0],
                    ];
                    $totalSumFacilityArray = ['total_sum'=> 0];

                    $timeSchedulesSubconArray = [
                        ['time'=>'7:30AM-3:30PM','count'=> 0, 'row'=>'27', 'total_count'=> 0],
                        ['time'=>'7:30AM-4:30PM','count'=> 0, 'row'=>'28', 'total_count'=> 0],
                        ['time'=>'7:30AM-7:30PM','count'=> 0, 'row'=>'29', 'total_count'=> 0],
                        ['time'=>'7:30PM-7:30AM','count'=> 0, 'row'=>'30', 'total_count'=> 0],
                    ];
                    $totalSumSubconArray = ['total_sum'=> 0];
                    
                    $timeSchedulesFinanceArray = [
                        ['time'=>'7:30AM-3:30PM','count'=> 0, 'row'=>'34', 'total_count'=> 0],
                        ['time'=>'7:30AM-4:30PM','count'=> 0, 'row'=>'35', 'total_count'=> 0],
                        ['time'=>'7:30AM-7:30PM','count'=> 0, 'row'=>'36', 'total_count'=> 0],
                        ['time'=>'7:30PM-7:30AM','count'=> 0, 'row'=>'37', 'total_count'=> 0],
                    ];
                    $totalSumFinanceArray = ['total_sum'=> 0];

                    $timeSchedulesSecretariatArray = [
                        ['time'=>'7:30AM-3:30PM','count'=> 0, 'row'=>'41', 'total_count'=> 0],
                        ['time'=>'7:30AM-4:30PM','count'=> 0, 'row'=>'42', 'total_count'=> 0],
                        ['time'=>'7:30AM-7:30PM','count'=> 0, 'row'=>'43', 'total_count'=> 0],
                        ['time'=>'7:30PM-7:30AM','count'=> 0, 'row'=>'44', 'total_count'=> 0],
                    ];
                    $totalSumSecretariatArray = ['total_sum'=> 0];

                    $timeSchedulesIASArray = [
                        ['time'=>'7:30AM-3:30PM','count'=> 0, 'row'=>'48', 'total_count'=> 0],
                        ['time'=>'7:30AM-4:30PM','count'=> 0, 'row'=>'49', 'total_count'=> 0],
                        ['time'=>'7:30AM-7:30PM','count'=> 0, 'row'=>'50', 'total_count'=> 0],
                        ['time'=>'7:30PM-7:30AM','count'=> 0, 'row'=>'51', 'total_count'=> 0],
                    ];
                    $totalSumIASArray = ['total_sum'=> 0];

                    $timeSchedulesQADArray = [
                        ['time'=>'7:30AM-3:30PM','count'=> 0, 'row'=>'55', 'total_count'=> 0],
                        ['time'=>'7:30AM-4:30PM','count'=> 0, 'row'=>'56', 'total_count'=> 0],
                        ['time'=>'7:30AM-7:30PM','count'=> 0, 'row'=>'57', 'total_count'=> 0],
                        ['time'=>'7:30PM-7:30AM','count'=> 0, 'row'=>'58', 'total_count'=> 0],
                    ];
                    $totalSumQADArray = ['total_sum'=> 0];

                    $timeSchedulesISSArray = [
                        ['time'=>'7:30AM-3:30PM','count'=> 0, 'row'=>'62', 'total_count'=> 0],
                        ['time'=>'7:30AM-4:30PM','count'=> 0, 'row'=>'63', 'total_count'=> 0],
                        ['time'=>'7:30AM-7:30PM','count'=> 0, 'row'=>'64', 'total_count'=> 0],
                        ['time'=>'7:30PM-7:30AM','count'=> 0, 'row'=>'65', 'total_count'=> 0],
                    ];
                    $totalSumISSArray = ['total_sum'=> 0];
                    
                    $timeSchedulesPPCArray = [
                        ['time'=>'7:30AM-3:30PM','count'=> 0, 'row'=>'69', 'total_count'=> 0],
                        ['time'=>'7:30AM-4:30PM','count'=> 0, 'row'=>'70', 'total_count'=> 0],
                        ['time'=>'7:30AM-7:30PM','count'=> 0, 'row'=>'71', 'total_count'=> 0],
                        ['time'=>'7:30PM-7:30AM','count'=> 0, 'row'=>'72', 'total_count'=> 0],
                    ];
                    $totalSumPPCArray = ['total_sum'=> 0];

                    $timeSchedulesDesignArray = [
                        ['time'=>'7:30AM-3:30PM','count'=> 0, 'row'=>'76', 'total_count'=> 0],
                        ['time'=>'7:30AM-4:30PM','count'=> 0, 'row'=>'77', 'total_count'=> 0],
                        ['time'=>'7:30AM-7:30PM','count'=> 0, 'row'=>'78', 'total_count'=> 0],
                        ['time'=>'7:30PM-7:30AM','count'=> 0, 'row'=>'79', 'total_count'=> 0],
                    ];
                    $totalSumDesignArray = ['total_sum'=> 0];

                    $timeSchedulesEMSArray = [
                        ['time'=>'7:30AM-3:30PM','count'=> 0, 'row'=>'83', 'total_count'=> 0],
                        ['time'=>'7:30AM-4:30PM','count'=> 0, 'row'=>'84', 'total_count'=> 0],
                        ['time'=>'7:30AM-7:30PM','count'=> 0, 'row'=>'85', 'total_count'=> 0],
                        ['time'=>'7:30PM-7:30AM','count'=> 0, 'row'=>'86', 'total_count'=> 0],
                    ];
                    $totalSumEMSArray = ['total_sum'=> 0];

                    $timeSchedulesESSArray = [
                        ['time'=>'7:30AM-3:30PM','count'=> 0, 'row'=>'90', 'total_count'=> 0],
                        ['time'=>'7:30AM-4:30PM','count'=> 0, 'row'=>'91', 'total_count'=> 0],
                        ['time'=>'7:30AM-7:30PM','count'=> 0, 'row'=>'92', 'total_count'=> 0],
                        ['time'=>'7:30PM-7:30AM','count'=> 0, 'row'=>'93', 'total_count'=> 0],
                    ];
                    $totalSumESSArray = ['total_sum'=> 0];

                    // dd($totalCountForAllRoutesArray);
                    for ($l=0; $l < count($totalCountForAllRoutesArray); $l++) {
                        if($totalCountForAllRoutesArray[$l]['section'] == "HRD"){
                            $temporaryTimeScheduleOfEachRoutesHRD = $totalCountForAllRoutesArray[$l]['incoming']."-".$totalCountForAllRoutesArray[$l]['outgoing'];
                            for ($m=0; $m < count($timeSchedulesHRDArray); $m++) {
                                /**
                                 * Total count computation for each routes & time schedule for HRD only
                                 */
                                if($this->routesData[$i]->id == $totalCountForAllRoutesArray[$l]['routes_id']){ // check the routes added in the $totalCountForAllRoutesArray to know which column to be set value
                                    if($temporaryTimeScheduleOfEachRoutesHRD == $timeSchedulesHRDArray[$m]['time']){
                                        $timeSchedulesHRDArray[$m]['count']++;
                                        $event->sheet->setCellValue($alphabet[$counterOfAllDivisionStartWithColumnC].$timeSchedulesHRDArray[$m]['row'], $timeSchedulesHRDArray[$m]['count']);
                                    }
                                }

                                /**
                                 * Total sum computation for each time schedule for HRD only
                                 */
                                if($temporaryTimeScheduleOfEachRoutesHRD == $timeSchedulesHRDArray[$m]['time']){
                                    $timeSchedulesHRDArray[$m]['total_count']++;
                                    $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].$timeSchedulesHRDArray[$m]['row'], $timeSchedulesHRDArray[$m]['total_count']);
                                }
                            }

                            /**
                             * Total sum computation for HRD only
                             */
                            $totalSumHRDArray['total_sum']++;
                            $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'10', $totalSumHRDArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'10')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'10')->applyFromArray($alignmentAllCenter);
                        }

                        if($totalCountForAllRoutesArray[$l]['section'] == "Logistics"){
                            $temporaryTimeScheduleOfEachRoutesLogistics = $totalCountForAllRoutesArray[$l]['incoming']."-".$totalCountForAllRoutesArray[$l]['outgoing'];
                            for ($m=0; $m < count($timeSchedulesLogisticsArray); $m++) {
                                /**
                                 * Total count computation for each routes & time schedule for Logistics only
                                 */
                                if($this->routesData[$i]->id == $totalCountForAllRoutesArray[$l]['routes_id']){ // check the routes added in the $totalCountForAllRoutesArray to know which column to be set value
                                    if($temporaryTimeScheduleOfEachRoutesLogistics == $timeSchedulesLogisticsArray[$m]['time']){
                                        $timeSchedulesLogisticsArray[$m]['count']++;
                                        $event->sheet->setCellValue($alphabet[$counterOfAllDivisionStartWithColumnC].$timeSchedulesLogisticsArray[$m]['row'], $timeSchedulesLogisticsArray[$m]['count']);
                                    }
                                }

                                /**
                                 * Total sum computation for each time schedule for Logistics only
                                 */
                                if($temporaryTimeScheduleOfEachRoutesLogistics == $timeSchedulesLogisticsArray[$m]['time']){
                                    $timeSchedulesLogisticsArray[$m]['total_count']++;
                                    $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].$timeSchedulesLogisticsArray[$m]['row'], $timeSchedulesLogisticsArray[$m]['total_count']);
                                }
                            }

                            /**
                             * Total sum computation for HRD only
                             */
                            $totalSumLogisticsArray['total_sum']++;
                            $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'17', $totalSumLogisticsArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'17')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'17')->applyFromArray($alignmentAllCenter);
                        }

                        if($totalCountForAllRoutesArray[$l]['section'] == "Facility"){
                            $temporaryTimeScheduleOfEachRoutesFacility = $totalCountForAllRoutesArray[$l]['incoming']."-".$totalCountForAllRoutesArray[$l]['outgoing'];
                            for ($m=0; $m < count($timeSchedulesFacilityArray); $m++) {
                                /**
                                 * Total count computation for each routes & time schedule for Facility only
                                 */
                                if($this->routesData[$i]->id == $totalCountForAllRoutesArray[$l]['routes_id']){ // check the routes added in the $totalCountForAllRoutesArray to know which column to be set value
                                    if($temporaryTimeScheduleOfEachRoutesFacility == $timeSchedulesFacilityArray[$m]['time']){
                                        $timeSchedulesFacilityArray[$m]['count']++;
                                        $event->sheet->setCellValue($alphabet[$counterOfAllDivisionStartWithColumnC].$timeSchedulesFacilityArray[$m]['row'], $timeSchedulesFacilityArray[$m]['count']);
                                    }
                                }

                                /**
                                 * Total sum computation for each time schedule for Facility only
                                 */
                                if($temporaryTimeScheduleOfEachRoutesFacility == $timeSchedulesFacilityArray[$m]['time']){
                                    $timeSchedulesFacilityArray[$m]['total_count']++;
                                    $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].$timeSchedulesFacilityArray[$m]['row'], $timeSchedulesFacilityArray[$m]['total_count']);
                                }
                            }

                            /**
                             * Total sum computation for HRD only
                             */
                            $totalSumFacilityArray['total_sum']++;
                            $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'24', $totalSumFacilityArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'24')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'24')->applyFromArray($alignmentAllCenter);
                        }

                        if($totalCountForAllRoutesArray[$l]['section'] == "Subcon"){
                            $temporaryTimeScheduleOfEachRoutesSubcon = $totalCountForAllRoutesArray[$l]['incoming']."-".$totalCountForAllRoutesArray[$l]['outgoing'];
                            for ($m=0; $m < count($timeSchedulesSubconArray); $m++) {
                                /**
                                 * Total count computation for each routes & time schedule for Subcon only
                                 */
                                if($this->routesData[$i]->id == $totalCountForAllRoutesArray[$l]['routes_id']){ // check the routes added in the $totalCountForAllRoutesArray to know which column to be set value
                                    if($temporaryTimeScheduleOfEachRoutesSubcon == $timeSchedulesSubconArray[$m]['time']){
                                        $timeSchedulesSubconArray[$m]['count']++;
                                        $event->sheet->setCellValue($alphabet[$counterOfAllDivisionStartWithColumnC].$timeSchedulesSubconArray[$m]['row'], $timeSchedulesSubconArray[$m]['count']);
                                    }
                                }

                                /**
                                 * Total sum computation for each time schedule for Subcon only
                                 */
                                if($temporaryTimeScheduleOfEachRoutesSubcon == $timeSchedulesSubconArray[$m]['time']){
                                    $timeSchedulesSubconArray[$m]['total_count']++;
                                    $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].$timeSchedulesSubconArray[$m]['row'], $timeSchedulesSubconArray[$m]['total_count']);
                                }
                            }

                            /**
                             * Total sum computation for HRD only
                             */
                            $totalSumSubconArray['total_sum']++;
                            $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'31', $totalSumSubconArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'31')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'31')->applyFromArray($alignmentAllCenter);
                        }

                        if($totalCountForAllRoutesArray[$l]['department'] == "Finance"){
                            $temporaryTimeScheduleOfEachRoutesFinance = $totalCountForAllRoutesArray[$l]['incoming']."-".$totalCountForAllRoutesArray[$l]['outgoing'];
                            for ($m=0; $m < count($timeSchedulesFinanceArray); $m++) {
                                /**
                                 * Total count computation for each routes & time schedule for Finance only
                                 */
                                if($this->routesData[$i]->id == $totalCountForAllRoutesArray[$l]['routes_id']){ // check the routes added in the $totalCountForAllRoutesArray to know which column to be set value
                                    if($temporaryTimeScheduleOfEachRoutesFinance == $timeSchedulesFinanceArray[$m]['time']){
                                        $timeSchedulesFinanceArray[$m]['count']++;
                                        $event->sheet->setCellValue($alphabet[$counterOfAllDivisionStartWithColumnC].$timeSchedulesFinanceArray[$m]['row'], $timeSchedulesFinanceArray[$m]['count']);
                                    }
                                }

                                /**
                                 * Total sum computation for each time schedule for Finance only
                                 */
                                if($temporaryTimeScheduleOfEachRoutesFinance == $timeSchedulesFinanceArray[$m]['time']){
                                    $timeSchedulesFinanceArray[$m]['total_count']++;
                                    $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].$timeSchedulesFinanceArray[$m]['row'], $timeSchedulesFinanceArray[$m]['total_count']);
                                }
                            }

                            /**
                             * Total sum computation for HRD only
                             */
                            $totalSumFinanceArray['total_sum']++;
                            $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'38', $totalSumFinanceArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'38')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'38')->applyFromArray($alignmentAllCenter);
                        }

                        if($totalCountForAllRoutesArray[$l]['section'] == "Secretariat Office"){
                            $temporaryTimeScheduleOfEachRoutesSecretariat = $totalCountForAllRoutesArray[$l]['incoming']."-".$totalCountForAllRoutesArray[$l]['outgoing'];
                            for ($m=0; $m < count($timeSchedulesSecretariatArray); $m++) {
                                /**
                                 * Total count computation for each routes & time schedule for Secretariat only
                                 */
                                if($this->routesData[$i]->id == $totalCountForAllRoutesArray[$l]['routes_id']){ // check the routes added in the $totalCountForAllRoutesArray to know which column to be set value
                                    if($temporaryTimeScheduleOfEachRoutesSecretariat == $timeSchedulesSecretariatArray[$m]['time']){
                                        $timeSchedulesSecretariatArray[$m]['count']++;
                                        $event->sheet->setCellValue($alphabet[$counterOfAllDivisionStartWithColumnC].$timeSchedulesSecretariatArray[$m]['row'], $timeSchedulesSecretariatArray[$m]['count']);
                                    }
                                }

                                /**
                                 * Total sum computation for each time schedule for Secretariat only
                                 */
                                if($temporaryTimeScheduleOfEachRoutesSecretariat == $timeSchedulesSecretariatArray[$m]['time']){
                                    $timeSchedulesSecretariatArray[$m]['total_count']++;
                                    $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].$timeSchedulesSecretariatArray[$m]['row'], $timeSchedulesSecretariatArray[$m]['total_count']);
                                }
                            }

                            /**
                             * Total sum computation for HRD only
                             */
                            $totalSumSecretariatArray['total_sum']++;
                            $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'45', $totalSumSecretariatArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'45')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'45')->applyFromArray($alignmentAllCenter);
                        }

                        if($totalCountForAllRoutesArray[$l]['section'] == "Internal Audit"){
                            $temporaryTimeScheduleOfEachRoutesIAS = $totalCountForAllRoutesArray[$l]['incoming']."-".$totalCountForAllRoutesArray[$l]['outgoing'];
                            for ($m=0; $m < count($timeSchedulesIASArray); $m++) {
                                /**
                                 * Total count computation for each routes & time schedule for IAS only
                                 */
                                if($this->routesData[$i]->id == $totalCountForAllRoutesArray[$l]['routes_id']){ // check the routes added in the $totalCountForAllRoutesArray to know which column to be set value
                                    if($temporaryTimeScheduleOfEachRoutesIAS == $timeSchedulesIASArray[$m]['time']){
                                        $timeSchedulesIASArray[$m]['count']++;
                                        $event->sheet->setCellValue($alphabet[$counterOfAllDivisionStartWithColumnC].$timeSchedulesIASArray[$m]['row'], $timeSchedulesIASArray[$m]['count']);
                                    }
                                }

                                /**
                                 * Total sum computation for each time schedule for IAS only
                                 */
                                if($temporaryTimeScheduleOfEachRoutesIAS == $timeSchedulesIASArray[$m]['time']){
                                    $timeSchedulesIASArray[$m]['total_count']++;
                                    $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].$timeSchedulesIASArray[$m]['row'], $timeSchedulesIASArray[$m]['total_count']);
                                }
                            }

                            /**
                             * Total sum computation for HRD only
                             */
                            $totalSumIASArray['total_sum']++;
                            $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'52', $totalSumIASArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'52')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'52')->applyFromArray($alignmentAllCenter);
                        }

                        if($totalCountForAllRoutesArray[$l]['division'] == "Quality Assurance Division"){
                            $temporaryTimeScheduleOfEachRoutesQAD = $totalCountForAllRoutesArray[$l]['incoming']."-".$totalCountForAllRoutesArray[$l]['outgoing'];
                            for ($m=0; $m < count($timeSchedulesQADArray); $m++) {
                                /**
                                 * Total count computation for each routes & time schedule for QAD only
                                 */
                                if($this->routesData[$i]->id == $totalCountForAllRoutesArray[$l]['routes_id']){ // check the routes added in the $totalCountForAllRoutesArray to know which column to be set value
                                    if($temporaryTimeScheduleOfEachRoutesQAD == $timeSchedulesQADArray[$m]['time']){
                                        $timeSchedulesQADArray[$m]['count']++;
                                        $event->sheet->setCellValue($alphabet[$counterOfAllDivisionStartWithColumnC].$timeSchedulesQADArray[$m]['row'], $timeSchedulesQADArray[$m]['count']);
                                    }
                                }

                                /**
                                 * Total sum computation for each time schedule for QAD only
                                 */
                                if($temporaryTimeScheduleOfEachRoutesQAD == $timeSchedulesQADArray[$m]['time']){
                                    $timeSchedulesQADArray[$m]['total_count']++;
                                    $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].$timeSchedulesQADArray[$m]['row'], $timeSchedulesQADArray[$m]['total_count']);
                                }
                            }

                            /**
                             * Total sum computation for HRD only
                             */
                            $totalSumQADArray['total_sum']++;
                            $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'59', $totalSumQADArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'59')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'59')->applyFromArray($alignmentAllCenter);
                        }

                        if($totalCountForAllRoutesArray[$l]['section'] == "Information Systems Section"){
                            $temporaryTimeScheduleOfEachRoutesISS = $totalCountForAllRoutesArray[$l]['incoming']."-".$totalCountForAllRoutesArray[$l]['outgoing'];
                            for ($m=0; $m < count($timeSchedulesISSArray); $m++) {
                                /**
                                 * Total count computation for each routes & time schedule for ISS only
                                 */
                                if($this->routesData[$i]->id == $totalCountForAllRoutesArray[$l]['routes_id']){ // check the routes added in the $totalCountForAllRoutesArray to know which column to be set value
                                    if($temporaryTimeScheduleOfEachRoutesISS == $timeSchedulesISSArray[$m]['time']){
                                        $timeSchedulesISSArray[$m]['count']++;
                                        $event->sheet->setCellValue($alphabet[$counterOfAllDivisionStartWithColumnC].$timeSchedulesISSArray[$m]['row'], $timeSchedulesISSArray[$m]['count']);
                                    }
                                }

                                /**
                                 * Total sum computation for each time schedule for ISS only
                                 */
                                if($temporaryTimeScheduleOfEachRoutesISS == $timeSchedulesISSArray[$m]['time']){
                                    $timeSchedulesISSArray[$m]['total_count']++;
                                    $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].$timeSchedulesISSArray[$m]['row'], $timeSchedulesISSArray[$m]['total_count']);
                                }
                            }

                            /**
                             * Total sum computation for HRD only
                             */
                            $totalSumISSArray['total_sum']++;
                            $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'66', $totalSumISSArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'66')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'66')->applyFromArray($alignmentAllCenter);
                        }

                        if($totalCountForAllRoutesArray[$l]['department'] == "CN - PPC" || $totalCountForAllRoutesArray[$l]['department'] == "TS - PPC" || $totalCountForAllRoutesArray[$l]['department'] == "PPS - PPC" || $totalCountForAllRoutesArray[$l]['department'] == "YF - PPC"){
                            $temporaryTimeScheduleOfEachRoutesPPC = $totalCountForAllRoutesArray[$l]['incoming']."-".$totalCountForAllRoutesArray[$l]['outgoing'];
                            for ($m=0; $m < count($timeSchedulesPPCArray); $m++) {
                                /**
                                 * Total count computation for each routes & time schedule for PPC only
                                 */
                                if($this->routesData[$i]->id == $totalCountForAllRoutesArray[$l]['routes_id']){ // check the routes added in the $totalCountForAllRoutesArray to know which column to be set value
                                    if($temporaryTimeScheduleOfEachRoutesPPC == $timeSchedulesPPCArray[$m]['time']){
                                        $timeSchedulesPPCArray[$m]['count']++;
                                        $event->sheet->setCellValue($alphabet[$counterOfAllDivisionStartWithColumnC].$timeSchedulesPPCArray[$m]['row'], $timeSchedulesPPCArray[$m]['count']);
                                    }
                                }

                                /**
                                 * Total sum computation for each time schedule for PPC only
                                 */
                                if($temporaryTimeScheduleOfEachRoutesPPC == $timeSchedulesPPCArray[$m]['time']){
                                    $timeSchedulesPPCArray[$m]['total_count']++;
                                    $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].$timeSchedulesPPCArray[$m]['row'], $timeSchedulesPPCArray[$m]['total_count']);
                                }
                            }

                            /**
                             * Total sum computation for HRD only
                             */
                            $totalSumPPCArray['total_sum']++;
                            $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'73', $totalSumPPCArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'73')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'73')->applyFromArray($alignmentAllCenter);
                        }

                        if($totalCountForAllRoutesArray[$l]['section'] == "Design Engineering"){
                            $temporaryTimeScheduleOfEachRoutesDesign = $totalCountForAllRoutesArray[$l]['incoming']."-".$totalCountForAllRoutesArray[$l]['outgoing'];
                            for ($m=0; $m < count($timeSchedulesDesignArray); $m++) {
                                /**
                                 * Total count computation for each routes & time schedule for Design only
                                 */
                                if($this->routesData[$i]->id == $totalCountForAllRoutesArray[$l]['routes_id']){ // check the routes added in the $totalCountForAllRoutesArray to know which column to be set value
                                    if($temporaryTimeScheduleOfEachRoutesDesign == $timeSchedulesDesignArray[$m]['time']){
                                        $timeSchedulesDesignArray[$m]['count']++;
                                        $event->sheet->setCellValue($alphabet[$counterOfAllDivisionStartWithColumnC].$timeSchedulesDesignArray[$m]['row'], $timeSchedulesDesignArray[$m]['count']);
                                    }
                                }

                                /**
                                 * Total sum computation for each time schedule for Design only
                                 */
                                if($temporaryTimeScheduleOfEachRoutesDesign == $timeSchedulesDesignArray[$m]['time']){
                                    $timeSchedulesDesignArray[$m]['total_count']++;
                                    $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].$timeSchedulesDesignArray[$m]['row'], $timeSchedulesDesignArray[$m]['total_count']);
                                }
                            }

                            /**
                             * Total sum computation for HRD only
                             */
                            $totalSumDesignArray['total_sum']++;
                            $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'80', $totalSumDesignArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'80')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'80')->applyFromArray($alignmentAllCenter);
                        }

                        if($totalCountForAllRoutesArray[$l]['section'] == "Environmental Management Section"){
                            $temporaryTimeScheduleOfEachRoutesEMS = $totalCountForAllRoutesArray[$l]['incoming']."-".$totalCountForAllRoutesArray[$l]['outgoing'];
                            for ($m=0; $m < count($timeSchedulesEMSArray); $m++) {
                                /**
                                 * Total count computation for each routes & time schedule for EMS only
                                 */
                                if($this->routesData[$i]->id == $totalCountForAllRoutesArray[$l]['routes_id']){ // check the routes added in the $totalCountForAllRoutesArray to know which column to be set value
                                    if($temporaryTimeScheduleOfEachRoutesEMS == $timeSchedulesEMSArray[$m]['time']){
                                        $timeSchedulesEMSArray[$m]['count']++;
                                        $event->sheet->setCellValue($alphabet[$counterOfAllDivisionStartWithColumnC].$timeSchedulesEMSArray[$m]['row'], $timeSchedulesEMSArray[$m]['count']);
                                    }
                                }

                                /**
                                 * Total sum computation for each time schedule for EMS only
                                 */
                                if($temporaryTimeScheduleOfEachRoutesEMS == $timeSchedulesEMSArray[$m]['time']){
                                    $timeSchedulesEMSArray[$m]['total_count']++;
                                    $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].$timeSchedulesEMSArray[$m]['row'], $timeSchedulesEMSArray[$m]['total_count']);
                                }
                            }

                            /**
                             * Total sum computation for HRD only
                             */
                            $totalSumEMSArray['total_sum']++;
                            $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'87', $totalSumEMSArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'87')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'87')->applyFromArray($alignmentAllCenter);
                        }

                        if($totalCountForAllRoutesArray[$l]['section'] == "Employee Services Section"){
                            $temporaryTimeScheduleOfEachRoutesESS = $totalCountForAllRoutesArray[$l]['incoming']."-".$totalCountForAllRoutesArray[$l]['outgoing'];
                            for ($m=0; $m < count($timeSchedulesESSArray); $m++) {
                                /**
                                 * Total count computation for each routes & time schedule for ESS only
                                 */
                                if($this->routesData[$i]->id == $totalCountForAllRoutesArray[$l]['routes_id']){ // check the routes added in the $totalCountForAllRoutesArray to know which column to be set value
                                    if($temporaryTimeScheduleOfEachRoutesESS == $timeSchedulesESSArray[$m]['time']){
                                        $timeSchedulesESSArray[$m]['count']++;
                                        $event->sheet->setCellValue($alphabet[$counterOfAllDivisionStartWithColumnC].$timeSchedulesESSArray[$m]['row'], $timeSchedulesESSArray[$m]['count']);
                                    }
                                }

                                /**
                                 * Total sum computation for each time schedule for ESS only
                                 */
                                if($temporaryTimeScheduleOfEachRoutesESS == $timeSchedulesESSArray[$m]['time']){
                                    $timeSchedulesESSArray[$m]['total_count']++;
                                    $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].$timeSchedulesESSArray[$m]['row'], $timeSchedulesESSArray[$m]['total_count']);
                                }
                            }

                            /**
                             * Total sum computation for HRD only
                             */
                            $totalSumESSArray['total_sum']++;
                            $event->sheet->setCellValue($alphabet[$counterStartWithColumnC].'94', $totalSumESSArray['total_sum']);
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'94')->applyFromArray($fontStyleArialBold14); // Column C to up Font Arial 14
                            $event->sheet->getDelegate()->getStyle($alphabet[$counterStartWithColumnC].'94')->applyFromArray($alignmentAllCenter);
                        }
                    }
                    $counterOfAllDivisionStartWithColumnC++;
                }
            },
        ];
    }

}


