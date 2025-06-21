<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\ItemImage;
use Illuminate\Support\Facades\DB;

class ItemImageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('item_images')->delete();

        // Map tiêu đề item => tên file ảnh
        $imageMap = [
            'Máy tính laptop Gamming' => 'laptopGaming.jpg',
            'Ví da màu nâu' => 'vida.png',
            'Chìa khóa xe máy Honda' => 'chiakhoaxemay.png',
            'Thẻ sinh viên' => 'thesinhvien.png',
            'Balo màu đen' => 'Balomauden.png',
            'Điện thoại iPhone 12' => 'iphone12.png',
            'Áo khoác gió màu xanh' => 'aomauxanh.png',
            'Máy tính Casio fx-580VN X' => 'MayTinhCasio.png',
            'Thẻ ATM Vietcombank' => 'TheATM.png',
            'Sách Giáo trình Toán cao cấp' => 'GiaoTrinhToan.png',
        ];

        $items = Item::all();
        foreach ($items as $item) {
            $title = $item->title;
            if (isset($imageMap[$title])) {
                ItemImage::create([
                    'item_id' => $item->id,
                    'image_url' => 'item-images/' . $imageMap[$title],
                    'caption' => $title,
                ]);
            }
        }
    }
}