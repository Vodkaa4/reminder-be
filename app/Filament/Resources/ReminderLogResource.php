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



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('entity')
                    ->colors([
                        'primary' => 'contract',
                        'secondary' => 'permit',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'contract' => 'Kontrak Karyawan',
                        'permit' => 'Perizinan',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('entity_id')
                    ->numeric()
                    ->sortable()
                    ->label('ID'),
                Tables\Columns\TextColumn::make('target_date')
                    ->date()
                    ->sortable()
                    ->label('Tanggal Target'),
                Tables\Columns\TextColumn::make('rule_days')
                    ->formatStateUsing(fn (int $state): string => "H-{$state}")
                    ->sortable()
                    ->label('Reminder'),
                Tables\Columns\TextColumn::make('recipient')
                    ->searchable()
                    ->label('Penerima'),
                Tables\Columns\BadgeColumn::make('channel')
                    ->colors([
                        'info' => 'email',
                        'success' => 'whatsapp',
                        'warning' => 'both',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'email' => 'Email',
                        'whatsapp' => 'WhatsApp',
                        'both' => 'Email & WhatsApp',
                        default => $state,
                    }),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'sent',
                        'warning' => 'skipped',
                        'danger' => 'failed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sent' => 'Terkirim',
                        'skipped' => 'Dilewati',
                        'failed' => 'Gagal',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Dibuat')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('entity')
                    ->options([
                        'contract' => 'Kontrak Karyawan',
                        'permit' => 'Perizinan'
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'sent' => 'Terkirim',
                        'skipped' => 'Dilewati',
                        'failed' => 'Gagal'
                    ]),
                Tables\Filters\SelectFilter::make('channel')
                    ->options([
                        'email' => 'Email',
                        'whatsapp' => 'WhatsApp',
                        'both' => 'Email & WhatsApp'
                    ]),
                Tables\Filters\SelectFilter::make('rule_days')
                    ->options([
                        30 => 'H-30',
                        60 => 'H-60',
                        90 => 'H-90',
                    ])
                    ->label('Reminder'),
                Tables\Filters\Filter::make('target_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('target_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('target_date', '<=', $date),
                            );
                    })
            ])
            ->actions([])
            ->bulkActions([]);
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
        ];
    }
}
