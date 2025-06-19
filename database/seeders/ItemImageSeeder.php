<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\ItemImage;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ItemImageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('item_images')->delete();
        $faker = Faker::create();

        $itemIds = Item::pluck('id')->toArray();

        if (empty($itemIds)) {
            $this->command->info('Không có tin đăng nào để thêm ảnh.');
            return;
        }

        foreach ($itemIds as $itemId) {
            // Mỗi tin đăng có từ 0 đến 3 ảnh
            $numberOfImages = $faker->numberBetween(0, 3);
            for ($i = 0; $i < $numberOfImages; $i++) {
                ItemImage::create([
                    'item_id' => $itemId,
                    'image_url' => $faker->imageUrl(640, 480, 'technics', true, 'Faker'), // Thêm tham số text
                    'caption' => $faker->optional(0.5)->sentence(3), // 50% có caption
                ]);
            }
        }
    }
}