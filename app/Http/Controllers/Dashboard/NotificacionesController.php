<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Setting;

class NotificacionesController extends Controller
{
    public function index()
    {
        $lastSeen = Setting::get('notifications.bookings_last_seen_at');

        $query = Cita::query()
            ->where('origen', 'publica')
            ->orderByDesc('created_at')
            ->limit(50);

        $todas = $query->get();

        $nuevas = $todas->filter(function ($cita) use ($lastSeen) {
            return !$lastSeen || $cita->created_at->gt($lastSeen);
        })->values();

        $vistas = $todas->reject(function ($cita) use ($lastSeen) {
            return !$lastSeen || $cita->created_at->gt($lastSeen);
        })->values();

        Setting::set('notifications.bookings_last_seen_at', now()->toDateTimeString(), 'notifications');

        return view('dashboard.notificaciones.index', compact('nuevas', 'vistas'));
    }
}
