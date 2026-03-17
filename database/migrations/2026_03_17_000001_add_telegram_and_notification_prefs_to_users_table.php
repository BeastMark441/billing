<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_user_id')->nullable()->after('pterodactyl_id');
            $table->string('telegram_chat_id')->nullable()->after('telegram_user_id');
            $table->timestamp('telegram_linked_at')->nullable()->after('telegram_chat_id');
            $table->boolean('notify_email')->default(true)->after('telegram_linked_at');
            $table->boolean('notify_telegram')->default(true)->after('notify_email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'telegram_user_id',
                'telegram_chat_id',
                'telegram_linked_at',
                'notify_email',
                'notify_telegram',
            ]);
        });
    }
};
