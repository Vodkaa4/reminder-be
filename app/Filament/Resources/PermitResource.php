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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('number')
                    ->maxLength(100),
                Forms\Components\TextInput::make('holder')
                    ->maxLength(100),
                Forms\Components\TextInput::make('asset_location')
                    ->maxLength(50),
                Forms\Components\DatePicker::make('issued_at'),
                Forms\Components\DatePicker::make('expires_at')
                    ->required(),
                Forms\Components\TextInput::make('pic')
                    ->label('PIC Email')
                    ->email()
                    ->placeholder('pic@example.com')
                    ->helperText('Masukkan email penanggung jawab (PIC) untuk pengingat.')
                    ->maxLength(191),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('attachment_path')
                    ->label('Attachment')
                    ->disk('private')
                    ->directory('permits')
                    ->preserveFilenames()
                    ->visibility('private')
                    ->downloadable()
                    ->acceptedFileTypes(['application/pdf','image/*'])
                    ->maxSize(10240)
                    ->helperText('Upload file terkait izin (tersimpan privat).'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('holder')
                    ->searchable(),
                Tables\Columns\TextColumn::make('asset_location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('issued_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pic')
                    ->label('PIC Email')
                    ->searchable()
                    ->copyable()
                    ->url(fn ($record) => $record->pic ? 'mailto:'.$record->pic : null),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('attachment_path')
                    ->label('Attachment')
                    ->formatStateUsing(fn ($state) => $state ? 'Download' : '-')
                    ->url(fn ($record) => $record->attachment_path ? route('permits.download', ['permit' => $record->id]) : null, shouldOpenInNewTab: true)
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Actions\Action::make('download_attachment')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => $record->attachment_path ? route('permits.download', ['permit' => $record->id]) : null, shouldOpenInNewTab: true)
                    ->visible(fn ($record) => filled($record->attachment_path)),
                Tables\Actions\Action::make('delete_attachment')
                    ->label('Delete attachment')
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
            'index' => Pages\ListPermits::route('/'),
            'create' => Pages\CreatePermit::route('/create'),
            'edit' => Pages\EditPermit::route('/{record}/edit'),
        ];
    }
}
