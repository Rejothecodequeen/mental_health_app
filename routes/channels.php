<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{receiverId}', function ($user, $receiverId) {
    // Only allow the authenticated user to listen on their own channel
    return (int) $user->id === (int) $receiverId;
});

Broadcast::channel('online', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});
