<?php

namespace App\Http\Controllers\Dashboard\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class RedesSocialesController extends Controller
{
    private const REDES = ['facebook', 'instagram', 'linkedin', 'twitter', 'youtube', 'tiktok', 'whatsapp'];

    public function edit()
    {
        $redes = [];
        foreach (self::REDES as $red) {
            $val = Setting::get("social.{$red}", '');
            $redes[$red] = is_array($val) ? '' : (string) $val;
        }

        return view('dashboard.configuracion.redes-sociales.edit', compact('redes'));
    }

    private const LABELS = [
        'facebook'  => 'Facebook',
        'instagram' => 'Instagram',
        'linkedin'  => 'LinkedIn',
        'twitter'   => 'X / Twitter',
        'youtube'   => 'YouTube',
        'tiktok'    => 'TikTok',
        'whatsapp'  => 'WhatsApp',
    ];

    public function update(Request $request)
    {
        $rules = [];
        $messages = [];
        foreach (self::REDES as $red) {
            $rules[$red] = ['nullable', 'url', 'max:500'];
            $label = self::LABELS[$red] ?? ucfirst($red);
            $messages["{$red}.url"] = "El enlace de {$label} debe ser una URL válida (ej: https://...).";
        }
        $data = $request->validate($rules, $messages);

        foreach (self::REDES as $red) {
            Setting::set("social.{$red}", (string) ($data[$red] ?? ''), 'social');
        }

        return back()->with('success', 'Redes sociales actualizadas correctamente.');
    }
}
