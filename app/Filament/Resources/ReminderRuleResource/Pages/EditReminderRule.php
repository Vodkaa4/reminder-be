<?php

namespace App\Filament\Resources\ReminderRuleResource\Pages;

use App\Filament\Resources\ReminderRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReminderRule extends EditRecord
{
    protected static string $resource = ReminderRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
