<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'Admin@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
            ],
        );

        $admin = config('auth.admin_seed');

        if (blank($admin['email']) || blank($admin['password'])) {
            $this->command?->warn('Admin user was not seeded. Set ADMIN_EMAIL and ADMIN_PASSWORD in .env.');

            return;
        }

        User::updateOrCreate(
            ['email' => $admin['email']],
            [
                'name' => $admin['name'],
                'password' => $admin['password'],
            ],
        );
    }
}
