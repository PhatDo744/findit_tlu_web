# FindIt@TLU Web - Hệ thống quản lý đồ thất lạc Trường Đại học Thủy Lợi

## Giới thiệu
FindIt@TLU là hệ thống web giúp quản lý, đăng tin tìm kiếm và trả lại đồ thất lạc cho sinh viên, cán bộ tại Trường Đại học Thủy Lợi. Dự án xây dựng trên nền tảng Laravel (PHP) kết hợp giao diện hiện đại với Vite + TailwindCSS.

## Tính năng chính
- Đăng nhập, đăng ký, quên mật khẩu cho người dùng
- Quản trị viên quản lý người dùng, danh mục, bài đăng
- Người dùng đăng tin tìm/nhặt được đồ, cập nhật trạng thái
- Quản lý thông báo, xác thực email, phân quyền
- API cho mobile app hoặc tích hợp bên ngoài (sử dụng Laravel Sanctum để xác thực)

## Yêu cầu hệ thống
- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL/MariaDB

## Cài đặt & Khởi động
1. **Clone dự án:**
   ```bash
   git clone <repo-url>
   cd findit_tlu_web
   ```
2. **Cài đặt thư viện:**
   ```bash
   composer install
   npm install
   ```
3. **Cài đặt và cấu hình Sanctum:**
   ```bash
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   php artisan migrate
   ```
   - Đảm bảo đã thêm middleware `\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class` vào nhóm middleware `api` trong `app/Http/Kernel.php` nếu cần.
4. **Tạo file cấu hình môi trường:**
   ```bash
   cp .env.example .env
   ```
   - Chỉnh sửa thông tin DB, mail... trong file `.env` cho phù hợp.
5. **Sinh key ứng dụng:**
   ```bash
   php artisan key:generate
   ```
6. **Tạo database và migrate:**
   - Tạo database `findit_tlu_web` (hoặc tên khác, sửa trong `.env`)
   ```bash
   php artisan migrate
   php artisan db:seed
   ```
7. **Chạy ứng dụng:**
   - Chạy server backend Laravel:
     ```bash
     php artisan serve
     ```
   - Chạy queue xử lý background (bắt buộc để gửi mail/thông báo):
     ```bash
     php artisan queue:work
     ```
   - Chạy frontend Vite (tự động reload khi sửa giao diện):
     ```bash
     npm run dev
     ```
   - Hoặc build frontend production:
     ```bash
     npm run build
     ```
   - Truy cập: http://localhost:8000
   
   > **Tip:** Có thể dùng lệnh sau để chạy đồng thời cả backend, queue và frontend (nếu đã cài `concurrently`):
   > ```bash
   > npx concurrently "php artisan serve" "php artisan queue:work" "npm run dev"
   > ```

## Tài khoản mẫu
- Admin: `admin@example.com` / `password`
- Moderator: `moderator@example.com` / `password`
- User: `test@e.tlu.edu.vn` / `12345678`

## Cấu trúc thư mục
- `app/` - Code backend Laravel
- `resources/views/` - Giao diện Blade
- `resources/js/`, `resources/css/` - Frontend Vite + Tailwind
- `routes/` - Định nghĩa route web/api
- `database/seeders/` - Dữ liệu mẫu

## API
- Xem chi tiết các route API trong `routes/api.php`
- Sử dụng xác thực Sanctum cho các route cần đăng nhập

## Đóng góp & phát triển
- Fork, tạo branch mới, pull request
- Liên hệ nhóm phát triển nếu cần hỗ trợ

---
© 2025 Nhóm 2 - FindIt@TLU