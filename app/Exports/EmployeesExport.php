<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class EmployeesExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithColumnFormatting,
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

    public function map($record): array
    {
        return [
            $record->nip,
            $record->name,
            $record->email,
            $record->dept,
            $record->sect,
            $record->position,
            $record->location,
            $record->is_permanent ? 'Tetap' : 'Kontrak',
            optional($record->contract_start)?->format('Y-m-d'),
            optional($record->contract_end)?->format('Y-m-d'),
            optional($record->resign_date)?->format('Y-m-d'),
        ];
    }

    public function headings(): array
    {
        return ['NIP','Nama','Email','Departemen','Bagian','Jabatan','Lokasi','Status','Tanggal Mulai Kontrak','Tanggal Akhir Kontrak','Tanggal Resign'];
    }

    public function columnFormats(): array
    {
        return [
            'I' => 'yyyy-mm-dd',
            'J' => 'yyyy-mm-dd',
            'K' => 'yyyy-mm-dd',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Bold header row
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Freeze header row
                $sheet->freezePane('A2');

                // Apply auto filter on header row
                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();
                $sheet->setAutoFilter("A1:{$highestColumn}1");

                // Set alignment for all cells
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Add thin borders to all cells
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
