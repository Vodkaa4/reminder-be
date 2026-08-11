<?php

namespace App\Filament\Resources\PermitResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PermitHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'permitHistories';

    protected static ?string $title = 'Riwayat Perpanjangan Izin';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('issued_at')
                    ->label('Tgl Terbit'),
                Forms\Components\DatePicker::make('expires_at')
                    ->label('Tgl Kadaluarsa')
                    ->required(),
                Forms\Components\FileUpload::make('document_path')
                    ->label('Lampiran')
                    ->directory('permits_history')
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(10240),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('expires_at')
            ->columns([
                Tables\Columns\TextColumn::make('issued_at')
                    ->label('Tgl Terbit')
                    ->date(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Tgl Kadaluarsa')
                    ->date(),
                Tables\Columns\TextColumn::make('old_number')
                    ->label('No. Lama')
                    ->default('-'),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(30),
                Tables\Columns\TextColumn::make('updater_name')
                    ->label('Diperbarui Oleh')
                    ->default('-'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
