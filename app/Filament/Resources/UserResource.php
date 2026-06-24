<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    protected static ?string $navigationLabel = 'Pengguna';
    protected static ?string $modelLabel = 'Pengguna';
    protected static ?string $pluralModelLabel = 'Pengguna';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengguna')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Masukkan nama lengkap'),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Masukkan alamat email'),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Email Terverifikasi Pada')
                            ->placeholder('Biarkan kosong jika belum verifikasi'),
                        Forms\Components\Select::make('role')
                            ->label('Peran')
                            ->options([
                                'admin' => 'Admin',
                                'manager' => 'Manager',
                            ])
                            ->default('manager')
                            ->required()
                            ->placeholder('Pilih peran pengguna'),
                    ])->columns(2),

                Forms\Components\Section::make('Keamanan')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(8)
                            ->placeholder('Masukkan kata sandi (minimal 8 karakter)')
                            ->helperText('Kata sandi harus terdiri dari minimal 8 karakter'),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Konfirmasi Kata Sandi')
                            ->password()
                            ->dehydrated(false)
                            ->required(fn (string $context): bool => $context === 'create')
                            ->same('password')
                            ->placeholder('Konfirmasi kata sandi'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Terverifikasi')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('role')
                    ->label('Peran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'success',
                        'manager' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Bergabung Pada')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('verified')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at'))
                    ->label('Hanya Pengguna Terverifikasi'),
                Tables\Filters\Filter::make('unverified')
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at'))
                    ->label('Hanya Pengguna Belum Terverifikasi'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('verify_email')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        $record->update(['email_verified_at' => now()]);
                    })
                    ->visible(fn (User $record) => is_null($record->email_verified_at))
                    ->tooltip('Tandai email sebagai terverifikasi'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('verify_selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->label('Verifikasi Terpilih')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['email_verified_at' => now()]);
                            });
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListUsers::route('/'),
            // 'create' => Pages\CreateUser::route('/create'), // Disabled for Modal
            // 'edit' => Pages\EditUser::route('/{record}/edit'), // Disabled for Modal
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return cache()->remember('users_count', 300, fn() => static::getModel()::count());
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->select(['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at']);
    }
}
