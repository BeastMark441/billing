<?php

namespace App\Filament\Resources\Nodes\Pages;

use App\Filament\Resources\Nodes\NodeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewNode extends ViewRecord
{
    protected static string $resource = NodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
