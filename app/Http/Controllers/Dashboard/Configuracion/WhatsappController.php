<?php

namespace App\Http\Controllers\Dashboard\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Configuracion\WhatsappRequest;
use App\Models\Setting;
use App\Services\WhatsappService;

class WhatsappController extends Controller
{
    public function __construct(private WhatsappService $whatsapp)
    {
    }

    public function edit()
    {
        $plantilla = $this->whatsapp->plantilla();
        $plantillaPago = $this->whatsapp->plantillaPago();

        return view('dashboard.configuracion.whatsapp.edit', [
            'plantilla' => $plantilla,
            'plantillaPago' => $plantillaPago,
            'variables' => WhatsappService::VARIABLES,
            'variablesPago' => WhatsappService::VARIABLES_PAGO,
            'emojis' => WhatsappService::EMOJIS_SUGERIDOS,
            'enlaceMasEmojis' => WhatsappService::ENLACE_MAS_EMOJIS,
            'previsualizacion' => $this->whatsapp->previsualizar($plantilla),
            'previsualizacionPago' => $this->whatsapp->previsualizarPago($plantillaPago),
            'datosPrevisualizacion' => $this->whatsapp->datosPrevisualizacion(),
            'datosPrevisualizacionPago' => $this->whatsapp->datosPrevisualizacionPago(),
        ]);
    }

    public function update(WhatsappRequest $request)
    {
        Setting::set('whatsapp.plantilla_confirmacion', $request->validated('plantilla_confirmacion'), 'whatsapp');
        Setting::set('whatsapp.plantilla_recordatorio_pago', $request->validated('plantilla_recordatorio_pago'), 'whatsapp');

        return redirect()
            ->route('dashboard.configuracion.whatsapp.edit')
            ->with('success', 'Plantillas de WhatsApp actualizadas correctamente.');
    }
}
