<?php

namespace Tests\Feature\Admin;

use App\Models\BalanceLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceHistoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_superadmin_cannot_delete_finance_logs(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        config()->set('security.super_admin_user_ids', []);

        $log = BalanceLog::create([
            'user_id' => $admin->id,
            'amount' => 100,
            'type' => 'deposit',
            'description' => 'Test',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.finance.logs.destroy', $log), [
                'confirm' => 'DELETE',
                'password' => 'password',
            ])
            ->assertStatus(403);
    }

    public function test_superadmin_can_delete_single_finance_log(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'admin',
        ]);

        config()->set('security.super_admin_user_ids', [(string) $superAdmin->id]);

        $user = User::factory()->create();
        $log = BalanceLog::create([
            'user_id' => $user->id,
            'amount' => -50,
            'type' => 'purchase',
            'description' => 'Test',
        ]);

        $this->assertDatabaseHas('balance_logs', ['id' => $log->id]);

        $this->actingAs($superAdmin)
            ->delete(route('admin.finance.logs.destroy', $log), [
                'confirm' => 'DELETE',
                'password' => 'password',
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('balance_logs', ['id' => $log->id]);
    }

    public function test_superadmin_can_delete_finance_logs_by_filter(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'admin',
        ]);

        config()->set('security.super_admin_user_ids', [(string) $superAdmin->id]);

        $u1 = User::factory()->create();
        $u2 = User::factory()->create();

        BalanceLog::create([
            'user_id' => $u1->id,
            'amount' => 100,
            'type' => 'deposit',
            'description' => 'A',
        ]);
        BalanceLog::create([
            'user_id' => $u1->id,
            'amount' => 200,
            'type' => 'deposit',
            'description' => 'B',
        ]);
        BalanceLog::create([
            'user_id' => $u2->id,
            'amount' => 300,
            'type' => 'deposit',
            'description' => 'C',
        ]);

        $this->assertSame(3, BalanceLog::count());

        $this->actingAs($superAdmin)
            ->delete(route('admin.finance.destroy'), [
                'confirm' => 'DELETE FINANCE',
                'password' => 'password',
                'user_id' => $u1->id,
                'type' => 'deposit',
            ])
            ->assertRedirect(route('admin.finance.index'));

        $this->assertSame(1, BalanceLog::count());
        $this->assertDatabaseHas('balance_logs', ['user_id' => $u2->id]);
    }
}
