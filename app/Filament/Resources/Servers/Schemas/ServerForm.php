<?php

namespace App\Filament\Resources\Servers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ServerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Server Details')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->required(),
                        Select::make('node_id')
                            ->relationship('node', 'name')
                            ->required(),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'suspended' => 'Suspended',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('active'),
                        DateTimePicker::make('expires_at')
                            ->required(),
                    ])->columns(2),

                Section::make('Technical Details')
                    ->schema([
                        TextInput::make('ptero_server_id')
                            ->numeric()
                            ->required()
                            ->label('Pterodactyl ID'),
                        TextInput::make('identifier')
                            ->label('UUID Short')
                            ->maxLength(255),
                        TextInput::make('ip')
                            ->required()
                            ->ipv4(),
                        TextInput::make('port')
                            ->required()
                            ->numeric(),
                    ])->columns(2),
            ]);
    }
}
