<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PhoneHelper;
use Illuminate\Http\Request;

class PasswordRecoveryController extends Controller
{
    public function show()
    {
        return view('auth.recuperar-pwd');
    }

    public function recover(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'telefono' => 'required|string',
            'nueva_contrasena' => 'required|string|min:6|confirmed',
        ]);

        $telefonoNormalizado = PhoneHelper::normalize($request->telefono);

        $usuario = User::where('email', $request->email)
            ->where('telefono', $telefonoNormalizado)
            ->first();

        if (!$usuario) {
            return back()
                ->withErrors(['error' => 'No encontramos una cuenta con ese email y teléfono.'])
                ->withInput();
        }

        $usuario->update([
            'password' => bcrypt($request->nueva_contrasena),
        ]);

        return redirect('/acceso-psicologa')->with('success', 'Contraseña actualizada. Ahora puedes iniciar sesión.');
    }
}
