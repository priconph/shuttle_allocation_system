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

class AllocationV3 implements FromView, ShouldAutoSize, WithEvents, WithTitle
{
    use Exportable;

    protected $routeNameCounts;
    // protected $routeDestinationFinalCount;
    protected $factory;
    protected $from;
    protected $to;
    protected $route_code;

    public function __construct(
        $routeNameCounts,
        // $routeDestinationFinalCount,
        $factory,
        $from,
        $to,
        $route_code
    )
    {
        $this->routeNameCounts              = $routeNameCounts;
        // $this->routeDestinationFinalCount   = $routeDestinationFinalCount;
        $this->factory                      = $factory;
        $this->from                         = $from;
        $this->to                           = $to;
        $this->route_code                   = $route_code;    
    }

    public function view(): View
    {
        return view('exports.overall', 
                [
                    'routeNameCounts'               => $this->routeNameCounts,
                    // 'routeDestinationFinalCount'    => $this->routeDestinationFinalCount,
                    'factory'                       => $this->factory,
                    'from'                          => $this->from,
                    'to'                            => $this->to,
                    'route_code'                    => $this->route_code,
                ]);
    }

    public function title(): string
    {
        return 'Allocation';
    }

    public function registerEvents(): array
    {
        $routeNameCounts            =   $this->routeNameCounts;      
        // $routeDestinationFinalCount =   $this->routeDestinationFinalCount;      
        $factory                    =   $this->factory;      
        $from                       =   $this->from;
        $to                         =   $this->to;     
        $route_code                 =   $this->route_code;

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

        $font10Arial = [
            'font' => [
                'name' => 'Arial',
                'size' => 10,
            ],
        ];

        $font10ArialBold = [
            'font' => [
                'name' => 'Arial',
                'size' => 10,
                'bold' => true,
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
            // $routeDestinationFinalCount,
            AfterSheet::class => function (AfterSheet $event) use (
                $routeNameCounts,
                $factory,
                $from,
                $to,
                $route_code,
                $border,
                $textAlignCenter,
                $textAlignLeft,
                $font10Arial,
                $font10ArialBold,
                $font12ArialBold,
                $font14ArialBold
            ) {
                $sheet = $event->sheet->getDelegate();
        
                // Allowed times per type
                $allowedIncomingTimes = ['07:30 AM', '07:30 PM'];
                $allowedOutgoingTimes = ['03:30 PM', '04:30 PM', '07:30 PM', '07:30 AM'];
        
                // Helper to get Excel column letter by index (0=A, 1=B, ...)
                $getExcelColumn = function($index) {
                    $letters = '';
                    while ($index >= 0) {
                        $letters = chr($index % 26 + 65) . $letters;
                        $index = intdiv($index, 26) - 1;
                    }
                    return $letters;
                };
        
                $setUp = [
                    'Incoming' => [
                        'location' => $from,
                        'startCol' => 'A',
                        'baseColumns' => ['A', 'B', 'C'],  // Letter Code, Route Name, Pick-Up Points
                        'allowedTimes' => $allowedIncomingTimes,
                        'fixedEndColsCount' => 2,  // Shuttle Allocation & Provider
                        'widths' => [
                            'B' => 20, 'C' => 30,
                            // dynamic widths for time and fixed columns set later
                        ],
                    ],
                    'Outgoing' => [
                        'location' => $to,
                        'startCol' => 'I',
                        'baseColumns' => ['I', 'J', 'K'],
                        'allowedTimes' => $allowedOutgoingTimes,
                        'fixedEndColsCount' => 2,
                        'widths' => [
                            'J' => 20, 'K' => 30,
                        ],
                    ]
                ];
        
                // Prepare dynamic columns, headers, merges, fills, and widths
                foreach ($setUp as $type => &$config) {
                    $startIndex = ord($config['startCol']) - 65;
                    $columns = $config['baseColumns'];
        
                    $timeColStartIndex = $startIndex + count($config['baseColumns']);
                    foreach ($config['allowedTimes'] as $i => $time) {
                        $columns[] = $getExcelColumn($timeColStartIndex + $i);
                    }
        
                    $lastColsStartIndex = $timeColStartIndex + count($config['allowedTimes']);
                    for ($i = 0; $i < $config['fixedEndColsCount']; $i++) {
                        $columns[] = $getExcelColumn($lastColsStartIndex + $i);
                    }
        
                    $config['columns'] = $columns;
        
                    // Set header cells
                    $config['headerCells'] = [];
                    $config['headerCells'][$columns[0] . '3'] = "Letter \nCode";
                    $config['headerCells'][$columns[1] . '3'] = 'Route Name';
                    $config['headerCells'][$columns[2] . '3'] = 'Pick-Up Points';
        
                    foreach ($config['allowedTimes'] as $i => $time) {
                        $col = $columns[3 + $i];
                        $config['headerCells']["{$col}3"] = $time;
                        $config['widths'][$col] = 12;  // Width for time columns
                    }
        
                    $lastBaseIndex = 3 + count($config['allowedTimes']);
                    $config['headerCells'][$columns[$lastBaseIndex] . '3'] = 'SHUTTLE ALLOCATION';
                    $config['headerCells'][$columns[$lastBaseIndex + 1] . '3'] = 'SHUTTLE PROVIDER';
        
                    $config['widths'][$columns[$lastBaseIndex]] = 20;
                    $config['widths'][$columns[$lastBaseIndex + 1]] = 20;
        
                    // Merge cells for subheaders
                    $merge1Start = $columns[0] . '1';
                    $merge1End = $columns[count($config['baseColumns']) - 1] . '2';
        
                    $merge2Start = $columns[count($config['baseColumns'])] . '1';
                    $merge2End = end($columns) . '2';
        
                    $config['mergeCells'] = ["{$merge1Start}:{$merge1End}", "{$merge2Start}:{$merge2End}"];
        
                    // Header fill range
                    $config['headerFillRange'] = ["{$columns[0]}1:{$columns[count($columns) - 1]}3" => 'B7D8FF'];
        
                    $config['mainStyleRange'] = "{$merge1Start}:{$merge1End}";
                    $config['headerStyleRange'] = "{$columns[0]}3:{$columns[count($columns) - 1]}3";
                }
                unset($config);
        
                // Now generate tables per Incoming and Outgoing
                foreach ($setUp as $type => $config) {
                    // Fill header fills
                    foreach ($config['headerFillRange'] as $range => $color) {
                        $sheet->getStyle($range)->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($color);
                    }
        
                    // Set widths
                    foreach ($config['widths'] as $col => $width) {
                        $sheet->getColumnDimension($col)->setWidth($width);
                    }
        
                    // Merge header cells
                    foreach ($config['mergeCells'] as $mergeRange) {
                        $sheet->mergeCells($mergeRange);
                    }
        
                    // Set header texts
                    foreach ($config['headerCells'] as $cell => $text) {
                        $sheet->setCellValue($cell, $text);
                    }
        
                    list($topLeft1,) = explode(':', $config['mergeCells'][0]);
                    // list($topLeft2,) = explode(':', $config['mergeCells'][1]);
        
                    $sheet->setCellValue($topLeft1, ' ' . $config['location'] . "  {$type}  ");
                    // $sheet->setCellValue($topLeft2, 'Factory ' . $factory);
        
                    // Apply styles to headers
                    $sheet->getStyle($config['mainStyleRange'])->applyFromArray($textAlignLeft + $font14ArialBold);
                    $sheet->getStyle($config['headerStyleRange'])->applyFromArray($textAlignCenter + $font12ArialBold + $border);
        
                    $startRow = 4;
        
                    foreach ($route_code as $route) {
                        $details = is_iterable($route->routes_details) ? $route->routes_details : [];
                        $detailCount = count($details);
                        $endRow = $detailCount ? $startRow + $detailCount - 1 : $startRow;
                        $col = $config['columns'];
        
                        // Route Code & Destination with background color
                        $sheet->setCellValue("{$col[0]}{$startRow}", $route->routes_code);
                        $sheet->setCellValue("{$col[1]}{$startRow}", $route->routes_destination);
                        $sheet->getStyle("{$col[0]}{$startRow}:{$col[1]}{$startRow}")
                            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($route->color_code);
        
                        // Merge vertical cells for certain columns if multiple details exist
                        if ($detailCount) {
                            foreach ([$col[0], $col[1], $col[count($col) - 2], $col[count($col) - 1]] as $mergeCol) {
                                $sheet->mergeCells("{$mergeCol}{$startRow}:{$mergeCol}{$endRow}");
                            }
                        }
        
                        $sheet->getStyle("{$col[0]}{$startRow}:{$col[1]}{$endRow}")
                            ->applyFromArray($border + $textAlignCenter + $font10ArialBold)
                            ->getAlignment()->setWrapText(true);
        
                        $currentRow = $startRow;
        
                        if ($detailCount) {
                            for ($i = 0; $i < $detailCount; $i++) {
                                $detail = $details[$i];
                                $sheet->setCellValue("{$col[2]}{$currentRow}", "\n  " . $detail->routes_name);
                                $sheet->getStyle("{$col[2]}{$currentRow}:{$col[count($col) - 1]}{$currentRow}")
                                    ->applyFromArray($border + $textAlignCenter + $font10Arial)
                                    ->getAlignment()->setWrapText(true);
        
                                // Find matching routeNameCounts for this route name
                                foreach ($routeNameCounts as $countEntry) {
                                    if ($detail->routes_name == $countEntry['route_name']) {
                                        $counts = ($type === 'Incoming') ? ($countEntry['incoming_counts'] ?? []) : ($countEntry['outgoing_counts'] ?? []);
        
                                        // Fill counts per time column
                                        foreach ($config['allowedTimes'] as $timeIndex => $time) {
                                            $countValue = $counts[$time] ?? 0;
                                            $sheet->setCellValue($col[3 + $timeIndex] . $currentRow, $countValue);
                                        }
        
                                        // Shuttle allocation & provider from details if exists
                                        $shuttleAllocationCol = $col[3 + count($config['allowedTimes'])];
                                        $shuttleProviderCol = $col[4 + count($config['allowedTimes'])];
        
                                        // $sheet->setCellValue($shuttleAllocationCol . $currentRow, $detail->shuttle_allocation ?? '');
                                        // $sheet->setCellValue($shuttleProviderCol . $currentRow, $detail->shuttle_provider_info->shuttle_provider_name ?? '');
        
                                        break;
                                    }
                                }
                                $currentRow++;
                            }
                        } else {
                            // No details, just blank cells with border
                            $sheet->getStyle("{$col[2]}{$currentRow}:{$col[count($col) - 1]}{$currentRow}")
                                ->applyFromArray($border + $textAlignCenter + $font10Arial)
                                ->getAlignment()->setWrapText(true);
                        }
        
                        $startRow = $endRow + 1;
                    }
                }
            },
        ];
        
    }
}
