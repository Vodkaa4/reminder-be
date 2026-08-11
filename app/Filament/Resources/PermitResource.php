<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermitResource\Pages;
use App\Filament\Resources\PermitResource\RelationManagers;
use App\Models\Permit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PermitResource extends Resource
{
    protected static ?string $model = Permit::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Perizinan';
    protected static ?string $modelLabel = 'Perizinan';
    protected static ?string $pluralModelLabel = 'Perizinan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type')
                    ->label('Jenis')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('number')
                    ->label('Nomor')
                    ->maxLength(100),
                Forms\Components\TextInput::make('holder')
                    ->label('Pemegang')
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('asset_location')
                    ->label('Lokasi Aset')
                    ->maxLength(50),
                Forms\Components\DatePicker::make('issued_at')
                    ->label('Diterbitkan'),
                Forms\Components\DatePicker::make('expires_at')
                    ->label('Kadaluarsa')
                    ->required(),
                Forms\Components\TextInput::make('pic')
                    ->label('Email PIC')
                    ->email()
                    ->placeholder('pic@example.com')
                    ->helperText('Masukkan email penanggung jawab (PIC) untuk pengingat.')
                    ->maxLength(191),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
                Forms\Components\Select::make('progress_status')
                    ->label('Status Proses (Tracking)')
                    ->options([
                        'Pendaftaran' => 'Pendaftaran',
                        'Peninjauan' => 'Peninjauan',
                        'Penilaian' => 'Penilaian',
                        'Dalam Proses Perpanjangan' => 'Dalam Proses Perpanjangan',
                        'Selesai' => 'Selesai',
                    ])
                    ->nullable()
                    ->placeholder('Pilih status (jika sedang diproses)'),
                Forms\Components\FileUpload::make('attachment_path')
                    ->label('Lampiran')
                    ->disk('private')
                    ->directory('permits')
                    ->preserveFilenames()
                    ->visibility('private')
                    ->downloadable()
                    ->acceptedFileTypes(['application/pdf','image/*'])
                    ->maxSize(10240)
                    ->helperText('Upload file terkait izin (tersimpan privat).'),

                // Repeater removed to use RelationManager instead
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number')
                    ->label('Nomor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('holder')
                    ->label('Pemegang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('asset_location')
                    ->label('Lokasi Aset')
                    ->searchable(),
                Tables\Columns\TextColumn::make('issued_at')
                    ->label('Diterbitkan')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Kadaluarsa')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pic')
                    ->label('Email PIC')
                    ->searchable()
                    ->copyable()
                    ->url(fn ($record) => $record->pic ? 'mailto:'.$record->pic : null),
                Tables\Columns\TextColumn::make('progress_status')
                    ->label('Status Proses')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('calculated_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'renewal' => 'Masa Perpanjangan',
                        'expired' => 'Kedaluwarsa',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'renewal' => 'warning',
                        'expired' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('progress_status')
                    ->label('Tracking')
                    ->badge()
                    ->default('-')
                    ->color(fn (string $state): string => match ($state) {
                        'Pendaftaran' => 'info',
                        'Peninjauan' => 'warning',
                        'Penilaian' => 'primary',
                        'Dalam Proses Perpanjangan' => 'warning',
                        'Selesai' => 'success',
                        default => 'gray',
                    }),

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
                Tables\Filters\SelectFilter::make('type')
                    ->label('Jenis Izin')
                    ->options(fn () => Permit::distinct()->pluck('type', 'type')->toArray()),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'renewal' => 'Masa Perpanjangan',
                        'expired' => 'Kedaluwarsa',
                    ]),
            ])
            ->actions([

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('download_attachment')
                        ->label('Unduh Lampiran')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->url(fn ($record) => $record->attachment_path ? route('permits.download', ['permit' => $record->id]) : null, shouldOpenInNewTab: true)
                        ->visible(fn ($record) => filled($record->attachment_path)),
                    Tables\Actions\Action::make('delete_attachment')
                        ->label('Hapus Lampiran')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => filled($record->attachment_path))
                        ->action(function ($record) {
                            if ($record->attachment_path && \Illuminate\Support\Facades\Storage::disk('private')->exists($record->attachment_path)) {
                                \Illuminate\Support\Facades\Storage::disk('private')->delete($record->attachment_path);
                            }
                            $record->update(['attachment_path' => null]);
                        }),
                    Tables\Actions\DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-vertical')->tooltip('Aksi'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('expires_at', 'asc')
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PermitHistoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermits::route('/'),
            // 'create' => Pages\CreatePermit::route('/create'),
            // 'edit' => Pages\EditPermit::route('/{record}/edit'),
            'view' => Pages\ViewPermit::route('/{record}'),
        ];
    }
}
