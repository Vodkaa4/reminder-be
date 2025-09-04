<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReminderRuleResource\Pages;
use App\Filament\Resources\ReminderRuleResource\RelationManagers;
use App\Models\ReminderRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReminderRuleResource extends Resource
{
    protected static ?string $model = ReminderRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('entity')
                    ->options([
                        'contract' => 'Kontrak Karyawan',
                        'permit' => 'Perizinan (SIM, STNK, KIR, dll)'
                    ])
                    ->required(),
                Forms\Components\TextInput::make('days_before')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('channel')
                    ->options([
                        'email' => 'Email',
                        'whatsapp' => 'WhatsApp',
                        'both' => 'Email & WhatsApp'
                    ])
                    ->required(),
                Forms\Components\Toggle::make('active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entity')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'contract' => 'Kontrak Karyawan',
                        'permit' => 'Perizinan',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('days_before')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('channel')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'email' => 'Email',
                        'whatsapp' => 'WhatsApp',
                        'both' => 'Email & WhatsApp',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReminderRules::route('/'),
            'create' => Pages\CreateReminderRule::route('/create'),
            'edit' => Pages\EditReminderRule::route('/{record}/edit'),
        ];
    }
}
