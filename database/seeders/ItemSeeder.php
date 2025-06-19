<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Models\Role; // Thêm dòng này
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('items')->delete();
        $faker = Faker::create('vi_VN'); // Sử dụng Faker tiếng Việt

        // Chỉ lấy user có vai trò 'user' để đăng tin
        $userRole = Role::where('name', 'user')->first();
        if (!$userRole) {
            $this->command->info('Không tìm thấy vai trò "user". ItemSeeder sẽ không chạy.');
            return;
        }
        $regularUserIds = User::where('role_id', $userRole->id)->pluck('id')->toArray();

        if (empty($regularUserIds)) {
            $this->command->info('Không có người dùng nào với vai trò "user" để tạo tin đăng.');
            return;
        }

        $categoryIds = Category::pluck('id')->toArray();
        if (empty($categoryIds)) {
            $this->command->info('Không có danh mục nào. ItemSeeder sẽ không chạy.');
            return;
        }

        $itemTypes = ['lost', 'found'];
        $statuses = ['pending_approval', 'approved', 'rejected', 'returned', 'expired'];

        for ($i = 0; $i < 50; $i++) { // Tạo 50 tin đăng mẫu
            Item::create([
                'user_id' => $faker->randomElement($regularUserIds),
                'category_id' => $faker->randomElement($categoryIds),
                'title' => $faker->sentence(6),
                'description' => $faker->paragraph(3),
                'location_description' => $faker->address,
                'item_type' => $faker->randomElement($itemTypes),
                'status' => $faker->randomElement($statuses),
                'date_lost_or_found' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
                'is_contact_info_public' => $faker->boolean(30), // 30% là public
                'expiration_date' => $faker->optional(0.7, null)->dateTimeBetween('now', '+3 months'), // 70% có ngày hết hạn
                'admin_comment' => $faker->optional(0.2)->sentence, // 20% có admin comment
            ]);
        }
    }
}