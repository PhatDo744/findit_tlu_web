<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Cho soft delete
use Illuminate\Database\Eloquent\Factories\HasFactory; // Thêm nếu dùng factory
use Illuminate\Notifications\DatabaseNotification; // Sửa nếu cần

class Item extends Model
{
    use HasFactory, SoftDeletes; // Thêm HasFactory và SoftDeletes

    protected $fillable = [
        'user_id', 'category_id', 'title', 'description', 'location_description',
        'item_type', 'status', 'date_lost_or_found', 'is_contact_info_public',
        'expiration_date', 'admin_comment',
    ];

    protected $casts = [
        'date_lost_or_found' => 'date',
        'expiration_date' => 'datetime',
        'is_contact_info_public' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ItemImage::class);
    }

    public function notifications()
    {
        // Nếu bạn muốn truy cập notifications liên quan trực tiếp đến item này qua item_id
        return $this->hasMany(DatabaseNotification::class, 'item_id');
    }

    public function scopeExpiringSoon($query, $days = 1)
    {
        $now = now();
        $soon = now()->addDays($days);
        return $query->where('expiration_date', '>', $now)
                     ->where('expiration_date', '<=', $soon)
                     ->where('status', 'approved');
    }
}