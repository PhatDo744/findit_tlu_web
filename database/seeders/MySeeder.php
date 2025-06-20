<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userRole = Role::where('name', 'user')->first();

        // User test cố định
        User::factory()->create([
            'full_name' => 'Quang',
            'email' => 'test@e.tlu.edu.vn',
            'password' => Hash::make('12345678'),
            'role_id' => $userRole->id,
            'phone_number' => '0900000000',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
    }
}
