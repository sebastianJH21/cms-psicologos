<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;

class PrivacidadController extends Controller
{
    public function index()
    {
        $user = User::first();
        $profile = Profile::first();

        return view('public.privacidad', [
            'nombre' => $user ? trim($user->nombre . ' ' . $user->apellidos) : null,
            'emailPublico' => $profile?->email_publico,
            'telefonoPublico' => $profile?->telefono_publico,
            'numeroColegiado' => $profile?->numero_colegiado,
            'direccion' => $profile?->direccion,
        ]);
    }
}
