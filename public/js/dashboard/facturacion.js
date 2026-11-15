(() => {
    const formatCop = (valor) => '$' + Math.round(valor).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    const formatCorto = (valor) => {
        if (valor >= 1e6) return '$' + (valor / 1e6).toFixed(1).replace('.', ',') + 'M';
        if (valor >= 1e3) return '$' + Math.round(valor / 1e3) + 'k';
        return '$' + Math.round(valor);
    };

    const niceMax = (max) => {
        if (max <= 0) return 1;
        const escala = 10 ** Math.floor(Math.log10(max));
        const pasos = [1, 1.5, 2, 2.5, 3, 4, 5, 6, 8, 10];
        for (const paso of pasos) {
            if (paso * escala >= max) return paso * escala;
        }
        return 10 * escala;
    };

    const crearDiv = (clase) => {
        const div = document.createElement('div');
        div.className = clase;
        return div;
    };

    /* =========================================================
       PAGINADOR genérico (cliente): oculta/muestra filas ya
       renderizadas por el servidor, sin peticiones adicionales.
       ========================================================= */
    const crearPaginador = (id, tamanoInicial = 10) => {
        const envoltorio = document.querySelector(`[data-tabla-wrapper="${id}"]`);
        const pie = document.querySelector(`[data-paginar-pie="${id}"]`);
        if (!envoltorio || !pie) return null;

        const filas = Array.from(envoltorio.querySelectorAll('tbody tr'));
        const info = pie.querySelector('[data-paginar-info]');
        const nav = pie.querySelector('[data-paginar-nav]');
        const selectorTamano = pie.querySelector('[data-paginar-tamano]');

        let tamanoPagina = tamanoInicial;
        let paginaActual = 1;
        let predicado = () => true;

        const crearBoton = (etiqueta, { activo, deshabilitado, icono, onClick } = {}) => {
            const boton = document.createElement('button');
            boton.type = 'button';
            boton.className = 'factu-pager__btn' + (activo ? ' is-active' : '');
            boton.disabled = Boolean(deshabilitado);

            if (icono) {
                const i = document.createElement('i');
                i.className = icono;
                i.setAttribute('aria-hidden', 'true');
                boton.appendChild(i);
                boton.setAttribute('aria-label', etiqueta);
            } else {
                boton.textContent = etiqueta;
            }

            if (onClick && !deshabilitado) {
                boton.addEventListener('click', onClick);
            }

            return boton;
        };

        const render = () => {
            const activas = filas.filter(predicado);
            const total = activas.length;
            const totalPaginas = Math.max(1, Math.ceil(total / tamanoPagina));
            paginaActual = Math.min(Math.max(paginaActual, 1), totalPaginas);

            filas.forEach((fila) => { fila.hidden = true; });

            const inicio = (paginaActual - 1) * tamanoPagina;
            activas.slice(inicio, inicio + tamanoPagina).forEach((fila) => { fila.hidden = false; });

            if (info) {
                info.textContent = total === 0
                    ? 'Sin resultados'
                    : `Mostrando ${inicio + 1}–${Math.min(inicio + tamanoPagina, total)} de ${total}`;
            }

            if (nav) {
                nav.replaceChildren();

                if (totalPaginas > 1) {
                    nav.appendChild(crearBoton('Anterior', {
                        icono: 'fa-solid fa-chevron-left',
                        deshabilitado: paginaActual === 1,
                        onClick: () => { paginaActual--; render(); },
                    }));

                    const numeros = [];
                    for (let p = 1; p <= totalPaginas; p++) {
                        if (p === 1 || p === totalPaginas || Math.abs(p - paginaActual) <= 1) {
                            numeros.push(p);
                        } else if (numeros.at(-1) !== '…') {
                            numeros.push('…');
                        }
                    }

                    numeros.forEach((n) => {
                        if (n === '…') {
                            const puntos = document.createElement('span');
                            puntos.className = 'factu-pager__ellipsis';
                            puntos.textContent = '…';
                            nav.appendChild(puntos);
                        } else {
                            nav.appendChild(crearBoton(String(n), {
                                activo: n === paginaActual,
                                onClick: () => { paginaActual = n; render(); },
                            }));
                        }
                    });

                    nav.appendChild(crearBoton('Siguiente', {
                        icono: 'fa-solid fa-chevron-right',
                        deshabilitado: paginaActual === totalPaginas,
                        onClick: () => { paginaActual++; render(); },
                    }));
                }
            }
        };

        if (selectorTamano) {
            selectorTamano.value = String(tamanoPagina);
            selectorTamano.addEventListener('change', () => {
                tamanoPagina = parseInt(selectorTamano.value, 10) || 10;
                paginaActual = 1;
                render();
            });
        }

        render();

        return {
            setFiltro(nuevoPredicado) {
                predicado = nuevoPredicado;
                paginaActual = 1;
                render();
            },
        };
    };

    const renderStat = (contenedor, items) => {
        if (!contenedor) return;
        contenedor.replaceChildren();
        items.forEach(([etiqueta, valor]) => {
            const bloque = document.createElement('div');
            const small = document.createElement('small');
            small.textContent = etiqueta;
            const fuerte = document.createElement('b');
            fuerte.textContent = valor;
            bloque.appendChild(small);
            bloque.appendChild(fuerte);
            contenedor.appendChild(bloque);
        });
    };

    /* =========================================================
       GRÁFICO DE LÍNEA EN CSS + JAVASCRIPT (sin SVG ni canvas):
       el área, la línea, los puntos y el cursor son <div> posicionados
       con coordenadas calculadas en JS (mismo cálculo que un gráfico
       SVG, pero compuestos por el navegador como CSS normal).
       ========================================================= */
    const LineChart = (contenedor, opcionesIniciales) => {
        let o = {
            height: 220,
            color: '#2c4a7e',
            labels: [],
            values: [],
            ref: null,
            refLabel: '',
            tipLabel: () => '',
            aria: 'Gráfica',
            ...opcionesIniciales,
        };
        let anchoAnterior = 0;

        const dibujar = () => {
            contenedor.replaceChildren();
            contenedor.style.setProperty('--factu-chart-color', o.color);
            contenedor.setAttribute('role', 'img');
            contenedor.setAttribute('aria-label', o.aria);

            const ancho = Math.max(contenedor.clientWidth, 280);
            const alto = o.height;
            contenedor.style.height = alto + 'px';

            const margen = { arriba: 16, derecha: 16, abajo: 28, izquierda: 52 };
            const valores = o.values;
            const n = valores.length;
            const validos = valores
                .map((valor, indice) => [indice, valor])
                .filter(([, valor]) => valor !== null && valor !== undefined);

            if (!validos.length) return;

            const max = niceMax(Math.max(...validos.map(([, valor]) => valor), o.ref || 0) * 1.08);
            const x = (i) => margen.izquierda + (n === 1 ? 0 : (i * (ancho - margen.izquierda - margen.derecha)) / (n - 1));
            const y = (valor) => margen.arriba + (alto - margen.arriba - margen.abajo) * (1 - valor / max);
            const yBase = y(0);

            for (let k = 0; k <= 4; k++) {
                const valorLinea = (max * k) / 4;
                const yy = y(valorLinea);

                const linea = crearDiv('factu-lc-grid' + (k ? ' factu-lc-grid--dashed' : ''));
                linea.style.top = yy + 'px';
                contenedor.appendChild(linea);

                const etiquetaEje = crearDiv('factu-lc-axis-label');
                etiquetaEje.style.top = yy + 'px';
                etiquetaEje.style.width = (margen.izquierda - 10) + 'px';
                etiquetaEje.textContent = formatCorto(valorLinea);
                contenedor.appendChild(etiquetaEje);
            }

            const paso = Math.max(1, Math.ceil(n / (ancho < 480 ? 6 : 10)));
            o.labels.forEach((etiqueta, indice) => {
                if (indice % paso === 0 || indice === n - 1) {
                    const textoEje = crearDiv('factu-lc-xlabel');
                    textoEje.style.left = x(indice) + 'px';
                    textoEje.textContent = etiqueta;
                    contenedor.appendChild(textoEje);
                }
            });

            const puntosPoligono = validos.map(([indice, valor]) => `${x(indice).toFixed(1)}px ${y(valor).toFixed(1)}px`);
            const ultimo = validos.at(-1);
            const primero = validos[0];
            puntosPoligono.push(`${x(ultimo[0]).toFixed(1)}px ${yBase.toFixed(1)}px`);
            puntosPoligono.push(`${x(primero[0]).toFixed(1)}px ${yBase.toFixed(1)}px`);

            const area = crearDiv('factu-lc-area');
            area.style.clipPath = `polygon(${puntosPoligono.join(', ')})`;
            contenedor.appendChild(area);

            for (let k = 1; k < validos.length; k++) {
                const [i1, v1] = validos[k - 1];
                const [i2, v2] = validos[k];
                const x1 = x(i1);
                const y1 = y(v1);
                const dx = x(i2) - x1;
                const dy = y(v2) - y1;
                const longitud = Math.sqrt(dx * dx + dy * dy);
                const angulo = Math.atan2(dy, dx) * (180 / Math.PI);

                const segmento = crearDiv('factu-lc-segment');
                segmento.style.left = x1 + 'px';
                segmento.style.top = y1 + 'px';
                segmento.style.width = longitud + 'px';
                segmento.style.transform = `rotate(${angulo}deg)`;
                contenedor.appendChild(segmento);
            }

            if (o.ref !== null && o.ref !== undefined) {
                const yReferencia = y(o.ref);

                const lineaRef = crearDiv('factu-lc-ref');
                lineaRef.style.top = yReferencia + 'px';
                contenedor.appendChild(lineaRef);

                const badge = crearDiv('factu-lc-ref-badge');
                badge.style.left = (margen.izquierda + 6) + 'px';
                badge.style.top = (yReferencia - 9) + 'px';
                badge.textContent = o.refLabel;
                contenedor.appendChild(badge);
            }

            validos.forEach(([indice, valor]) => {
                const punto = crearDiv('factu-lc-point');
                punto.style.left = x(indice) + 'px';
                punto.style.top = y(valor) + 'px';
                contenedor.appendChild(punto);
            });

            const crosshair = crearDiv('factu-lc-crosshair');
            contenedor.appendChild(crosshair);

            const puntoHover = crearDiv('factu-lc-point--hover');
            contenedor.appendChild(puntoHover);

            const overlay = crearDiv('factu-lc-overlay');
            overlay.style.left = margen.izquierda + 'px';
            overlay.style.width = Math.max(ancho - margen.izquierda - margen.derecha, 1) + 'px';
            overlay.style.height = alto + 'px';
            contenedor.appendChild(overlay);

            const tooltip = crearDiv('factu-tooltip');
            contenedor.appendChild(tooltip);

            const mover = (evento) => {
                const rect = overlay.getBoundingClientRect();
                const clienteX = evento.touches ? evento.touches[0].clientX : evento.clientX;
                const mx = clienteX - rect.left + margen.izquierda;

                let mejor = validos[0];
                let distanciaMinima = Infinity;
                validos.forEach((punto) => {
                    const distancia = Math.abs(x(punto[0]) - mx);
                    if (distancia < distanciaMinima) {
                        distanciaMinima = distancia;
                        mejor = punto;
                    }
                });

                const [indice, valor] = mejor;
                crosshair.style.left = x(indice) + 'px';
                crosshair.style.opacity = '1';
                puntoHover.style.left = x(indice) + 'px';
                puntoHover.style.top = y(valor) + 'px';
                puntoHover.style.opacity = '1';

                tooltip.replaceChildren();
                const small = document.createElement('small');
                small.textContent = o.tipLabel(indice);
                const fuerte = document.createElement('strong');
                fuerte.textContent = formatCop(valor);
                tooltip.appendChild(small);
                tooltip.appendChild(fuerte);
                tooltip.style.left = Math.min(Math.max(x(indice), 70), ancho - 70) + 'px';
                tooltip.style.top = y(valor) + 'px';
                tooltip.style.opacity = '1';
            };

            const salir = () => {
                tooltip.style.opacity = '0';
                crosshair.style.opacity = '0';
                puntoHover.style.opacity = '0';
            };

            overlay.addEventListener('mousemove', mover);
            overlay.addEventListener('touchmove', mover, { passive: true });
            overlay.addEventListener('mouseleave', salir);
        };

        new ResizeObserver(() => {
            if (contenedor.clientWidth && contenedor.clientWidth !== anchoAnterior) {
                anchoAnterior = contenedor.clientWidth;
                dibujar();
            }
        }).observe(contenedor);

        return {
            update(nuevasOpciones) {
                o = { ...o, ...nuevasOpciones };
                dibujar();
            },
        };
    };

    document.addEventListener('DOMContentLoaded', () => {
        const datos = window.FacturacionDatos;
        const estilos = getComputedStyle(document.documentElement);
        const colorPrimario = estilos.getPropertyValue('--color-primary').trim() || '#2c4a7e';
        const colorInfo = estilos.getPropertyValue('--color-info').trim() || '#2f7ea1';

        /* ---------- Pestañas ---------- */
        const tabs = document.querySelectorAll('.factu-tab');
        const panes = document.querySelectorAll('[data-pane]');
        const campoTabPeriodo = document.querySelector('#form-periodo-facturacion input[name="tab"]');
        tabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                tabs.forEach((t) => t.classList.toggle('is-active', t === tab));
                panes.forEach((p) => p.classList.toggle('is-active', p.dataset.pane === tab.dataset.tab));
                if (campoTabPeriodo) campoTabPeriodo.value = tab.dataset.tab;
            });
        });

        /* ---------- Selector de mes ---------- */
        const selectorMes = document.getElementById('selector-mes-facturacion');
        if (selectorMes) {
            selectorMes.addEventListener('change', () => {
                selectorMes.form.submit();
            });
        }

        /* ---------- Modales ---------- */
        document.querySelectorAll('[data-open-modal]').forEach((boton) => {
            boton.addEventListener('click', () => {
                const modal = document.getElementById(boton.dataset.openModal);
                if (!modal) return;
                modal.hidden = false;
                modal.setAttribute('aria-hidden', 'false');
                const primerCampo = modal.querySelector('input, textarea');
                if (primerCampo) primerCampo.focus();
            });
        });

        const cerrarModales = () => {
            document.querySelectorAll('.modal[data-modal]').forEach((modal) => {
                if (!modal.hasAttribute('hidden')) {
                    modal.hidden = true;
                    modal.setAttribute('aria-hidden', 'true');
                }
            });
        };

        document.querySelectorAll('[data-modal-close]').forEach((boton) => {
            boton.addEventListener('click', cerrarModales);
        });

        document.addEventListener('keydown', (evento) => {
            if (evento.key === 'Escape') cerrarModales();
        });

        if (!datos) return;

        /* ---------- Gráfico: ingresos del mes (acumulado / diario) ---------- */
        const elChartMes = document.querySelector('[data-chart-mes]');
        const statsMes = document.querySelector('[data-stats-mes]');
        const segMes = document.querySelector('[data-seg-mes]');
        const legendRefMes = document.querySelector('[data-legend-ref-mes]');
        const chartMes = elChartMes ? LineChart(elChartMes, { color: colorPrimario, aria: 'Ingresos diarios del mes actual' }) : null;

        const actualizarChartMes = (modo) => {
            if (!chartMes) return;

            const diario = datos.serieDiariaCitas;
            const etiquetas = diario.map((punto) => punto.etiqueta);
            let acumulado = 0;
            const valores = diario.map((punto) => {
                if (modo === 'acumulado') {
                    acumulado += punto.total;
                    return acumulado;
                }
                return punto.total;
            });

            const total = datos.citasPagadasMes.total;
            const cantidad = datos.citasPagadasMes.count;
            const diasConIngresos = diario.filter((punto) => punto.total > 0).length;

            const ref = modo === 'acumulado'
                ? datos.citasPagadasMesAnterior.total
                : Math.round(total / Math.max(diasConIngresos, 1));
            const refLabel = modo === 'acumulado' ? ('Mes anterior: ' + formatCorto(ref)) : ('Prom. día: ' + formatCorto(ref));

            chartMes.update({
                labels: etiquetas,
                values: valores,
                ref,
                refLabel,
                tipLabel: (i) => etiquetas[i],
            });

            renderStat(statsMes, [
                ['Cobrado en citas', formatCop(total)],
                ['Citas pagadas', String(cantidad)],
                ['Ticket promedio', formatCop(cantidad > 0 ? total / cantidad : 0)],
            ]);

            if (legendRefMes) {
                legendRefMes.replaceChildren();
                const marca = document.createElement('i');
                marca.className = 'is-dash';
                legendRefMes.appendChild(marca);
                legendRefMes.appendChild(document.createTextNode(
                    modo === 'acumulado' ? 'Total mes anterior' : 'Promedio por día con ingresos',
                ));
            }
        };

        if (segMes) {
            segMes.querySelectorAll('.factu-seg__btn').forEach((boton) => {
                boton.addEventListener('click', () => {
                    segMes.querySelectorAll('.factu-seg__btn').forEach((b) => b.classList.toggle('is-active', b === boton));
                    actualizarChartMes(boton.dataset.value);
                });
            });
        }
        actualizarChartMes('diario');

        /* ---------- Gráfico: tendencia mensual (6 / 12 meses) ---------- */
        const elChartTendencia = document.querySelector('[data-chart-tendencia]');
        const statsTendencia = document.querySelector('[data-stats-tendencia]');
        const segTendencia = document.querySelector('[data-seg-tendencia]');
        const chartTendencia = elChartTendencia ? LineChart(elChartTendencia, { color: colorInfo, aria: 'Tendencia mensual de ingresos' }) : null;

        const actualizarChartTendencia = (rango) => {
            if (!chartTendencia) return;

            const n = parseInt(rango, 10) || 6;
            const serie = datos.serieMensualCitas.slice(-n);
            const etiquetas = serie.map((punto) => punto.etiqueta);
            const valores = serie.map((punto) => punto.total);
            const promedio = valores.reduce((suma, valor) => suma + valor, 0) / Math.max(valores.length, 1);
            const indiceMejor = valores.indexOf(Math.max(...valores));

            chartTendencia.update({
                labels: etiquetas,
                values: valores,
                ref: promedio,
                refLabel: 'Promedio: ' + formatCorto(promedio),
                tipLabel: (i) => etiquetas[i] + (i === serie.length - 1 ? ' (en curso)' : ''),
            });

            renderStat(statsTendencia, [
                [`Promedio ${n} meses`, formatCop(promedio)],
                ['Mejor mes', `${etiquetas[indiceMejor] || '—'} · ${formatCorto(valores[indiceMejor] || 0)}`],
            ]);
        };

        if (segTendencia) {
            segTendencia.querySelectorAll('.factu-seg__btn').forEach((boton) => {
                boton.addEventListener('click', () => {
                    segTendencia.querySelectorAll('.factu-seg__btn').forEach((b) => b.classList.toggle('is-active', b === boton));
                    actualizarChartTendencia(boton.dataset.value);
                });
            });
        }
        actualizarChartTendencia('6');

        /* ---------- Paginación de tablas ---------- */
        const paginadorMovimientos = crearPaginador('movimientos');
        crearPaginador('ingresos');
        crearPaginador('gastos');

        /* ---------- Filtro de "Últimos movimientos" ---------- */
        const segMovimientos = document.querySelector('[data-seg-movimientos]');

        if (segMovimientos && paginadorMovimientos) {
            segMovimientos.querySelectorAll('.factu-seg__btn').forEach((boton) => {
                boton.addEventListener('click', () => {
                    segMovimientos.querySelectorAll('.factu-seg__btn').forEach((b) => b.classList.toggle('is-active', b === boton));
                    const filtro = boton.dataset.value;
                    paginadorMovimientos.setFiltro((fila) => filtro === 'todos' || fila.dataset.dir === filtro);
                });
            });
        }
    });
})();
