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

class ReminderLogsExport implements
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
            'Entity',
            'Entity ID',
            'Target Date',
            'Days Before',
            'Recipient',
            'Channel',
            'Status',
            'Sent At',
        ];
    }

    public function map($record): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            ucfirst($record->entity),
            $record->entity_id,
            optional($record->target_date)?->format('d/m/Y'),
            $record->rule_days . ' days',
            $record->recipient,
            strtoupper($record->channel),
            strtoupper($record->status),
            optional($record->created_at)?->format('d/m/Y H:i'),
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
                $sheet      = $event->sheet->getDelegate();
                $highestCol = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                $sheet->freezePane('A2');
                $sheet->setAutoFilter("A1:{$highestCol}1");

                $sheet->getStyle("A1:{$highestCol}{$highestRow}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle("A1:{$highestCol}{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

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
