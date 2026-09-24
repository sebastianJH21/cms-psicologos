(() => {
    const searchInput = document.getElementById('input-buscar-paciente');
    if (!searchInput) return;

    const dropdown = document.getElementById('dropdown-pacientes');
    const seleccionado = document.getElementById('paciente-seleccionado');
    const clearBtn = document.getElementById('btn-limpiar-paciente');
    const fieldId = document.getElementById('field-paciente-id');
    const fieldNombre = document.getElementById('nombre_provisional');
    const fieldTelefono = document.getElementById('telefono_provisional');
    const fieldEmail = document.getElementById('email_provisional');

    const searchUrl = searchInput.dataset.searchUrl;
    let timer = null;

    const hideDropdown = () => {
        dropdown.hidden = true;
        while (dropdown.firstChild) {
            dropdown.removeChild(dropdown.firstChild);
        }
    };

    const bloquearCamposPaciente = () => {
        [fieldNombre, fieldTelefono, fieldEmail].forEach(f => {
            if (!f) return;
            f.readOnly = true;
            f.classList.add('form-input--locked');
        });
    };

    const desbloquearCamposPaciente = () => {
        [fieldNombre, fieldTelefono, fieldEmail].forEach(f => {
            if (!f) return;
            f.value = '';
            f.readOnly = false;
            f.classList.remove('form-input--locked');
        });
    };

    const seleccionarPaciente = (p) => {
        if (fieldId) fieldId.value = p.id;
        if (fieldNombre) fieldNombre.value = p.nombre || '';
        if (fieldTelefono) fieldTelefono.value = p.telefono || '';
        if (fieldEmail) fieldEmail.value = p.email || '';
        bloquearCamposPaciente();

        searchInput.value = p.nombre;
        if (clearBtn) clearBtn.hidden = false;

        if (seleccionado) {
            while (seleccionado.firstChild) {
                seleccionado.removeChild(seleccionado.firstChild);
            }
            const icon = document.createElement('i');
            icon.className = 'fa-solid fa-circle-check';
            icon.setAttribute('aria-hidden', 'true');
            const txt = document.createTextNode(' Vinculado: ' + p.nombre + ' · ' + p.telefono);
            seleccionado.appendChild(icon);
            seleccionado.appendChild(txt);
            seleccionado.hidden = false;
        }

        hideDropdown();
    };

    const limpiar = () => {
        if (fieldId) fieldId.value = '';
        searchInput.value = '';
        if (clearBtn) clearBtn.hidden = true;
        if (seleccionado) {
            seleccionado.hidden = true;
            while (seleccionado.firstChild) {
                seleccionado.removeChild(seleccionado.firstChild);
            }
        }
        desbloquearCamposPaciente();
        hideDropdown();
    };

    const mostrarResultados = (results) => {
        hideDropdown();

        if (results.length === 0) {
            const item = document.createElement('div');
            item.className = 'paciente-busqueda__item paciente-busqueda__item--vacio';
            item.textContent = 'No se encontraron pacientes';
            dropdown.appendChild(item);
            dropdown.hidden = false;
            return;
        }

        results.forEach((p) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'paciente-busqueda__item';

            const nombre = document.createElement('span');
            nombre.className = 'paciente-busqueda__item-nombre';
            nombre.textContent = p.nombre;

            const meta = document.createElement('span');
            meta.className = 'paciente-busqueda__item-meta';
            meta.textContent = p.telefono + (p.email ? ' · ' + p.email : '');

            btn.appendChild(nombre);
            btn.appendChild(meta);
            btn.addEventListener('click', () => seleccionarPaciente(p));
            dropdown.appendChild(btn);
        });

        dropdown.hidden = false;
    };

    const buscar = async (q) => {
        if (q.length < 2) {
            hideDropdown();
            return;
        }

        try {
            const url = new URL(searchUrl, window.location.origin);
            url.searchParams.set('q', q);
            const res = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            mostrarResultados(await res.json());
        } catch {
            hideDropdown();
        }
    };

    searchInput.addEventListener('input', () => {
        if (fieldId && fieldId.value) {
            fieldId.value = '';
            desbloquearCamposPaciente();
            if (seleccionado) seleccionado.hidden = true;
            if (clearBtn) clearBtn.hidden = true;
        }
        clearTimeout(timer);
        timer = setTimeout(() => buscar(searchInput.value.trim()), 300);
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', limpiar);
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#paciente-busqueda')) {
            hideDropdown();
        }
    });
})();

