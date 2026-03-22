<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Notifications\GeneralNotification;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function __construct(protected AuditLogger $auditLogger) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Ticket::with('user')->latest()->paginate(20);

        return view('admin.tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('admin.tickets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:open,pending,closed',
        ]);

        $ticket = Ticket::create([
            'user_id' => $validated['user_id'],
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
        ]);

        $ticket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $validated['message'],
        ]);

        return redirect()->route('admin.tickets.index')->with('success', 'Тикет успешно создан.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'messages.user', 'messages.attachments']);

        return view('admin.tickets.show', compact('ticket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,pending,closed',
            'priority' => 'required|in:low,medium,high',
        ]);

        $before = $ticket->only(['status', 'priority']);
        $ticket->update($validated);

        $this->auditLogger->log('admin_ticket_updated', ['from' => $before, 'to' => $validated], 'ticket', (string) $ticket->id);

        if ($before['status'] !== $validated['status']) {
            $ticket->user->notify(new GeneralNotification(
                'Статус тикета изменен',
                'Тикет #'.$ticket->id.' теперь имеет статус: '.$validated['status'].'.',
                'info',
                route('dashboard.tickets.show', $ticket),
                'Открыть тикет'
            ));
        }

        return back()->with('success', 'Ticket updated successfully.');
    }

    /**
     * Store a reply for the ticket.
     */
    public function reply(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:102400',
        ]);

        $message = $ticket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $validated['message'],
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets', 'public');
                $message->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        // Automatically set status to pending (waiting for user) when admin replies
        if ($ticket->status !== 'closed') {
            $ticket->update(['status' => 'pending']);
        }

        $this->auditLogger->log('admin_ticket_reply', ['ticket_id' => $ticket->id], 'ticket', (string) $ticket->id);

        $ticket->user->notify(new GeneralNotification(
            'Ответ поддержки',
            'В тикете #'.$ticket->id.' есть новый ответ.',
            'info',
            route('dashboard.tickets.show', $ticket),
            'Открыть тикет'
        ));

        return back()->with('success', 'Reply sent successfully.');
    }

    public function updateMessage(Request $request, TicketMessage $message)
    {
        $validated = $request->validate(['message' => 'required|string']);
        $message->update($validated);

        return back()->with('success', 'Сообщение обновлено.');
    }

    public function deleteMessage(TicketMessage $message)
    {
        $message->delete();

        return back()->with('success', 'Сообщение удалено.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('admin.tickets.index')->with('success', 'Ticket deleted successfully.');
    }
}
