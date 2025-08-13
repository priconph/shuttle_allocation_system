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

class Allocation implements FromView, ShouldAutoSize, WithEvents, WithTitle
{
    use Exportable;

    protected $routeNameCounts;
    protected $routeDestinationFinalCount;
    protected $factory;
    protected $incoming;
    protected $outgoing;
    protected $from;
    protected $to;
    protected $route_code;

    public function __construct(
        $routeNameCounts,
        $routeDestinationFinalCount,
        $factory,
        $incoming,
        $outgoing,
        $from,
        $to,
        $route_code
    )
    {
        $this->routeNameCounts              = $routeNameCounts;
        $this->routeDestinationFinalCount   = $routeDestinationFinalCount;
        $this->factory                      = $factory;
        $this->incoming                     = $incoming;
        $this->outgoing                     = $outgoing;
        $this->from                         = $from;
        $this->to                           = $to;
        $this->route_code                   = $route_code;    
    }

    public function view(): View
    {
        return view('exports.overall', 
                [
                    'routeNameCounts'               => $this->routeNameCounts,
                    'routeDestinationFinalCount'    => $this->routeDestinationFinalCount,
                    'factory'                       => $this->factory,
                    'incoming'                      => $this->incoming,
                    'outgoing'                      => $this->outgoing,
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
        $routeDestinationFinalCount =   $this->routeDestinationFinalCount;      
        $factory                    =   $this->factory;      
        $incoming                   =   $this->incoming;    
        $outgoing                   =   $this->outgoing;    
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
                $routeDestinationFinalCount,
                $factory,
                $incoming,
                $outgoing,
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
        
                $setUp = [
                    'Incoming' => [
                        'label' => $incoming,
                        'location' => $from,
                        'startCol' => 'A',
                        'columns' => ['A', 'B', 'C', 'D', 'E', 'F'],
                        'widths' => [
                            'C' => 30, 'E' => 30,
                            'B' => 20, 'F' => 20
                        ],
                        'mergeCells' => ['A1:C2', 'D1:F2'],
                        'headerStyleRange' => 'A3:F3',
                        'mainStyleRange' => 'A1:D1',
                        'headerFillRange' => ['A1:F3' => 'B7D8FF'],
                        'headerCells' => [
                            'A3' => "Letter \nCode",
                            'B3' => 'Route Name',
                            'C3' => 'Pick-Up Points',
                            'D3' => 'Head count',
                            'E3' => 'SHUTTLE ALLOCATION',
                            'F3' => 'SHUTTLE PROVIDER',
                        ]
                    ],
                    'Outgoing' => [
                        'label' => $outgoing,
                        'location' => $to,
                        'startCol' => 'H',
                        'columns' => ['H', 'I', 'J', 'K', 'L', 'M'],
                        'widths' => [
                            'J' => 30, 'L' => 30,
                            'I' => 20, 'M' => 20
                        ],
                        'mergeCells' => ['H1:J2', 'K1:M2'],
                        'headerStyleRange' => 'H3:M3',
                        'mainStyleRange' => 'H1:K1',
                        'headerFillRange' => ['H1:M3' => 'B7D8FF'],
                        'headerCells' => [
                            'H3' => "Letter \nCode",
                            'I3' => 'Route Name',
                            'J3' => 'Pick-Up Points',
                            'K3' => 'Head count',
                            'L3' => 'SHUTTLE ALLOCATION',
                            'M3' => 'SHUTTLE PROVIDER',
                        ]
                    ]
                ];
                
                foreach ($setUp as $type => $config) {
                    // Set fills for header and subheader
                    foreach ($config['headerFillRange'] as $range => $color) {
                        $sheet->getStyle($range)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($color);
                    }

                    foreach ($config['widths'] as $col => $width) {
                        $sheet->getColumnDimension($col)->setWidth($width);
                    }

                    foreach ($config['mergeCells'] as $mergeRange) {
                        $sheet->mergeCells($mergeRange);
                    }

                    foreach ($config['headerCells'] as $cell => $text) {
                        $sheet->setCellValue($cell, $text);
                    }

                    list($topLeft1,) = explode(':', $config['mergeCells'][0]);
                    list($topLeft2,) = explode(':', $config['mergeCells'][1]);

                    $sheet->setCellValue($topLeft1, ' ' . $config['location'] . "  {$type}  " . $config['label'] . ' ');
                    $sheet->setCellValue($topLeft2, 'Factory ' . $factory);

                    // Apply styles
                    $sheet->getStyle($config['mainStyleRange'])->applyFromArray($textAlignLeft + $font14ArialBold);
                    $sheet->getStyle($config['headerStyleRange'])->applyFromArray($textAlignCenter + $font12ArialBold + $border);

                    // Fill route data
                    $startRow = 4;
                    for ($a=0; $a < count($route_code); $a++) { 
                        $details = is_iterable($route_code[$a]->routes_details) ? $route_code[$a]->routes_details : [];

                        $detailCount = count($details);
                        $endRow = $detailCount ? $startRow + $detailCount - 1 : $startRow;
                        $col = $config['columns'];
                        $sheet->setCellValue("{$col[0]}{$startRow}", $route_code[$a]->routes_code);
                        $sheet->setCellValue("{$col[1]}{$startRow}", $route_code[$a]->routes_destination);
                        $sheet->getStyle("{$col[0]}{$startRow}:{$col[1]}{$startRow}")
                            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($route_code[$a]->color_code);

                        if ($detailCount) {
                            foreach ([$col[0], $col[1], $col[4], $col[5]] as $mergeCol) {
                                $sheet->mergeCells("{$mergeCol}{$startRow}:{$mergeCol}{$endRow}");
                            }
                        }

                        $sheet->getStyle("{$col[0]}{$startRow}:{$col[1]}{$endRow}")
                            ->applyFromArray($border + $textAlignCenter + $font10ArialBold)
                            ->getAlignment()->setWrapText(true); 

                        $currentRow = $startRow;

                        if ($detailCount) {   
                            $capacity = 0;    
                            for ($i=0; $i < count($details); $i++) {
                                if ($details[$i]->shuttle_provider_info != null) {    
                                    $capacity = $details[$i]->shuttle_provider_info->shuttle_provider_capacity ?? 0;
                                }
                                $sheet->setCellValue("{$col[2]}{$currentRow}", "\n  " . $details[$i]->routes_name);
                                $sheet->getStyle("{$col[2]}{$currentRow}:{$col[5]}{$currentRow}")
                                    ->applyFromArray($border + $textAlignCenter + $font10Arial)
                                    ->getAlignment()->setWrapText(true);

                                for ($ii=0; $ii < count($routeNameCounts); $ii++) {
                                    if($details[$i]->routes_name == $routeNameCounts[$ii]['route_name']){
                                        if($type === 'Incoming'){
                                            $sheet->setCellValue("{$col[3]}{$currentRow}", $routeNameCounts[$ii]['incoming_count']);
                                        }else {
                                            $sheet->setCellValue("{$col[3]}{$currentRow}", $routeNameCounts[$ii]['outgoing_count']);
                                        }
                                    }
                                }
                                $currentRow++;
                            }
                        } else {
                            $sheet->getStyle("{$col[2]}{$startRow}:{$col[5]}{$startRow}")
                                ->applyFromArray($border + $textAlignCenter + $font10Arial)
                                ->getAlignment()->setWrapText(true);
                        }
                        $startRow = $endRow + 1;
                    }
                }
            }
        ];
    }
}
