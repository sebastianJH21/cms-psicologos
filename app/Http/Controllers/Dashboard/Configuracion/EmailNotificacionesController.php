<?php

namespace App\Http\Controllers\Dashboard\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class EmailNotificacionesController extends Controller
{
    public function edit()
    {
        $config = [
            'smtp_host'    => Setting::get('mail.smtp_host', ''),
            'smtp_port'    => Setting::get('mail.smtp_port', 587),
            'smtp_user'    => Setting::get('mail.smtp_user', ''),
            'from_address' => Setting::get('mail.from_address', ''),
            'notif_enabled'=> Setting::get('mail.notif_enabled', false),
            'has_password' => !empty(Setting::get('mail.smtp_password', '')),
        ];

        return view('dashboard.configuracion.email-notificaciones.edit', compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'smtp_host'     => 'nullable|string|max:255',
            'smtp_port'     => 'nullable|integer|min:1|max:65535',
            'smtp_user'     => 'nullable|email|max:255',
            'smtp_password' => 'nullable|string|max:500',
            'from_address'  => 'nullable|email|max:255',
        ]);

        Setting::set('mail.smtp_host', $request->smtp_host ?? '', 'mail');
        Setting::set('mail.smtp_port', (int) ($request->smtp_port ?? 587), 'mail');
        Setting::set('mail.smtp_user', $request->smtp_user ?? '', 'mail');
        Setting::set('mail.from_address', $request->from_address ?? '', 'mail');
        Setting::set('mail.notif_enabled', $request->boolean('notif_enabled'), 'mail');

        if ($request->filled('smtp_password')) {
            $passwordLimpia = preg_replace('/\s+/', '', $request->smtp_password);
            Setting::set('mail.smtp_password', Crypt::encryptString($passwordLimpia), 'mail');
        }

        return back()->with('success', 'Configuración de email guardada correctamente.');
    }
}
