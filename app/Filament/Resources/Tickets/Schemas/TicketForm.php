<?php

namespace App\Filament\Resources\Tickets\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ticket Information')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->required(),
                        Select::make('server_id')
                            ->relationship('server', 'name')
                            ->searchable()
                            ->nullable(),
                        TextInput::make('subject')
                            ->required()
                            ->maxLength(255),
                        Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'critical' => 'Critical',
                            ])
                            ->required()
                            ->default('medium'),
                        Select::make('status')
                            ->options([
                                'open' => 'Open',
                                'pending' => 'Pending',
                                'closed' => 'Closed',
                                'resolved' => 'Resolved',
                            ])
                            ->required()
                            ->default('open'),
                        TextInput::make('category'),
                    ])->columns(2),

                Section::make('Management')
                    ->schema([
                        Select::make('assigned_to')
                            ->relationship('assignedTo', 'name')
                            ->searchable()
                            ->nullable(),
                        DateTimePicker::make('sla_due_at'),
                        TagsInput::make('tags'),
                    ])->columns(2),
            ]);
    }
}
