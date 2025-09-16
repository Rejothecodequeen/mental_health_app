<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Presence channel for online users
Broadcast::channel('online', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});

// Private chat channel
Broadcast::channel('chat.{receiver}.{sender}', function ($user, $receiver, $sender) {
    return (int) $user->id === (int) $receiver || (int) $user->id === (int) $sender;
});
