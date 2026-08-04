<?php

namespace App\Filament\Resources\PermitResource\Pages;

use App\Filament\Resources\PermitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Facades\Filament;

class ListPermits extends ListRecords
{
    protected static string $resource = PermitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => optional(Filament::auth()->user())->role !== 'manager'),
            Actions\ImportAction::make()
                ->importer(\App\Filament\Imports\PermitImporter::class)
                ->label('Import Excel/CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->visible(fn () => optional(Filament::auth()->user())->role !== 'manager'),
            Actions\Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PermitsExport(\App\Models\Permit::query()->get()), 'permits.csv');
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Semua' => \Filament\Resources\Components\Tab::make(),
            'Aktif' => \Filament\Resources\Components\Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'active')),
            'Masa Perpanjangan' => \Filament\Resources\Components\Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'renewal')),
            'Kedaluwarsa' => \Filament\Resources\Components\Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'expired')),
        ];
    }
}
