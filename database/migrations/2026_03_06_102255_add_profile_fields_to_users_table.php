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
        Schema::table('users', function (Blueprint $table) {
            $table->string('account_number')->nullable()->after('email');
            $table->string('phone')->nullable()->after('account_number');
            $table->string('uid')->nullable()->after('phone');
            $table->string('role_label')->default('Владелец аккаунта')->after('uid');
            $table->date('birth_date')->nullable()->after('role_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['account_number', 'phone', 'uid', 'role_label', 'birth_date']);
        });
    }
};
