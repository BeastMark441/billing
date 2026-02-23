<?php

namespace App\Filament\Resources\Trials\Pages;

use App\Filament\Resources\Trials\TrialResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrials extends ListRecords
{
    protected static string $resource = TrialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
