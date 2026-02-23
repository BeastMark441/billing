<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('discount')
                    ->required()
                    ->numeric(),
                TextInput::make('max_uses')
                    ->numeric(),
                DateTimePicker::make('expires_at'),
            ]);
    }
}
