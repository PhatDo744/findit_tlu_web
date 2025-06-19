<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->delete(); // Xóa dữ liệu cũ để tránh trùng lặp

        Role::create(['name' => 'admin', 'description' => 'Quản trị viên hệ thống']);
        Role::create(['name' => 'moderator', 'description' => 'Kiểm duyệt viên bài đăng']);
        Role::create(['name' => 'user', 'description' => 'Người dùng thông thường']);
    }
}