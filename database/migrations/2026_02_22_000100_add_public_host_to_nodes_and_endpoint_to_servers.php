<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('nodes') && !Schema::hasColumn('nodes', 'public_host')) {
            Schema::table('nodes', function (Blueprint $table) {
                $table->string('public_host')->nullable()->after('ip');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('nodes') && Schema::hasColumn('nodes', 'public_host')) {
            Schema::table('nodes', function (Blueprint $table) {
                $table->dropColumn('public_host');
            });
        }
    }
};
