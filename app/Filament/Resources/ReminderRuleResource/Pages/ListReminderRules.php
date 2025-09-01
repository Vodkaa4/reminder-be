<?php

namespace App\Filament\Resources\ReminderRuleResource\Pages;

use App\Filament\Resources\ReminderRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReminderRules extends ListRecords
{
    protected static string $resource = ReminderRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
