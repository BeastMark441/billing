<?php

use App\Models\Ticket;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tickets = Ticket::all();
        foreach ($tickets as $ticket) {
            // Check if initial message exists (by content and user)
            // We use loose checking or just assume if no messages exist with this content
            $exists = $ticket->messages()
                ->where('user_id', $ticket->user_id)
                ->where('message', $ticket->message)
                ->exists();

            if (! $exists) {
                $ticket->messages()->create([
                    'user_id' => $ticket->user_id,
                    'message' => $ticket->message,
                    'created_at' => $ticket->created_at,
                    'updated_at' => $ticket->updated_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy rollback without deleting user data
    }
};
