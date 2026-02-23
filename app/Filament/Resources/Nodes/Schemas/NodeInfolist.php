<?php

namespace App\Filament\Resources\Nodes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class NodeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('ptero_id')
                    ->numeric(),
                TextEntry::make('ip'),
                TextEntry::make('port_range_start')
                    ->numeric(),
                TextEntry::make('port_range_end')
                    ->numeric(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('public_host')
                    ->placeholder('-'),
            ]);
    }
}
