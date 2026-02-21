<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->tickets()->with(['messages.user'])->orderBy('updated_at', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'in:low,medium,high',
            'category' => 'required|string|max:255',
            'server_id' => 'nullable|exists:servers,id',
            'attachments.*' => 'file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ]);

        try {
            return DB::transaction(function () use ($request, $validated) {
                $serverId = $validated['server_id'] ?? null;
                if ($serverId) {
                    $owns = $request->user()->servers()->where('id', $serverId)->exists();
                    if (!$owns) {
                        abort(422, 'Invalid server_id');
                    }
                }
                $ticket = $request->user()->tickets()->create([
                    'server_id' => $serverId,
                    'subject' => $validated['subject'],
                    'priority' => $validated['priority'] ?? 'medium',
                    'status' => 'open',
                    'status_v2' => 'open',
                    'category' => $validated['category'],
                ]);

                $message = $ticket->messages()->create([
                    'user_id' => $request->user()->id,
                    'message' => $validated['message'],
                ]);

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
                    'action' => 'ticket.create',
                    'meta' => [
                        'priority' => $ticket->priority,
                        'category' => $ticket->category
                    ],
                ]);

                return $ticket->load(['messages.user','server']);
            });
        } catch (\Exception $e) {
            Log::error('Ticket creation failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create ticket: ' . $e->getMessage()], 500);
        }
    }

    public function show(Request $request, Ticket $ticket)
    {
        if ($ticket->user_id !== $request->user()->id) {
            abort(403);
        }

        return $ticket->load(['messages.user', 'messages.attachments']);
    }

    public function reply(Request $request, Ticket $ticket)
    {
        if ($ticket->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ]);

        $message = $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
        ]);

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

        $ticket->update(['status' => 'open', 'status_v2' => 'open']);

        TicketAudit::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'action' => 'message.create',
            'meta' => ['message_id' => $message->id],
        ]);

        return $message->load('user');
    }

    public function destroy(Request $request, Ticket $ticket)
    {
        if ($ticket->user_id !== $request->user()->id) {
            abort(403);
        }
        TicketAudit::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'action' => 'ticket.delete',
            'meta' => ['subject' => $ticket->subject],
        ]);
        $ticket->delete();
        return response()->noContent();
    }
}
