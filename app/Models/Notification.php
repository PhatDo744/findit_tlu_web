<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification as BaseDatabaseNotification;

class Notification extends BaseDatabaseNotification
{
    // Bạn không cần khai báo $fillable ở đây vì nó được quản lý bởi lớp cha
    // và hệ thống notification của Laravel.

    /**
     * Lấy item liên quan đến notification này (nếu có).
     */
    public function item()
    {
        // Giả sử cột khóa ngoại trong bảng 'notifications' là 'item_id'
        return $this->belongsTo(Item::class, 'item_id');
    }

    // Mối quan hệ 'notifiable' đã được định nghĩa trong lớp cha (morphTo)
    // public function notifiable()
    // {
    //     return $this->morphTo();
    // }
}