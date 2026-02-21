<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tickets') && !Schema::hasColumn('tickets', 'server_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->unsignedBigInteger('server_id')->nullable()->after('user_id');
                $table->foreign('server_id')->references('id')->on('servers')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tickets') && Schema::hasColumn('tickets', 'server_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropForeign(['server_id']);
                $table->dropColumn('server_id');
            });
        }
    }
};
