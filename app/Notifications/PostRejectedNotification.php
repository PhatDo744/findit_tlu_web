<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Item;

class PostRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected $item;
    protected $reason;
    public function __construct(Item $item, $reason = null)
    {
        $this->item = $item;
        $this->reason = $reason;
    }
    public function via($notifiable)
    {
        return ['database'];
    }
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Tin đăng "' . $this->item->title . '" của bạn đã bị từ chối.' . ($this->reason ? ' Lý do: ' . $this->reason : ''),
            'item_id' => $this->item->id,
            'type' => 'rejected',
        ];
    }
} 