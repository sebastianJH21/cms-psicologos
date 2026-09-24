<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Support\PhoneHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class PerfilPrivadoController extends Controller
{
    public function edit()
    {
        $usuario = auth()->user();
        return view('dashboard.perfil-privado.edit', compact('usuario'));
    }

    public function update(Request $request)
    {
        $usuario = auth()->user();

        $rules = [
            'nombre'    => 'required|string|max:80',
            'apellidos' => 'nullable|string|max:120',
            'email'     => 'required|email|max:150|unique:users,email,' . $usuario->id,
            'telefono'  => 'required|string|max:30',
            'avatar'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Password::min(10)->numbers()->mixedCase()];
        }

        $data = $request->validate($rules);

        $normalizado = PhoneHelper::normalize($data['telefono']);
        if ($normalizado !== $usuario->telefono) {
            $request->validate([
                'telefono' => 'unique:users,telefono,' . $usuario->id,
            ]);
        }

        $usuario->nombre    = $data['nombre'];
        $usuario->apellidos = $data['apellidos'] ?? '';
        $usuario->email     = $data['email'];
        $usuario->telefono  = PhoneHelper::normalize($data['telefono']) ?? $data['telefono'];

        if ($request->filled('password')) {
            $usuario->password = Hash::make($data['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($usuario->avatar_path) {
                if (Storage::disk('local')->exists($usuario->avatar_path)) {
                    Storage::disk('local')->delete($usuario->avatar_path);
                } elseif (Storage::disk('public')->exists($usuario->avatar_path)) {
                    Storage::disk('public')->delete($usuario->avatar_path);
                }
            }
            $usuario->avatar_path = $this->guardarAvatar($request->file('avatar'));
        }

        $usuario->save();

        return back()->with('success', 'Datos actualizados correctamente.');
    }

    public function servirAvatar()
    {
        $usuario = auth()->user();

        if (!$usuario->avatar_path) {
            abort(404);
        }

        // Avatars nuevos en disco local (privado)
        if (Storage::disk('local')->exists($usuario->avatar_path)) {
            return response()->file(Storage::disk('local')->path($usuario->avatar_path));
        }

        // Avatars antiguos en disco público (compatibilidad hacia atrás)
        if (Storage::disk('public')->exists($usuario->avatar_path)) {
            return response()->file(Storage::disk('public')->path($usuario->avatar_path));
        }

        abort(404);
    }

    public function eliminarAvatar()
    {
        $usuario = auth()->user();
        if ($usuario->avatar_path) {
            if (Storage::disk('local')->exists($usuario->avatar_path)) {
                Storage::disk('local')->delete($usuario->avatar_path);
            } elseif (Storage::disk('public')->exists($usuario->avatar_path)) {
                Storage::disk('public')->delete($usuario->avatar_path);
            }
            $usuario->avatar_path = null;
            $usuario->save();
        }

        return back()->with('success', 'Avatar eliminado.');
    }

    private function guardarAvatar($file): string
    {
        $ext = strtolower($file->extension());
        $filename = 'avatars/' . uniqid('av_') . '.' . $ext;

        Storage::disk('local')->makeDirectory('avatars');

        if (!extension_loaded('gd') || !function_exists('imagecreatetruecolor')) {
            return $file->store('avatars', 'local');
        }

        $path = $file->getRealPath();
        $info = @getimagesize($path);
        if ($info === false) {
            return $file->store('avatars', 'local');
        }

        [$origW, $origH] = $info;
        $maxSize = 150;

        if ($origW <= $maxSize && $origH <= $maxSize) {
            return $file->store('avatars', 'local');
        }

        $ratio = min($maxSize / $origW, $maxSize / $origH);
        $newW  = (int) round($origW * $ratio);
        $newH  = (int) round($origH * $ratio);

        $src = match ($ext) {
            'png'  => function_exists('imagecreatefrompng')  ? imagecreatefrompng($path)  : false,
            'webp' => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($path) : false,
            default => function_exists('imagecreatefromjpeg') ? imagecreatefromjpeg($path) : false,
        };

        if ($src === false) {
            return $file->store('avatars', 'local');
        }

        $dst = imagecreatetruecolor($newW, $newH);
        if ($ext === 'png' || $ext === 'webp') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
        imagedestroy($src);

        $fullPath = Storage::disk('local')->path($filename);
        $saved = match ($ext) {
            'png'  => function_exists('imagepng')  && imagepng($dst, $fullPath),
            'webp' => function_exists('imagewebp') && imagewebp($dst, $fullPath, 90),
            default => function_exists('imagejpeg') && imagejpeg($dst, $fullPath, 90),
        };
        imagedestroy($dst);

        if (!$saved) {
            return $file->store('avatars', 'local');
        }

        return $filename;
    }
}
