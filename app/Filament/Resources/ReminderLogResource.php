<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReminderLogResource\Pages;
use App\Filament\Resources\ReminderLogResource\RelationManagers;
use App\Models\ReminderLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReminderLogResource extends Resource
{
    protected static ?string $model = ReminderLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('entity')
                    ->required(),
                Forms\Components\TextInput::make('entity_id')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('target_date')
                    ->required(),
                Forms\Components\TextInput::make('rule_days')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('recipient')
                    ->maxLength(191),
                Forms\Components\TextInput::make('channel')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Textarea::make('meta')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entity'),
                Tables\Columns\TextColumn::make('entity_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('target_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rule_days')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('recipient')
                    ->searchable(),
                Tables\Columns\TextColumn::make('channel'),
                Tables\Columns\TextColumn::make('status'),
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
            'index' => Pages\ListReminderLogs::route('/'),
            'create' => Pages\CreateReminderLog::route('/create'),
            'edit' => Pages\EditReminderLog::route('/{record}/edit'),
        ];
    }
}
