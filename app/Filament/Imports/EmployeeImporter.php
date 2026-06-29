<?php

namespace App\Filament\Imports;

use App\Models\Employee;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class EmployeeImporter extends Importer
{
    protected static ?string $model = Employee::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nip')
                ->label('NIP')
                ->exampleHeader('NIP')
                ->guess(['NIP', 'nip', 'Nomor Induk Pegawai'])
                ->requiredMapping()
                ->rules(['required', 'max:50']),
            ImportColumn::make('name')
                ->label('Nama')
                ->exampleHeader('Nama')
                ->guess(['Nama', 'nama', 'Nama Lengkap'])
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('email')
                ->label('Email')
                ->exampleHeader('Email')
                ->guess(['Email', 'email', 'Alamat Email'])
                ->rules(['nullable', 'email', 'max:255']),
            ImportColumn::make('supervisor')
                ->label('Atasan')
                ->exampleHeader('Atasan')
                ->guess(['Atasan', 'atasan', 'Supervisor'])
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('is_permanent')
                ->label('Karyawan Tetap (1=Tetap, 0=Kontrak)')
                ->exampleHeader('Karyawan Tetap (1=Tetap, 0=Kontrak)')
                ->guess(['Karyawan Tetap', 'karyawan tetap', 'Karyawan Tetap (1=Tetap, 0=Kontrak)', 'is_permanent'])
                ->boolean()
                ->rules(['nullable', 'boolean']),
            ImportColumn::make('contract_start')
                ->label('Tanggal Mulai Kontrak')
                ->exampleHeader('Tanggal Mulai Kontrak')
                ->guess(['Tanggal Mulai Kontrak', 'Mulai Kontrak', 'contract_start'])
                ->rules(['nullable', 'date']),
            ImportColumn::make('contract_end')
                ->label('Tanggal Akhir Kontrak')
                ->exampleHeader('Tanggal Akhir Kontrak')
                ->guess(['Tanggal Akhir Kontrak', 'Akhir Kontrak', 'contract_end'])
                ->rules(['nullable', 'date']),
            ImportColumn::make('resign_date')
                ->label('Tanggal Resign')
                ->exampleHeader('Tanggal Resign')
                ->guess(['Tanggal Resign', 'Resign', 'resign_date'])
                ->rules(['nullable', 'date']),
            ImportColumn::make('dept')
                ->label('Departemen')
                ->exampleHeader('Departemen')
                ->guess(['Departemen', 'Dept', 'dept'])
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('sect')
                ->label('Bagian')
                ->exampleHeader('Bagian')
                ->guess(['Bagian', 'Sect', 'sect'])
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('position')
                ->label('Jabatan')
                ->exampleHeader('Jabatan')
                ->guess(['Jabatan', 'Position', 'position'])
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('location')
                ->label('Lokasi')
                ->exampleHeader('Lokasi')
                ->guess(['Lokasi', 'Location', 'location'])
                ->rules(['nullable', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Employee
    {
        return Employee::firstOrNew([
            'nip' => $this->data['nip'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your employee import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
