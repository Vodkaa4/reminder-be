<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PermitsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithEvents
{
    protected Collection $records;

    public function __construct(Collection $records)
    {
        $this->records = $records;
    }

    public function collection(): Collection
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            'No',
            'Type',
            'Number',
            'Holder',
            'Asset Location',
            'Issued At',
            'Expires At',
            'PIC Email',
            'Status',
            'Notes',
        ];
    }

    public function map($record): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $record->type,
            $record->number,
            $record->holder,
            $record->asset_location,
            optional($record->issued_at)?->format('d/m/Y'),
            optional($record->expires_at)?->format('d/m/Y'),
            $record->pic,
            strtoupper($record->status),
            $record->notes,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A5F'],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet        = $event->sheet->getDelegate();
                $highestCol   = $sheet->getHighestColumn();
                $highestRow   = $sheet->getHighestRow();

                // Freeze header
                $sheet->freezePane('A2');

                // Auto filter
                $sheet->setAutoFilter("A1:{$highestCol}1");

                // Center & middle all cells
                $sheet->getStyle("A1:{$highestCol}{$highestRow}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Thin borders
                $sheet->getStyle("A1:{$highestCol}{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // Zebra stripes
                for ($i = 2; $i <= $highestRow; $i++) {
                    if ($i % 2 === 0) {
                        $sheet->getStyle("A{$i}:{$highestCol}{$i}")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB('FFF0F4FA');
                    }
                }
            },
        ];
    }
}
