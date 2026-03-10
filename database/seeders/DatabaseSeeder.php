<?php

namespace Database\Seeders;

use App\Models\BalanceLog;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Test User
        $testUser = User::firstOrCreate(
            ['email' => 'test@test.ru'],
            [
                'name' => 'Test User',
                'password' => 'test123',
                'account_number' => '123456',
                'phone' => '+7 (999) 000-00-00',
                'uid' => Str::uuid(),
                'role_label' => 'Пользователь',
                'birth_date' => '2000-01-01',
                'balance' => 1500.00,
            ]
        );

        // Admin User
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@admin.ru'],
            [
                'name' => 'Admin User',
                'password' => 'admin123', // Note: In real app use Hash::make, but factory/model might handle hashing or not depending on implementation.
                // However, UserFactory uses Hash::make.
                // But updateOrCreate takes raw values.
                // Let's check User model casts. It casts password => hashed.
                // So if we pass plain text 'admin123', Laravel will hash it automatically if cast is present?
                // Actually, casts 'hashed' works when setting attribute.
                'account_number' => '000001',
                'phone' => '+7 (999) 111-11-11',
                'uid' => Str::uuid(),
                'role_label' => 'Администратор',
                'role' => 'admin',
                'birth_date' => '1990-01-01',
                'balance' => 999999.00,
            ]
        );

        // Specific Example User
        $exampleUser = User::firstOrCreate(
            ['email' => 'beastmark441@gmail.com'],
            [
                'name' => 'Гаазе Руслан Владимирович',
                'password' => 'password', // Default password for example
                'account_number' => '551951',
                'phone' => '+7 (999) 476-47-96',
                'uid' => '0d6aca7a46d64d4c870fabccf608761d',
                'role_label' => 'Владелец аккаунта',
                'birth_date' => '2005-10-11',
                'balance' => 2540.50,
            ]
        );

        // Seed Expenses/BalanceLogs for Example User only if they don't exist
        if (BalanceLog::where('user_id', $exampleUser->id)->count() === 0) {
            BalanceLog::create([
                'user_id' => $exampleUser->id,
                'amount' => -450.00,
                'type' => 'expense',
                'description' => 'Аренда VDS сервера (Start)',
                'created_at' => now()->subDays(2),
            ]);
            BalanceLog::create([
                'user_id' => $exampleUser->id,
                'amount' => -1200.00,
                'type' => 'expense',
                'description' => 'Аренда игрового сервера Minecraft',
                'created_at' => now()->subDays(15),
            ]);
            BalanceLog::create([
                'user_id' => $exampleUser->id,
                'amount' => -300.00,
                'type' => 'expense',
                'description' => 'Продление домена nodeum.ru',
                'created_at' => now()->subMonth(),
            ]);
            // Initial deposit
            BalanceLog::create([
                'user_id' => $exampleUser->id,
                'amount' => 5000.00,
                'type' => 'admin_deposit',
                'description' => 'Стартовое пополнение',
                'admin_id' => $adminUser->id,
                'created_at' => now()->subMonths(2),
            ]);
        }

        // Seed Tickets for Example User only if they don't exist
        if (Ticket::where('user_id', $exampleUser->id)->count() === 0) {
            Ticket::create([
                'user_id' => $exampleUser->id,
                'subject' => 'Проблема с подключением к VDS',
                'message' => 'Не могу подключиться по SSH, ошибка connection refused.',
                'status' => 'open',
                'priority' => 'high',
                'created_at' => now()->subHours(4),
            ]);
            Ticket::create([
                'user_id' => $exampleUser->id,
                'subject' => 'Вопрос по тарифам',
                'message' => 'Как перейти на тариф Pro?',
                'status' => 'closed',
                'priority' => 'low',
                'created_at' => now()->subDays(5),
            ]);
        }
    }
}
