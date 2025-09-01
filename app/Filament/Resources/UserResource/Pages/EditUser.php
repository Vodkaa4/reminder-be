<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Edit User';

    protected static ?string $description = 'Update user information';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('verify_email')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->label('Verify Email')
                ->requiresConfirmation()
                ->visible(fn () => is_null($this->record->email_verified_at))
                ->action(function () {
                    $this->record->update(['email_verified_at' => now()]);
                    $this->notify('success', 'Email verified successfully!');
                }),
            Actions\DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'User updated successfully!';
    }
}
