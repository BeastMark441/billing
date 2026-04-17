<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('infrastructure_services', function (Blueprint $table) {
            $table->string('integration_type')->nullable()->after('one_per_user')->index();
        });
    }

    public function down(): void
    {
        Schema::table('infrastructure_services', function (Blueprint $table) {
            $table->dropColumn('integration_type');
        });
    }
};
