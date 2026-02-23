<?php

namespace App\Filament\Resources\Nodes\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Node Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('ptero_id')
                            ->label('Pterodactyl Node ID')
                            ->required()
                            ->numeric(),
                        TextInput::make('public_host')
                            ->label('Public Host (FQDN)')
                            ->placeholder('node1.example.com')
                            ->maxLength(255),
                        Toggle::make('is_active')
                            ->required(),
                    ])->columns(2),

                Section::make('Network Configuration')
                    ->schema([
                        TextInput::make('ip')
                            ->label('IP Address')
                            ->required()
                            ->ipv4(),
                        TextInput::make('port_range_start')
                            ->label('Port Start')
                            ->required()
                            ->numeric()
                            ->minValue(1024)
                            ->maxValue(65535),
                        TextInput::make('port_range_end')
                            ->label('Port End')
                            ->required()
                            ->numeric()
                            ->minValue(1024)
                            ->maxValue(65535),
                    ])->columns(3),
            ]);
    }
}
