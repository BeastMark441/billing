<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbank_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_hash')->unique();
            $table->string('order_id')->nullable();
            $table->string('provider_payment_id')->nullable();
            $table->string('status')->nullable();
            $table->boolean('signature_valid')->default(false);
            $table->json('payload');
            $table->timestamp('processed_at')->nullable();
            $table->string('process_result')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['provider_payment_id']);
            $table->index(['status']);
            $table->index(['processed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbank_webhook_events');
    }
};
