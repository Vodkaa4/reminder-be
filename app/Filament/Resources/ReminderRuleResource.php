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

    protected static ?string $navigationLabel = 'Aturan Pengingat';
    protected static ?string $modelLabel = 'Aturan Pengingat';
    protected static ?string $pluralModelLabel = 'Aturan Pengingat';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('entity')
                    ->label('Entitas')
                    ->options([
                        'contract' => 'Kontrak Karyawan',
                        'permit' => 'Perizinan',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('days_before')
                    ->label('H- Hari')
                    ->required()
                    ->numeric()
                    ->unique(modifyRuleUsing: function (\Illuminate\Validation\Rules\Unique $rule, Forms\Get $get) {
                        return $rule->where('entity', $get('entity'));
                    }, ignoreRecord: true),
                Forms\Components\Select::make('channel')
                    ->label('Saluran')
                    ->options([
                        'email' => 'Email',
                        'whatsapp' => 'WhatsApp',
                        'system' => 'Sistem',
                    ])
                    ->required(),
                Forms\Components\Toggle::make('active')
                    ->label('Aktif')
                    ->required(),
                Forms\Components\Toggle::make('is_recurring')
                    ->label('Set Berulang (Recurring)')
                    ->live(),
                Forms\Components\TextInput::make('recurring_interval_days')
                    ->label('Ulangi setiap X hari')
                    ->numeric()
                    ->required(fn (Forms\Get $get) => $get('is_recurring'))
                    ->visible(fn (Forms\Get $get) => $get('is_recurring')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entity')
                    ->label('Entitas')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'contract' => 'Kontrak Karyawan',
                        'permit' => 'Perizinan',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('days_before')
                    ->label('H- Hari')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('channel')
                    ->label('Saluran')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'email' => 'Email',
                        'whatsapp' => 'WhatsApp',
                        'system' => 'Sistem',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_recurring')
                    ->label('Berulang')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diubah Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            // 'create' => Pages\CreateReminderRule::route('/create'), // Disabled to use Modal
            // 'edit' => Pages\EditReminderRule::route('/{record}/edit'), // Disabled to use Modal
        ];
    }
}
