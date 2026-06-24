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

    protected static ?string $navigationLabel = 'Log Pengingat';
    protected static ?string $modelLabel = 'Log Pengingat';
    protected static ?string $pluralModelLabel = 'Log Pengingat';

    // Read-only enforcement
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('entity')
                    ->label('Entitas')
                    ->required(),
                Forms\Components\TextInput::make('entity_id')
                    ->label('ID Entitas')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('target_date')
                    ->label('Tanggal Target')
                    ->required(),
                Forms\Components\TextInput::make('rule_days')
                    ->label('H- Hari')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('recipient')
                    ->label('Penerima')
                    ->maxLength(191),
                Forms\Components\TextInput::make('channel')
                    ->label('Saluran')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->label('Status')
                    ->required(),
                Forms\Components\Textarea::make('meta')
                    ->label('Meta Data')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entity')
                    ->label('Entitas')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'contract' => 'success',
                        'permit' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'contract' => 'Kontrak Karyawan',
                        'permit' => 'Perizinan',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('entity_id')
                    ->label('ID Entitas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('target_date')
                    ->label('Tanggal Target')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rule_days')
                    ->label('H- Hari')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('recipient')
                    ->label('Penerima')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('channel')
                    ->label('Saluran')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'email' => 'Email',
                        'whatsapp' => 'WhatsApp',
                        'system' => 'Sistem',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sent' => 'success',
                        'failed' => 'danger',
                        'skipped' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('meta')
                    ->label('Meta Data')
                    ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : '')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('entity')
                    ->label('Entitas')
                    ->options([
                        'contract' => 'Kontrak',
                        'permit' => 'Perizinan',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'sent' => 'Terkirim',
                        'failed' => 'Gagal',
                        'skipped' => 'Dilewati',
                    ]),
                Tables\Filters\SelectFilter::make('rule_days')
                    ->label('H- Hari')
                    ->multiple()
                    ->options([
                        15 => '15 hari',
                        30 => '30 hari',
                        60 => '60 hari',
                        90 => '90 hari',
                    ]),
                Tables\Filters\Filter::make('target_date')
                    ->label('Tanggal Target')
                    ->form([
                        Forms\Components\DatePicker::make('target_date_from')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('target_date_until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['target_date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('target_date', '>=', $date),
                            )
                            ->when(
                                $data['target_date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('target_date', '<=', $date),
                            );
                    }),
                Tables\Filters\Filter::make('created_at')
                    ->label('Dibuat Pada')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('created_until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for read-only resource
            ])
            ->defaultSort('created_at', 'desc');
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
            'view' => Pages\ViewReminderLog::route('/{record}'),
        ];
    }
}
