<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $plantilla = <<<'HTML'
<h1 style="text-align:center;">Documento de protección de datos personales</h1>
<p style="text-align:center;"><strong>Reglamento (UE) 2016/679 (RGPD) y Ley Orgánica 3/2018 (LOPDGDD)</strong></p>
<p>&nbsp;</p>

<h2>1. Datos del responsable del tratamiento</h2>
<p><strong>{{psicologa_nombre}}</strong> (Nº de colegiado/a <strong>{{psicologa_colegiado}}</strong>), con email de contacto <strong>{{psicologa_email}}</strong> y teléfono <strong>{{psicologa_telefono}}</strong>, es la responsable del tratamiento de los datos personales facilitados por el paciente.</p>

<h2>2. Datos del paciente</h2>
<table style="width:100%;border-collapse:collapse;margin:1em 0;" border="1" cellpadding="6">
    <tr><td style="background:#f5f5f5;width:40%;"><strong>Nombre y apellidos</strong></td><td>{{nombre}} {{apellidos}}</td></tr>
    <tr><td style="background:#f5f5f5;"><strong>DNI / NIE</strong></td><td>{{dni}}</td></tr>
    <tr><td style="background:#f5f5f5;"><strong>Teléfono</strong></td><td>{{telefono}}</td></tr>
    <tr><td style="background:#f5f5f5;"><strong>Email</strong></td><td>{{email}}</td></tr>
    <tr><td style="background:#f5f5f5;"><strong>Dirección</strong></td><td>{{direccion}}</td></tr>
    <tr><td style="background:#f5f5f5;"><strong>Fecha</strong></td><td>{{fecha}}</td></tr>
</table>

<h2>3. Finalidad del tratamiento</h2>
<p>Los datos personales recogidos serán tratados con la finalidad de proporcionar los servicios de atención psicológica solicitados por el paciente, así como gestionar la relación profesional, mantener un historial clínico de las sesiones y cumplir con las obligaciones legales y deontológicas aplicables.</p>

<h2>4. Base legal del tratamiento</h2>
<p>El tratamiento de los datos se basa en el consentimiento expreso del paciente otorgado mediante la firma del presente documento, así como en la ejecución del contrato de prestación de servicios profesionales y el cumplimiento de obligaciones legales aplicables al ejercicio de la psicología.</p>

<h2>5. Conservación de los datos</h2>
<p>Los datos serán conservados durante el tiempo necesario para cumplir con la finalidad para la que fueron recabados y, en todo caso, durante el plazo legalmente establecido para la conservación de la documentación clínica (mínimo 5 años desde la última asistencia, conforme a la legislación aplicable).</p>

<h2>6. Confidencialidad y secreto profesional</h2>
<p>La psicóloga garantiza la más absoluta confidencialidad sobre los datos personales y la información compartida durante las sesiones, en cumplimiento del secreto profesional regulado por el Código Deontológico del Consejo General de la Psicología de España.</p>

<h2>7. Derechos del paciente</h2>
<p>El paciente puede ejercer en cualquier momento los derechos de acceso, rectificación, supresión, limitación, portabilidad y oposición al tratamiento de sus datos, así como retirar el consentimiento prestado, contactando con la responsable a través de los datos indicados en el apartado 1.</p>
<p>Asimismo, tiene derecho a presentar una reclamación ante la Agencia Española de Protección de Datos (www.aepd.es) si considera que el tratamiento de sus datos no se ajusta a la normativa.</p>

<h2>8. Consentimiento</h2>
<p>El paciente declara haber leído y comprendido la presente información y otorga su consentimiento expreso para el tratamiento de sus datos personales en los términos descritos.</p>

<p style="margin-top:3em;">&nbsp;</p>
<table style="width:100%;margin-top:2em;">
    <tr>
        <td style="width:50%;text-align:center;">
            <p>_________________________</p>
            <p><strong>Firma del paciente</strong></p>
            <p>{{nombre}} {{apellidos}}</p>
        </td>
        <td style="width:50%;text-align:center;">
            <p>_________________________</p>
            <p><strong>Firma de la psicóloga</strong></p>
            <p>{{psicologa_nombre}}</p>
        </td>
    </tr>
</table>
<p style="text-align:center;margin-top:2em;">Fecha: {{fecha}}</p>
HTML;

        DB::table('settings')->updateOrInsert(
            ['key' => 'proteccion_datos.plantilla_html'],
            [
                'value' => json_encode($plantilla),
                'group' => 'proteccion_datos',
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'proteccion_datos.plantilla_html')->delete();
    }
};
