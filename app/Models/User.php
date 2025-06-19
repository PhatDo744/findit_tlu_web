<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Thêm nếu dùng factory
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // Thêm HasFactory

    protected $fillable = [
        'full_name', 'email', 'password', 'role_id', 'phone_number', 'photo_url', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Quan trọng cho Laravel 10+
        'is_active' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // Notifications relationship đã có sẵn từ trait Notifiable
}