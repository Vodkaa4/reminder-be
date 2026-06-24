<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeesExport;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Karyawan';

    protected static ?string $modelLabel = 'Karyawan';

    protected static ?string $pluralModelLabel = 'Karyawan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas & Kontak')
                    ->schema([
                        Forms\Components\TextInput::make('nip')
                            ->label('NIP')
                            ->required()
                            ->maxLength(191)
                            ->unique(ignoreRecord: true)
                            ->placeholder('EMP-0001'),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(191)
                            ->placeholder('Nama lengkap karyawan'),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(191)
                            ->unique(ignoreRecord: true)
                            ->placeholder('email@example.com'),
                        Forms\Components\TextInput::make('supervisor')
                            ->label('Atasan')
                            ->maxLength(191)
                            ->placeholder('Nama atasan langsung'),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Kepegawaian')
                    ->schema([
                        Forms\Components\Toggle::make('is_permanent')
                            ->label('Karyawan Tetap')
                            ->default(false)
                            ->reactive(),
                        Forms\Components\DatePicker::make('contract_start')
                            ->label('Tanggal Mulai Kontrak')
                            ->required()
                            ->placeholder('Tanggal mulai kontrak'),
                        Forms\Components\DatePicker::make('contract_end')
                            ->label('Tanggal Berakhir Kontrak')
                            ->visible(fn ($get) => !$get('is_permanent'))
                            ->placeholder('Tanggal berakhir kontrak'),
                        Forms\Components\DatePicker::make('resign_date')
                            ->label('Tanggal Resign')
                            ->placeholder('Tanggal resign (jika ada)'),
                    ])->columns(2),

                Forms\Components\Section::make('Organisasi')
                    ->schema([
                        Forms\Components\TextInput::make('dept')
                            ->label('Departemen')
                            ->required()
                            ->maxLength(191)
                            ->placeholder('Produksi, HRD, IT'),
                        Forms\Components\TextInput::make('sect')
                            ->label('Bagian')
                            ->maxLength(191)
                            ->placeholder('Sub-seksi (jika ada)'),
                        Forms\Components\TextInput::make('position')
                            ->label('Jabatan')
                            ->required()
                            ->maxLength(191)
                            ->placeholder('Operator, Supervisor'),
                        Forms\Components\TextInput::make('location')
                            ->label('Lokasi')
                            ->required()
                            ->maxLength(191)
                            ->placeholder('Plant CILAMPENI'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->visible(false),
                Tables\Columns\TextColumn::make('supervisor')
                    ->label('Atasan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_permanent')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->getStateUsing(fn ($record) => $record->is_permanent),
                Tables\Columns\TextColumn::make('contract_start')
                    ->label('Mulai Kontrak')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contract_end')
                    ->label('Akhir Kontrak')
                    ->date()
                    ->sortable()
                    // ->visible(fn ($record) => $record && !$record->is_permanent)
                    ->color(fn ($record) => 
                                $record && $record->contract_end && $record->contract_end->isBetween(today(), today()->addDays(30))
                                    ? 'danger'
                                    : 'default'
                                        )
                    ->tooltip(fn ($record) => $record && $record->contract_end ? $record->contract_end->diffForHumans() : null),
                Tables\Columns\TextColumn::make('dept')
                    ->label('Departemen')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sect')
                    ->label('Bagian')
                    ->searchable()
                    ->sortable()
                    ->visible(false),
                Tables\Columns\TextColumn::make('position')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable()
                    ->visible(false),
                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('resign_date')
                    ->label('Tanggal Resign')
                    ->date()
                    ->sortable()
                    ->visible(fn ($record) => $record && $record->resign_date),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('dept')
                    ->label('Departemen')
                    ->options(fn () => Employee::distinct()->pluck('dept', 'dept')->toArray()),
                Tables\Filters\TernaryFilter::make('is_permanent')
                    ->label('Status Kepegawaian')
                    ->placeholder('Semua')
                    ->trueLabel('Tetap')
                    ->falseLabel('Kontrak'),
                Tables\Filters\Filter::make('contract_expiring_soon_30')
                    ->label('Kontrak Segera Habis (≤30 hari)')
                    ->query(fn (Builder $query) => 
                        $query->where('contract_end', '<=', today()->addDays(30))
                              ->where('contract_end', '>=', today())
                              ->where('is_permanent', false)
                    ),
                Tables\Filters\Filter::make('contract_expiring_soon_15')
                ->label('Kontrak Segera Habis (≤15 hari)')
                ->query(fn (Builder $query) => 
                    $query->where('contract_end', '<=', today()->addDays(15))
                            ->where('contract_end', '>=', today())
                            ->where('is_permanent', false)
                ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                BulkAction::make('export')
                ->label('Ekspor ke CSV')
                ->action(fn (Collection $records) => 
                    Excel::download(new EmployeesExport($records), 'karyawan.csv')
                ),
            ])
            ->defaultSort('name', 'asc')
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25);
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
            'index' => Pages\ListEmployees::route('/'),
            // 'create' => Pages\CreateEmployee::route('/create'), // Disabled to use Modal
            // 'edit' => Pages\EditEmployee::route('/{record}/edit'), // Disabled to use Modal
        ];
    }
}
