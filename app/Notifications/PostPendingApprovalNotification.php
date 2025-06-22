<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Item;

class PostPendingApprovalNotification extends Notification implements ShouldQueue
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
            'title' => 'Tin "' . $this->item->title . '" đã được bạn đang chờ duyệt.',
            'item_id' => $this->item->id,
            'type' => 'pending_approval',
        ];
    }
} 