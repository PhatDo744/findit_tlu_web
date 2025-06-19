<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class, // Chạy UserSeeder sau RoleSeeder
            CategorySeeder::class,
            ItemSeeder::class, // Chạy ItemSeeder sau UserSeeder và CategorySeeder
            ItemImageSeeder::class, // Chạy ItemImageSeeder sau ItemSeeder
            // NotificationSeeder::class, // Tùy chọn
        ]);
    }
}