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

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Employees';

    protected static ?string $modelLabel = 'Employee';

    protected static ?string $pluralModelLabel = 'Employees';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identity & Contact')
                    ->schema([
                        Forms\Components\TextInput::make('nip')
                            ->label('NIP')
                            ->required()
                            ->maxLength(191)
                            ->unique(ignoreRecord: true)
                            ->placeholder('EMP-0001'),
                        Forms\Components\TextInput::make('name')
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
                            ->maxLength(191)
                            ->placeholder('Nama atasan langsung'),
                    ])->columns(2),

                Forms\Components\Section::make('Employment Details')
                    ->schema([
                        Forms\Components\Toggle::make('is_permanent')
                            ->label('Permanent Employee')
                            ->default(false)
                            ->reactive(),
                        Forms\Components\DatePicker::make('contract_start')
                            ->label('Contract Start Date')
                            ->required()
                            ->placeholder('Tanggal mulai kontrak'),
                        Forms\Components\DatePicker::make('contract_end')
                            ->label('Contract End Date')
                            ->visible(fn ($get) => !$get('is_permanent'))
                            ->placeholder('Tanggal berakhir kontrak'),
                        Forms\Components\DatePicker::make('resign_date')
                            ->label('Resign Date')
                            ->placeholder('Tanggal resign (jika ada)'),
                    ])->columns(2),

                Forms\Components\Section::make('Organization')
                    ->schema([
                        Forms\Components\TextInput::make('dept')
                            ->label('Department')
                            ->required()
                            ->maxLength(191)
                            ->placeholder('Produksi, HRD, IT'),
                        Forms\Components\TextInput::make('sect')
                            ->label('Section')
                            ->maxLength(191)
                            ->placeholder('Sub-seksi (jika ada)'),
                        Forms\Components\TextInput::make('position')
                            ->label('Position')
                            ->required()
                            ->maxLength(191)
                            ->placeholder('Operator, Supervisor'),
                        Forms\Components\TextInput::make('location')
                            ->label('Location')
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
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('supervisor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_permanent')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->getStateUsing(fn ($record) => $record?->is_permanent ? 'Permanent' : 'Contract'),
                Tables\Columns\TextColumn::make('contract_start')
                    ->label('Contract Start')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contract_end')
                    ->label('Contract End')
                    ->date()
                    ->sortable()
                    ->visible(fn ($record) => $record && !$record->is_permanent)
                    ->color(fn ($record) => 
                        $record && $record->contract_end && $record->contract_end->diffInDays(now()) <= 30 
                            ? 'danger' 
                            : 'default'
                    ),
                Tables\Columns\TextColumn::make('dept')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sect')
                    ->label('Section')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('position')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('resign_date')
                    ->label('Resign Date')
                    ->date()
                    ->sortable()
                    ->visible(fn ($record) => $record && $record->resign_date),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('dept')
                    ->label('Department')
                    ->options(fn () => Employee::distinct()->pluck('dept', 'dept')->toArray()),
                Tables\Filters\TernaryFilter::make('is_permanent')
                    ->label('Employment Status')
                    ->placeholder('All')
                    ->trueLabel('Permanent')
                    ->falseLabel('Contract'),
                Tables\Filters\Filter::make('contract_expiring_soon')
                    ->label('Contract Expiring Soon (≤30 days)')
                    ->query(fn (Builder $query) => 
                        $query->where('contract_end', '<=', now()->addDays(30))
                              ->where('contract_end', '>=', now())
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
            ])
            ->defaultSort('name', 'asc');
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
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
