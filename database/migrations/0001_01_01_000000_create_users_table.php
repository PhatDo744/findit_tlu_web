<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // Khóa ngoại role_id sẽ được thêm ở một migration sau
            $table->string('full_name', 100);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone_number', 20)->nullable()->unique();
            $table->string('photo_url', 500)->nullable();
            $table->boolean('is_active')->default(true); // Đảm bảo cột này được thêm
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};