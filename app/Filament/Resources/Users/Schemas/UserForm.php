<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        DateTimePicker::make('email_verified_at'),
                        TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                        TextInput::make('balance')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->prefix('RUB'),
                        Select::make('role')
                            ->options([
                                'user' => 'User',
                                'admin' => 'Admin',
                            ])
                            ->required()
                            ->default('user'),
                        Toggle::make('is_blocked')
                            ->required(),
                    ])->columns(2),

                Section::make('Profile Details')
                    ->schema([
                        TextInput::make('first_name'),
                        TextInput::make('last_name'),
                        TextInput::make('middle_name'),
                        TextInput::make('phone')
                            ->tel(),
                        TextInput::make('telegram'),
                        TextInput::make('vk'),
                    ])->columns(3),

                Section::make('System')
                    ->schema([
                        TextInput::make('ptero_id')
                            ->numeric()
                            ->label('Pterodactyl ID')
                            ->disabled(),
                        TextInput::make('ip_address')
                            ->label('Last IP')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }
}
