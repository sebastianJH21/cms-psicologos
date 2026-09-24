<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const MAX_ATTEMPTS_IP = 20;
    private const DECAY_SECONDS = 900;

    public function show(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended('/panel-psicologa');
        }

        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $key = 'login:' . $request->throttleKey();
        $ipKey = 'login-ip:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)
            || RateLimiter::tooManyAttempts($ipKey, self::MAX_ATTEMPTS_IP)) {
            $seconds = max(RateLimiter::availableIn($key), RateLimiter::availableIn($ipKey));
            $minutes = (int) ceil($seconds / 60);
            \Illuminate\Support\Facades\Log::warning('Login bloqueado por rate limit', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
            ]);
            throw ValidationException::withMessages([
                'email' => "Demasiados intentos. Vuelve a probar en {$minutes} minutos.",
            ]);
        }

        $data = $request->validated();

        $user = User::query()
            ->where('email', $data['email'])
            ->where('telefono', $data['telefono'])
            ->first();

        $credentialsOk = $user
            && Auth::attempt(
                ['email' => $data['email'], 'password' => $data['password']],
                (bool) ($data['remember'] ?? false)
            );

        if (!$credentialsOk) {
            RateLimiter::hit($key, self::DECAY_SECONDS);
            RateLimiter::hit($ipKey, self::DECAY_SECONDS);
            \Illuminate\Support\Facades\Log::warning('Login fallido', [
                'email' => $data['email'],
                'ip' => $request->ip(),
            ]);
            throw ValidationException::withMessages([
                'email' => 'Las credenciales no son correctas. Revisa email, teléfono y contraseña.',
            ]);
        }

        RateLimiter::clear($key);
        RateLimiter::clear($ipKey);
        $request->session()->regenerate();

        return redirect()->intended('/panel-psicologa');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/acceso-psicologa');
    }
}
