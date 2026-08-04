<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeesExport;
use App\Models\Employee;
use Filament\Facades\Filament;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => optional(Filament::auth()->user())->role !== 'manager'),
            Actions\ImportAction::make()
                ->importer(\App\Filament\Imports\EmployeeImporter::class)
                ->label('Import Excel/CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->visible(fn () => optional(Filament::auth()->user())->role !== 'manager'),
            Actions\Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return Excel::download(new EmployeesExport(Employee::query()->get()), 'employees.csv');
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Semua' => \Filament\Resources\Components\Tab::make(),
            'Karyawan Tetap' => \Filament\Resources\Components\Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('is_permanent', true)),
            'Kontrak Aktif' => \Filament\Resources\Components\Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('is_permanent', false)->where('contract_end', '>', today()->addDays(30))),
            'Mendekati Kadaluarsa' => \Filament\Resources\Components\Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('is_permanent', false)->where('contract_end', '<=', today()->addDays(30))->where('contract_end', '>=', today())),
            'Kadaluarsa' => \Filament\Resources\Components\Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('is_permanent', false)->where('contract_end', '<', today())),
        ];
    }
}