// === Picker de disponibilidad (modal con calendario) =======================
(() => {
    const btnAbrir = document.getElementById('btn-disponibilidad-cita');
    if (!btnAbrir) return;

    const modal = document.getElementById('cita-slots-modal');
    const backdrop = document.getElementById('cita-slots-modal-backdrop');
    const btnClose = document.getElementById('cita-slots-close');
    const titulo = document.getElementById('cita-slots-titulo');
    const grid = document.getElementById('cita-slots-grid');
    const lista = document.getElementById('cita-slots-list');
    const hint = document.getElementById('cita-slots-hint');
    const btnPrev = document.getElementById('cita-slots-prev');
    const btnNext = document.getElementById('cita-slots-next');
    const inputFecha = document.getElementById('fecha_inicio');
    const selectModalidad = document.getElementById('modalidad');
    const btnManual = document.getElementById('btn-fecha-manual');
    const hintBloqueada = document.getElementById('hint-fecha-bloqueada');

    // ─── Lock / unlock campo fecha ────────────────────────────────────────────
    const lockFecha = () => {
        if (!inputFecha) return;
        inputFecha.readOnly = true;
        inputFecha.classList.add('form-input--locked');
        if (hintBloqueada) hintBloqueada.hidden = true;
    };
    const unlockFecha = () => {
        if (!inputFecha) return;
        inputFecha.readOnly = false;
        inputFecha.classList.remove('form-input--locked');
        if (hintBloqueada) hintBloqueada.hidden = true;
        inputFecha.focus();
    };

    if (inputFecha) {
        inputFecha.addEventListener('click', () => {
            if (inputFecha.readOnly && hintBloqueada) hintBloqueada.hidden = false;
        });
        inputFecha.addEventListener('blur', () => {
            if (hintBloqueada) hintBloqueada.hidden = true;
        });
    }
    if (btnManual) btnManual.addEventListener('click', unlockFecha);

    const MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    const DIAS = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];

    const pad = (n) => String(n).padStart(2, '0');
    const hoy = new Date();
    let mesVista = hoy.getMonth();
    let anyoVista = hoy.getFullYear();
    let diasDisponibles = new Set();
    let diaSeleccionado = null;

    const limpiar = (el) => { while (el.firstChild) el.removeChild(el.firstChild); };

    const cerrar = () => { modal.hidden = true; };
    const abrir = () => { modal.hidden = false; cargarDiasYRenderizar(); };

    const cargarDiasYRenderizar = async () => {
        const modalidad = selectModalidad.value || 'presencial';
        diasDisponibles = new Set();
        try {
            const res = await fetch(`/reservas/dias?modalidad=${encodeURIComponent(modalidad)}`);
            if (res.ok) {
                const data = await res.json();
                (data.dias || []).forEach((d) => diasDisponibles.add(d));
            }
        } catch {}
        renderizarCalendario();
    };

    const renderizarCalendario = () => {
        titulo.textContent = `${MESES[mesVista]} ${anyoVista}`;
        limpiar(grid);

        DIAS.forEach((d) => {
            const cab = document.createElement('div');
            cab.className = 'cal-dia-cab';
            cab.textContent = d;
            grid.appendChild(cab);
        });

        const primerDia = new Date(anyoVista, mesVista, 1);
        const ultimoDia = new Date(anyoVista, mesVista + 1, 0);
        const inicio = (primerDia.getDay() + 6) % 7;
        const total = Math.ceil((inicio + ultimoDia.getDate()) / 7) * 7;

        for (let i = 0; i < total; i++) {
            const cel = document.createElement('div');
            cel.className = 'cal-dia';
            const numDia = i - inicio + 1;
            if (numDia < 1 || numDia > ultimoDia.getDate()) {
                cel.classList.add('cal-dia--fuera');
                grid.appendChild(cel);
                continue;
            }
            const fechaStr = `${anyoVista}-${pad(mesVista + 1)}-${pad(numDia)}`;
            cel.textContent = numDia;
            if (diasDisponibles.has(fechaStr)) {
                cel.classList.add('cal-dia--disponible');
                if (diaSeleccionado === fechaStr) cel.classList.add('cal-dia--seleccionado');
                cel.addEventListener('click', () => {
                    diaSeleccionado = fechaStr;
                    renderizarCalendario();
                    cargarSlotsDia(fechaStr);
                });
            }
            grid.appendChild(cel);
        }
    };

    const cargarSlotsDia = async (fecha) => {
        limpiar(lista);
        hint.textContent = 'Cargando huecos...';
        const modalidad = selectModalidad.value || 'presencial';
        try {
            const res = await fetch(`/reservas/slots?modalidad=${encodeURIComponent(modalidad)}&fecha=${encodeURIComponent(fecha)}`);
            const data = await res.json();
            const slots = data.slots || [];
            if (!slots.length) {
                hint.textContent = 'No hay huecos libres para este día.';
                return;
            }
            hint.textContent = `${slots.length} ${slots.length === 1 ? 'hueco' : 'huecos'} disponibles:`;
            slots.forEach((s) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'slot-btn';
                btn.textContent = s.hora;
                btn.addEventListener('click', () => {
                    const dt = new Date(s.inicio_iso);
                    inputFecha.value = `${dt.getFullYear()}-${pad(dt.getMonth()+1)}-${pad(dt.getDate())}T${pad(dt.getHours())}:${pad(dt.getMinutes())}`;
                    lockFecha();
                    cerrar();
                });
                lista.appendChild(btn);
            });
        } catch {
            hint.textContent = 'No se pudieron cargar los huecos.';
        }
    };

    const abrirConFechaActual = () => {
        const v = inputFecha.value;
        if (v) {
            const d = new Date(v);
            if (!isNaN(d)) {
                mesVista = d.getMonth();
                anyoVista = d.getFullYear();
                diaSeleccionado = `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
            }
        }
        abrir();
        if (diaSeleccionado) {
            setTimeout(() => cargarSlotsDia(diaSeleccionado), 50);
        }
    };

    btnAbrir.addEventListener('click', (e) => { e.preventDefault(); lockFecha(); abrirConFechaActual(); });
    btnClose.addEventListener('click', cerrar);
    backdrop.addEventListener('click', cerrar);
    btnPrev.addEventListener('click', () => {
        mesVista--;
        if (mesVista < 0) { mesVista = 11; anyoVista--; }
        renderizarCalendario();
    });
    btnNext.addEventListener('click', () => {
        mesVista++;
        if (mesVista > 11) { mesVista = 0; anyoVista++; }
        renderizarCalendario();
    });
    selectModalidad.addEventListener('change', () => {
        if (!modal.hidden) cargarDiasYRenderizar();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.hidden) cerrar();
    });
})();
