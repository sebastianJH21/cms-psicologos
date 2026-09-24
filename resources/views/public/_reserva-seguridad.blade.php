@php
    $captchaA = random_int(1, 9);
    $captchaB = random_int(1, 9);
    $captchaToken = \Illuminate\Support\Facades\Crypt::encryptString(($captchaA + $captchaB) . '|' . time());
@endphp

<div class="cita-form__field cita-form__seguridad">
    <label for="cita-captcha">Pregunta de seguridad: ¿cuánto es {{ $captchaA }} + {{ $captchaB }}? *</label>
    <input id="cita-captcha" type="text" name="captcha" inputmode="numeric" pattern="[0-9]*"
        autocomplete="off" maxlength="3" required>
    <input type="hidden" name="captcha_token" value="{{ $captchaToken }}">
</div>

<label class="cita-form__field cita-form__privacidad">
    <input type="checkbox" name="privacidad" value="1" required>
    <span>
        Confirmo que quiero reservar la cita y acepto la
        <a href="{{ route('public.privacidad') }}" target="_blank" rel="noopener">política de privacidad</a>.
    </span>
</label>

<style>
    .cita-form__privacidad {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        gap: .8rem;
        cursor: pointer;
        line-height: 1.5;
    }
    .cita-form__privacidad input[type="checkbox"] {
        width: 1.8rem;
        height: 1.8rem;
        min-width: 1.8rem;
        margin-top: .2rem;
        accent-color: currentColor;
        cursor: pointer;
    }
    .cita-form__privacidad span { font-size: 1.4rem; }
    .cita-form__privacidad a { text-decoration: underline; }
</style>

<script>
    (() => {
        const captcha = document.getElementById('cita-captcha');
        if (!captcha) return;
        captcha.addEventListener('input', () => {
            captcha.value = captcha.value.replace(/\D/g, '');
        });
    })();
</script>
