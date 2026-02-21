<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('category')->nullable()->after('subject');
            $table->string('status_v2')->default('open')->after('status');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sla_due_at')->nullable();
            $table->timestamp('last_reply_at')->nullable();
            $table->json('tags')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['category','status_v2','sla_due_at','last_reply_at','tags']);
            $table->dropConstrainedForeignId('assigned_to');
        });
    }
};

