<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $query = $user->tickets()->latest();
        $filter = $request->get('status', 'all');

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        $tickets = $query->paginate(10);

        return view('dashboard.tickets.index', compact('tickets', 'filter'));
    }

    public function create()
    {
        /** @var User $user */
        $user = Auth::user();
        $orders = $user->orders()->whereIn('status', ['active', 'suspended', 'failed'])->get();

        return view('dashboard.tickets.create', compact('orders'));
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'order_id' => 'nullable|exists:orders,id',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'attachments.*' => 'nullable|file|max:102400', // 100MB
        ]);

        // Verify order belongs to user if provided
        if (! empty($validated['order_id'])) {
            $hasOrder = $user->orders()->where('id', $validated['order_id'])->exists();
            if (! $hasOrder) {
                return back()->withErrors(['order_id' => 'Выбранный заказ не найден.']);
            }
        }

        $ticket = $user->tickets()->create([
            'subject' => $validated['subject'],
            'order_id' => $validated['order_id'] ?? null,
            'message' => $validated['message'], // Initial message content for quick preview
            'priority' => $validated['priority'],
            'status' => 'open',
        ]);

        // Create initial message entry
        $message = $ticket->messages()->create([
            'user_id' => $user->id,
            'message' => $validated['message'],
        ]);

        // Handle attachments
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

        return redirect()->route('dashboard.tickets.index')->with('success', 'Тикет успешно создан!');
    }

    public function show(Ticket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $ticket->load(['messages.user', 'messages.attachments']);

        return view('dashboard.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

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

        // Automatically set status to open if user replies
        if ($ticket->status !== 'open') {
            $ticket->update(['status' => 'open']);
        }

        return back()->with('success', 'Ответ отправлен.');
    }

    public function updateMessage(Request $request, TicketMessage $message)
    {
        if ($message->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate(['message' => 'required|string']);
        $message->update($validated);

        return back()->with('success', 'Сообщение обновлено.');
    }

    public function deleteMessage(TicketMessage $message)
    {
        if ($message->user_id !== Auth::id()) {
            abort(403);
        }

        $message->delete();

        return back()->with('success', 'Сообщение удалено.');
    }
}
