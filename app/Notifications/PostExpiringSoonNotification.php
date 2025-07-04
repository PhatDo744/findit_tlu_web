<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Item;

class PostExpiringSoonNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected $item;
    public function __construct(Item $item)
    {
        $this->item = $item;
    }
    public function via($notifiable)
    {
        return ['database'];
    }
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Tin "' . $this->item->title . '" của bạn sắp hết hạn hiển thị. Hãy gia hạn nếu cần.',
            'item_id' => $this->item->id,
            'type' => 'expiring_soon',
        ];
    }
} 