<?php

namespace App\View\Composers;

use App\Models\Cita;
use App\Models\Setting;
use Illuminate\View\View;

class HeaderComposer
{
    public function compose(View $view): void
    {
        $view->with('nuevasReservasCount', $this->contarNuevasReservas());
    }

    private function contarNuevasReservas(): int
    {
        $lastSeen = Setting::get('notifications.bookings_last_seen_at');

        $query = Cita::query()->where('origen', 'publica');

        if ($lastSeen) {
            $query->where('created_at', '>', $lastSeen);
        }

        return $query->count();
    }
}
