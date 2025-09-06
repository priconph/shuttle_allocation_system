<?php

namespace App\Exports\Sheet;
use DateTime;
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
        
                // Helper: Excel column by index (0=A, 1=B, ...26 = AA)
                $getExcelColumn = function ($index) {
                    $letters = '';
                    while ($index >= 0) {
                        $letters = chr($index % 26 + 65) . $letters;
                        $index = intdiv($index, 26) - 1;
                    }
                    return $letters;
                };
        
                /**
                 * Build one table (headers + rows) and return the last row used
                 */
                $buildTable = function ($table) use (
                    $sheet,
                    $route_code,
                    $routeNameCounts,
                    $factory,
                    $border,
                    $textAlignCenter,
                    $textAlignLeft,
                    $font10Arial,
                    $font10ArialBold,
                    $font12ArialBold,
                    $font14ArialBold,
                    $getExcelColumn
                ) {
                    $colStartIndex = ord($table['startCol']) - 65;
        
                    // Define columns (6 fixed)
                    $columns = [];
                    for ($i = 0; $i < 6; $i++) {
                        $columns[] = $getExcelColumn($colStartIndex + $i);
                    }
        
                    // Headers row index
                    $headerRow = $table['startRow'] + 2;
        
                    // Headers
                    $headers = [
                        $columns[0] . $headerRow => "Letter \nCode",
                        $columns[1] . $headerRow => 'Route Name',
                        $columns[2] . $headerRow => 'Pick-Up Points',
                        $columns[3] . $headerRow => $table['time'], // only one time slot
                        $columns[4] . $headerRow => 'SHUTTLE ALLOCATION',
                        $columns[5] . $headerRow => 'SHUTTLE PROVIDER',
                    ];
        
                    // Merge top headers
                    $merge1 = "{$columns[0]}{$table['startRow']}:{$columns[2]}" . ($table['startRow'] + 1);
                    $merge2 = "{$columns[3]}{$table['startRow']}:{$columns[5]}" . ($table['startRow'] + 1);
        
                    $sheet->mergeCells($merge1);
                    $sheet->mergeCells($merge2);
        
                    // Title text
                    $sheet->setCellValue($columns[0] . $table['startRow'], " {$table['location']} {$table['type']} ");
                    // $sheet->setCellValue($columns[3] . $table['startRow'], "Factory {$factory}");
        
                    // Set headers
                    foreach ($headers as $cell => $text) {
                        $sheet->setCellValue($cell, $text);
                    }
        
                    // Fill & Style
                    $headerFillRange = "{$columns[0]}{$table['startRow']}:{$columns[5]}" . ($table['startRow'] + 2);
                    $sheet->getStyle($headerFillRange)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('B7D8FF');
        
                    $sheet->getStyle("{$columns[0]}{$table['startRow']}:{$columns[2]}" . ($table['startRow'] + 1))
                        ->applyFromArray($textAlignLeft + $font14ArialBold);
        
                    $sheet->getStyle("{$columns[0]}{$headerRow}:{$columns[5]}{$headerRow}")
                        ->applyFromArray($textAlignCenter + $font12ArialBold + $border);
        
                    // Set widths
                    $sheet->getColumnDimension($columns[1])->setWidth(20);
                    $sheet->getColumnDimension($columns[2])->setWidth(30);
                    $sheet->getColumnDimension($columns[3])->setWidth(12);
                    $sheet->getColumnDimension($columns[4])->setWidth(20);
                    $sheet->getColumnDimension($columns[5])->setWidth(20);
        
                    // Fill data rows
                    $startRow = $table['startRow'] + 3;
                    foreach ($route_code as $route) {
                        $details = is_iterable($route->routes_details) ? $route->routes_details : [];
                        $detailCount = count($details);
                        $endRow = $detailCount ? $startRow + $detailCount - 1 : $startRow;
        
                        // Code + Destination
                        $sheet->setCellValue("{$columns[0]}{$startRow}", $route->routes_code);
                        $sheet->setCellValue("{$columns[1]}{$startRow}", $route->routes_destination);
                        $sheet->getStyle("{$columns[0]}{$startRow}:{$columns[1]}{$startRow}")
                            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($route->color_code);
        
                        // Merge vertical cells if multiple details
                        if ($detailCount) {
                            foreach ([$columns[0], $columns[1], $columns[4], $columns[5]] as $mergeCol) {
                                $sheet->mergeCells("{$mergeCol}{$startRow}:{$mergeCol}{$endRow}");
                            }
                        }
        
                        // Apply style for Code & Destination
                        $sheet->getStyle("{$columns[0]}{$startRow}:{$columns[1]}{$endRow}")
                            ->applyFromArray($border + $textAlignCenter + $font10ArialBold)
                            ->getAlignment()->setWrapText(true);
        
                        // Fill route details
                        $currentRow = $startRow;
                        if ($detailCount) {
                            foreach ($details as $detail) {
                                $sheet->setCellValue("{$columns[2]}{$currentRow}", $detail->routes_name);
        
                                // Find counts for this time
                                foreach ($routeNameCounts as $countEntry) {
                                    if ($detail->routes_name == $countEntry['route_name']) {
                                        $counts = ($table['type'] === 'Incoming')
                                            ? ($countEntry['incoming_counts'] ?? [])
                                            : ($countEntry['outgoing_counts'] ?? []);
                                        $countValue = $counts[$table['time']] ?? 0;
                                        $sheet->setCellValue($columns[3] . $currentRow, $countValue);
                                        break;
                                    }
                                }
        
                                // Style
                                $sheet->getStyle("{$columns[2]}{$currentRow}:{$columns[5]}{$currentRow}")
                                    ->applyFromArray($border + $textAlignCenter + $font10Arial)
                                    ->getAlignment()->setWrapText(true);
        
                                $currentRow++;
                            }
                        } else {
                            // No details → blank row with border
                            $sheet->getStyle("{$columns[2]}{$startRow}:{$columns[5]}{$startRow}")
                                ->applyFromArray($border + $textAlignCenter + $font10Arial)
                                ->getAlignment()->setWrapText(true);
                        }
        
                        $startRow = $endRow + 1;
                    }
        
                    return $startRow - 1; // last row used
                };
        
                // --------------------------
                // Define table layout
                // --------------------------
        
                // First row (side by side)
                $firstRowTables = [
                    ['type' => 'Incoming', 'time' => '07:30 AM', 'location' => $from, 'startCol' => 'A'],
                    ['type' => 'Outgoing', 'time' => '03:30 PM', 'location' => $to,   'startCol' => 'H'],
                    ['type' => 'Outgoing', 'time' => '04:30 PM', 'location' => $to,   'startCol' => 'O'],
                    ['type' => 'Outgoing', 'time' => '07:30 PM', 'location' => $to,   'startCol' => 'V'],
                ];
        
                // Build first row
                $maxRowUsed = 1;
                foreach ($firstRowTables as $table) {
                    $table['startRow'] = 1;
                    $lastRow = $buildTable($table);
                    $maxRowUsed = max($maxRowUsed, $lastRow);
                }
        
                // Second row (below first row)
                $secondRowStart = $maxRowUsed + 2; // leave 1 blank row
                $toPlusOneDay = (new DateTime($to))->modify('+1 day')->format('Y-m-d');
                $secondRowTables = [
                    ['type' => 'Incoming', 'time' => '07:30 PM', 'location' => $from, 'startCol' => 'A', 'startRow' => $secondRowStart],
                    ['type' => 'Outgoing', 'time' => '07:30 AM', 'location' => $toPlusOneDay,   'startCol' => 'H', 'startRow' => $secondRowStart],
                ];
        
                foreach ($secondRowTables as $table) {
                    $buildTable($table);
                }
            },
        ];

        // return [
        //     // $routeDestinationFinalCount,
        //     AfterSheet::class => function (AfterSheet $event) use (
        //         $routeNameCounts,
        //         $factory,
        //         $from,
        //         $to,
        //         $route_code,
        //         $border,
        //         $textAlignCenter,
        //         $textAlignLeft,
        //         $font10Arial,
        //         $font10ArialBold,
        //         $font12ArialBold,
        //         $font14ArialBold
        //     ) {
        //         $sheet = $event->sheet->getDelegate();
        
        //         // Allowed times per type
        //         $allowedIncomingTimes = ['07:30 AM', '07:30 PM'];
        //         $allowedOutgoingTimes = ['03:30 PM', '04:30 PM', '07:30 PM', '07:30 AM'];
        
        //         // Helper to get Excel column letter by index (0=A, 1=B, ...)
        //         $getExcelColumn = function($index) {
        //             $letters = '';
        //             while ($index >= 0) {
        //                 $letters = chr($index % 26 + 65) . $letters;
        //                 $index = intdiv($index, 26) - 1;
        //             }
        //             return $letters;
        //         };
        
        //         $setUp = [
        //             'Incoming' => [
        //                 'location' => $from,
        //                 'startCol' => 'A',
        //                 'baseColumns' => ['A', 'B', 'C'],  // Letter Code, Route Name, Pick-Up Points
        //                 'allowedTimes' => $allowedIncomingTimes,
        //                 'fixedEndColsCount' => 2,  // Shuttle Allocation & Provider
        //                 'widths' => [
        //                     'B' => 20, 'C' => 30,
        //                 ],
        //             ],
        //             'Outgoing' => [
        //                 'location' => $to,
        //                 'startCol' => 'I',
        //                 'baseColumns' => ['I', 'J', 'K'],
        //                 'allowedTimes' => $allowedOutgoingTimes,
        //                 'fixedEndColsCount' => 2,
        //                 'widths' => [
        //                     'J' => 20, 'K' => 30,
        //                 ],
        //             ]
        //         ];
        
        //         // Prepare dynamic columns, headers, merges, fills, and widths
        //         foreach ($setUp as $type => &$config) {
        //             $startIndex = ord($config['startCol']) - 65;
        //             $columns = $config['baseColumns'];
        
        //             $timeColStartIndex = $startIndex + count($config['baseColumns']);
        //             foreach ($config['allowedTimes'] as $i => $time) {
        //                 $columns[] = $getExcelColumn($timeColStartIndex + $i);
        //             }
        
        //             $lastColsStartIndex = $timeColStartIndex + count($config['allowedTimes']);
        //             for ($i = 0; $i < $config['fixedEndColsCount']; $i++) {
        //                 $columns[] = $getExcelColumn($lastColsStartIndex + $i);
        //             }
        
        //             $config['columns'] = $columns;
        
        //             // Set header cells
        //             $config['headerCells'] = [];
        //             $config['headerCells'][$columns[0] . '3'] = "Letter \nCode";
        //             $config['headerCells'][$columns[1] . '3'] = 'Route Name';
        //             $config['headerCells'][$columns[2] . '3'] = 'Pick-Up Points';
        
        //             foreach ($config['allowedTimes'] as $i => $time) {
        //                 $col = $columns[3 + $i];
        //                 $config['headerCells']["{$col}3"] = $time;
        //                 $config['widths'][$col] = 12;  // Width for time columns
        //             }
        
        //             $lastBaseIndex = 3 + count($config['allowedTimes']);
        //             $config['headerCells'][$columns[$lastBaseIndex] . '3'] = 'SHUTTLE ALLOCATION';
        //             $config['headerCells'][$columns[$lastBaseIndex + 1] . '3'] = 'SHUTTLE PROVIDER';
        
        //             $config['widths'][$columns[$lastBaseIndex]] = 20;
        //             $config['widths'][$columns[$lastBaseIndex + 1]] = 20;
        
        //             // Merge cells for subheaders
        //             $merge1Start = $columns[0] . '1';
        //             $merge1End = $columns[count($config['baseColumns']) - 1] . '2';
        
        //             $merge2Start = $columns[count($config['baseColumns'])] . '1';
        //             $merge2End = end($columns) . '2';
        
        //             $config['mergeCells'] = ["{$merge1Start}:{$merge1End}", "{$merge2Start}:{$merge2End}"];
        
        //             // Header fill range
        //             $config['headerFillRange'] = ["{$columns[0]}1:{$columns[count($columns) - 1]}3" => 'B7D8FF'];
        
        //             $config['mainStyleRange'] = "{$merge1Start}:{$merge1End}";
        //             $config['headerStyleRange'] = "{$columns[0]}3:{$columns[count($columns) - 1]}3";
        //         }
        //         unset($config);
        
        //         // Now generate tables per Incoming and Outgoing
        //         foreach ($setUp as $type => $config) {
        //             // Fill header fills
        //             foreach ($config['headerFillRange'] as $range => $color) {
        //                 $sheet->getStyle($range)->getFill()
        //                     ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        //                     ->getStartColor()->setARGB($color);
        //             }
        
        //             // Set widths
        //             foreach ($config['widths'] as $col => $width) {
        //                 $sheet->getColumnDimension($col)->setWidth($width);
        //             }
        
        //             // Merge header cells
        //             foreach ($config['mergeCells'] as $mergeRange) {
        //                 $sheet->mergeCells($mergeRange);
        //             }
        
        //             // Set header texts
        //             foreach ($config['headerCells'] as $cell => $text) {
        //                 $sheet->setCellValue($cell, $text);
        //             }
        
        //             list($topLeft1,) = explode(':', $config['mergeCells'][0]);
        //             // list($topLeft2,) = explode(':', $config['mergeCells'][1]);
        
        //             $sheet->setCellValue($topLeft1, ' ' . $config['location'] . "  {$type}  ");
        //             // $sheet->setCellValue($topLeft2, 'Factory ' . $factory);
        
        //             // Apply styles to headers
        //             $sheet->getStyle($config['mainStyleRange'])->applyFromArray($textAlignLeft + $font14ArialBold);
        //             $sheet->getStyle($config['headerStyleRange'])->applyFromArray($textAlignCenter + $font12ArialBold + $border);
        
        //             $startRow = 4;
        
        //             foreach ($route_code as $route) {
        //                 $details = is_iterable($route->routes_details) ? $route->routes_details : [];
        //                 $detailCount = count($details);
        //                 $endRow = $detailCount ? $startRow + $detailCount - 1 : $startRow;
        //                 $col = $config['columns'];
        
        //                 // Route Code & Destination with background color
        //                 $sheet->setCellValue("{$col[0]}{$startRow}", $route->routes_code);
        //                 $sheet->setCellValue("{$col[1]}{$startRow}", $route->routes_destination);
        //                 $sheet->getStyle("{$col[0]}{$startRow}:{$col[1]}{$startRow}")
        //                     ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        //                     ->getStartColor()->setARGB($route->color_code);
        
        //                 // Merge vertical cells for certain columns if multiple details exist
        //                 if ($detailCount) {
        //                     foreach ([$col[0], $col[1], $col[count($col) - 2], $col[count($col) - 1]] as $mergeCol) {
        //                         $sheet->mergeCells("{$mergeCol}{$startRow}:{$mergeCol}{$endRow}");
        //                     }
        //                 }
        
        //                 $sheet->getStyle("{$col[0]}{$startRow}:{$col[1]}{$endRow}")
        //                     ->applyFromArray($border + $textAlignCenter + $font10ArialBold)
        //                     ->getAlignment()->setWrapText(true);
        
        //                 $currentRow = $startRow;
        
        //                 if ($detailCount) {
        //                     for ($i = 0; $i < $detailCount; $i++) {
        //                         $detail = $details[$i];
        //                         $sheet->setCellValue("{$col[2]}{$currentRow}", "\n  " . $detail->routes_name);
        //                         $sheet->getStyle("{$col[2]}{$currentRow}:{$col[count($col) - 1]}{$currentRow}")
        //                             ->applyFromArray($border + $textAlignCenter + $font10Arial)
        //                             ->getAlignment()->setWrapText(true);
        
        //                         // Find matching routeNameCounts for this route name
        //                         foreach ($routeNameCounts as $countEntry) {
        //                             if ($detail->routes_name == $countEntry['route_name']) {
        //                                 $counts = ($type === 'Incoming') ? ($countEntry['incoming_counts'] ?? []) : ($countEntry['outgoing_counts'] ?? []);
        
        //                                 // Fill counts per time column
        //                                 foreach ($config['allowedTimes'] as $timeIndex => $time) {
        //                                     $countValue = $counts[$time] ?? 0;
        //                                     $sheet->setCellValue($col[3 + $timeIndex] . $currentRow, $countValue);
        //                                 }
        
        //                                 // Shuttle allocation & provider from details if exists
        //                                 $shuttleAllocationCol = $col[3 + count($config['allowedTimes'])];
        //                                 $shuttleProviderCol = $col[4 + count($config['allowedTimes'])];
        
        //                                 // $sheet->setCellValue($shuttleAllocationCol . $currentRow, $detail->shuttle_allocation ?? '');
        //                                 // $sheet->setCellValue($shuttleProviderCol . $currentRow, $detail->shuttle_provider_info->shuttle_provider_name ?? '');
        
        //                                 break;
        //                             }
        //                         }
        //                         $currentRow++;
        //                     }
        //                 } else {
        //                     // No details, just blank cells with border
        //                     $sheet->getStyle("{$col[2]}{$currentRow}:{$col[count($col) - 1]}{$currentRow}")
        //                         ->applyFromArray($border + $textAlignCenter + $font10Arial)
        //                         ->getAlignment()->setWrapText(true);
        //                 }
        
        //                 $startRow = $endRow + 1;
        //             }
        //         }
        //     },
        // ];
    }
}
