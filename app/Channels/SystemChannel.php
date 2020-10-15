<?php

namespace App\Channels;

use App\Models\Notification;
use Illuminate\Notifications\Notification as LaravelNotification;
use Illuminate\Support\Facades\Auth;

class SystemChannel
{
    public function send($notifiable, LaravelNotification $notification)
    {
        $message = $notification->toSystem($notifiable);
    
        Notification::create([
            'partner_id' => $message['partner_id'] ?? -1,
            'source_id' => $message['source_id'] ?? -1,
            'subject' => $message['subject'],
            'message' => $message['message'],
            'redirect_url' => $message['redirect_url'],
            'recipient' => $message['recipient'],
            'status' => 'A',
            'create_by' => $message['create_by'] ?? (Auth::user()->username ?? 'SYSTEM'),
            'update_by' => $message['update_by'] ?? (Auth::user()->username ?? 'SYSTEM'),
        ]);
    }
}