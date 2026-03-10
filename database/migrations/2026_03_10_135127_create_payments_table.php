<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('payment_id')->unique()->nullable(); // ID from T-Bank
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending'); // pending, authorized, confirmed, canceled, rejected, refunded
            $table->string('payment_url')->nullable();
            $table->json('payload')->nullable(); // Store extra info from init/callback
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
