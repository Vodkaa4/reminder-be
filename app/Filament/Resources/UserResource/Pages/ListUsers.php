<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Facades\Filament;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Users';

    protected static ?string $description = 'Manage all users in the system';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add New User')
                ->icon('heroicon-o-plus')
                ->visible(fn () => optional(Filament::auth()->user())->role !== 'manager'),
        ];
    }
}
