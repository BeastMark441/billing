<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_message_edits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_message_id')->constrained('ticket_messages')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('old_message');
            $table->text('new_message');
            $table->timestamp('edited_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_message_edits');
    }
};

