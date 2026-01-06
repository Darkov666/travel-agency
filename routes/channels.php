<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\ServiceOrder;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('service-order.{serviceOrder}', function ($user, ServiceOrder $serviceOrder) {
    if ($user->role === 'root' || $user->role === 'admin') {
        return true;
    }
    return (int) $user->id === (int) $serviceOrder->driver_id;
});

Broadcast::channel('admin-map', function ($user) {
    return $user->role === 'root' || $user->role === 'admin';
});
