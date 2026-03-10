<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;

class UserLogObserver
{
    public function created($model)
    {
        $this->logAction($model, 'created');
    }

    public function updated($model)
    {
        $this->logAction($model, 'updated');
    }

    public function deleted($model)
    {
        $this->logAction($model, 'deleted');
    }

    protected function logAction($model, $actionType)
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        $modelName = class_basename($model);
        $description = "{$modelName} was {$actionType}";

        // Customize description based on model
        if ($model instanceof Order) {
            $description = "Заказ #{$model->id} ({$model->service->name}) был " . ($actionType === 'created' ? 'создан' : ($actionType === 'updated' ? 'обновлен' : 'удален'));
            if ($actionType === 'updated' && $model->isDirty('status')) {
                $description .= ". Статус изменен на {$model->status}";
            }
        } elseif ($model instanceof Ticket) {
            $description = "Тикет #{$model->id} '{$model->subject}' был " . ($actionType === 'created' ? 'создан' : ($actionType === 'updated' ? 'обновлен' : 'удален'));
        } elseif ($model instanceof TicketMessage) {
            $ticketId = $model->ticket_id;
            $description = "Сообщение в тикете #{$ticketId} было " . ($actionType === 'created' ? 'отправлено' : ($actionType === 'updated' ? 'изменено' : 'удалено'));
        }

        UserLog::create([
            'user_id' => $user->id,
            'ip_address' => request()->ip(),
            'action' => strtolower($modelName) . '_' . $actionType,
            'description' => $description,
        ]);
    }
}
