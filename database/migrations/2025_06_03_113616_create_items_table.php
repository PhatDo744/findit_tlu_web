<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('title', 200);
            $table->text('description');
            $table->string('location_description', 500);
            $table->enum('item_type', ['lost', 'found']);
            $table->string('status', 20)->default('pending_approval'); // pending_approval, approved, rejected, returned, expired
            $table->date('date_lost_or_found');
            $table->boolean('is_contact_info_public')->default(false);
            $table->dateTime('expiration_date')->nullable();
            $table->text('admin_comment')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Thêm cột deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};