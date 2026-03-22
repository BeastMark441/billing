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
        Schema::table('orders', function (Blueprint $table) {
            $table->softDeletes(); // Добавляет deleted_at для истории и возможности восстановления
            $table->timestamp('cart_added_at')->nullable()->after('status'); // Время добавления в корзину для очистки через 7 дней
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('cart_added_at');
        });
    }
};
