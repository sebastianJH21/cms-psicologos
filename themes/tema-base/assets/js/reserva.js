(function () {
    const cfg = window.PSICOCMS_RESERVA;
    if (!cfg) return;

    const form = document.getElementById('reserva-form');
    if (!form) return;

    const monthEl = document.getElementById('cita-calendar-month');
    const gridEl = document.getElementById('cita-calendar-grid');
    const slotsEl = document.getElementById('cita-slots');
    const slotsFechaEl = document.getElementById('cita-slots-fecha');
    const fechaHoraInput = document.getElementById('cita-fecha-hora');
    const resumenEl = document.getElementById('cita-resumen');
    const errorEl = document.getElementById('cita-form-error');
    const btnText = form.querySelector('.cita-form__btn-text');
    const btnLoading = form.querySelector('.cita-form__btn-loading');
    const modal = document.getElementById('cita-modal');
    const modalText = document.getElementById('cita-modal-text');
    const modalGcal = document.getElementById('cita-modal-gcal');

    const nombreInput = document.getElementById('cita-nombre');
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

    const meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    const state = {
        modalidad: null,
        cursorDate: new Date(),
        diasDisponibles: new Set(),
        fechaSeleccionada: null,
        slotSeleccionado: null,
    };

    state.cursorDate.setDate(1);

    function showStep(num) {
        form.querySelectorAll('.cita-form__step').forEach(s => {
            s.hidden = parseInt(s.dataset.step, 10) > num;
        });
    }

    function setLoading(on) {
        const btn = form.querySelector('.cita-form__btn-submit');
        if (!btn) return;
        btn.disabled = on;
        btnText.hidden = on;
        btnLoading.hidden = !on;
    }

    async function fetchDias() {
        const r = await fetch(`${cfg.diasUrl}?modalidad=${state.modalidad}`);
        const data = await r.json();
        state.diasDisponibles = new Set(data.dias || []);
    }

    async function fetchSlots(fecha) {
        slotsEl.replaceChildren();
        const span = document.createElement('span');
        span.className = 'cita-form__loading-text';
        span.textContent = 'Cargando horarios…';
        slotsEl.appendChild(span);

        const r = await fetch(`${cfg.slotsUrl}?modalidad=${state.modalidad}&fecha=${fecha}`);
        const data = await r.json();
        renderSlots(data.slots || []);
    }

    function pad(n) { return n < 10 ? '0' + n : '' + n; }

    function isoDate(d) {
        return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
    }

    function renderCalendar() {
        const year = state.cursorDate.getFullYear();
        const month = state.cursorDate.getMonth();
        monthEl.textContent = `${meses[month]} ${year}`;

        gridEl.replaceChildren();
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        let startWeekday = firstDay.getDay();
        startWeekday = startWeekday === 0 ? 6 : startWeekday - 1;

        const today = new Date();
        today.setHours(0,0,0,0);

        for (let i = 0; i < startWeekday; i++) {
            const empty = document.createElement('span');
            empty.className = 'cita-calendar__day cita-calendar__day--empty';
            gridEl.appendChild(empty);
        }

        for (let d = 1; d <= lastDay.getDate(); d++) {
            const date = new Date(year, month, d);
            const iso = isoDate(date);
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'cita-calendar__day';
            btn.textContent = d;
            const disponible = state.diasDisponibles.has(iso) && date >= today;

            if (!disponible) {
                btn.disabled = true;
                btn.classList.add('cita-calendar__day--disabled');
            } else {
                btn.classList.add('cita-calendar__day--available');
                btn.addEventListener('click', () => seleccionarDia(iso, btn));
            }

            if (state.fechaSeleccionada === iso) {
                btn.classList.add('cita-calendar__day--selected');
            }

            gridEl.appendChild(btn);
        }
    }

    function seleccionarDia(iso, btn) {
        state.fechaSeleccionada = iso;
        state.slotSeleccionado = null;
        fechaHoraInput.value = '';
        gridEl.querySelectorAll('.cita-calendar__day--selected').forEach(el => el.classList.remove('cita-calendar__day--selected'));
        btn.classList.add('cita-calendar__day--selected');

        const [y, m, d] = iso.split('-');
        slotsFechaEl.textContent = `Horarios para el ${d}/${m}/${y}`;
        showStep(3);
        fetchSlots(iso);
    }

    function renderSlots(slots) {
        slotsEl.replaceChildren();
        if (slots.length === 0) {
            const p = document.createElement('p');
            p.className = 'cita-form__loading-text';
            p.textContent = 'No hay horarios disponibles para este día.';
            slotsEl.appendChild(p);
            return;
        }
        slots.forEach(slot => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'cita-form__slot';
            btn.textContent = slot.hora;
            btn.dataset.iso = slot.inicio_iso;
            btn.addEventListener('click', () => {
                slotsEl.querySelectorAll('.cita-form__slot--selected').forEach(el => el.classList.remove('cita-form__slot--selected'));
                btn.classList.add('cita-form__slot--selected');
                state.slotSeleccionado = slot;
                fechaHoraInput.value = slot.inicio_iso;
                actualizarResumen();
                showStep(4);
            });
            slotsEl.appendChild(btn);
        });
    }

    function actualizarResumen() {
        if (!state.slotSeleccionado || !state.fechaSeleccionada) return;
        const [y, m, d] = state.fechaSeleccionada.split('-');
        const lbl = state.modalidad === 'online' ? 'Online' : 'Presencial';
        resumenEl.textContent = '';
        const div = document.createElement('div');
        div.className = 'cita-form__resumen-text';
        div.textContent = `${lbl} · ${d}/${m}/${y} a las ${state.slotSeleccionado.hora}`;
        resumenEl.appendChild(div);
    }

    form.querySelectorAll('input[name="modalidad"]').forEach(r => {
        r.addEventListener('change', async (e) => {
            state.modalidad = e.target.value;
            state.fechaSeleccionada = null;
            state.slotSeleccionado = null;
            fechaHoraInput.value = '';
            slotsEl.replaceChildren();
            await fetchDias();
            renderCalendar();
            showStep(2);
        });
    });

    document.querySelectorAll('.cita-calendar__nav').forEach(b => {
        b.addEventListener('click', () => {
            const dir = b.dataset.action === 'prev' ? -1 : 1;
            state.cursorDate.setMonth(state.cursorDate.getMonth() + dir);
            renderCalendar();
        });
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        errorEl.hidden = true;
        errorEl.textContent = '';

        if (!fechaHoraInput.value) {
            errorEl.hidden = false;
            errorEl.textContent = 'Falta seleccionar la hora.';
            return;
        }

        if (telefonoInput && (telefonoInput.value.replace(/\D/g, '') || '').length < 9) {
            errorEl.hidden = false;
            errorEl.textContent = 'El teléfono no es correcto: debe tener al menos 9 dígitos. Revisa que no falte ningún número.';
            return;
        }

        const privacidadInput = form.querySelector('input[name="privacidad"]');
        if (privacidadInput && !privacidadInput.checked) {
            errorEl.hidden = false;
            errorEl.textContent = 'Debes aceptar la política de privacidad para reservar la cita.';
            return;
        }

        setLoading(true);
        const fd = new FormData(form);
        try {
            const r = await fetch(cfg.crearUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': cfg.csrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: fd,
            });
            const data = await r.json();

            if (!r.ok || !data.ok) {
                errorEl.hidden = false;
                errorEl.textContent = data.message || 'No se pudo reservar. Inténtalo de nuevo.';
                setLoading(false);
                return;
            }

            mostrarConfirmacion(data);
            form.reset();
            state.fechaSeleccionada = null;
            state.slotSeleccionado = null;
            fechaHoraInput.value = '';
            showStep(1);
            renderCalendar();
        } catch (err) {
            errorEl.hidden = false;
            errorEl.textContent = 'Error de red. Inténtalo de nuevo.';
        } finally {
            setLoading(false);
        }
    });

    function mostrarConfirmacion(data) {
        const c = data.cita;
        modalText.textContent = `Tu cita ${c.modalidad === 'online' ? 'online' : 'presencial'} queda agendada para el ${c.fecha} a las ${c.hora}. Te confirmaré por teléfono.`;
        modalGcal.href = data.google_calendar;
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
    }

    document.querySelectorAll('[data-cita-close]').forEach(b => {
        b.addEventListener('click', () => {
            modal.hidden = true;
            document.body.style.overflow = '';
        });
    });
})();
