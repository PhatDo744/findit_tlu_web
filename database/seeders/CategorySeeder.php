<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->delete();

        $categories = [
            ['name' => 'Điện tử', 'description' => 'Các thiết bị điện tử như điện thoại, laptop, tai nghe.'],
            ['name' => 'Giấy tờ tùy thân', 'description' => 'CMND/CCCD, bằng lái xe, thẻ sinh viên, hộ chiếu.'],
            ['name' => 'Ví/Túi xách', 'description' => 'Các loại ví tiền, túi xách, balo.'],
            ['name' => 'Quần áo/Phụ kiện', 'description' => 'Quần áo, giày dép, mũ, kính, trang sức.'],
            ['name' => 'Sách vở/Dụng cụ học tập', 'description' => 'Sách, vở, bút, thước, máy tính cầm tay.'],
            ['name' => 'Chìa khóa', 'description' => 'Chìa khóa nhà, chìa khóa xe, các loại khóa khác.'],
            ['name' => 'Khác', 'description' => 'Các đồ vật không thuộc danh mục trên.'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}