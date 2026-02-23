<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('description')
                            ->maxLength(65535),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required(),
                        TextInput::make('price_monthly')
                            ->required()
                            ->numeric()
                            ->prefix('RUB'),
                        TextInput::make('type')
                            ->required()
                            ->default('pterodactyl'),
                        Toggle::make('is_active')
                            ->required(),
                        Toggle::make('is_hidden')
                            ->required(),
                    ])->columns(2),

                Section::make('Resources')
                    ->schema([
                        TextInput::make('resources.cpu')
                            ->label('CPU Limit (%)')
                            ->numeric()
                            ->required()
                            ->default(100),
                        TextInput::make('resources.ram')
                            ->label('RAM (MB)')
                            ->numeric()
                            ->required()
                            ->default(1024),
                        TextInput::make('resources.disk')
                            ->label('Disk (MB)')
                            ->numeric()
                            ->required()
                            ->default(10240),
                        TextInput::make('resources.databases')
                            ->label('Databases')
                            ->numeric()
                            ->default(0),
                        TextInput::make('resources.allocations')
                            ->label('Allocations')
                            ->numeric()
                            ->default(0),
                        TextInput::make('resources.backups')
                            ->label('Backups')
                            ->numeric()
                            ->default(0),
                        TextInput::make('resources.egg_id')
                            ->label('Egg ID')
                            ->numeric()
                            ->required(),
                        TextInput::make('resources.nest_id')
                            ->label('Nest ID')
                            ->numeric()
                            ->required(fn () => !config('services.pterodactyl.is_pelican'))
                            ->hidden(fn () => config('services.pterodactyl.is_pelican')),
                    ])->columns(3),
            ]);
    }
}
