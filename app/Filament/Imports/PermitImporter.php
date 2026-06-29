<?php

namespace App\Filament\Imports;

use App\Models\Permit;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class PermitImporter extends Importer
{
    protected static ?string $model = Permit::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('number')
                ->label('Nomor')
                ->exampleHeader('Nomor')
                ->guess(['Nomor', 'nomor', 'number'])
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('type')
                ->label('Jenis Izin')
                ->exampleHeader('Jenis Izin')
                ->guess(['Jenis Izin', 'jenis izin', 'Jenis', 'type'])
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('holder')
                ->label('Pemegang')
                ->exampleHeader('Pemegang')
                ->guess(['Pemegang', 'pemegang', 'holder'])
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('asset_location')
                ->label('Lokasi Aset')
                ->exampleHeader('Lokasi Aset')
                ->guess(['Lokasi Aset', 'lokasi aset', 'Lokasi', 'asset_location'])
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('issued_at')
                ->label('Tanggal Terbit')
                ->exampleHeader('Tanggal Terbit')
                ->guess(['Tanggal Terbit', 'tanggal terbit', 'Diterbitkan', 'issued_at'])
                ->rules(['nullable', 'date']),
            ImportColumn::make('expires_at')
                ->label('Tanggal Kedaluwarsa')
                ->exampleHeader('Tanggal Kedaluwarsa')
                ->guess(['Tanggal Kedaluwarsa', 'tanggal kedaluwarsa', 'Kedaluwarsa', 'expires_at'])
                ->rules(['nullable', 'date']),
            ImportColumn::make('pic')
                ->label('Email PIC')
                ->exampleHeader('Email PIC')
                ->guess(['Email PIC', 'email pic', 'PIC', 'pic'])
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('notes')
                ->label('Catatan')
                ->exampleHeader('Catatan')
                ->guess(['Catatan', 'catatan', 'notes'])
                ->rules(['nullable']),
        ];
    }

    public function resolveRecord(): ?Permit
    {
        return Permit::firstOrNew([
            'number' => $this->data['number'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your permit import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
