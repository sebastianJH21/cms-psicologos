(() => {
    const cfg = window.CalendarioConfig;

    // Configurar diccionario español para Calendar.js
    if (typeof calendarjs !== 'undefined' && calendarjs.setDictionary) {
        calendarjs.setDictionary({
            'Monday': 'Lunes',
            'Tuesday': 'Martes',
            'Wednesday': 'Miércoles',
            'Thursday': 'Jueves',
            'Friday': 'Viernes',
            'Saturday': 'Sábado',
            'Sunday': 'Domingo',
            'Mon': 'Lun',
            'Tue': 'Mar',
            'Wed': 'Mié',
            'Thu': 'Jue',
            'Fri': 'Vie',
            'Sat': 'Sáb',
            'Sun': 'Dom',
        });
    }

    const MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    const DIAS_CABECERA = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];

    // ─── Persistencia en localStorage ───────────────────────────────────────────
    const LS_VISTA = 'psicocms_cal_vista';
    const guardarVista = (v) => { try { localStorage.setItem(LS_VISTA, v); } catch (_) {} };
    const cargarVistaPersistida = () => {
        try { return localStorage.getItem(LS_VISTA) || 'month'; } catch (_) { return 'month'; }
    };

    // ─── Estado ─────────────────────────────────────────────────────────────────
    const hoy = new Date();
    let vistaActual = cargarVistaPersistida();
    let añoActual = hoy.getFullYear();
    let mesActual = hoy.getMonth();       // 0-11
    let fechaDia = new Date(hoy);         // para vista día
    let scheduleInst = null;
    let eventosPorGuid = {};
    let scheduleClickAbort = null;

    // ─── DOM refs ────────────────────────────────────────────────────────────────
    const root = document.getElementById('calendario-root');
    const tituloPeriodo = document.getElementById('cal-titulo-periodo');
    const selectMes = document.getElementById('cal-select-mes');
    const btnPrev = document.getElementById('cal-prev');
    const btnNext = document.getElementById('cal-next');

    // Modal 2: Nueva cita
    const modalNueva = document.getElementById('cal-nueva-modal');
    const modalNuevaBackdrop = document.getElementById('cal-nueva-backdrop');
    const modalNuevaClose = document.getElementById('cal-nueva-close');
    const modalNuevaCancelar = document.getElementById('cal-nueva-cancelar');
    const modalNuevaGuardar = document.getElementById('cal-nueva-guardar');

    // Modal 3: Detalle de cita
    const modalCitaDetalle = document.getElementById('cal-cita-detalle-modal');
    const modalCitaBackdrop = document.getElementById('cal-cita-detalle-backdrop');
    const modalCitaClose = document.getElementById('cal-cita-detalle-close');
    const modalCitaCancelar = document.getElementById('cal-cita-detalle-cancelar');
    const modalCitaGuardar = document.getElementById('cal-cita-detalle-guardar');
    const linkDetalle = document.getElementById('cal-link-detalle');
    const selectEstadoDetalle = document.getElementById('cal-d-estado-select');
    const errorDetalle = document.getElementById('cal-d-error');
    const okDetalle = document.getElementById('cal-d-ok');

    // Modal 4: Detalle de evento extra
    const modalEvDet = document.getElementById('cal-evento-detalle-modal');
    const modalEvDetBackdrop = document.getElementById('cal-evento-detalle-backdrop');
    const modalEvDetClose = document.getElementById('cal-evento-detalle-close');
    const modalEvDetBorrar = document.getElementById('cal-evd-borrar');
    const modalEvDetAceptar = document.getElementById('cal-evd-aceptar');
    const errorEvDetalle = document.getElementById('cal-evd-error');

    const btnNueva = document.getElementById('btn-nueva-cita');

    let detalleGuidActual = null;
    let estadoOriginal = null;
    let eventoIdActual = null;

    // ─── Helpers fecha ───────────────────────────────────────────────────────────
    const pad = (n) => String(n).padStart(2, '0');

    const toDatetimeLocal = (date) => {
        const d = date instanceof Date ? date : new Date(date);
        return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    };

    const addMinutes = (dtLocal, mins) => {
        const d = new Date(dtLocal);
        d.setMinutes(d.getMinutes() + mins);
        return toDatetimeLocal(d);
    };

    const duracionActual = () => {
        const modalidad = document.getElementById('cal-modalidad').value;
        return modalidad === 'online' ? cfg.duracionOnline : cfg.duracionPresencial;
    };

    const datetimeLocalToSQL = (val) => {
        if (!val) return '';
        const d = new Date(val);
        return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:00`;
    };

    const formatDisplay = (dateStr, startStr, endStr) => {
        const d = new Date(dateStr);
        const dia = d.toLocaleDateString('es-ES', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
        return `${dia}, ${startStr} – ${endStr}`;
    };

    // ─── Fetch ───────────────────────────────────────────────────────────────────
    const fetchJSON = async (url, opts = {}) => {
        const resp = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': cfg.csrfToken,
                'Accept': 'application/json',
                ...(opts.headers || {}),
            },
            ...opts,
        });
        const data = await resp.json().catch(() => ({}));
        return { ok: resp.ok, data };
    };

    const cargarEventos = async (start, end) => {
        const url = new URL(cfg.eventosUrl);
        url.searchParams.set('start', start);
        url.searchParams.set('end', end);
        const { ok, data } = await fetchJSON(url.toString(), { method: 'GET' });
        if (!ok) return [];
        eventosPorGuid = {};
        data.forEach((e) => { eventosPorGuid[e.guid] = e; });
        return data;
    };

    // ─── Actualizar título y selector de mes ─────────────────────────────────────
    const actualizarToolbar = () => {
        if (vistaActual === 'month') {
            tituloPeriodo.textContent = `${MESES[mesActual]} ${añoActual}`;
        } else if (vistaActual === 'week') {
            // CalendarJS dibuja la semana empezando en domingo, así que alineamos aquí.
            const domingo = new Date(fechaDia);
            domingo.setDate(domingo.getDate() - domingo.getDay());
            const sabado = new Date(domingo);
            sabado.setDate(sabado.getDate() + 6);
            const opts = { day: 'numeric', month: 'short' };
            tituloPeriodo.textContent = `${domingo.toLocaleDateString('es-ES', opts)} – ${sabado.toLocaleDateString('es-ES', opts)} ${sabado.getFullYear()}`;
        } else {
            tituloPeriodo.textContent = fechaDia.toLocaleDateString('es-ES', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
        }

        selectMes.value = vistaActual === 'month' ? mesActual : fechaDia.getMonth();
    };

    // ─── Vista MES (grid propio) ─────────────────────────────────────────────────
    const renderMes = (eventos) => {
        if (scheduleInst) {
            scheduleInst = null;
        }
        while (root.firstChild) root.removeChild(root.firstChild);
        root.className = 'cal-mes-grid';

        // Cabecera días
        const cabecera = document.createElement('div');
        cabecera.className = 'cal-mes-grid__cabecera';
        DIAS_CABECERA.forEach((d) => {
            const cel = document.createElement('div');
            cel.className = 'cal-mes-grid__cab-dia';
            cel.textContent = d;
            cabecera.appendChild(cel);
        });
        root.appendChild(cabecera);

        // Agrupar eventos por fecha
        const eventosPorFecha = {};
        eventos.forEach((ev) => {
            if (!eventosPorFecha[ev.date]) eventosPorFecha[ev.date] = [];
            eventosPorFecha[ev.date].push(ev);
        });

        // Celdas del mes
        const primerDia = new Date(añoActual, mesActual, 1);
        const ultimoDia = new Date(añoActual, mesActual + 1, 0);
        const inicioGrid = (primerDia.getDay() + 6) % 7; // lunes = 0
        const totalCeldas = Math.ceil((inicioGrid + ultimoDia.getDate()) / 7) * 7;

        const cuerpo = document.createElement('div');
        cuerpo.className = 'cal-mes-grid__cuerpo';

        for (let i = 0; i < totalCeldas; i++) {
            const cel = document.createElement('div');
            cel.className = 'cal-mes-grid__celda';

            const dayNum = i - inicioGrid + 1;
            if (dayNum < 1 || dayNum > ultimoDia.getDate()) {
                cel.classList.add('cal-mes-grid__celda--fuera');
            } else {
                const fechaStr = `${añoActual}-${pad(mesActual + 1)}-${pad(dayNum)}`;
                cel.classList.add('cal-mes-grid__celda--activa');
                cel.title = `Ver día ${dayNum}`;

                const numEl = document.createElement('span');
                numEl.className = 'cal-mes-grid__num';
                numEl.textContent = dayNum;

                const esHoy = hoy.getFullYear() === añoActual && hoy.getMonth() === mesActual && hoy.getDate() === dayNum;
                if (esHoy) numEl.classList.add('cal-mes-grid__num--hoy');
                cel.appendChild(numEl);

                const evs = eventosPorFecha[fechaStr] || [];
                evs.slice(0, 3).forEach((ev) => {
                    const chip = crearChipEvento(ev);
                    cel.appendChild(chip);
                });
                if (evs.length > 3) {
                    const mas = document.createElement('span');
                    mas.className = 'cal-mes-grid__mas';
                    mas.textContent = `+${evs.length - 3} más`;
                    cel.appendChild(mas);
                }

                // Click en la celda (fuera del chip) → abre vista día para esa fecha
                cel.addEventListener('click', (e) => {
                    if (e.target.closest('.cal-mes-chip')) return;
                    fechaDia = new Date(añoActual, mesActual, dayNum);
                    cambiarVista('day');
                });
            }

            cuerpo.appendChild(cel);
        }

        root.appendChild(cuerpo);
    };

    const crearChipEvento = (ev) => {
        const chip = document.createElement('button');
        chip.type = 'button';
        chip.className = 'cal-mes-chip';
        chip.style.setProperty('--chip-color', ev.color);
        chip.textContent = ev.title;
        chip.addEventListener('click', (e) => {
            e.preventDefault();
            abrirDetalle(ev);
        });
        return chip;
    };

    // ─── Scroll a la hora actual (día/semana) ────────────────────────────────────
    const periodoIncluyeHoy = () => {
        const h = new Date();
        h.setHours(0, 0, 0, 0);
        if (vistaActual === 'day') {
            const f = new Date(fechaDia);
            f.setHours(0, 0, 0, 0);
            return f.getTime() === h.getTime();
        }
        if (vistaActual === 'week') {
            const domingo = new Date(fechaDia);
            domingo.setDate(domingo.getDate() - domingo.getDay());
            domingo.setHours(0, 0, 0, 0);
            const sabado = new Date(domingo);
            sabado.setDate(sabado.getDate() + 6);
            sabado.setHours(23, 59, 59, 999);
            return h >= domingo && h <= sabado;
        }
        return false;
    };

    const scrollToCurrentHour = () => {
        if (!periodoIncluyeHoy()) return;
        setTimeout(() => {
            const hora = new Date().getHours();
            const horaStr = `${pad(hora)}:00`;

            const allEls = root.querySelectorAll('*');
            for (const el of allEls) {
                if (el.children.length === 0 && el.textContent.trim() === horaStr) {
                    el.scrollIntoView({ block: 'center', behavior: 'smooth' });
                    return;
                }
            }

            const horaBase = 8;
            const horaFin = 20;
            const fraccion = Math.max(0, Math.min(1, (hora - horaBase) / (horaFin - horaBase)));
            const buscarScrollable = (el) => {
                const s = window.getComputedStyle(el);
                if ((s.overflowY === 'scroll' || s.overflowY === 'auto') && el.scrollHeight > el.clientHeight + 20) {
                    return el;
                }
                for (const child of el.children) {
                    const found = buscarScrollable(child);
                    if (found) return found;
                }
                return null;
            };
            const scrollable = buscarScrollable(root);
            if (scrollable) scrollable.scrollTop = scrollable.scrollHeight * fraccion;
        }, 200);
    };

    // ─── Traducir días al español en el DOM del Schedule ─────────────────────────
    const traducirDiasSchedule = () => {
        const MAP = {
            'Monday': 'Lunes', 'Tuesday': 'Martes', 'Wednesday': 'Miércoles',
            'Thursday': 'Jueves', 'Friday': 'Viernes', 'Saturday': 'Sábado', 'Sunday': 'Domingo',
            'Mon': 'Lun', 'Tue': 'Mar', 'Wed': 'Mié',
            'Thu': 'Jue', 'Fri': 'Vie', 'Sat': 'Sáb', 'Sun': 'Dom',
        };
        const RE_TEST = /\b(Mon|Tue|Wed|Thu|Fri|Sat|Sun|Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)\b/;
        const RE_REPLACE = /\b(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday|Mon|Tue|Wed|Thu|Fri|Sat|Sun)\b/g;

        const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);
        const nodos = [];
        let node;
        while ((node = walker.nextNode())) {
            nodos.push(node);
        }
        nodos.forEach((n) => {
            if (RE_TEST.test(n.nodeValue)) {
                n.nodeValue = n.nodeValue.replace(RE_REPLACE, (m) => MAP[m] || m);
            }
        });
    };

    // ─── Vista SEMANA / DÍA (calendarjs.Schedule) ────────────────────────────────
    const construirSchedule = (tipo, eventos, fechaStr) => {
        const { Schedule } = calendarjs;
        return Schedule(root, {
            type: tipo,
            value: fechaStr,
            data: eventos,
            ondblclick: (_self, evento) => {
                if (!evento) return;
                const ev = eventosPorGuid[evento.guid] || evento;
                abrirDetalle(ev);
            },
            onbeforecreate: () => false,
        });
    };

    const renderSchedule = (tipo, eventos) => {
        while (root.firstChild) root.removeChild(root.firstChild);
        root.className = '';

        const fechaStr = `${fechaDia.getFullYear()}-${pad(fechaDia.getMonth()+1)}-${pad(fechaDia.getDate())}`;

        scheduleInst = construirSchedule(tipo, eventos, fechaStr);

        requestAnimationFrame(() => {
            if (scheduleInst && typeof scheduleInst.render === 'function') {
                scheduleInst.render();
            }
            setTimeout(traducirDiasSchedule, 100);
        });

        // Single click sobre el evento abre el detalle también
        if (scheduleClickAbort) scheduleClickAbort.abort();
        scheduleClickAbort = new AbortController();
        root.addEventListener('click', (e) => {
            const el = e.target.closest('.lm-schedule-item');
            if (!el) return;
            const guid = el.id;
            if (!guid) return;
            const ev = eventosPorGuid[guid];
            if (ev) abrirDetalle(ev);
        }, { signal: scheduleClickAbort.signal });

        // Bloquear creación de eventos por drag/click en celdas vacías
        const bloquearCreacion = (e) => {
            if (e.target.closest('.lm-schedule-item')) return;
            const tag = (e.target.tagName || '').toLowerCase();
            if (tag === 'td' || tag === 'tr' || tag === 'tbody') {
                e.stopPropagation();
                e.preventDefault();
            }
        };
        root.addEventListener('mousedown', bloquearCreacion, { signal: scheduleClickAbort.signal, capture: true });
        root.addEventListener('dragstart', bloquearCreacion, { signal: scheduleClickAbort.signal, capture: true });

        scrollToCurrentHour();
        setTimeout(traducirDiasSchedule, 300);
    };

    // ─── Renderizar según vista ────────────────────────────────────────────────────
    const renderizar = async () => {
        let start, end;

        if (vistaActual === 'month') {
            start = `${añoActual}-${pad(mesActual + 1)}-01`;
            const ultimo = new Date(añoActual, mesActual + 1, 0).getDate();
            end = `${añoActual}-${pad(mesActual + 1)}-${pad(ultimo)}`;
        } else if (vistaActual === 'week') {
            // CalendarJS dibuja la semana empezando en domingo, así que pedimos ese rango.
            const domingo = new Date(fechaDia);
            domingo.setDate(domingo.getDate() - domingo.getDay());
            const sabado = new Date(domingo);
            sabado.setDate(sabado.getDate() + 6);
            start = `${domingo.getFullYear()}-${pad(domingo.getMonth()+1)}-${pad(domingo.getDate())}`;
            end = `${sabado.getFullYear()}-${pad(sabado.getMonth()+1)}-${pad(sabado.getDate())}`;
        } else {
            const f = fechaDia;
            start = end = `${f.getFullYear()}-${pad(f.getMonth()+1)}-${pad(f.getDate())}`;
        }

        actualizarToolbar();
        const eventos = await cargarEventos(start, end);

        if (vistaActual === 'month') {
            renderMes(eventos);
        } else {
            renderSchedule(vistaActual, eventos);
        }
    };

    // ─── Navegación ───────────────────────────────────────────────────────────────
    const navegar = (dir) => {
        if (vistaActual === 'month') {
            mesActual += dir;
            if (mesActual < 0) { mesActual = 11; añoActual--; }
            if (mesActual > 11) { mesActual = 0; añoActual++; }
        } else if (vistaActual === 'week') {
            fechaDia.setDate(fechaDia.getDate() + dir * 7);
        } else {
            fechaDia.setDate(fechaDia.getDate() + dir);
        }
        renderizar();
    };

    const cambiarVista = (nuevaVista) => {
        vistaActual = nuevaVista;
        guardarVista(nuevaVista);
        if (nuevaVista === 'month') {
            añoActual = fechaDia.getFullYear();
            mesActual = fechaDia.getMonth();
        }
        document.querySelectorAll('.cal-vista-btn').forEach((btn) => {
            btn.classList.toggle('cal-vista-btn--active', btn.dataset.vista === nuevaVista);
        });
        renderizar();
    };

    // ═══════════════════════════════════════════════════════════════════════════
    // MODAL 2: NUEVA CITA
    // ═══════════════════════════════════════════════════════════════════════════
    const abrirNuevaCita = () => {
        limpiarErrores();
        const calNombre = document.getElementById('cal-nombre');
        const calTelefono = document.getElementById('cal-telefono');
        if (calNombre) { calNombre.value = ''; calNombre.readOnly = false; calNombre.classList.remove('form-input--locked'); }
        if (calTelefono) { calTelefono.value = ''; calTelefono.readOnly = false; calTelefono.classList.remove('form-input--locked'); }
        document.getElementById('cal-modalidad').value = 'presencial';
        document.getElementById('cal-estado').value = 'confirmada';
        document.getElementById('cal-motivo').value = '';
        const fieldPid = document.getElementById('cal-paciente-id');
        if (fieldPid) fieldPid.value = '';
        const sel = document.getElementById('cal-paciente-seleccionado');
        if (sel) { sel.hidden = true; while (sel.firstChild) sel.removeChild(sel.firstChild); }
        const dropdown = document.getElementById('cal-paciente-dropdown');
        if (dropdown) { dropdown.hidden = true; while (dropdown.firstChild) dropdown.removeChild(dropdown.firstChild); }

        const ahora = new Date();
        ahora.setSeconds(0, 0);
        ahora.setMinutes(Math.ceil(ahora.getMinutes() / 30) * 30);
        document.getElementById('cal-fecha-inicio').value = toDatetimeLocal(ahora);
        document.getElementById('cal-fecha-fin').value = addMinutes(toDatetimeLocal(ahora), duracionActual());
        calLockFecha();

        if (modalNueva) modalNueva.hidden = false;
        document.getElementById('cal-nombre').focus();
    };

    const cerrarNuevaCita = () => {
        if (modalNueva) modalNueva.hidden = true;
        limpiarErrores();
    };

    // ═══════════════════════════════════════════════════════════════════════════
    // MODAL 3: DETALLE DE CITA
    // ═══════════════════════════════════════════════════════════════════════════
    const abrirDetalleCita = (ev) => {
        if (errorDetalle) errorDetalle.hidden = true;
        if (okDetalle) okDetalle.classList.remove('is-visible');

        detalleGuidActual = ev.guid;

        const setTxt = (id, txt) => { const el = document.getElementById(id); if (el) el.textContent = txt || '—'; };
        setTxt('cal-d-nombre', ev.title);
        setTxt('cal-d-fecha', formatDisplay(ev.date, ev.start, ev.end));
        setTxt('cal-d-modalidad', ev.modalidad || ev.location || '');

        const telDigits = (ev.telefono || '').replace(/[^+\d]/g, '');
        const ddTel = document.getElementById('cal-d-telefono');
        if (ddTel) {
            while (ddTel.firstChild) ddTel.removeChild(ddTel.firstChild);
            if (ev.telefono) {
                const aTel = document.createElement('a');
                aTel.href = 'tel:' + telDigits;
                aTel.className = 'cal-detalle__tel';
                aTel.textContent = ev.telefono;
                ddTel.appendChild(aTel);
            } else {
                ddTel.textContent = '—';
            }
        }

        const waRow = document.getElementById('cal-d-whatsapp-row');
        const waLink = document.getElementById('cal-d-whatsapp');
        const waDigits = (ev.telefono || '').replace(/\D/g, '');
        if (waRow && waLink) {
            if (waDigits) {
                const primerNombre = (ev.title || '').trim().split(' ')[0] || 'hola';
                const partesFecha = (ev.date || '').split('-');
                const fechaFmt = partesFecha.length === 3
                    ? `${partesFecha[2]}/${partesFecha[1]}/${partesFecha[0]}`
                    : (ev.date || '');
                const mensaje = `Hola ${primerNombre}, soy ${cfg.psicologa || 'tu psicóloga'}. `
                    + `Te escribo para confirmar tu cita ${ev.modalidad || ''} `
                    + `del ${fechaFmt} a las ${ev.start || ''}. `
                    + `¿Me confirmas que podrás asistir? ¡Gracias!`;
                waLink.href = `https://wa.me/${waDigits}?text=${encodeURIComponent(mensaje)}`;
                waRow.hidden = false;
            } else {
                waRow.hidden = true;
            }
        }

        if (selectEstadoDetalle) {
            selectEstadoDetalle.disabled = false;
            const valorClave = ev.estado_key || '';
            if (valorClave && [...selectEstadoDetalle.options].some((o) => o.value === valorClave)) {
                selectEstadoDetalle.value = valorClave;
            } else {
                const opt = [...selectEstadoDetalle.options].find((o) => o.text === ev.estado);
                if (opt) selectEstadoDetalle.value = opt.value;
            }
            estadoOriginal = selectEstadoDetalle.value;
        }

        const motivoRow = document.getElementById('cal-d-motivo-row');
        if (ev.motivo) {
            setTxt('cal-d-motivo', ev.motivo);
            if (motivoRow) motivoRow.hidden = false;
        } else if (motivoRow) {
            motivoRow.hidden = true;
        }

        if (linkDetalle) {
            if (ev.url) {
                linkDetalle.href = ev.url;
                linkDetalle.hidden = false;
            } else {
                linkDetalle.hidden = true;
            }
        }

        if (modalCitaDetalle) modalCitaDetalle.hidden = false;
    };

    const cerrarDetalleCita = () => {
        if (modalCitaDetalle) modalCitaDetalle.hidden = true;
        if (okDetalle) okDetalle.classList.remove('is-visible');
        if (errorDetalle) errorDetalle.hidden = true;
    };

    const guardarEstadoDetalle = async () => {
        if (!detalleGuidActual || !selectEstadoDetalle) return;
        if (errorDetalle) errorDetalle.hidden = true;
        if (okDetalle) okDetalle.classList.remove('is-visible');

        const nuevoEstado = selectEstadoDetalle.value;
        if (nuevoEstado === estadoOriginal) {
            if (okDetalle) {
                okDetalle.firstChild.nodeValue = ' Sin cambios';
                okDetalle.classList.add('is-visible');
            }
            return;
        }

        modalCitaGuardar.disabled = true;
        const url = `${cfg.actualizarBase}/${detalleGuidActual}`;
        const { ok, data } = await fetchJSON(url, {
            method: 'PATCH',
            body: JSON.stringify({ estado: nuevoEstado }),
        });
        modalCitaGuardar.disabled = false;

        if (!ok) {
            const msgs = Object.values(data.errors || {}).flat().join(' ');
            if (errorDetalle) {
                errorDetalle.textContent = msgs || 'Error al actualizar el estado.';
                errorDetalle.hidden = false;
            }
            return;
        }

        if (okDetalle) {
            okDetalle.firstChild.nodeValue = ' Estado actualizado';
            okDetalle.classList.add('is-visible');
        }
        estadoOriginal = nuevoEstado;
        renderizar();
    };

    // ═══════════════════════════════════════════════════════════════════════════
    // MODAL 4: DETALLE DE EVENTO EXTRA
    // ═══════════════════════════════════════════════════════════════════════════
    const abrirDetalleEvento = (ev) => {
        if (errorEvDetalle) errorEvDetalle.hidden = true;
        eventoIdActual = ev.evento_id || (ev.guid || '').replace(/^evt-/, '');

        const setTxt = (id, txt) => { const el = document.getElementById(id); if (el) el.textContent = txt || '—'; };
        setTxt('cal-evd-titulo', ev.title);
        setTxt('cal-evd-fecha', formatDisplay(ev.date, ev.start, ev.end));

        const ubicacionRow = document.getElementById('cal-evd-ubicacion-row');
        if (ev.location) {
            setTxt('cal-evd-ubicacion', ev.location);
            if (ubicacionRow) ubicacionRow.hidden = false;
        } else if (ubicacionRow) {
            ubicacionRow.hidden = true;
        }

        const descRow = document.getElementById('cal-evd-desc-row');
        if (ev.motivo || ev.description) {
            setTxt('cal-evd-desc', ev.motivo || ev.description);
            if (descRow) descRow.hidden = false;
        } else if (descRow) {
            descRow.hidden = true;
        }

        if (modalEvDet) modalEvDet.hidden = false;
    };

    const cerrarDetalleEvento = () => {
        if (modalEvDet) modalEvDet.hidden = true;
        if (errorEvDetalle) errorEvDetalle.hidden = true;
    };

    const borrarEvento = async () => {
        if (!eventoIdActual) return;
        const url = `${cfg.eventoExtraBase}/${eventoIdActual}`;
        modalEvDetBorrar.disabled = true;
        const { ok } = await fetchJSON(url, { method: 'DELETE' });
        modalEvDetBorrar.disabled = false;
        if (ok) {
            cerrarDetalleEvento();
            renderizar();
        } else if (errorEvDetalle) {
            errorEvDetalle.textContent = 'No se pudo borrar el evento.';
            errorEvDetalle.hidden = false;
        }
    };

    // Dispatcher: decide qué modal de detalle abrir según tipo
    const abrirDetalle = (ev) => {
        if (ev.tipo === 'evento') {
            abrirDetalleEvento(ev);
        } else {
            abrirDetalleCita(ev);
        }
    };

    const limpiarErrores = () => {
        ['cal-error-nombre','cal-error-telefono','cal-error-fecha','cal-error-solapamiento'].forEach((id) => {
            const el = document.getElementById(id);
            if (el) el.hidden = true;
        });
    };

    const mostrarError = (id, msg) => {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = msg;
        el.hidden = false;
    };

    // ─── Guardar nueva cita ───────────────────────────────────────────────────────
    const guardarCita = async () => {
        limpiarErrores();

        const nombre = document.getElementById('cal-nombre').value.trim();
        const telefono = document.getElementById('cal-telefono').value.trim();
        const fechaInicio = document.getElementById('cal-fecha-inicio').value;
        const fechaFin = document.getElementById('cal-fecha-fin').value;
        const modalidad = document.getElementById('cal-modalidad').value;
        const estado = document.getElementById('cal-estado').value;
        const motivo = document.getElementById('cal-motivo').value.trim();
        const pacienteIdEl = document.getElementById('cal-paciente-id');
        const pacienteId = pacienteIdEl ? pacienteIdEl.value : '';

        let valido = true;
        if (!nombre) { mostrarError('cal-error-nombre', 'El nombre es obligatorio.'); valido = false; }
        if (!telefono) { mostrarError('cal-error-telefono', 'El teléfono es obligatorio.'); valido = false; }
        if (!fechaInicio || !fechaFin) { mostrarError('cal-error-fecha', 'Indica la fecha y hora de inicio y fin.'); valido = false; }
        if (!valido) return;

        const payload = {
            nombre_provisional: nombre,
            telefono_provisional: telefono,
            modalidad,
            estado,
            fecha_inicio: datetimeLocalToSQL(fechaInicio),
            fecha_fin: datetimeLocalToSQL(fechaFin),
            motivo,
        };
        if (pacienteId) payload.paciente_id = pacienteId;

        const { ok, data } = await fetchJSON(cfg.crearUrl, {
            method: 'POST',
            body: JSON.stringify(payload),
        });

        if (!ok) {
            const msgs = Object.values(data.errors || {}).flat().join(' ');
            mostrarError('cal-error-solapamiento', msgs || 'Error al guardar la cita.');
            return;
        }

        cerrarNuevaCita();
        renderizar();
    };

    // ═══════════════════════════════════════════════════════════════════════════
    // BUSCADOR AJAX DE PACIENTES (en modal nueva cita)
    // ═══════════════════════════════════════════════════════════════════════════
    const inputNombre = document.getElementById('cal-nombre');
    const inputTelefono = document.getElementById('cal-telefono');
    const fieldPacienteId = document.getElementById('cal-paciente-id');
    const dropdownPaciente = document.getElementById('cal-paciente-dropdown');
    const seleccionadoPaciente = document.getElementById('cal-paciente-seleccionado');
    let pacienteBuscarTimer = null;

    const ocultarDropdown = () => {
        if (!dropdownPaciente) return;
        dropdownPaciente.hidden = true;
        while (dropdownPaciente.firstChild) dropdownPaciente.removeChild(dropdownPaciente.firstChild);
    };

    const marcarPacienteSeleccionado = (p) => {
        if (!seleccionadoPaciente) return;
        while (seleccionadoPaciente.firstChild) seleccionadoPaciente.removeChild(seleccionadoPaciente.firstChild);
        const icon = document.createElement('i');
        icon.className = 'fa-solid fa-circle-check';
        const txt = document.createTextNode(' Paciente vinculado: ' + p.nombre + ' · ' + p.telefono);
        const btnClear = document.createElement('button');
        btnClear.type = 'button';
        btnClear.className = 'cal-paciente-seleccionado__clear';
        btnClear.setAttribute('aria-label', 'Quitar selección');
        const xIcon = document.createElement('i');
        xIcon.className = 'fa-solid fa-xmark';
        btnClear.appendChild(xIcon);
        btnClear.addEventListener('click', () => {
            if (fieldPacienteId) fieldPacienteId.value = '';
            if (inputNombre) inputNombre.value = '';
            desbloquearCamposCalPaciente();
            seleccionadoPaciente.hidden = true;
            while (seleccionadoPaciente.firstChild) seleccionadoPaciente.removeChild(seleccionadoPaciente.firstChild);
        });
        seleccionadoPaciente.appendChild(icon);
        seleccionadoPaciente.appendChild(txt);
        seleccionadoPaciente.appendChild(btnClear);
        seleccionadoPaciente.hidden = false;
    };

    const bloquearCamposCalPaciente = () => {
        if (inputNombre) {
            inputNombre.readOnly = true;
            inputNombre.classList.add('form-input--locked');
        }
        if (inputTelefono) {
            inputTelefono.readOnly = true;
            inputTelefono.classList.add('form-input--locked');
        }
    };

    const desbloquearCamposCalPaciente = () => {
        if (inputNombre) {
            inputNombre.readOnly = false;
            inputNombre.classList.remove('form-input--locked');
        }
        if (inputTelefono) {
            inputTelefono.value = '';
            inputTelefono.readOnly = false;
            inputTelefono.classList.remove('form-input--locked');
        }
    };

    const seleccionarPaciente = (p) => {
        if (fieldPacienteId) fieldPacienteId.value = p.id;
        if (inputNombre) inputNombre.value = p.nombre || '';
        if (inputTelefono) inputTelefono.value = p.telefono || '';
        bloquearCamposCalPaciente();
        ocultarDropdown();
        marcarPacienteSeleccionado(p);
    };

    const renderResultadosPacientes = (resultados) => {
        ocultarDropdown();
        if (!dropdownPaciente) return;
        if (!resultados.length) {
            const empty = document.createElement('div');
            empty.className = 'cal-paciente-dropdown__empty';
            empty.textContent = 'No se encontraron pacientes';
            dropdownPaciente.appendChild(empty);
            dropdownPaciente.hidden = false;
            return;
        }
        resultados.forEach((p) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'cal-paciente-dropdown__item';
            const nombre = document.createElement('span');
            nombre.className = 'cal-paciente-dropdown__item-nombre';
            nombre.textContent = p.nombre;
            const meta = document.createElement('span');
            meta.className = 'cal-paciente-dropdown__item-meta';
            meta.textContent = p.telefono + (p.email ? ' · ' + p.email : '');
            btn.appendChild(nombre);
            btn.appendChild(meta);
            btn.addEventListener('click', () => seleccionarPaciente(p));
            dropdownPaciente.appendChild(btn);
        });
        dropdownPaciente.hidden = false;
    };

    const buscarPacientes = async (q) => {
        if (!cfg.pacientesBuscarUrl) return;
        if (q.length < 2) { ocultarDropdown(); return; }
        try {
            const url = new URL(cfg.pacientesBuscarUrl, window.location.origin);
            url.searchParams.set('q', q);
            const res = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            });
            if (!res.ok) return;
            const data = await res.json();
            renderResultadosPacientes(Array.isArray(data) ? data : []);
        } catch { ocultarDropdown(); }
    };

    if (inputNombre) {
        inputNombre.addEventListener('input', () => {
            if (fieldPacienteId && fieldPacienteId.value) {
                fieldPacienteId.value = '';
                desbloquearCamposCalPaciente();
                if (seleccionadoPaciente) {
                    seleccionadoPaciente.hidden = true;
                    while (seleccionadoPaciente.firstChild) seleccionadoPaciente.removeChild(seleccionadoPaciente.firstChild);
                }
            }
            clearTimeout(pacienteBuscarTimer);
            pacienteBuscarTimer = setTimeout(() => buscarPacientes(inputNombre.value.trim()), 300);
        });
    }

    document.addEventListener('click', (e) => {
        if (dropdownPaciente && !dropdownPaciente.hidden) {
            if (!e.target.closest('#cal-paciente-dropdown') && e.target !== inputNombre) {
                ocultarDropdown();
            }
        }
    });

    // ─── Event listeners ──────────────────────────────────────────────────────────
    btnNueva.addEventListener('click', (e) => { e.preventDefault(); abrirNuevaCita(); });

    // Modal 2: Nueva cita
    if (modalNuevaGuardar) modalNuevaGuardar.addEventListener('click', (e) => { e.preventDefault(); guardarCita(); });
    if (modalNuevaClose) modalNuevaClose.addEventListener('click', cerrarNuevaCita);
    if (modalNuevaCancelar) modalNuevaCancelar.addEventListener('click', cerrarNuevaCita);
    if (modalNuevaBackdrop) modalNuevaBackdrop.addEventListener('click', cerrarNuevaCita);

    // Modal 3: Detalle de cita
    if (modalCitaGuardar) modalCitaGuardar.addEventListener('click', (e) => { e.preventDefault(); guardarEstadoDetalle(); });
    if (modalCitaClose) modalCitaClose.addEventListener('click', cerrarDetalleCita);
    if (modalCitaCancelar) modalCitaCancelar.addEventListener('click', cerrarDetalleCita);
    if (modalCitaBackdrop) modalCitaBackdrop.addEventListener('click', cerrarDetalleCita);

    // Modal 4: Detalle de evento
    if (modalEvDetClose) modalEvDetClose.addEventListener('click', cerrarDetalleEvento);
    if (modalEvDetAceptar) modalEvDetAceptar.addEventListener('click', cerrarDetalleEvento);
    if (modalEvDetBorrar) modalEvDetBorrar.addEventListener('click', (e) => { e.preventDefault(); borrarEvento(); });
    if (modalEvDetBackdrop) modalEvDetBackdrop.addEventListener('click', cerrarDetalleEvento);

    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        if (modalNueva && !modalNueva.hidden) cerrarNuevaCita();
        if (modalCitaDetalle && !modalCitaDetalle.hidden) cerrarDetalleCita();
        if (modalEvDet && !modalEvDet.hidden) cerrarDetalleEvento();
    });

    btnPrev.addEventListener('click', (e) => { e.preventDefault(); navegar(-1); });
    btnNext.addEventListener('click', (e) => { e.preventDefault(); navegar(1); });

    selectMes.addEventListener('change', () => {
        const nuevoMes = parseInt(selectMes.value, 10);
        mesActual = nuevoMes;
        fechaDia = new Date(añoActual, nuevoMes, 1);
        if (vistaActual !== 'month') {
            vistaActual = 'month';
            document.querySelectorAll('.cal-vista-btn').forEach((btn) => {
                btn.classList.toggle('cal-vista-btn--active', btn.dataset.vista === 'month');
            });
        }
        renderizar();
    });

    document.querySelectorAll('.cal-vista-btn').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            cambiarVista(btn.dataset.vista);
        });
    });

    document.getElementById('cal-fecha-inicio').addEventListener('change', () => {
        const inicio = document.getElementById('cal-fecha-inicio').value;
        if (inicio) {
            document.getElementById('cal-fecha-fin').value = addMinutes(inicio, duracionActual());
        }
    });

    document.getElementById('cal-modalidad').addEventListener('change', () => {
        const inicio = document.getElementById('cal-fecha-inicio').value;
        if (inicio) {
            document.getElementById('cal-fecha-fin').value = addMinutes(inicio, duracionActual());
        }
    });

    // ─── Picker de disponibilidad en modal ─────────────────────────────────────────
    const dispToggle = document.getElementById('cal-disp-toggle');
    const dispPanel = document.getElementById('cal-disp');
    const dispDias = document.getElementById('cal-disp-dias');
    const dispSlots = document.getElementById('cal-disp-slots');
    const dispEmpty = document.getElementById('cal-disp-empty');
    const dispCerrar = document.getElementById('cal-disp-cerrar');
    const calBtnManual = document.getElementById('cal-btn-manual');
    const calHintBloqueada = document.getElementById('cal-hint-fecha-bloqueada');

    const dispLimpiar = (el) => { while (el.firstChild) el.removeChild(el.firstChild); };

    // ─── Lock / unlock campo fecha modal ─────────────────────────────────────
    const calLockFecha = () => {
        const f = document.getElementById('cal-fecha-inicio');
        if (!f) return;
        f.readOnly = true;
        f.classList.add('form-input--locked');
        if (calHintBloqueada) calHintBloqueada.hidden = true;
    };
    const calUnlockFecha = () => {
        const f = document.getElementById('cal-fecha-inicio');
        if (!f) return;
        f.readOnly = false;
        f.classList.remove('form-input--locked');
        if (calHintBloqueada) calHintBloqueada.hidden = true;
        f.focus();
    };

    const calFechaInput = document.getElementById('cal-fecha-inicio');
    if (calFechaInput) {
        calFechaInput.addEventListener('click', () => {
            if (calFechaInput.readOnly && calHintBloqueada) calHintBloqueada.hidden = false;
        });
        calFechaInput.addEventListener('blur', () => {
            if (calHintBloqueada) calHintBloqueada.hidden = true;
        });
    }
    if (calBtnManual) calBtnManual.addEventListener('click', calUnlockFecha);

    const dispCargarSlots = async (fecha) => {
        if (!dispSlots) return;
        dispLimpiar(dispSlots);
        dispSlots.hidden = false;
        const modalidad = document.getElementById('cal-modalidad').value || 'presencial';
        try {
            const url = `/reservas/slots?modalidad=${encodeURIComponent(modalidad)}&fecha=${encodeURIComponent(fecha)}`;
            const res = await fetch(url);
            if (!res.ok) throw new Error();
            const data = await res.json();
            const slots = data.slots || [];
            if (!slots.length) {
                const p = document.createElement('p');
                p.className = 'cita-disponibilidad__empty-slots';
                p.textContent = 'No hay huecos libres para ese día.';
                dispSlots.appendChild(p);
                return;
            }
            slots.forEach((s) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'cita-disponibilidad__slot';
                btn.textContent = s.hora;
                btn.addEventListener('click', () => {
                    const dt = new Date(s.inicio_iso);
                    const valor = toDatetimeLocal(dt);
                    document.getElementById('cal-fecha-inicio').value = valor;
                    document.getElementById('cal-fecha-fin').value = addMinutes(valor, duracionActual());
                    calLockFecha();
                    dispPanel.hidden = true;
                });
                dispSlots.appendChild(btn);
            });
        } catch {}
    };

    const dispCargarDias = async () => {
        if (!dispDias) return;
        dispLimpiar(dispDias);
        dispSlots.hidden = true;
        dispEmpty.hidden = true;
        const modalidad = document.getElementById('cal-modalidad').value || 'presencial';
        try {
            const res = await fetch(`/reservas/dias?modalidad=${encodeURIComponent(modalidad)}`);
            if (!res.ok) throw new Error();
            const data = await res.json();
            const dias = data.dias || [];
            if (!dias.length) {
                dispEmpty.hidden = false;
                return;
            }
            dias.forEach((d) => {
                const b = document.createElement('button');
                b.type = 'button';
                b.className = 'cita-disponibilidad__dia';
                const dObj = new Date(d + 'T00:00:00');
                b.textContent = dObj.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric', month: 'short' });
                b.addEventListener('click', () => {
                    document.querySelectorAll('#cal-disp-dias .cita-disponibilidad__dia').forEach((x) => x.classList.remove('is-active'));
                    b.classList.add('is-active');
                    dispCargarSlots(d);
                });
                dispDias.appendChild(b);
            });
        } catch {
            dispEmpty.hidden = false;
        }
    };

    if (dispToggle) {
        dispToggle.addEventListener('click', () => {
            calLockFecha();
            dispPanel.hidden = !dispPanel.hidden;
            if (!dispPanel.hidden) dispCargarDias();
        });
        if (dispCerrar) dispCerrar.addEventListener('click', () => { dispPanel.hidden = true; });
        document.getElementById('cal-modalidad').addEventListener('change', () => {
            if (!dispPanel.hidden) dispCargarDias();
        });
    }

    // ─── Modal Eventos Extra ───────────────────────────────────────────────────────
    const evtModal = document.getElementById('cal-evento-modal');
    const btnNuevoEvento = document.getElementById('btn-nuevo-evento');
    const btnEvtClose = document.getElementById('cal-evento-close');
    const btnEvtCancel = document.getElementById('cal-evento-cancelar');
    const btnEvtSave = document.getElementById('cal-evento-guardar');
    const evtBackdrop = document.getElementById('cal-evento-backdrop');

    const abrirEvento = () => {
        if (!evtModal) return;
        document.getElementById('cal-ev-titulo').value = '';
        const ahora = new Date();
        ahora.setSeconds(0, 0);
        ahora.setMinutes(Math.ceil(ahora.getMinutes() / 30) * 30);
        const luego = new Date(ahora.getTime() + 60 * 60 * 1000);
        document.getElementById('cal-ev-inicio').value = toDatetimeLocal(ahora);
        document.getElementById('cal-ev-fin').value = toDatetimeLocal(luego);
        document.getElementById('cal-ev-color').value = '#9b59b6';
        document.getElementById('cal-ev-ubicacion').value = '';
        document.getElementById('cal-ev-desc').value = '';
        document.getElementById('cal-ev-error').hidden = true;
        document.getElementById('cal-ev-error-titulo').hidden = true;
        evtModal.hidden = false;
        document.getElementById('cal-ev-titulo').focus();
    };

    const cerrarEvento = () => { if (evtModal) evtModal.hidden = true; };

    const guardarEvento = async () => {
        const titulo = document.getElementById('cal-ev-titulo').value.trim();
        const inicio = document.getElementById('cal-ev-inicio').value;
        const fin = document.getElementById('cal-ev-fin').value;
        const color = document.getElementById('cal-ev-color').value;
        const ubicacion = document.getElementById('cal-ev-ubicacion').value.trim();
        const desc = document.getElementById('cal-ev-desc').value.trim();
        const errEl = document.getElementById('cal-ev-error');
        const errTit = document.getElementById('cal-ev-error-titulo');

        errEl.hidden = true;
        errTit.hidden = true;

        if (!titulo) {
            errTit.textContent = 'El título es obligatorio.';
            errTit.hidden = false;
            return;
        }

        btnEvtSave.disabled = true;
        const { ok, data } = await fetchJSON(cfg.eventoExtraUrl, {
            method: 'POST',
            body: JSON.stringify({
                titulo, descripcion: desc, color, ubicacion,
                fecha_inicio: datetimeLocalToSQL(inicio),
                fecha_fin: datetimeLocalToSQL(fin),
            }),
        });
        btnEvtSave.disabled = false;

        if (!ok) {
            const msgs = Object.values(data.errors || {}).flat().join(' ');
            errEl.textContent = msgs || 'No se pudo guardar el evento.';
            errEl.hidden = false;
            return;
        }
        cerrarEvento();
        renderizar();
    };

    if (btnNuevoEvento) btnNuevoEvento.addEventListener('click', (e) => { e.preventDefault(); abrirEvento(); });
    if (btnEvtClose) btnEvtClose.addEventListener('click', cerrarEvento);
    if (btnEvtCancel) btnEvtCancel.addEventListener('click', cerrarEvento);
    if (evtBackdrop) evtBackdrop.addEventListener('click', cerrarEvento);
    if (btnEvtSave) btnEvtSave.addEventListener('click', (e) => { e.preventDefault(); guardarEvento(); });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && evtModal && !evtModal.hidden) cerrarEvento();
    });

    // ─── Gestos táctiles (swipe) ──────────────────────────────────────────────────
    let touchStartX = null;
    let touchStartY = null;
    let touchStartTime = 0;
    const SWIPE_UMBRAL = 60;
    const SWIPE_TIEMPO_MAX = 600;

    root.addEventListener('touchstart', (e) => {
        if (e.touches.length !== 1) { touchStartX = null; return; }
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
        touchStartTime = Date.now();
    }, { passive: true });

    root.addEventListener('touchend', (e) => {
        if (touchStartX === null) return;
        const t = e.changedTouches[0];
        const dx = t.clientX - touchStartX;
        const dy = t.clientY - touchStartY;
        const dt = Date.now() - touchStartTime;
        touchStartX = null;
        if (dt > SWIPE_TIEMPO_MAX) return;
        if (Math.abs(dx) < SWIPE_UMBRAL) return;
        if (Math.abs(dx) < Math.abs(dy) * 1.5) return;
        navegar(dx < 0 ? 1 : -1);
    }, { passive: true });

    // ─── Arranque ─────────────────────────────────────────────────────────────────
    // Sincronizar botón activo con la vista persistida
    document.querySelectorAll('.cal-vista-btn').forEach((btn) => {
        btn.classList.toggle('cal-vista-btn--active', btn.dataset.vista === vistaActual);
    });

    renderizar();
})();
