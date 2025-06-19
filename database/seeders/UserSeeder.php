<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->delete();

        $adminRole = Role::where('name', 'admin')->first();
        $moderatorRole = Role::where('name', 'moderator')->first();
        $userRole = Role::where('name', 'user')->first();

        if (!$adminRole || !$moderatorRole || !$userRole) {
            $this->command->error('Chưa có đủ các Role. Hãy chạy RoleSeeder trước.');
            return;
        }

        // Tạo Admin
        User::factory()->create([
            'full_name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'phone_number' => '0123456789',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Tạo Moderator
        User::factory()->create([
            'full_name' => 'Moderator User',
            'email' => 'moderator@example.com',
            'password' => Hash::make('password'),
            'role_id' => $moderatorRole->id,
            'phone_number' => '0987654321',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Tạo một số User thường
        User::factory(10)->create([
            'role_id' => $userRole->id,
            'is_active' => true,
        ]);

        // User test cố định
        User::factory()->create([
            'full_name' => 'Test User',
            'email' => 'test@e.tlu.edu.vn',
            'password' => Hash::make('12345678'),
            'role_id' => $userRole->id,
            'phone_number' => '0900000000',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
    }
}