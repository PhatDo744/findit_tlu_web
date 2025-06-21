<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('items')->delete();

        // Lấy user và category mẫu
        $user = User::where('email', 'test@e.tlu.edu.vn')->first();
        if (!$user) {
            $this->command->info('Không tìm thấy user test@e.tlu.edu.vn. ItemSeeder sẽ không chạy.');
            return;
        }
        $categories = Category::pluck('id', 'name')->toArray();

        $items = [
            [
                'title' => 'Máy tính laptop Gamming',
                'description' => 'Laptop hiệu Dell, màu xám bạc, model Inspiron 15, có dán sticker logo công ty ở mặt sau. Máy tính mới mua khoảng 1 năm, bên trong chứa nhiều dữ liệu quan trọng phục vụ công việc và học tập. Mất tại khu vực thư viện C1 vào chiều ngày 15/05/2025, khoảng 15h30, khi đang đặt máy trên bàn và rời đi một lúc để lấy sách.',
                'location_description' => 'Thư viện T45, tầng 2, khu vực đọc sách',
                'item_type' => 'lost',
                'status' => 'approved',
                'category_id' => $categories['Điện tử'] ?? 1,
                'date_lost_or_found' => '2025-05-15',
            ],
            [
                'title' => 'Ví da màu nâu',
                'description' => 'Ví da nam màu nâu, bên trong có giấy tờ tùy thân mang tên Nguyễn Văn B, một số tiền mặt và thẻ sinh viên. Bị mất khi đi ăn trưa tại căn tin.',
                'location_description' => 'Căn tin trường, gần khu vực bán cơm',
                'item_type' => 'lost',
                'status' => 'approved',
                'category_id' => $categories['Ví/Túi xách'] ?? 3,
                'date_lost_or_found' => '2025-05-10',
            ],
            [
                'title' => 'Chìa khóa xe máy Honda',
                'description' => 'Chìa khóa xe máy Honda có móc khóa hình con mèo, bị rơi khi đi từ bãi gửi xe sang giảng đường.',
                'location_description' => 'Bãi gửi xe K1',
                'item_type' => 'lost',
                'status' => 'approved',
                'category_id' => $categories['Chìa khóa'] ?? 6,
                'date_lost_or_found' => '2025-05-12',
            ],
            [
                'title' => 'Thẻ sinh viên',
                'description' => 'Thẻ sinh viên mang tên Trần Thị C, mã số 123456, bị mất khi đi học tại giảng đường A2.',
                'location_description' => 'Giảng đường A2, tầng 1',
                'item_type' => 'lost',
                'status' => 'approved',
                'category_id' => $categories['Giấy tờ tùy thân'] ?? 2,
                'date_lost_or_found' => '2025-05-09',
            ],
            [
                'title' => 'Balo màu đen',
                'description' => 'Balo màu đen hiệu Adidas, bên trong có sách vở và một áo khoác. Bị mất khi để ở phòng tự học.',
                'location_description' => 'Phòng tự học tầng 3, nhà C5',
                'item_type' => 'lost',
                'status' => 'approved',
                'category_id' => $categories['Ví/Túi xách'] ?? 3,
                'date_lost_or_found' => '2025-05-13',
            ],
            [
                'title' => 'Điện thoại iPhone 12',
                'description' => 'Điện thoại iPhone 12 màu xanh, ốp lưng trong suốt, bị mất khi tham gia hoạt động ngoại khóa.',
                'location_description' => 'Sân bóng trường',
                'item_type' => 'lost',
                'status' => 'approved',
                'category_id' => $categories['Điện tử'] ?? 1,
                'date_lost_or_found' => '2025-05-08',
            ],
            [
                'title' => 'Áo khoác gió màu xanh',
                'description' => 'Áo khoác gió màu xanh, hiệu Uniqlo, bị bỏ quên tại phòng học.',
                'location_description' => 'Phòng học B5-201',
                'item_type' => 'lost',
                'status' => 'approved',
                'category_id' => $categories['Quần áo/Phụ kiện'] ?? 4,
                'date_lost_or_found' => '2025-05-11',
            ],
            [
                'title' => 'Máy tính Casio fx-580VN X',
                'description' => 'Máy tính cầm tay Casio fx-580VN X, dán tên Nguyễn Văn D ở mặt sau, bị mất trong giờ kiểm tra.',
                'location_description' => 'Phòng thi tầng 2, nhà A5',
                'item_type' => 'found',
                'status' => 'approved',
                'category_id' => $categories['Sách vở/Dụng cụ học tập'] ?? 5,
                'date_lost_or_found' => '2025-05-07',
            ],
            [
                'title' => 'Thẻ ATM Vietcombank',
                'description' => 'Thẻ ATM Vietcombank mang tên Lê Thị E, bị rơi khi rút tiền tại cây ATM.',
                'location_description' => 'Cây ATM cạnh cổng trường',
                'item_type' => 'found',
                'status' => 'approved',
                'category_id' => $categories['Giấy tờ tùy thân'] ?? 2,
                'date_lost_or_found' => '2025-05-06',
            ],
            [
                'title' => 'Sách Giáo trình Toán cao cấp',
                'description' => 'Sách Giáo trình Toán cao cấp, bìa màu vàng, có ghi chú bằng bút đỏ ở trang đầu.',
                'location_description' => 'Thư viện T45, tầng 1',
                'item_type' => 'found',
                'status' => 'approved',
                'category_id' => $categories['Sách vở/Dụng cụ học tập'] ?? 5,
                'date_lost_or_found' => '2025-05-05',
            ],
            
        ];

        foreach ($items as $item) {
            Item::create(array_merge($item, [
                'user_id' => $user->id,
                'is_contact_info_public' => true,
                'expiration_date' => now()->addDays(14),
            ]));
        }
    }
}