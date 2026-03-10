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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('infrastructure_service_id')->constrained()->nullOnDelete();
            $table->string('status')->default('pending'); // pending, paid, active, suspended, cancelled, failed
            $table->decimal('price', 10, 2);
            $table->json('payload')->nullable(); // Stores specific config for this order

            // Server Info
            $table->integer('pterodactyl_server_id')->nullable();
            $table->string('pterodactyl_server_identifier')->nullable();
            $table->string('server_ip')->nullable();
            $table->string('server_port')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
