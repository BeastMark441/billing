<?php

namespace App\Filament\Resources\Servers\Tables;

use App\Services\PterodactylService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ServersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.email')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->sortable(),
                TextColumn::make('node.name')
                    ->label('Node')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'suspended' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('ip')
                    ->label('IP Address')
                    ->searchable(),
                TextColumn::make('port'),
                TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('identifier')
                    ->label('UUID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('suspend')
                    ->icon('heroicon-o-pause')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        try {
                            app(PterodactylService::class)->suspendServer($record->ptero_server_id);
                            $record->update(['status' => 'suspended']);
                            Notification::make()->title('Server suspended')->success()->send();
                        } catch (\Exception $e) {
                            Notification::make()->title('Failed to suspend')->body($e->getMessage())->danger()->send();
                        }
                    })
                    ->hidden(fn ($record) => $record->status !== 'active'),
                Action::make('unsuspend')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        try {
                            app(PterodactylService::class)->unsuspendServer($record->ptero_server_id);
                            $record->update(['status' => 'active']);
                            Notification::make()->title('Server unsuspended')->success()->send();
                        } catch (\Exception $e) {
                            Notification::make()->title('Failed to unsuspend')->body($e->getMessage())->danger()->send();
                        }
                    })
                    ->hidden(fn ($record) => $record->status !== 'suspended'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
