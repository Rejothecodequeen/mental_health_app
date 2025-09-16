<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register broadcasting routes
        Broadcast::routes(['middleware' => ['web', 'auth']]); // <--- use 'web' or 'auth:sanctum'

        require base_path('routes/channels.php');
    }
}
