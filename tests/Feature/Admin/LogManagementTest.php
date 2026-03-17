<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_superadmin_cannot_delete_logs(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        config()->set('security.super_admin_user_ids', []);

        $response = $this->actingAs($admin)
            ->delete(route('admin.logs.destroy'), [
                'confirm' => 'DELETE ALL',
                'password' => 'password',
            ]);

        $response->assertStatus(403);
    }

    public function test_superadmin_can_delete_user_audit_logs_with_confirmation_and_password(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'admin',
        ]);

        config()->set('security.super_admin_user_ids', [(string) $superAdmin->id]);

        $target = User::factory()->create([
            'role' => 'user',
        ]);

        AuditLog::create([
            'user_id' => $target->id,
            'action' => 'auth_login',
            'severity' => 'info',
            'object_type' => 'user',
            'object_id' => (string) $target->id,
            'meta' => ['ip' => '127.0.0.1'],
        ]);

        AuditLog::create([
            'user_id' => $target->id,
            'action' => 'order_created',
            'severity' => 'info',
            'object_type' => 'order',
            'object_id' => '1',
            'meta' => [],
        ]);

        $this->assertSame(2, AuditLog::where('user_id', $target->id)->count());

        $response = $this->actingAs($superAdmin)
            ->delete(route('admin.logs.user.destroy'), [
                'source' => 'audit',
                'user_id' => $target->id,
                'confirm' => 'DELETE',
                'password' => 'password',
            ]);

        $response->assertRedirect(route('admin.logs.index', ['source' => 'audit']));

        $this->assertSame(0, AuditLog::where('user_id', $target->id)->count());
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_logs_user_deleted',
            'severity' => 'warning',
        ]);
    }

    public function test_superadmin_can_delete_all_logs(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'admin',
        ]);

        config()->set('security.super_admin_user_ids', [(string) $superAdmin->id]);

        $user = User::factory()->create();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'auth_login',
            'severity' => 'info',
            'object_type' => 'user',
            'object_id' => (string) $user->id,
            'meta' => [],
        ]);

        UserLog::create([
            'user_id' => $user->id,
            'action' => 'auth_login',
            'details' => 'x',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'test',
        ]);

        $response = $this->actingAs($superAdmin)
            ->delete(route('admin.logs.destroy'), [
                'confirm' => 'DELETE ALL',
                'password' => 'password',
            ]);

        $response->assertRedirect(route('admin.logs.index'));

        $this->assertSame(1, AuditLog::count());
        $this->assertSame(0, UserLog::count());
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_logs_all_deleted',
            'severity' => 'warning',
        ]);
    }
}
