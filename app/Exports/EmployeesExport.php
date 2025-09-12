<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeesExport implements FromCollection, WithHeadings
{
    protected Collection $records;

    public function __construct(Collection $records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records->map(fn($record) => [
            'NIP' => $record->nip,
            'Name' => $record->name,
            'Email' => $record->email,
            'Department' => $record->dept,
            'Section' => $record->sect,
            'Position' => $record->position,
            'Location' => $record->location,
            'Status' => $record->is_permanent ? 'Permanent' : 'Contract',
            'Contract Start' => $record->contract_start?->format('Y-m-d'),
            'Contract End' => $record->contract_end?->format('Y-m-d'),
            'Resign Date' => $record->resign_date?->format('Y-m-d'),
        ]);
    }

    public function headings(): array
    {
        return ['NIP','Name','Email','Department','Section','Position','Location','Status','Contract Start','Contract End','Resign Date'];
    }
}
