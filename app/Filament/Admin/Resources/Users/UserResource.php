<?php

namespace App\Filament\Admin\Resources\Users;

use App\Filament\Admin\Resources\Users\Pages\CreateUser;
use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Filament\Admin\Resources\Users\Pages\ListUsers;
use App\Filament\Admin\Resources\Users\Schemas\UserForm;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users';
    protected static ?string $recordTitleAttribute = 'User';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Employee Information ')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name ')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email ')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),

                        Select::make('role')
                            ->label(' User Role')
                            ->options([
                                'admin' => ' (Admin)',
                                'cashier' => ' (Cashier)',
                            ])
                            ->required()
                            ->native(false),
                    ])->columns(2),

                Section::make('Security')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password ')
                            ->password()
                            ->required(fn(string $context): bool => $context === 'create')
                            ->dehydrated(fn($state) => filled($state))
                            ->maxLength(255),
                    ]),
            ]);
    }
    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email ')
                    ->searchable(),

                TextColumn::make('role')
                    ->label('User Role')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'admin' => 'success',
                        'cashier' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Registered At ')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
