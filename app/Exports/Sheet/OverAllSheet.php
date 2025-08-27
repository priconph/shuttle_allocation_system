<?php

namespace App\Exports\Sheet;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\Exportable;

class OverAllSheet implements FromView, ShouldAutoSize, WithEvents, WithTitle
{
    use Exportable;

    // protected $masterlist;
    protected $mergedLists;

    // public function __construct($masterlist)
    public function __construct($mergedLists)
    {
        // $this->masterlist = $masterlist;
        $this->mergedLists = $mergedLists;
    }

    public function view(): View
    {
        // return view('exports.overall', ['masterlist' => $this->masterlist]);
        return view('exports.overall', ['masterlist' => $this->mergedLists]);
    }

    public function title(): string
    {
        return 'Overall';
    }

    public function registerEvents(): array
    {
        // $masterlist = $this->masterlist;
        $mergedLists = $this->mergedLists;

        // Styles
        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        $textAlignCenter = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];

        $textAlignLeft = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];

        $font9Arial = [
            'font' => [
                'name' => 'Arial',
                'size' => 9,
            ],
        ];

        $font9ArialBold = [
            'font' => [
                'name' => 'Arial',
                'size' => 9,
                'bold' => true,
            ],
        ];

        $font10Arial = [
            'font' => [
                'name' => 'Arial',
                'size' => 10,
            ],
        ];

        $font12ArialBold = [
            'font' => [
                'name' => 'Arial',
                'size' => 12,
                'bold' => true,
            ],
        ];

        $font14ArialBold = [
            'font' => [
                'name' => 'Arial',
                'size' => 14,
                'bold' => true,
            ],
        ];

        return [
            AfterSheet::class => function (AfterSheet $event) use (
                $mergedLists,
                $border,
                $textAlignCenter,
                $textAlignLeft,
                $font9Arial,
                $font10Arial,
                $font9ArialBold,
                $font12ArialBold,
                $font14ArialBold
            ) {
                $sheet = $event->sheet->getDelegate();

                // Freeze and background
                $sheet->freezePane('C4');
                $sheet->getStyle('A1:J2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B7D8FF');
                $sheet->getStyle('A3:J3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('70ECF9');
                $sheet->getStyle('A1:J3')->applyFromArray($border);

                // Column widths
                foreach (['A', 'B', 'E', 'H', 'I', 'J'] as $col) {
                    $sheet->getColumnDimension($col)->setWidth(30);
                }
                foreach (['C', 'D', 'F', 'G'] as $col) {
                    $sheet->getColumnDimension($col)->setWidth(20);
                }

                // Row heights
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(3)->setRowHeight(25);

                // Title and headers
                $sheet->mergeCells('A1:J2');
                $sheet->setCellValue('A1', 'Shuttle Bus Allocation');
                $sheet->getStyle('A1')->applyFromArray(array_merge($textAlignCenter, $font14ArialBold));

                $headers = [
                    'A3' => 'ID Number',
                    'B3' => 'Full Name',
                    'C3' => 'Incoming',
                    'D3' => 'Outgoing',
                    'E3' => 'Route',
                    'F3' => 'Position',
                    'G3' => 'Division',
                    'H3' => 'Department',
                    'I3' => 'Section',
                    'J3' => 'User',
                ];
                foreach ($headers as $cell => $text) {
                    $sheet->setCellValue($cell, $text);
                }
                $sheet->getStyle('A3:J3')->applyFromArray(array_merge($textAlignCenter, $font12ArialBold));

                $startRow = 4;

                foreach ($mergedLists as $item) {
                    $sheet->getStyle("A{$startRow}:J{$startRow}")
                        ->applyFromArray(array_merge($border, $textAlignLeft, $font10Arial));

                    $isAllocation = isset($item->request_ml_info);
                    $source = $isAllocation ? $item->request_ml_info : $item;

                    $incoming = $isAllocation ? $item->alloc_incoming : $source->masterlist_incoming;
                    $outgoing = $isAllocation ? $item->alloc_outgoing : $source->masterlist_outgoing;

                    // Set the value
                    $sheet->setCellValue("A{$startRow}", "\n  " . $source->masterlist_employee_number);
                    $sheet->setCellValue("C{$startRow}", "\n  " . $incoming);
                    $sheet->setCellValue("D{$startRow}", "\n  " . $outgoing);
                    $sheet->setCellValue("E{$startRow}", "\n  " . optional($source->routes_info)->routes_name);

                    $person = $source->hris_info ?? $source->subcon_info;
                    $name = "\n  " . optional($person)->FirstName . ' ' . optional($person)->LastName;
                    $replacements = [
                        'Ã±' => 'ñ',
                        'ÃÂ±' => 'ñ',
                    ];

                    $fixedName = strtr($name, $replacements);

                    // Set the value
                    $sheet->setCellValue("B{$startRow}", $fixedName);
                    $sheet->setCellValue("F{$startRow}", "\n  " . optional($person->position_info)->Position);
                    $sheet->setCellValue("G{$startRow}", "\n  " . optional($person->division_info)->Division);
                    $sheet->setCellValue("H{$startRow}", "\n  " . optional($person->department_info)->Department);
                    $sheet->setCellValue("I{$startRow}", "\n  " . optional($person->section_info)->Section);

                    $user = $isAllocation ? $source->rapidx_user_info : $item->rapidx_user_info;
                    $sheet->setCellValue("J{$startRow}", "\n  " . optional($user)->name);

                    $startRow++;
                }
            },
        ];
    }
}
