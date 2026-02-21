<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketAudit;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $q = Ticket::with('user');
        if ($request->filled('status_v2')) {
            $q->where('status_v2', $request->status_v2);
        }
        if ($request->filled('priority')) {
            $q->where('priority', $request->priority);
        }
        if ($request->filled('category')) {
            $q->where('category', $request->category);
        }
        if ($request->filled('assigned_to')) {
            $q->where('assigned_to', $request->assigned_to);
        }
        if ($request->filled('q')) {
            $term = '%' . $request->q . '%';
            $q->where(function ($w) use ($term) {
                $w->where('subject', 'like', $term)
                  ->orWhere('category', 'like', $term);
            });
        }
        return $q->orderBy('updated_at', 'desc')->get();
    }

    public function show(Ticket $ticket)
    {
        return $ticket->load(['user', 'messages.user', 'messages.attachments', 'assignedTo']);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'in:open,answered,closed',
            'priority' => 'in:low,medium,high',
            'status_v2' => 'in:open,in_progress,waiting,resolved,closed',
            'assigned_to' => 'nullable|exists:users,id',
            'category' => 'nullable|string|max:255',
            'tags' => 'array',
            'comment' => 'nullable|string',
        ]);

        $ticket->update($validated);

        if (isset($validated['status_v2']) || isset($validated['priority']) || isset($validated['assigned_to'])) {
            TicketAudit::create([
                'ticket_id' => $ticket->id,
                'user_id' => $request->user()->id,
                'action' => 'ticket.update',
                'meta' => [
                    'status_v2' => $validated['status_v2'] ?? $ticket->status_v2,
                    'priority' => $validated['priority'] ?? $ticket->priority,
                    'assigned_to' => $validated['assigned_to'] ?? $ticket->assigned_to,
                    'comment' => $validated['comment'] ?? null,
                ]
            ]);
        }
        return $ticket;
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'is_internal' => 'boolean',
            'attachments.*' => 'file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ]);

        $message = $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        $ticket->update(['status' => 'answered']);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets/attachments', 'public');
                TicketAttachment::create([
                    'ticket_message_id' => $message->id,
                    'user_id' => $request->user()->id,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        TicketAudit::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'action' => 'message.create',
            'meta' => ['message_id' => $message->id, 'is_internal' => $message->is_internal],
        ]);

        return $message->load('user');
    }

    public function destroy(Request $request, Ticket $ticket)
    {
        TicketAudit::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'action' => 'ticket.delete',
            'meta' => ['subject' => $ticket->subject],
        ]);
        $ticket->delete();
        return response()->noContent();
    }

    public function report()
    {
        $total = Ticket::count();
        $byStatus = Ticket::selectRaw('status_v2, count(*) as c')->groupBy('status_v2')->pluck('c','status_v2');
        $byPriority = Ticket::selectRaw('priority, count(*) as c')->groupBy('priority')->pluck('c','priority');
        $avgFirstResponse = 0;
        return response()->json([
            'total' => $total,
            'by_status' => $byStatus,
            'by_priority' => $byPriority,
            'avg_first_response_minutes' => $avgFirstResponse,
        ]);
    }
}
