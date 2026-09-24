(() => {
    const form = document.getElementById('reserva-form');
    if (!form) return;

    const inputModalidad  = document.getElementById('cita-modalidad');
    const inputFechaHora  = document.getElementById('cita-fecha-hora-input');
    const calGrid         = document.getElementById('cita-calendar-grid');
    const calMonth        = document.getElementById('cita-calendar-month');
    const calPrev         = document.getElementById('cita-cal-prev');
    const calNext         = document.getElementById('cita-cal-next');
    const slotsCont       = document.getElementById('cita-slots');
    const errorEl         = document.getElementById('cita-form-error');
    const submitBtn       = document.getElementById('cita-submit');
    const csrf            = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio',
                   'Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    const pad = (n) => String(n).padStart(2, '0');

    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    let mesVista        = hoy.getMonth();
    let anyoVista       = hoy.getFullYear();
    let diasDisponibles = new Set();
    let fechaSeleccionada = null;

    // ── Pasos progresivos ────────────────────────────────────────────────────

    const getStep = (n) => form.querySelector(`.cita-form__step[data-step="${n}"]`);

    const showStep = (n) => {
        const step = getStep(n);
        if (!step) return;
        step.hidden = false;
        step.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    };

    const hideStepsFrom = (n) => {
        form.querySelectorAll('.cita-form__step[data-step]').forEach((el) => {
            const stepNum = parseInt(el.dataset.step, 10);
            if (stepNum >= n) el.hidden = true;
        });
    };

    // Determinar el número de paso del calendario
    const calStep = calGrid?.closest('.cita-form__step')
        ? parseInt(calGrid.closest('.cita-form__step').dataset.step, 10)
        : 1;
    const slotsStep = calStep + 1;
    const dataStep  = calStep + 2;

    // Filtros de entrada
    const nombreInput   = document.getElementById('cita-nombre');
    const telefonoInput = document.getElementById('cita-telefono');
    if (nombreInput) {
        nombreInput.addEventListener('input', () => {
            nombreInput.value = nombreInput.value.replace(/[^\p{L}\s'.\-]/gu, '');
        });
    }
    if (telefonoInput) {
        telefonoInput.addEventListener('input', () => {
            telefonoInput.value = telefonoInput.value.replace(/[^\d\s+().\-]/g, '');
        });
    }

    const limpiar = (el) => { while (el && el.firstChild) el.removeChild(el.firstChild); };

    const mostrarError = (msg) => {
        if (!errorEl) return;
        errorEl.textContent = msg;
        errorEl.hidden = false;
    };
    const ocultarError = () => { if (errorEl) errorEl.hidden = true; };

    // ── Calendario ──────────────────────────────────────────────────────────

    const renderCalendario = () => {
        if (!calGrid || !calMonth) return;
        calMonth.textContent = `${MESES[mesVista]} ${anyoVista}`;
        limpiar(calGrid);

        const primerDia = new Date(anyoVista, mesVista, 1);
        const ultimoDia = new Date(anyoVista, mesVista + 1, 0);
        let iniciaSemana = primerDia.getDay();
        iniciaSemana = iniciaSemana === 0 ? 6 : iniciaSemana - 1;

        for (let i = 0; i < iniciaSemana; i++) {
            const empty = document.createElement('span');
            empty.className = 'cita-calendar__day cita-calendar__day--empty';
            calGrid.appendChild(empty);
        }

        for (let d = 1; d <= ultimoDia.getDate(); d++) {
            const fechaDate = new Date(anyoVista, mesVista, d);
            const iso = `${anyoVista}-${pad(mesVista + 1)}-${pad(d)}`;
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'cita-calendar__day';
            btn.textContent = d;

            const disponible = diasDisponibles.has(iso) && fechaDate >= hoy;
            if (!disponible) {
                btn.disabled = true;
                btn.classList.add('cita-calendar__day--disabled');
            } else {
                btn.classList.add('cita-calendar__day--available');
                btn.addEventListener('click', () => seleccionarDia(iso, btn));
            }

            if (fechaSeleccionada === iso) {
                btn.classList.add('cita-calendar__day--selected');
            }

            calGrid.appendChild(btn);
        }
    };

    const seleccionarDia = (iso, btn) => {
        fechaSeleccionada = iso;
        inputFechaHora.value = '';
        calGrid.querySelectorAll('.cita-calendar__day--selected')
               .forEach((el) => el.classList.remove('cita-calendar__day--selected'));
        btn.classList.add('cita-calendar__day--selected');
        // Ocultar pasos posteriores y mostrar slots
        hideStepsFrom(slotsStep);
        limpiar(slotsCont);
        showStep(slotsStep);
        cargarSlots(iso);
    };

    const fetchDiasDisponibles = async () => {
        diasDisponibles = new Set();
        try {
            const res = await fetch(`/reservas/dias?modalidad=${encodeURIComponent(inputModalidad.value)}`);
            if (res.ok) {
                const data = await res.json();
                diasDisponibles = new Set(data.dias || []);
            }
        } catch {}
        renderCalendario();
    };

    // Navegación del calendario
    if (calPrev) {
        calPrev.addEventListener('click', (e) => {
            e.preventDefault();
            mesVista--;
            if (mesVista < 0) { mesVista = 11; anyoVista--; }
            renderCalendario();
        });
    }
    if (calNext) {
        calNext.addEventListener('click', (e) => {
            e.preventDefault();
            mesVista++;
            if (mesVista > 11) { mesVista = 0; anyoVista++; }
            renderCalendario();
        });
    }

    // ── Slots de hora ────────────────────────────────────────────────────────

    const cargarSlots = async (fecha) => {
        if (!slotsCont) return;
        limpiar(slotsCont);
        const loading = document.createElement('span');
        loading.textContent = 'Cargando horarios…';
        loading.style.color = 'var(--color-text-light)';
        loading.style.fontSize = '1.3rem';
        slotsCont.appendChild(loading);

        try {
            const res = await fetch(`/reservas/slots?modalidad=${encodeURIComponent(inputModalidad.value)}&fecha=${encodeURIComponent(fecha)}`);
            const data = await res.json();
            const slots = data.slots || [];
            limpiar(slotsCont);
            if (!slots.length) {
                const empty = document.createElement('p');
                empty.textContent = 'No hay huecos libres este día.';
                empty.style.color = 'var(--color-text-light)';
                slotsCont.appendChild(empty);
                return;
            }
            slots.forEach((s) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'cita-form__slot';
                btn.textContent = s.hora;
                btn.dataset.iso = s.inicio_iso;
                btn.addEventListener('click', () => {
                    slotsCont.querySelectorAll('.cita-form__slot')
                             .forEach((el) => el.classList.remove('is-active'));
                    btn.classList.add('is-active');
                    inputFechaHora.value = s.inicio_iso;
                    // Mostrar paso de datos personales
                    showStep(dataStep);
                });
                slotsCont.appendChild(btn);
            });
        } catch {
            limpiar(slotsCont);
            const err = document.createElement('p');
            err.textContent = 'No se pudieron cargar los horarios.';
            slotsCont.appendChild(err);
        }
    };

    // ── Selector de modalidad ────────────────────────────────────────────────

    document.querySelectorAll('input[name="modalidad"]').forEach((radio) => {
        radio.addEventListener('change', () => {
            inputModalidad.value = radio.value;
            fechaSeleccionada = null;
            inputFechaHora.value = '';
            hideStepsFrom(calStep);
            limpiar(slotsCont);
            showStep(calStep);
            fetchDiasDisponibles();
        });
    });

    // ── Envío del formulario ─────────────────────────────────────────────────

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        ocultarError();

        if (!inputFechaHora.value) {
            mostrarError('Selecciona un día y una hora.');
            return;
        }

        const nombre    = document.getElementById('cita-nombre')?.value.trim();
        const telefono  = document.getElementById('cita-telefono')?.value.trim();
        if (!nombre || !telefono) {
            mostrarError('Completa nombre y teléfono.');
            return;
        }

        if ((telefono.replace(/\D/g, '') || '').length < 9) {
            mostrarError('El teléfono no es correcto: debe tener al menos 9 dígitos. Revisa que no falte ningún número.');
            return;
        }

        const privacidad = form.querySelector('input[name="privacidad"]');
        if (privacidad && !privacidad.checked) {
            mostrarError('Debes aceptar la política de privacidad para reservar la cita.');
            return;
        }

        submitBtn.disabled = true;
        const original = submitBtn.textContent;
        submitBtn.textContent = 'Enviando…';

        try {
            const fd = new FormData(form);
            const res = await fetch('/reservas/crear', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: fd,
            });
            const data = await res.json().catch(() => ({}));

            if (!res.ok || data.ok === false) {
                const msgs = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : (data.message || 'No se pudo reservar. Inténtalo de nuevo.');
                mostrarError(msgs);
                submitBtn.disabled = false;
                submitBtn.textContent = original;
                return;
            }

            mostrarConfirmacion(data);
            form.reset();
            fechaSeleccionada = null;
            inputFechaHora.value = '';
            limpiar(slotsCont);
            mesVista  = hoy.getMonth();
            anyoVista = hoy.getFullYear();
            // Resetear pasos al estado inicial
            hideStepsFrom(calStep);
            if (calStep === 1) {
                showStep(1);
                fetchDiasDisponibles();
            }
        } catch {
            mostrarError('Error de red. Inténtalo de nuevo.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = original;
        }
    });

    // ── Modal de confirmación ────────────────────────────────────────────────

    const mostrarConfirmacion = (data) => {
        const existing = document.getElementById('t-aurora-cita-modal');
        if (existing) existing.remove();

        const overlay = document.createElement('div');
        overlay.id = 't-aurora-cita-modal';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        Object.assign(overlay.style, {
            position: 'fixed', inset: '0',
            background: 'rgba(26,19,48,0.65)',
            backdropFilter: 'blur(8px)',
            display: 'flex', alignItems: 'center', justifyContent: 'center',
            padding: '2rem', zIndex: '200',
        });

        const box = document.createElement('div');
        Object.assign(box.style, {
            background: '#fff', borderRadius: '2rem',
            maxWidth: '46rem', width: '100%',
            padding: '3rem', textAlign: 'center',
            boxShadow: '0 32px 80px rgba(0,0,0,0.3)',
        });

        const icon = document.createElement('div');
        Object.assign(icon.style, {
            width: '7rem', height: '7rem', borderRadius: '50%',
            background: 'linear-gradient(135deg, #6b4ee6 0%, #ff8a7d 60%, #ffd5a0 100%)',
            color: '#fff',
            display: 'inline-flex', alignItems: 'center', justifyContent: 'center',
            fontSize: '2.6rem', margin: '0 auto 2rem',
        });
        const iconI = document.createElement('i');
        iconI.className = 'fa-solid fa-check';
        icon.appendChild(iconI);

        const h3 = document.createElement('h3');
        h3.textContent = '¡Tu cita está reservada!';
        Object.assign(h3.style, {
            fontFamily: "'Fraunces', Georgia, serif",
            fontSize: '2.6rem', marginBottom: '1.2rem', color: '#1a1330',
        });

        const txt = document.createElement('p');
        const c = data.cita || {};
        const modalidadLabel = c.modalidad === 'online' ? 'online' : 'presencial';
        txt.textContent = `Tu cita ${modalidadLabel} queda agendada para el ${c.fecha || ''} a las ${c.hora || ''}. Te confirmaré por teléfono.`;
        Object.assign(txt.style, { color: '#5a526d', fontSize: '1.5rem', marginBottom: '2.4rem' });

        const actions = document.createElement('div');
        Object.assign(actions.style, { display: 'flex', gap: '1rem', justifyContent: 'center', flexWrap: 'wrap' });

        if (data.google_calendar) {
            const gcal = document.createElement('a');
            gcal.href = data.google_calendar;
            gcal.target = '_blank';
            gcal.rel = 'noopener';
            gcal.className = 't-aurora__btn t-aurora__btn--ghost';
            const gi = document.createElement('i');
            gi.className = 'fa-brands fa-google';
            gcal.appendChild(gi);
            gcal.appendChild(document.createTextNode(' Añadir a Google Calendar'));
            actions.appendChild(gcal);
        }

        const close = document.createElement('button');
        close.type = 'button';
        close.textContent = 'Cerrar';
        close.className = 't-aurora__btn t-aurora__btn--primary';
        close.addEventListener('click', () => {
            overlay.remove();
            document.body.style.overflow = '';
        });
        actions.appendChild(close);

        box.appendChild(icon);
        box.appendChild(h3);
        box.appendChild(txt);
        box.appendChild(actions);
        overlay.appendChild(box);
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.remove();
                document.body.style.overflow = '';
            }
        });
        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
    };

    // ── Arranque ─────────────────────────────────────────────────────────────

    if (calStep === 1) {
        // Solo una modalidad: mostrar calendario directamente
        showStep(1);
        fetchDiasDisponibles();
    }
    // Si hay dos modalidades (calStep === 2), el calendario empieza oculto.
    // El usuario debe hacer clic en un botón de modalidad para revelarlo.
})();
