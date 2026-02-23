<?php

namespace App\Filament\Resources\Trials;

use App\Filament\Resources\Trials\Pages\CreateTrial;
use App\Filament\Resources\Trials\Pages\EditTrial;
use App\Filament\Resources\Trials\Pages\ListTrials;
use App\Filament\Resources\Trials\Pages\ViewTrial;
use App\Filament\Resources\Trials\Schemas\TrialForm;
use App\Filament\Resources\Trials\Schemas\TrialInfolist;
use App\Filament\Resources\Trials\Tables\TrialsTable;
use App\Models\Trial;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TrialResource extends Resource
{
    protected static ?string $model = Trial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return TrialForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TrialInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrialsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrials::route('/'),
            'create' => CreateTrial::route('/create'),
            'view' => ViewTrial::route('/{record}'),
            'edit' => EditTrial::route('/{record}/edit'),
        ];
    }
}
