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
        Schema::table('infrastructure_services', function (Blueprint $table) {
            $table->foreignId('infrastructure_subcategory_id')->nullable()->after('infrastructure_category_id')->constrained()->nullOnDelete();
            $table->foreignId('infrastructure_category_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('infrastructure_services', function (Blueprint $table) {
            $table->dropForeign(['infrastructure_subcategory_id']);
            $table->dropColumn('infrastructure_subcategory_id');
            $table->foreignId('infrastructure_category_id')->nullable(false)->change();
        });
    }
};
