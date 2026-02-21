<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tickets', 'category')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->string('category')->nullable()->after('subject');
            });
        }
        if (!Schema::hasColumn('tickets', 'status_v2')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->string('status_v2')->default('open')->after('status');
            });
        }
        if (!Schema::hasColumn('tickets', 'assigned_to')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            });
        }
        if (!Schema::hasColumn('tickets', 'sla_due_at')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->timestamp('sla_due_at')->nullable();
            });
        }
        if (!Schema::hasColumn('tickets', 'last_reply_at')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->timestamp('last_reply_at')->nullable();
            });
        }
        if (!Schema::hasColumn('tickets', 'tags')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->json('tags')->nullable();
            });
        }

        if (!Schema::hasColumn('ticket_messages', 'edited_at')) {
            Schema::table('ticket_messages', function (Blueprint $table) {
                $table->timestamp('edited_at')->nullable()->after('updated_at');
            });
        }
        if (!Schema::hasColumn('ticket_messages', 'edited_by')) {
            Schema::table('ticket_messages', function (Blueprint $table) {
                $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete();
            });
        }
        if (!Schema::hasColumn('ticket_messages', 'is_internal')) {
            Schema::table('ticket_messages', function (Blueprint $table) {
                $table->boolean('is_internal')->default(false);
            });
        }
        if (!Schema::hasColumn('ticket_messages', 'deleted_at')) {
            Schema::table('ticket_messages', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ticket_messages', 'deleted_at')) {
            Schema::table('ticket_messages', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
        if (Schema::hasColumn('ticket_messages', 'edited_at')) {
            Schema::table('ticket_messages', function (Blueprint $table) {
                $table->dropColumn('edited_at');
            });
        }
        if (Schema::hasColumn('ticket_messages', 'is_internal')) {
            Schema::table('ticket_messages', function (Blueprint $table) {
                $table->dropColumn('is_internal');
            });
        }
        if (Schema::hasColumn('ticket_messages', 'edited_by')) {
            Schema::table('ticket_messages', function (Blueprint $table) {
                $table->dropConstrainedForeignId('edited_by');
            });
        }

        if (Schema::hasColumn('tickets', 'category')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
        if (Schema::hasColumn('tickets', 'status_v2')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('status_v2');
            });
        }
        if (Schema::hasColumn('tickets', 'sla_due_at')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('sla_due_at');
            });
        }
        if (Schema::hasColumn('tickets', 'last_reply_at')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('last_reply_at');
            });
        }
        if (Schema::hasColumn('tickets', 'tags')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('tags');
            });
        }
        if (Schema::hasColumn('tickets', 'assigned_to')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropConstrainedForeignId('assigned_to');
            });
        }
    }
};
