<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva cita</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f6f2f0; margin:0; padding:30px;">
    <table style="max-width:600px; margin:0 auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 16px rgba(0,0,0,.06);">
        <tr>
            <td style="background:#976147; color:#fff; padding:24px 30px;">
                <h1 style="margin:0; font-size:22px;">Nueva cita reservada</h1>
                <p style="margin:6px 0 0; font-size:14px; opacity:.85;">Te ha llegado una solicitud de cita desde tu web.</p>
            </td>
        </tr>
        <tr>
            <td style="padding:30px;">
                <h2 style="margin:0 0 16px; font-size:18px; color:#1a1414;">Detalles de la cita</h2>
                <table style="width:100%; border-collapse:collapse; font-size:15px; color:#1a1414;">
                    <tr>
                        <td style="padding:10px 0; border-bottom:1px solid #eee8e5; color:#736b6b;">Paciente</td>
                        <td style="padding:10px 0; border-bottom:1px solid #eee8e5; text-align:right; font-weight:600;">{{ $cita->nombre_provisional }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0; border-bottom:1px solid #eee8e5; color:#736b6b;">Teléfono</td>
                        <td style="padding:10px 0; border-bottom:1px solid #eee8e5; text-align:right; font-weight:600;">
                            <a href="tel:{{ $cita->telefono_provisional }}" style="text-decoration:none;">{{ $cita->telefono_provisional }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0; border-bottom:1px solid #eee8e5; color:#736b6b;">Modalidad</td>
                        <td style="padding:10px 0; border-bottom:1px solid #eee8e5; text-align:right; font-weight:600;">{{ ucfirst($cita->modalidad) }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0; border-bottom:1px solid #eee8e5; color:#736b6b;">Fecha y hora</td>
                        <td style="padding:10px 0; border-bottom:1px solid #eee8e5; text-align:right; font-weight:600;">{{ $cita->fecha_inicio->format('d/m/Y H:i') }} — {{ $cita->fecha_fin->format('H:i') }}</td>
                    </tr>
                </table>

                @if ($cita->motivo)
                    <div style="margin-top:24px; padding:16px; background:#f6f2f0; border-radius:8px;">
                        <h3 style="margin:0 0 8px; font-size:14px; color:#976147;">Motivo de la consulta</h3>
                        <p style="margin:0; color:#1a1414; line-height:1.6; font-size:14px;">{{ $cita->motivo }}</p>
                    </div>
                @endif

                <p style="margin-top:30px; font-size:13px; color:#9d9d9d; line-height:1.6;">
                    Recuerda revisar la cita en tu panel: <br><strong>{{ url('/panel-psicologa/citas') }}</strong>
                </p>

                <br>
                <hr>
                <p>
                    <a href="https://victorroblesweb.es" 
                       title="Victor Robles es el creador original de PsicoCMS" 
                       style="text-decoration:none; color: gray; font-size: 12px; text-align: center;">
                       PsicoCMS &middot; Creado por Victor Robles WEB
                    </a>
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
