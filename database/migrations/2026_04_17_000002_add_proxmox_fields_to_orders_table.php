<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('proxmox_node')->nullable()->after('server_port');
            $table->unsignedInteger('proxmox_vmid')->nullable()->after('proxmox_node');
            $table->string('proxmox_type')->nullable()->after('proxmox_vmid');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['proxmox_node', 'proxmox_vmid', 'proxmox_type']);
        });
    }
};

