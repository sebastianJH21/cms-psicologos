<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <title>Política de privacidad{{ $nombre ? ' — ' . $nombre : '' }}</title>
    @include('_shared.favicon')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { font-size: 62.5%; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 1.6rem;
            line-height: 1.75;
            color: #2d2a32;
            background: #f6f5f3;
            padding: 4rem 1.6rem;
        }
        .priv {
            max-width: 80rem;
            margin: 0 auto;
            background: #fff;
            border-radius: 1.6rem;
            padding: 4.8rem clamp(2rem, 5vw, 5.6rem);
            box-shadow: 0 1rem 4rem rgba(0,0,0,.06);
        }
        .priv__back {
            display: inline-flex;
            align-items: center;
            gap: .6rem;
            color: #6b8e7f;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.4rem;
            margin-bottom: 2.4rem;
        }
        .priv__back:hover { text-decoration: underline; }
        .priv h1 {
            font-size: clamp(2.6rem, 4vw, 3.4rem);
            line-height: 1.25;
            color: #1f3d35;
            margin-bottom: .8rem;
        }
        .priv__updated { color: #8a8691; font-size: 1.35rem; margin-bottom: 3.2rem; }
        .priv h2 {
            font-size: 2rem;
            color: #1f3d35;
            margin: 3.6rem 0 1.2rem;
            padding-bottom: .6rem;
            border-bottom: 2px solid #e7e4df;
        }
        .priv p, .priv li { font-size: 1.55rem; }
        .priv p { margin-bottom: 1.2rem; }
        .priv ul { margin: 0 0 1.6rem 2rem; }
        .priv li { margin-bottom: .6rem; }
        .priv__datos {
            background: #f1f4f2;
            border-left: 4px solid #6b8e7f;
            border-radius: .8rem;
            padding: 2rem 2.4rem;
            margin: 1.6rem 0 2.4rem;
            list-style: none;
        }
        .priv__datos li { margin-bottom: .8rem; }
        .priv__datos li:last-child { margin-bottom: 0; }
        .priv__datos a { color: #4a6b5c; }
        .priv__foot {
            margin-top: 4rem;
            padding-top: 2.4rem;
            border-top: 1px solid #e7e4df;
            color: #8a8691;
            font-size: 1.35rem;
        }
    </style>
</head>
<body>
    <main class="priv">
        <a href="{{ url('/') }}" class="priv__back"><i class="fa-solid fa-arrow-left"></i> Volver al inicio</a>

        <h1>Política de privacidad</h1>
        <p class="priv__updated">Última actualización: {{ now()->translatedFormat('d \d\e F \d\e Y') }}</p>

        <p>
            La presente Política de Privacidad regula el tratamiento de los datos personales que
            puedas facilitar al solicitar una cita o utilizar los servicios de psicología ofrecidos
            a través de este sitio web. El tratamiento se realiza conforme al Reglamento (UE)
            2016/679 (RGPD) y a la Ley Orgánica 3/2018, de 5 de diciembre, de Protección de Datos
            Personales y garantía de los derechos digitales (LOPDGDD).
        </p>

        <h2>1. Responsable del tratamiento</h2>
        <p>La persona responsable del tratamiento de tus datos es:</p>
        <ul class="priv__datos">
            @if ($nombre)
                <li><i class="fa-solid fa-user-doctor"></i> <strong>{{ $nombre }}</strong></li>
            @else
                <li><i class="fa-solid fa-user-doctor"></i> <strong>La profesional de psicología titular de este sitio</strong></li>
            @endif
            @if ($numeroColegiado)
                <li><i class="fa-solid fa-id-badge"></i> N.º de colegiado/a: {{ $numeroColegiado }}</li>
            @endif
            @if ($emailPublico)
                <li><i class="fa-solid fa-envelope"></i> Email: <a href="mailto:{{ $emailPublico }}">{{ $emailPublico }}</a></li>
            @endif
            @if ($telefonoPublico)
                <li><i class="fa-solid fa-phone"></i> Teléfono: <a href="tel:{{ preg_replace('/[^+0-9]/', '', $telefonoPublico) }}">{{ $telefonoPublico }}</a></li>
            @endif
            @if ($direccion)
                <li><i class="fa-solid fa-location-dot"></i> Dirección: {{ $direccion }}</li>
            @endif
        </ul>

        <h2>2. ¿Con qué finalidad tratamos tus datos?</h2>
        <p>
            Tratamos la información que nos facilitas con la <strong>única finalidad</strong> de:
        </p>
        <ul>
            <li>Gestionar y confirmar la reserva de tu cita (presencial u online).</li>
            <li>Ponernos en contacto contigo para organizar, recordar o reprogramar la cita.</li>
            <li>Prestar y dar seguimiento a las sesiones de psicología, incluida la elaboración
                de tu historia clínica cuando seas paciente.</li>
        </ul>
        <p>
            Tus datos <strong>no se utilizarán para ninguna otra finalidad</strong>: no se emplean
            con fines publicitarios ni comerciales, ni se elaboran perfiles, ni se toman decisiones
            automatizadas con ellos.
        </p>

        <h2>3. ¿Qué datos tratamos?</h2>
        <ul>
            <li>Datos identificativos y de contacto: nombre, teléfono y, en su caso, correo
                electrónico.</li>
            <li>El motivo de consulta que, opcionalmente, decidas indicar al reservar.</li>
            <li>Datos relativos a la salud derivados de las sesiones de terapia (historia clínica).
                Estos datos pertenecen a una categoría especial y se tratan con especial diligencia,
                amparados por el <strong>secreto profesional</strong> y el deber de confidencialidad
                que rige la práctica de la psicología.</li>
        </ul>

        <h2>4. Legitimación</h2>
        <p>
            La base legal para gestionar tu cita es tu <strong>consentimiento</strong>, que prestas
            al enviar el formulario de reserva, así como la aplicación de medidas precontractuales
            y la posterior prestación del servicio de psicología solicitado. El tratamiento de los
            datos de salud se basa, además, en la finalidad de asistencia sanitaria por un
            profesional sujeto a secreto profesional.
        </p>

        <h2>5. ¿Durante cuánto tiempo conservamos tus datos?</h2>
        <p>
            Los datos se conservarán mientras dure la relación profesional y, posteriormente,
            durante los plazos legalmente exigibles para la documentación clínica. Si tu solicitud
            de cita no llega a materializarse, los datos se conservarán únicamente el tiempo
            necesario para gestionar dicha solicitud.
        </p>

        <h2>6. ¿A quién se comunican tus datos?</h2>
        <p>
            <strong>Tus datos no se ceden ni se venden a terceros.</strong> Solo serán accesibles
            por la profesional responsable. No se realizan transferencias internacionales de datos.
            Únicamente podrían comunicarse a las autoridades competentes cuando exista una
            obligación legal.
        </p>

        <h2>7. Seguridad de tus datos</h2>
        <p>
            Aplicamos las medidas técnicas y organizativas adecuadas para garantizar la seguridad
            y confidencialidad de tu información. Los datos se alojan en un servidor propio de la
            profesional, las contraseñas se almacenan cifradas y el acceso a la información está
            protegido y restringido. Trabajamos para que tus datos estén siempre protegidos frente
            a accesos no autorizados, pérdida o alteración.
        </p>

        <h2>8. ¿Cuáles son tus derechos?</h2>
        <p>Puedes ejercer en cualquier momento los siguientes derechos:</p>
        <ul>
            <li>Acceder a tus datos personales.</li>
            <li>Solicitar la rectificación de los datos inexactos.</li>
            <li>Solicitar su supresión cuando, entre otros motivos, ya no sean necesarios.</li>
            <li>Solicitar la limitación u oponerte al tratamiento.</li>
            <li>Solicitar la portabilidad de tus datos.</li>
            <li>Retirar el consentimiento prestado en cualquier momento.</li>
        </ul>
        <p>
            Para ejercer estos derechos puedes ponerte en contacto con la profesional responsable
            @if ($emailPublico)
                escribiendo a <a href="mailto:{{ $emailPublico }}">{{ $emailPublico }}</a>@if ($telefonoPublico) o llamando al {{ $telefonoPublico }}@endif.
            @elseif ($telefonoPublico)
                llamando al {{ $telefonoPublico }}.
            @else
                a través de los medios de contacto facilitados en este sitio web.
            @endif
            Asimismo, si consideras que el tratamiento no se ajusta a la normativa, tienes derecho
            a presentar una reclamación ante la Agencia Española de Protección de Datos
            (www.aepd.es).
        </p>

        <p class="priv__foot">
            Esta política de privacidad podrá actualizarse para adaptarse a cambios normativos o
            del servicio. Te recomendamos revisarla periódicamente.
        </p>
    </main>
</body>
</html>
