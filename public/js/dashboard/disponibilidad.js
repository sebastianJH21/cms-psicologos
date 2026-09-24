(() => {
    // ── Tabs presencial / online (compartido entre ambos paneles de huecos) ───────
    const tabs = document.querySelectorAll('[data-dispo-tab]');
    const grids = document.querySelectorAll('.dispo-grid');

    const activarTab = (modalidad) => {
        tabs.forEach((tab) => {
            const activa = tab.dataset.dispoTab === modalidad;
            tab.classList.toggle('dispo-tabs__btn--active', activa);
            tab.setAttribute('aria-selected', activa ? 'true' : 'false');
        });
        grids.forEach((grid) => {
            grid.classList.toggle('dispo-grid--active', grid.dataset.modalidad === modalidad);
        });
    };

    tabs.forEach((tab) => {
        tab.addEventListener('click', (event) => {
            event.preventDefault();
            activarTab(tab.dataset.dispoTab);
        });
    });

    // ── Toggle visual de slots ───────────────────────────────────────────────────
    document.querySelectorAll('.dispo-slot input').forEach((input) => {
        input.addEventListener('change', () => {
            input.closest('.dispo-slot').classList.toggle('dispo-slot--on', input.checked);
        });
    });

    // ── Bulk actions ─────────────────────────────────────────────────────────────
    document.querySelectorAll('[data-dispo-bulk]').forEach((btn) => {
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            const accion = btn.dataset.dispoBulk;
            const grid = btn.closest('.dispo-grid');
            if (!grid) return;
            const inputs = grid.querySelectorAll('.dispo-slot input');

            inputs.forEach((input) => {
                const slot = input.closest('.dispo-slot');
                const partes = input.value.split('|');
                const dia = parseInt(partes[1], 10);

                let activar = null;
                if (accion === 'all') activar = true;
                else if (accion === 'clear') activar = false;
                else if (accion === 'weekdays') activar = dia >= 1 && dia <= 5;

                if (activar !== null) {
                    input.checked = activar;
                    if (slot) slot.classList.toggle('dispo-slot--on', activar);
                }
            });
        });
    });

    // ── Campos de horario y duración ─────────────────────────────────────────────
    const aperturaMananaInput        = document.getElementById('hora_apertura_manana');
    const cierreMananaInput          = document.getElementById('hora_cierre_manana');
    const aperturaTardeInput         = document.getElementById('hora_apertura_tarde');
    const cierreTardeInput           = document.getElementById('hora_cierre_tarde');
    const duracionPresencialInput    = document.getElementById('duracion_sesion_presencial_min');
    const duracionOnlineInput        = document.getElementById('duracion_sesion_online_min');

    const descansoTogglePresencial = document.getElementById('descanso_activo_presencial');
    const descansoInputPresencial  = document.getElementById('descanso_min_presencial');
    const descansoFieldPresencial  = document.getElementById('descanso-field-presencial');
    const descansoEjemploPresencial = document.getElementById('descanso-ejemplo-presencial');

    const descansoToggleOnline = document.getElementById('descanso_activo_online');
    const descansoInputOnline  = document.getElementById('descanso_min_online');
    const descansoFieldOnline  = document.getElementById('descanso-field-online');
    const descansoEjemploOnline = document.getElementById('descanso-ejemplo-online');

    const errorSolapamiento = document.getElementById('error-solapamiento');
    const btnGuardar = document.getElementById('btn-guardar-dispo');
    const btnGuardarBottom = document.getElementById('btn-guardar-dispo-bottom');
    const btnGuardarConfig = document.getElementById('btn-guardar-config');

    // ── Validación solapamiento mañana/tarde ─────────────────────────────────────
    const validarSolapamiento = () => {
        if (!cierreMananaInput || !aperturaTardeInput || !errorSolapamiento) return;
        const cierreM  = cierreMananaInput.value;
        const aperturaT = aperturaTardeInput.value;
        const hayError = cierreM && aperturaT && cierreM >= aperturaT;

        if (hayError) {
            errorSolapamiento.hidden = false;
            const iconEl = document.createElement('i');
            iconEl.className = 'fa-solid fa-triangle-exclamation';
            iconEl.setAttribute('aria-hidden', 'true');
            while (errorSolapamiento.firstChild) errorSolapamiento.removeChild(errorSolapamiento.firstChild);
            errorSolapamiento.appendChild(iconEl);
            errorSolapamiento.appendChild(document.createTextNode(
                ' Los horarios se solapan: el cierre de mañana (' + cierreM +
                ') debe ser anterior a la apertura de tarde (' + aperturaT + ').'
            ));
        } else {
            errorSolapamiento.hidden = true;
        }

        if (btnGuardar)       btnGuardar.disabled = hayError;
        if (btnGuardarBottom) btnGuardarBottom.disabled = hayError;
        if (btnGuardarConfig) btnGuardarConfig.disabled = hayError;
    };

    [cierreMananaInput, aperturaTardeInput].forEach((el) => {
        if (!el) return;
        el.addEventListener('change', validarSolapamiento);
        el.addEventListener('input', validarSolapamiento);
    });
    validarSolapamiento();

    // ── Aviso de regeneración de huecos al cambiar parámetros ───────────────────
    const aviso = document.createElement('p');
    aviso.className = 'dispo-form__hint';
    aviso.style.color = 'var(--color-warning, #c98a1f)';
    const avisoIcono = document.createElement('i');
    avisoIcono.className = 'fa-solid fa-triangle-exclamation';
    avisoIcono.setAttribute('aria-hidden', 'true');
    const avisoTexto = document.createElement('span');
    avisoTexto.textContent = ' Has cambiado los horarios, la duración o el descanso: al guardar, los huecos se regenerarán con los nuevos valores. Revisa y vuelve a marcar los huecos de disponibilidad.';
    aviso.appendChild(avisoIcono);
    aviso.appendChild(avisoTexto);
    let avisoMostrado = false;

    const mostrarAviso = () => {
        if (avisoMostrado) return;
        avisoMostrado = true;
        const panel = duracionPresencialInput?.closest('.panel');
        if (panel) panel.appendChild(aviso);
    };

    [duracionPresencialInput, duracionOnlineInput, aperturaMananaInput, cierreMananaInput,
     aperturaTardeInput, cierreTardeInput, descansoInputPresencial, descansoInputOnline].forEach((el) => {
        if (!el) return;
        el.addEventListener('change', mostrarAviso);
        el.addEventListener('input', mostrarAviso);
    });

    // ── Paneles de descanso: mostrar/ocultar y ejemplos dinámicos ───────────────
    const formatHora = (minutosTotales) => {
        const h = String(Math.floor(minutosTotales / 60)).padStart(2, '0');
        const m = String(minutosTotales % 60).padStart(2, '0');
        return h + ':' + m;
    };

    const actualizarEjemplo = (toggle, descansoInput, durInput, ejemploEl) => {
        if (!ejemploEl) return;
        const duracion  = parseInt(durInput?.value || '60', 10);
        const descanso  = parseInt(descansoInput?.value || '0', 10);
        if (!toggle?.checked || isNaN(duracion) || isNaN(descanso) || descanso <= 0) {
            ejemploEl.textContent = '';
            return;
        }
        const efectivo = duracion + descanso;
        const iconoEl  = document.createElement('i');
        iconoEl.className = 'fa-solid fa-circle-info';
        iconoEl.setAttribute('aria-hidden', 'true');
        const strongEl = document.createElement('strong');
        strongEl.textContent = `${efectivo} minutos`;
        ejemploEl.textContent = '';
        ejemploEl.appendChild(iconoEl);
        ejemploEl.appendChild(document.createTextNode(
            ` Con sesiones de ${duracion} min y ${descanso} min de descanso, los huecos se espaciarán cada `
        ));
        ejemploEl.appendChild(strongEl);
        ejemploEl.appendChild(document.createTextNode(
            `. Ejemplo: 09:00, ${formatHora(9 * 60 + efectivo)}, ${formatHora(9 * 60 + efectivo * 2)}...`
        ));
    };

    const actualizarEjemploPresencial = () =>
        actualizarEjemplo(descansoTogglePresencial, descansoInputPresencial, duracionPresencialInput, descansoEjemploPresencial);
    const actualizarEjemploOnline = () =>
        actualizarEjemplo(descansoToggleOnline, descansoInputOnline, duracionOnlineInput, descansoEjemploOnline);

    if (descansoTogglePresencial && descansoFieldPresencial) {
        descansoTogglePresencial.addEventListener('change', () => {
            descansoFieldPresencial.hidden = !descansoTogglePresencial.checked;
            mostrarAviso();
            actualizarEjemploPresencial();
        });
    }

    if (descansoToggleOnline && descansoFieldOnline) {
        descansoToggleOnline.addEventListener('change', () => {
            descansoFieldOnline.hidden = !descansoToggleOnline.checked;
            mostrarAviso();
            actualizarEjemploOnline();
        });
    }

    if (descansoInputPresencial)  descansoInputPresencial.addEventListener('input', actualizarEjemploPresencial);
    if (descansoInputOnline)      descansoInputOnline.addEventListener('input', actualizarEjemploOnline);
    if (duracionPresencialInput)  duracionPresencialInput.addEventListener('input', actualizarEjemploPresencial);
    if (duracionOnlineInput)      duracionOnlineInput.addEventListener('input', actualizarEjemploOnline);

    actualizarEjemploPresencial();
    actualizarEjemploOnline();

    // ── Periodos de vacaciones (AJAX) ────────────────────────────────────────────
    const vacPanel = document.getElementById('vac-panel');
    if (vacPanel) {
        const vacStoreUrl  = vacPanel.dataset.vacStore;
        const vacDeleteBase = vacPanel.dataset.vacDelete;
        const vacAddBtn    = document.getElementById('vac-add-btn');
        const vacInicio    = document.getElementById('vac-inicio');
        const vacFin       = document.getElementById('vac-fin');
        const vacList      = document.getElementById('vac-list');
        const vacError     = document.getElementById('vac-error');
        const csrfToken    = document.querySelector('input[name="_token"]')?.value ?? '';

        const vacShowError = (msg) => { if (vacError) { vacError.textContent = msg; vacError.style.display = ''; } };
        const vacHideError = () => { if (vacError) vacError.style.display = 'none'; };

        const vacBuildItem = (p) => {
            const li = document.createElement('li');
            li.className = 'dispo-vac-item';
            li.dataset.id = p.id;

            const span = document.createElement('span');
            const ico  = document.createElement('i');
            ico.className = 'fa-solid fa-calendar-xmark';
            ico.setAttribute('aria-hidden', 'true');
            span.appendChild(ico);
            span.appendChild(document.createTextNode(` ${p.fecha_inicio} — ${p.fecha_fin}`));

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn--ghost btn--sm dispo-vac-del';
            btn.dataset.id = p.id;
            btn.title = 'Eliminar periodo';
            const trashIco = document.createElement('i');
            trashIco.className = 'fa-solid fa-trash';
            trashIco.setAttribute('aria-hidden', 'true');
            btn.appendChild(trashIco);
            btn.addEventListener('click', vacDelete);

            li.appendChild(span);
            li.appendChild(btn);
            return li;
        };

        const vacShowEmpty = () => {
            if (!vacList || vacList.querySelector('.dispo-vac-item:not(.dispo-vac-item--empty)')) return;
            if (document.getElementById('vac-empty')) return;
            const li = document.createElement('li');
            li.className = 'dispo-vac-item dispo-vac-item--empty';
            li.id = 'vac-empty';
            const ico = document.createElement('i');
            ico.className = 'fa-solid fa-circle-check';
            ico.setAttribute('aria-hidden', 'true');
            li.appendChild(ico);
            li.appendChild(document.createTextNode(' Sin periodos configurados'));
            vacList.appendChild(li);
        };

        const vacDelete = async (e) => {
            const id = e.currentTarget.dataset.id;
            try {
                const res = await fetch(`${vacDeleteBase}/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                });
                const data = await res.json();
                if (data.ok) {
                    vacList?.querySelector(`[data-id="${id}"]`)?.remove();
                    vacShowEmpty();
                }
            } catch (_) {}
        };

        document.querySelectorAll('.dispo-vac-del').forEach((btn) => btn.addEventListener('click', vacDelete));

        if (vacAddBtn) {
            vacAddBtn.addEventListener('click', async () => {
                vacHideError();
                const inicio = vacInicio?.value;
                const fin    = vacFin?.value;
                if (!inicio || !fin) { vacShowError('Indica la fecha de inicio y la de fin.'); return; }
                if (fin < inicio)    { vacShowError('La fecha de fin debe ser igual o posterior a la de inicio.'); return; }

                try {
                    const res = await fetch(vacStoreUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ fecha_inicio: inicio, fecha_fin: fin }),
                    });
                    const data = await res.json();
                    if (data.ok) {
                        document.getElementById('vac-empty')?.remove();
                        vacList?.appendChild(vacBuildItem(data.periodo));
                        if (vacInicio) vacInicio.value = '';
                        if (vacFin)    vacFin.value = '';
                    } else {
                        vacShowError(data.message ?? 'No se pudo añadir el periodo.');
                    }
                } catch (_) {
                    vacShowError('Error de conexión al añadir el periodo.');
                }
            });
        }
    }
})();
