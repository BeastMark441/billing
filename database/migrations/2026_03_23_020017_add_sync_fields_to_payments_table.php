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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'credited_at')) {
                $table->timestamp('credited_at')->nullable()->after('status');
            }
            $table->integer('sync_attempts')->default(0)->after('payload');
            $table->timestamp('last_sync_at')->nullable()->after('sync_attempts');
            $table->string('error_message')->nullable()->after('last_sync_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['sync_attempts', 'last_sync_at', 'error_message']);
        });
    }
};
