<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketMessage;
use App\Models\TicketMessageEdit;
use App\Models\TicketAudit;
use Illuminate\Http\Request;

class TicketMessageController extends Controller
{
    public function update(Request $request, TicketMessage $message)
    {
        $user = $request->user();
        if ($message->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        if ($message->created_at->diffInMinutes(now()) > 30) {
            return response()->json(['message' => 'Редактирование доступно в течение 30 минут'], 422);
        }

        TicketMessageEdit::create([
            'ticket_message_id' => $message->id,
            'user_id' => $user->id,
            'old_message' => $message->message,
            'new_message' => $request->message,
            'edited_at' => now(),
        ]);

        $message->update([
            'message' => $request->message,
            'edited_at' => now(),
            'edited_by' => $user->id,
        ]);

        TicketAudit::create([
            'ticket_id' => $message->ticket_id,
            'user_id' => $user->id,
            'action' => 'message.edit',
            'meta' => ['message_id' => $message->id],
        ]);

        return $message->load('user');
    }

    public function destroy(Request $request, TicketMessage $message)
    {
        $user = $request->user();
        if ($message->user_id !== $user->id) {
            abort(403);
        }

        $message->delete();

        TicketAudit::create([
            'ticket_id' => $message->ticket_id,
            'user_id' => $user->id,
            'action' => 'message.delete',
            'meta' => ['message_id' => $message->id],
        ]);

        return response()->noContent();
    }
}

