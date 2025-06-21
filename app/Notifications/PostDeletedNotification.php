<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Item;

class PostDeletedNotification extends Notification implements ShouldQueue
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
        $message = 'Tin đăng "' . $this->item->title . '" của bạn đã bị xóa khỏi hệ thống.';
        
        if ($this->reason) {
            $message .= ' Lý do: ' . $this->reason;
        }
        
        return [
            'title' => $message,
            'item_id' => $this->item->id,
            'type' => 'deleted',
            'reason' => $this->reason,
        ];
    }
} 