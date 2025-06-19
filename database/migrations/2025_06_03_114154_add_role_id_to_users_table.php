<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Thêm cột role_id sau cột id (hoặc một cột khác tùy bạn chọn)
            $table->foreignId('role_id')->after('id')->constrained('roles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']); // Xóa khóa ngoại trước
            $table->dropColumn('role_id');   // Sau đó xóa cột
        });
    }
};