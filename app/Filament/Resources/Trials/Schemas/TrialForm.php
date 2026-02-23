<?php

namespace App\Filament\Resources\Trials\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TrialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('product_id')
                    ->required()
                    ->numeric(),
                TextInput::make('duration_days')
                    ->required()
                    ->numeric(),
                TextInput::make('max_per_user')
                    ->required()
                    ->numeric()
                    ->default(1),
                Toggle::make('active')
                    ->required(),
            ]);
    }
}
