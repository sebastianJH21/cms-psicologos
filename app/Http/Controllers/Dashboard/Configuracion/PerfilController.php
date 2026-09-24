<?php

namespace App\Http\Controllers\Dashboard\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Configuracion\PerfilRequest;
use App\Models\Profile;
use App\Services\ImagenOptimizer;
use Illuminate\Support\Facades\Storage;
use Mews\Purifier\Facades\Purifier;

class PerfilController extends Controller
{
    public function __construct(private ImagenOptimizer $optimizador)
    {
    }

    public function edit()
    {
        return view('dashboard.configuracion.perfil.edit', [
            'profile' => Profile::singleton(),
        ]);
    }

    public function update(PerfilRequest $request)
    {
        $profile = Profile::singleton();
        $datos = $request->validated();

        if (isset($datos['sobre_mi'])) {
            $datos['sobre_mi'] = Purifier::clean($datos['sobre_mi'], 'blog');
        }

        if ($request->hasFile('foto')) {
            if ($profile->foto_path && Storage::disk('public')->exists($profile->foto_path)) {
                Storage::disk('public')->delete($profile->foto_path);
            }
            $datos['foto_path'] = $this->optimizador->procesarYGuardar(
                $request->file('foto'),
                'profile',
                1200,
                82
            );
        }
        unset($datos['foto']);

        $profile->fill($datos)->save();

        return redirect()
            ->route('dashboard.configuracion.perfil.edit')
            ->with('success', 'Perfil público actualizado correctamente.');
    }

    public function eliminarFoto()
    {
        $profile = Profile::singleton();
        if ($profile->foto_path && Storage::disk('public')->exists($profile->foto_path)) {
            Storage::disk('public')->delete($profile->foto_path);
        }
        $profile->update(['foto_path' => null]);

        return redirect()
            ->route('dashboard.configuracion.perfil.edit')
            ->with('success', 'Foto eliminada.');
    }
}
