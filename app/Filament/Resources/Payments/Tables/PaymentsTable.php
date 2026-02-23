<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Models\Order;
use App\Models\User;
use App\Services\ServerProvisioningService;
use App\Services\TBankService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.email')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order_id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable()
                    ->money('RUB'),
                TextColumn::make('gateway')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'tbank' => 'info',
                        'balance' => 'success',
                        'refund' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('gateway')
                    ->options([
                        'tbank' => 'TBank',
                        'balance' => 'Balance',
                        'refund' => 'Refund',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('check_status')
                    ->label('Check Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->action(function ($record) {
                        if ($record->gateway !== 'tbank' || !$record->transaction_id) {
                             Notification::make()->title('Cannot check status')->body('Invalid gateway or missing transaction ID')->warning()->send();
                             return;
                        }
                        
                        try {
                            $status = app(TBankService::class)->getState($record->transaction_id);
                            
                            if ($status === 'CONFIRMED' && $record->status !== 'completed') {
                                DB::transaction(function () use ($record) {
                                    $record->update(['status' => 'completed']);
                                    
                                    if ($record->order_id) {
                                        $order = Order::find($record->order_id);
                                        if ($order && $order->status !== 'paid') {
                                            $order->status = 'paid';
                                            $order->save();
                                            app(ServerProvisioningService::class)->provision($order);
                                            Notification::make()->title('Payment Confirmed')->body('Order marked as paid and provisioning started.')->success()->send();
                                        }
                                    } else {
                                        $record->user->increment('balance', $record->amount);
                                        Notification::make()->title('Payment Confirmed')->body('User balance updated.')->success()->send();
                                    }
                                });
                            } elseif ($status === 'REJECTED' || $status === 'CANCELED') {
                                $record->update(['status' => 'failed']);
                                Notification::make()->title('Payment Failed')->body("Status: $status")->danger()->send();
                            } else {
                                Notification::make()->title('Status Checked')->body("Current status: $status")->info()->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()->title('Error')->body($e->getMessage())->danger()->send();
                        }
                    })
                    ->hidden(fn ($record) => $record->status === 'completed' || $record->gateway !== 'tbank'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
