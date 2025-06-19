<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Notifications\PostExpiringSoonNotification;

class SendExpiringPostNotifications extends Command
{
    protected $signature = 'posts:notify-expiring {days=1}';
    protected $description = 'Gửi thông báo cho user khi bài đăng sắp hết hạn';

    public function handle()
    {
        $days = (int) $this->argument('days');
        $items = Item::expiringSoon($days)->with('user')->get();
        $count = 0;
        foreach ($items as $item) {
            if ($item->user) {
                $item->user->notify(new PostExpiringSoonNotification($item));
                $count++;
            }
        }
        $this->info("Đã gửi thông báo cho {$count} bài đăng sắp hết hạn trong {$days} ngày tới.");
    }
} 