<?php

namespace App\Filament\Resources\Servers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ServerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('product_id')
                    ->numeric(),
                TextEntry::make('node_id')
                    ->numeric(),
                TextEntry::make('ptero_server_id')
                    ->numeric(),
                TextEntry::make('name'),
                TextEntry::make('ip'),
                TextEntry::make('port')
                    ->numeric(),
                TextEntry::make('status'),
                TextEntry::make('expires_at')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('identifier')
                    ->placeholder('-'),
            ]);
    }
}
