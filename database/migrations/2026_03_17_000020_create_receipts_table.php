<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('receipt_number')->unique();
            $table->string('type');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('RUB');
            $table->string('payment_method')->nullable();

            $table->string('related_type')->nullable();
            $table->string('related_id')->nullable();

            $table->json('seller')->nullable();
            $table->json('buyer')->nullable();
            $table->json('items')->nullable();
            $table->json('meta')->nullable();

            $table->string('public_token')->unique();
            $table->string('signature', 64);

            $table->string('pdf_path')->nullable();
            $table->string('pdf_sha256', 64)->nullable();

            $table->timestamp('issued_at');
            $table->timestamps();

            $table->index(['user_id', 'issued_at']);
            $table->index(['type', 'issued_at']);
            $table->index(['amount']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
