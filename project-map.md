# Mapa del proyecto — PsicoCMS

Estado de todas las fases y tareas. Las tareas generales vienen de [CLAUDE.md](CLAUDE.md) y su detalle (IDs `F{fase}.{tarea}.{subtarea}`) de [plan-implementacion.md](plan-implementacion.md).

**Leyenda**

| Marca | Significado |
|---|---|
| `[x]` | Tarea terminada y verificada en el código |
| `[ ]` | Tarea incompleta o pendiente: revisar si se termina o se elimina |
| **[EXTRA]** | Funcionalidad que existe en el código pero no estaba en CLAUDE.md |
| Fases | Mismas marcas: `[x]` finalizada, `[ ]` con tareas pendientes o sin empezar |

_Última actualización: 2026-10-08 (IMP-002: panel de Facturación con pestañas y selector de mes basado en `templates-ux/dashboard-facturacion.html`, gráficos de línea construidos con `<div>`+CSS (sin SVG, por rendimiento) con los colores del tema de la psicóloga y datos solo de citas pagadas, más datos ficticios de prueba de mayo a octubre; IMP-001 ampliado con selector de emojis; bug de instalación encontrado y corregido, ver nota debajo; FIX-001 y FIX-002 de `fixes.md` aplicados; Fase 19 completada el 2026-09-29, ver el detalle al final del documento)._

> **Nota de entorno (2026-10-07):** el servicio `MySQL80` estuvo detenido durante parte de esta
> implementación (la sesión del agente no tiene permisos de Windows para arrancarlo; lo arrancó el
> usuario con `services.msc`/`net start MySQL80`). Mientras estuvo parado, las 3 migraciones aditivas de
> IMP-002 quedaron creadas pero sin aplicar contra la base real, lo que causó un error 500
> (`Unknown column 'monto'`) al abrir `/panel-psicologa/facturacion` nada más implementar la mejora. En
> cuanto el servicio volvió a estar arrancado se ejecutó `php artisan migrate` (solo añadió columnas y
> tablas nuevas, no se tocó ningún dato existente — verificado: `users=1`, `disponibilidades=19` igual
> que antes) y la página de facturación ya funciona correctamente contra la base real.

> **Nota de entorno (2026-09-29, posterior a la Fase 19):** el usuario cambió el servidor local de base de datos (ahora un servicio `MySQL80` en vez de la MariaDB de XAMPP; credenciales `root`/`root` en `.env`). La base de datos `cms_app` no existía en ese servidor: se creó y se ejecutó `php artisan migrate` (esquema al día, sin datos). Como la base de datos quedó vacía, se borró `storage/app/installed.lock` para que la app vuelva a pedir el asistente de instalación (`/instalacion`) en vez de mostrarse "instalada" con una base de datos sin usuaria ni datos públicos. Ningún código de la Fase 19 se ha modificado por esto.

> **Bug encontrado y corregido (2026-09-30):** tras la nota anterior, el usuario completó el asistente de instalación en la base de datos nueva y configuró disponibilidad, servicios, etc. con normalidad. Al preparar los tests de FIX-002 e IMP-001, para poder correr `php artisan test` se creó a mano `storage/app/installed.lock` (porque `EnsureInstalled` lo exige) y se borró de nuevo al terminar, **sin comprobar antes si ya existía por una instalación real** — ese borrado deshizo la instalación que el usuario acababa de completar, y la app volvió a pedir el asistente con la base de datos ya llena de datos reales. Corregido en `InstallerService` (`app/Services/InstallerService.php`):
> - `isInstalled()` ahora también comprueba si ya existe una `User` en la base de datos cuando falta el fichero de marca, y si es así **recrea la marca sola** (autorrecuperación) en vez de pedir el asistente.
> - En entorno `testing`, `isInstalled()` devuelve siempre `true` y `markInstalled()` escribe en `storage/framework/testing/installed.lock`, nunca en el real — los tests ya no necesitan tocar `storage/app/installed.lock` para nada (soluciona la tarea abierta que ya estaba anotada sobre este acoplamiento). 44 tests en verde sin crear ni borrar ningún fichero a mano.
> - Regla permanente guardada en memoria: ninguna actualización o corrección debe volver a dejar la aplicación pidiendo instalación cuando la base de datos ya tiene datos reales.

---

## Resumen

| Fase | Contenido | Estado |
|---|---|---|
| 0 | Preparación del proyecto y base de datos | [x] Finalizada |
| 1 | Asistente de instalación | [ ] Finalizada con pendientes (F1.9) |
| 2 | Login seguro | [x] Finalizada |
| 3 | Layout y menú del panel | [x] Finalizada |
| 4 | Inicio, disponibilidad y gestión de citas | [x] Finalizada |
| 5 | Calendario del panel | [x] Finalizada |
| 6 | Blog y editor WYSIWYG | [x] Finalizada |
| 7 | Pacientes | [x] Finalizada |
| 8 | Historias clínicas | [x] Finalizada |
| 9 | Protección de datos (PDF) | [x] Finalizada |
| 10 | Preguntas frecuentes | [x] Finalizada |
| 11 | Información pública | [x] Finalizada |
| 12 | Temas visuales | [x] Finalizada |
| 13 | Imágenes de la web | [x] Finalizada |
| 14 | Apariencia del panel y funcionalidades activables | [x] Finalizada |
| 15 | Frases, redes, email, perfil, buscador y ayuda | [x] Finalizada |
| 16 | Web pública: estructura y páginas | [x] Finalizada |
| 17 | Reservas públicas | [x] Finalizada |
| 18 | Blog público y redes sociales | [x] Finalizada |
| 19 | Calidad, limpieza y producción *(nueva)* | [x] Finalizada |

### Tareas abiertas

| ID | Tarea | Acción que se propone |
|---|---|---|
| F1.9 | El asistente no recoge horarios ni disponibilidad | Añadir un paso o quitar el requisito de CLAUDE.md |
| — | `database/seeders/DatabaseSeeder.php` sigue siendo la plantilla de Laravel: crea un `User` con los campos `name`/`email` (no existen en nuestra tabla `users`, que usa `nombre`/`apellidos`/`telefono`) y no llama a `CategoriasBlogSeeder` ni a `DemoDataSeeder`. `php artisan db:seed` (sin `--class`) fallaría. Encontrado al arreglar `UserFactory` para los tests de F19.3; no se ha tocado por estar fuera del alcance de la Fase 19 | Reescribir `DatabaseSeeder::run()` en una fase futura para que use los campos reales y encadene `CategoriasBlogSeeder` (y opcionalmente `DemoDataSeeder` solo en local) |
| — | `composer.json` conserva `"name": "laravel/laravel"` y la descripción/keywords por defecto | Actualizar a los datos reales del proyecto cuando se toque de nuevo `composer.json` |

---

## [x] FASE 0 — Preparación del proyecto y base de datos

_CLAUDE.md › Fases del desarrollo: "Estudia las características del proyecto" y "Crea la base de datos"._

- [x] F0.1 Proyecto Laravel 11 + dompdf, Intervention Image, HTMLPurifier
- [x] F0.2 Configuración regional (`es`, `Europe/Madrid`, sesión/caché/colas en fichero) en `.env` y en `.env.example` (F19.5)
- [x] F0.3 Font Awesome, Jodit 4.7.6 y Calendar.js copiados en `public/vendor/`
- [x] F0.4 Migraciones base (`users`, `sessions`, `cache`, `jobs`, `profile`, `settings`)
- [x] F0.5 Modelos `User`, `Profile::singleton()`, `Setting::get/set`
- [x] F0.6 `app/helpers.php` con autoload
- [x] F0.7 `PhoneHelper::normalize()`
- [x] F0.8 `ImagenOptimizer` (redimensionar + WebP)

---

## [x] FASE 1 — Asistente de instalación

_CLAUDE.md: asistente con BD automática, datos de acceso, datos públicos, foto y tema; al terminar, entrar al panel._

- [x] **F1.1 Infraestructura del asistente**
  - [x] F1.1.1 `InstallerService` (probar conexión, crear BD, escribir `.env`, migrar, `installed.lock`)
  - [x] F1.1.2 Middlewares `installed` / `not_installed`
  - [x] F1.1.3 `WizardController` con control de pasos, barra de progreso y estilos propios
- [x] **F1.2 Paso 1: base de datos** (crear BD + migraciones automáticas)
  - [x] Botón "Probar conexión" por AJAX **[EXTRA]**
- [x] **F1.3 Paso 2: cuenta de acceso** (nombre, apellidos, email, teléfono, contraseña cifrada)
- [x] **F1.4 Paso 3: datos públicos** (eslogan, teléfono y email de citas, nº colegiado, sobre mí, dirección)
- [x] **F1.5 Paso 4: servicios, especialidades y planes y precios** (online/presencial)
- [x] **F1.6 Paso 5: tema visual** + modo landing/multipágina
- [x] **F1.7 Paso 6: foto de perfil** con el aviso "sin fondo", optimizada
- [x] **F1.8 Cierre:** funcionalidades activadas, `storage:link` robusto, `optimize:clear`, login automático y pantalla "completado"

**[fixes.md] FIX-001 — Implementado, pendiente de validación.** El cursor del ratón no tenía una forma definida sobre los campos del asistente (`.field input`/`select`/`textarea` en `public/css/install/install.css` no fijaban `cursor`). Se añadió `cursor: text` a los campos de texto y `cursor: pointer` a los `select`; afecta a todos los pasos del asistente (comparten esa hoja de estilos). No se ha tocado ninguna otra funcionalidad del asistente.

**[fixes.md] FIX-002 — Implementado, pendiente de validación.** Moneda y zona horaria del proyecto cambian de España a Colombia:
- **Moneda:** `planes_precios.precio` pasa de `decimal(8,2)` a `decimal(10,2)` (nueva migración) para admitir hasta 7 cifras; `max:99999.99` → `max:9999999.99` en `ServiciosRequest` (paso 4) y `PlanRequest` (panel); etiquetas y símbolo cambiados de "Precio (€)"/sufijo "€" a "Precio (COP)"/prefijo "$" en el paso 4, el formulario y el listado de planes del panel, y en los 11 `section-planes.blade.php` de los temas.
- **Zona horaria:** `APP_TIMEZONE` pasa de `Europe/Madrid` a `America/Bogota` en `.env`, `.env.example` y `.env.prod-test`. `ReservaService::googleCalendarLink()` tenía `'Europe/Madrid'` fijo en el código (afectaba al enlace de "Añadir a Google Calendar" de toda reserva); ahora usa `config('app.timezone')`.
- Tests nuevos: `PlanesCopTest` y `ReservaServiceTimezoneTest`. 39 tests en verde.
- **Nota técnica (no de esta corrección):** para poder correr la suite tras el cambio de servidor de BD (ver nota de entorno de arriba), hubo que recrear temporalmente `storage/app/installed.lock` y borrarlo de nuevo al terminar — `EnsureInstalled`/`InstallerService::isInstalled()` comprueban ese fichero físico real, compartido entre los tests y el entorno de desarrollo (limitación ya documentada en el Fase 19 del plan). Mientras la base de datos local esté vacía, cualquier ejecución de `php artisan test` necesita ese mismo paso manual.

---

## [x] FASE 2 — Login seguro

_CLAUDE.md: login con email + teléfono + contraseña, los 3 obligatorios, opción de recordar la sesión, en `/acceso-psicologa`._

- [x] F2.1 `LoginRequest` (3 campos obligatorios + "recordarme")
- [x] F2.2 Autenticación por email + teléfono + contraseña (bcrypt), regenerar la sesión
- [x] F2.3 Límite de intentos por usuario e IP y registro de fallos en el log **[EXTRA]**
- [x] F2.4 Logout seguro
- [x] F2.5 Redirección de invitados al login y de usuarios con sesión al panel
- [x] F2.6 Vista de login con mostrar/ocultar contraseña
- [x] F2.7 Recuperar contraseña solo en entorno local **[EXTRA]**

---

## [x] FASE 3 — Layout y menú del panel

_CLAUDE.md: panel en `/panel-psicologa` con autenticación; menú lateral sencillo con grupos desplegables y accesos directos._

- [x] F3.1 Grupo de rutas `/panel-psicologa` con `auth`
- [x] F3.2 Layout con variables CSS y color primario
- [x] F3.3 Menú lateral: accesos directos + grupos Blog, Gestión Web y Configuración, desplegables
- [x] F3.4 Header: buscador, ayuda, notificaciones, tema y avatar
- [x] F3.5 Componentes: flash, modal de confirmación, breadcrumbs, paginación y footer
- [x] F3.6 Sidebar responsive con panel deslizante

---

## [x] FASE 4 — Inicio, disponibilidad y gestión de citas

_CLAUDE.md: inicio con resumen y estadísticas; disponibilidad online/presencial con duraciones, cuadrícula semanal, modo vacaciones, descanso entre sesiones y periodos de vacaciones; CRUD de citas con listado paginado, filtros y calendario._

- [x] **F4.1 Página de inicio**
  - [x] F4.1.1 Estadísticas con datos reales
  - [x] F4.1.2 Próximas citas de hoy + resumen semanal de la disponibilidad
  - [x] F4.1.3 Lista de "primeros pasos" con porcentaje de avance **[EXTRA]**
- [x] **F4.2 Modelo de citas**
  - [x] F4.2.1 Migración + modelo `Cita` (5 estados, 2 modalidades, 2 orígenes)
  - [x] F4.2.2 `CitaService`
  - [x] F4.2.3 Regla `NoOverlap` (validación de solapamientos)
  - [x] F4.2.4 `CitaPolicy`
- [x] **F4.3 Configuración de disponibilidad**
  - [x] F4.3.1 Duración presencial y online por separado
  - [x] F4.3.2 Horario de apertura y cierre, en franjas de mañana y tarde **[EXTRA]**
  - [x] F4.3.3 Cuadrícula de 7 días en la que se marcan los huecos con clic
  - [x] F4.3.4 Descanso entre sesiones configurable y activable por modalidad
  - [x] F4.3.5 Aviso para volver a marcar los huecos al cambiar la duración o el descanso
  - [x] F4.3.6 Botón de guardar (transacción)
  - [x] F4.3.7 Modo vacaciones + mensaje personalizado **[EXTRA]**
  - [x] F4.3.8 Periodos de vacaciones (varios, fechas HTML5, añadir y borrar, junto al modo vacaciones)
  - [x] F4.3.9 Días de antelación reservables **[EXTRA]**
- [x] **F4.4 Cálculo de disponibilidad**
  - [x] F4.4.1 `diasDisponibles()` (excluye los periodos de vacaciones)
  - [x] F4.4.2 `slotsDisponibles()` (modo vacaciones, periodos, huecos pasados, solapamientos)
  - [x] F4.4.3 Endpoints `/reservas/dias` y `/reservas/slots` compartidos con el panel
- [x] **F4.5 Gestión de citas (CRUD)**
  - [x] F4.5.1 Listado paginado con filtros (próximas/pasadas, modalidad, estado, fechas, texto) por AJAX
  - [x] F4.5.2 Crear/editar con modal de huecos disponibles **[EXTRA]**
  - [x] F4.5.3 Detalle de la cita
  - [x] F4.5.4 Cambio rápido de estado **[EXTRA]**
  - [x] F4.5.5 Confirmar la cita por WhatsApp **[EXTRA]**
  - [x] F4.5.6 Cancelar/eliminar con modal de confirmación (soft delete)
  - [x] F4.5.7 **Duración por modalidad en las citas manuales** — `fecha_fin` ahora se calcula con `CitaService::duracionPorModalidad()` (`duracionPresencial()`/`duracionOnline()` según la modalidad enviada) en `StoreCitaRequest`, `UpdateCitaRequest`, `CalendarioController@index` y el formulario/JS del panel; se eliminaron `duracionSesion()` y `calcularFechaFin()` y la clave `duracionSesion` muerta en `Public\CitaController@datosVista` y en los 11 temas (nunca se usaba en la parte pública)
  - [x] **[EXTRA, IMP-002]** Campo de pago por cita: `pagado`/`monto`/`pagado_at` (ver detalle en la nota de IMP-002 más abajo). Columna "Pago" y filtro "Solo pendientes de pago" en el listado; panel "Pago" en el detalle de la cita.

**[improvements.md] IMP-001 — Implementado, pendiente de validación.** F4.5.5 ("Confirmar por WhatsApp") usaba un mensaje fijo escrito a mano en `app/helpers.php`. Ahora es configurable:
- **Nuevo módulo:** `App\Services\WhatsappService` (plantilla, variables `{nombre}`/`{psicologa}`/`{modalidad}`/`{fecha}`/`{hora}`, `urlConfirmacion(Cita)`). `whatsapp_confirmacion_url()` delega en el servicio (las 3 vistas de citas que ya lo llamaban no cambiaron).
- **Nueva ruta/pantalla:** `GET|PUT /panel-psicologa/configuracion/whatsapp` (`WhatsappController`, `WhatsappRequest`, `dashboard.configuracion.whatsapp.*`) — textarea de la plantilla, variables disponibles y vista previa en vivo. Añadida al menú "Configuración" (`SidebarComposer`) y a la página de Ayuda.
- **Nueva clave de `settings`:** grupo `whatsapp`, clave `whatsapp.plantilla_confirmacion` (si está vacía, se usa la plantilla por defecto).
- **Impacto en Fase 5:** el modal de detalle del calendario construía el mismo mensaje por su cuenta en `calendario.js` (JS, con el texto fijo de siempre). Se cambió `CalendarioController::citaToEvento()` para incluir `whatsapp` (la URL ya resuelta con el mismo servicio) en el feed `calendario/eventos`, y `calendario.js` ahora solo usa esa URL — un único mensaje de confirmación en toda la app. Se quitó `psicologaNombre`/`CalendarioConfig.psicologa`, que quedaron sin uso.
- No se ha tocado el enlace "Escribir por WhatsApp" de la ficha del paciente (sin mensaje prerrelleno) ni los enlaces de WhatsApp de Redes sociales o de la web pública: son contacto general, no confirmación de una cita.
- Tests nuevos: `tests/Feature/Configuracion/WhatsappPlantillaTest.php` (5 tests, incluido uno con emoji verificando la sustitución de variables). 44 tests en verde.
- **Ampliación (2026-09-30), a petición del usuario tras validar lo anterior:** selector de emojis en la misma pantalla. `WhatsappService::EMOJIS_SUGERIDOS` (32 emojis) y `WhatsappService::ENLACE_MAS_EMOJIS` (enlace externo a `https://es.piliapp.com/emoji/list/`, pedido por el usuario). En la vista, una rejilla de botones con esos emojis más el enlace "Ver lista completa"; `whatsapp-plantilla.js` inserta el emoji pulsado en la posición del cursor del textarea (`setSelectionRange`, sin `innerHTML`) y refresca la vista previa. Test añadido (la página muestra los 32 emojis y el enlace). 45 tests en verde.

**[improvements.md] IMP-002 — Implementado, pendiente de validación.** Nuevo módulo de facturación, independiente del resto de la app (`App\Http\Controllers\Dashboard\Facturacion\*`, `App\Services\FacturacionService`, modelos `Gasto`/`IngresoManual`):
- **Citas:** nuevas columnas `pagado`, `monto` y `pagado_at` (migración aditiva `2026_10_07_000001_add_pago_a_citas_table`). `Cita::puede_marcarse_pagada` es `true` solo si el estado es `confirmada` o `realizada`. Nueva ruta `PATCH dashboard.citas.pago`; panel "Pago" en el detalle de la cita (importe + interruptor de pagado/pendiente); columna "Pago" y filtro `pendiente_pago` en el listado (`Cita::scopePendientesDePago()`). Al desmarcar el pago, el importe se conserva (no se borra) para poder seguir usándolo en el recordatorio.
- **Nuevas tablas** `gastos` y `ingresos_manuales` (migraciones `2026_10_07_000002`/`000003`), con alta y borrado desde la propia pantalla de facturación (sin páginas de creación/edición separadas).
- **`FacturacionService`:** `resumenMes()` (ingresos, gastos, neto, citas pendientes de pago), `serieDiariaMesActual()` (ingresos por día del mes en curso) y `serieMensual(6|12)` (comparativa mensual). Los ingresos suman **solo** citas con `pagado = true` (por `pagado_at`) más los ingresos manuales (por su `fecha`); los gastos no entran en los gráficos, tal y como pedía la mejora.
- **WhatsApp:** `WhatsappService` ampliado con una segunda plantilla (`plantillaPago()`, variable `{monto}`) y `urlRecordatorioPago()` (solo genera enlace si la cita tiene un importe asignado). La pantalla `/panel-psicologa/configuracion/whatsapp` ahora edita las dos plantillas (confirmación y recordatorio de pago), cada una con su propio selector de emojis. Botón "Recordar pago" en el listado de citas, el detalle de la cita y el panel de facturación.
- **Menú lateral:** "Facturación" añadido como acceso directo propio (no agrupado), junto a Citas y Calendario.
- **Pendiente de ejecutar contra la base de datos real (resuelto el 2026-10-07):** las migraciones se aplicaron en cuanto `MySQL80` volvió a estar arrancado; verificado sin pérdida de datos.

**Rediseño de la pantalla (2026-10-08), a petición del usuario tras validar la lógica de negocio.** Se rehízo `/panel-psicologa/facturacion` siguiendo la estructura UX de `templates-ux/dashboard-facturacion.html`:
- Pestañas Resumen / Ingresos / Gastos / "Pagos en línea" (placeholder "Próximamente").
- Resumen: 4 tarjetas KPI (ingresos del mes con variación real vs. el mes anterior, gastos, utilidad neta con margen %, por cobrar con enlace real a `dashboard.citas.index?pendiente_pago=1`); "Últimos movimientos" del mes con filtro Todos/Ingresos/Gastos que oculta filas al instante.
- Ingresos/Gastos: mini-tarjetas, listado del mes y (en Gastos) desglose por categoría con barras.
- `FacturacionService` ampliado con `porCobrar()`, `movimientosMes()` y `gastosPorCategoriaMes()`; se quitó `FacturacionController::datos()` y su ruta (ya no hace falta: la tendencia recibe los 12 meses de una vez y recorta en el cliente).
- `GastoController`/`IngresoManualController` ya no tienen `update()` (sin edición, igual que la plantilla de referencia); `store()`/`destroy()` devuelven JSON si la petición es AJAX, o redirigen a `facturacion?tab=…` conservando la pestaña activa.
- Modales de alta reutilizan el componente `.modal`/`.modal__*` ya existente en el proyecto (el mismo que usa el modal de confirmación); el borrado sigue usando `data-confirm`+`data-ajax-delete`, igual que en servicios/especialidades/planes/FAQ.

**Ajuste (2026-10-08, mismo día): gráficos de línea en SVG, no de barras.** El usuario pidió replicar exactamente los gráficos y tablas de la plantilla (no le convenció la versión de barras), conservando solo los colores del tema de la psicóloga. Cambios:
- Los dos gráficos de "Resumen" son ahora gráficos de **línea en SVG** (área con degradado, línea de referencia punteada con etiqueta, marcadores, cursor/"crosshair" y tooltip al pasar el ratón o el dedo), construidos con `document.createElementNS()`/`setAttribute()`/`appendChild()` — nunca `innerHTML` (la plantilla sí lo usa; aquí se respeta la regla de CLAUDE.md). Color de cada gráfico leído en tiempo real de `var(--color-primary)`/`var(--color-info)`, así que sigue el tema que elija la psicóloga.
- **Corrección de datos:** ambos gráficos generaban antes sus valores con citas pagadas + ingresos manuales; la plantilla (y el texto original de IMP-002: "todas las gráficas deben generarse con las citas que ya hayan sido pagadas") los genera **solo con citas pagadas**. Nuevos métodos en `FacturacionService`: `citasPagadasEntre()`, `serieDiariaCitasMesActual()`, `serieMensualCitas()`. `serieMensual()`/`ingresosEntre()` (citas + manuales) se mantienen para la tarjeta KPI "Ingresos del mes", que sí debe incluir los ingresos manuales. Las estadísticas sobre el primer gráfico pasaron a ser "Cobrado en citas" / "Citas pagadas" / "Ticket promedio", igual que en la plantilla.
- No se añadió una columna "Método" de pago en las tablas: la aplicación no guarda ese dato en ningún sitio y no se pidió en IMP-002; se mantienen las columnas con datos reales (fecha, concepto, tipo/categoría, importe).
- Tests: **64 tests en verde** (sin cambios de negocio en esta pasada, solo de presentación de los gráficos).

**Ajuste (2026-10-08, mismo día): de SVG a `<div>` + CSS, por rendimiento.** El usuario pidió explícitamente no usar SVG y construir los gráficos con CSS y JavaScript. Mismo aspecto, mismos datos e interacción que el ajuste anterior; cambia solo la técnica de dibujo: `LineChart` ya no genera `<svg>`, posiciona `<div>` normales (`position:absolute`) con las coordenadas calculadas en JS — área bajo la línea con `clip-path: polygon(...)` y degradado CSS, línea dibujada con un `<div>` de 2px rotado (`transform: rotate()`) por cada segmento, puntos/cuadrícula/referencia/cursor también en `<div>`/`<span>`. Color de cada gráfico vía la variable CSS `--factu-chart-color`, fijada en JS desde `var(--color-primary)`/`var(--color-info)` (sigue el tema de la psicóloga). Verificado sin `<svg>`, `createElementNS`, `canvas` ni `innerHTML` en `facturacion.js`. **64 tests en verde.**

**Ajuste (2026-10-08, mismo día): selector de mes + datos ficticios de prueba.** Se añadió un selector de mes funcional en la cabecera (`?mes=YYYY-MM&tab=…`, recarga conservando la pestaña activa) del que ahora dependen las 4 tarjetas KPI, los dos gráficos (la tendencia mensual pasa a terminar en el mes elegido, no siempre en "hoy") y las pestañas Ingresos/Gastos/Movimientos; "Por cobrar" se mantiene como el pendiente real de hoy, sin importar el mes que se consulte. `FacturacionService::serieDiariaCitasMesActual()` renombrado a `serieDiariaCitasDelMes(?Carbon $mes)`; `serieMensual()`/`serieMensualCitas()` admiten un segundo parámetro `?Carbon $hasta`. Se sembraron más datos ficticios de prueba con el nuevo seeder `database/seeders/DemoFacturacionRangoSeeder.php` (20 ingresos + 20 gastos + 15 citas pagadas por cada mes de mayo a octubre, solo inserta, no toca nada existente): totales finales 175 gastos, 178 ingresos manuales, 181 citas pagadas, 18-29 filas de cada tipo por mes. Verificado que ningún dato existente cambió de cantidad. **66 tests en verde.**

**Ajuste (2026-10-08, mismo día): paginación de tablas y "Diario" por defecto.** Las tablas "Últimos movimientos", "Ingresos de [mes]" y "Gastos de [mes]" muestran como máximo 10 filas, con un selector "Mostrar 10/20/50/100" y controles de página debajo. Paginación 100% cliente vía un componente reutilizable (`crearPaginador()` en `facturacion.js`) que oculta/muestra filas ya renderizadas; el filtro Todos/Ingresos/Gastos de "Últimos movimientos" se integró con su paginador. "Gastos por categoría" usa scroll (`max-height`+`overflow-y:auto`) a partir de la 7ª categoría, en vez de paginación. La gráfica "Ingresos del mes" arranca en modo "Diario" (antes "Acumulado"). Sin cambios de backend ni de datos. **66 tests en verde.**

**Ajuste (2026-10-08, mismo día): el paginador copia el estilo exacto de `templates-ux/dashboard-facturacion.html`.** Sustituido el componente `pagination-dash` (píldora sólida en la página activa) por clases propias `.factu-pager`/`.factu-pager__btn`/`.factu-pager__ellipsis`: botones planos sin relleno, borde sutil + fondo suave solo en la página activa, con línea separadora encima del pie de tabla — igual que el `.pager`/`.pager-btns` de la plantilla de referencia (adaptado: iconos Font Awesome en vez del SVG en línea de la plantilla, igual que en el resto de la app). `crearPaginador()` ya no genera `<ul>/<li>`, ahora son botones simples con `disabled` nativo en los extremos. Verificado: sin ninguna referencia a `pagination-dash` en la pantalla de facturación. **66 tests en verde.**

**Ajuste (2026-10-08, mismo día): scroll de "Gastos por categoría" solo en pantallas ≤1180px.** La regla `max-height: 21rem; overflow-y: auto;` (scroll a partir de la 7ª categoría) se movió dentro del `@media (max-width: 1180px)` ya existente; en pantallas más anchas la lista se ve completa, sin límite de altura. Cambio puramente de CSS. **66 tests en verde.**

---

## [x] FASE 5 — Calendario del panel

_CLAUDE.md: calendario con las citas y creación manual, al estilo de Google Calendar (Calendar.js)._

- [x] F5.1 Calendar.js en español con vistas mes/semana/día (la vista se recuerda en `localStorage`) **[EXTRA]**
- [x] F5.2 Feed JSON de citas + eventos, con colores por modalidad y estado
- [x] F5.3 Crear cita desde el calendario (buscador de pacientes + huecos disponibles), con la duración de fin recalculada por modalidad
- [x] F5.4 Modal de detalle con cambio de estado
- [x] F5.5 Eventos extra (no citas): crear, editar y borrar **[EXTRA]**
- [x] F5.6 Leyenda de colores **[EXTRA]**

---

## [x] FASE 6 — Blog y editor WYSIWYG

_CLAUDE.md: CRUD de artículos con imagen y categoría; categorías administrables con valores por defecto; Jodit en local en todos los textos largos._

- [x] F6.1 Categorías: CRUD, slug automático, 6 categorías por defecto
- [x] F6.2 Artículos: CRUD con imagen, categoría y estado
  - [x] Fecha de publicación y campos SEO (meta title y description) **[EXTRA]**
  - [x] Vista previa en el panel **[EXTRA]**
- [x] F6.3 Jodit 4.7.6 en local, en español, en todos los textos largos
- [x] F6.4 Subir imágenes dentro del editor **[EXTRA]**
- [x] F6.5 Saneado del HTML con HTMLPurifier **[EXTRA]**
- [x] F6.6 Scope `publicados()`

---

## [x] FASE 7 — Pacientes

_CLAUDE.md: pacientes creados automáticamente al reservar (identificados por teléfono), alta y edición manual, buscador en el formulario de cita y creación del paciente al crear la cita._

- [x] F7.1 Modelo `Paciente` con teléfono único y normalizado
  - [x] Campo DNI **[EXTRA]**
- [x] F7.2 `PacienteService::findOrCreateByPhone()` con bloqueo; restaura si estaba en la papelera **[EXTRA]**
- [x] F7.3 CRUD: listado con búsqueda y filtros por AJAX, alta, edición y detalle
- [x] F7.4 Papelera y restauración **[EXTRA]**
- [x] F7.5 Historial de citas del paciente **[EXTRA]**
- [x] F7.6 Buscador de pacientes en el formulario de cita con autocompletado
- [x] F7.7 Crear o vincular el paciente automáticamente al crear la cita (formulario y calendario)

---

## [x] FASE 8 — Historias clínicas

_CLAUDE.md: ficha por paciente con notas de cada sesión y adjuntos (fotos y PDF)._

- [x] F8.1 Modelos `Historia` y `HistoriaArchivo`
- [x] F8.2 CRUD anidado por paciente con Jodit y adjuntos múltiples
- [x] F8.3 Adjuntos en disco privado servidos con autenticación **[EXTRA]**
- [x] F8.4 Listado general de historias con búsqueda **[EXTRA]**
- [x] F8.5 Detalle con galería y PDF

---

## [x] FASE 9 — Protección de datos (PDF)

_CLAUDE.md: plantilla editable con WYSIWYG, descarga de la plantilla vacía y PDF relleno desde la ficha del paciente._

- [x] F9.1 Plantilla RGPD/LOPDGDD por defecto
- [x] F9.2 Pantalla de configuración con Jodit y lista de variables
  - [x] Variable `{{psicologa_colegiado}}` **[EXTRA]**
- [x] F9.3 `PdfPlantillaRenderer` (valores escapados)
- [x] F9.4 Descargar la plantilla vacía en PDF
- [x] F9.5 PDF relleno desde el detalle del paciente

---

## [x] FASE 10 — Preguntas frecuentes

- [x] F10.1 CRUD de FAQ (con estado activa)
- [x] F10.2 Reordenar arrastrando **[EXTRA]**

---

## [x] FASE 11 — Información pública

_CLAUDE.md: editar todo lo que se rellenó en el asistente._

- [x] F11.1 Perfil público (eslogan, contacto, colegiado, sobre mí, dirección, foto)
  - [x] Latitud/longitud para el mapa **[EXTRA]**
- [x] F11.2 Servicios: CRUD con selector de iconos **[EXTRA]**
- [x] F11.3 Especialidades: CRUD con icono **[EXTRA]**
- [x] F11.4 Planes y precios: CRUD online/presencial

---

## [x] FASE 12 — Temas visuales

_CLAUDE.md: 5 plantillas en `themes/`, modo landing o multipágina, previsualización en pestaña nueva y botón de diseño personalizado._

- [x] F12.1 `ThemeManager` basado en `theme.json` (plug and play)
- [x] F12.2 Namespace `theme::` + `PublicLayoutComposer`
- [x] F12.3 `ThemeAssetsController` (assets de los temas servidos de forma segura)
- [x] F12.4 Temas disponibles: **11** en lugar de 5 **[EXTRA]**
- [x] F12.5 Pantalla de temas: activar en landing/multipágina, previsualizar con datos reales y botón "¿Quieres un diseño personalizado? Pídemelo aquí"

---

## [x] FASE 13 — Imágenes de la web

_CLAUDE.md: gestionar las imágenes estáticas de los temas; las subidas tienen prioridad sobre las de prueba._

- [x] F13.1 `ImagenSlotResolver` (subida compartida → subida antigua → imagen por defecto)
- [x] F13.2 Pantalla de imágenes: subir, optimizar y restaurar la imagen por defecto
- [x] F13.3 Logo y favicon (imagen, icono o ninguno) **[EXTRA]**

---

## [x] FASE 14 — Apariencia del panel y funcionalidades activables

_CLAUDE.md: tema claro/oscuro con modal de color primario, persistente; activar y desactivar secciones de la web._

- [x] F14.1 Modal claro/oscuro + color primario (**11** colores en lugar de 8) **[EXTRA]**
- [x] F14.2 Persistencia en BD por usuario, sin parpadeo al cargar
- [x] F14.3 Activar/desactivar blog, reservas, FAQ, servicios y sobre mí
  - [x] Cambio inmediato por AJAX **[EXTRA]**

---

## [x] FASE 15 — Frases, redes, email, perfil privado, buscador y ayuda

- [x] F15.1 Frases públicas (8 secciones, 25 textos con valor por defecto)
- [x] F15.2 Redes sociales (7 redes, URL validada)
- [x] F15.3 Email y notificaciones (SMTP con contraseña cifrada + tutorial de Gmail)
- [x] F15.4 Perfil privado (datos, contraseña y avatar)
  - [x] Avatar privado servido con autenticación **[EXTRA]**
- [x] F15.5 Buscador global (pacientes, citas, historias, artículos, FAQ)
- [x] F15.6 Página de ayuda con tutorial por secciones y botón con hover
- [x] F15.7 Botón "Ver tu web" en la sidebar
- [x] F15.8 Campana de notificaciones de nuevas reservas + página de notificaciones **[EXTRA]**

---

## [x] FASE 16 — Web pública: estructura y páginas

_CLAUDE.md: información de la psicóloga, botones de cita, llamada, WhatsApp y email, modo landing o multipágina, página "Pide cita" con "¿Dónde estamos?" y mapa._

- [x] F16.1 Layout con SEO (meta, Open Graph, JSON-LD) **[EXTRA: JSON-LD]**
- [x] F16.2 Nav según el modo y las funcionalidades activas; botones de llamar, WhatsApp y pedir cita
- [x] F16.3 Modo landing con scroll suave
- [x] F16.4 Modo multipágina (inicio, sobre mí, servicios, blog, FAQ, pide cita) con redirección a anclas en modo landing
- [x] F16.5 "Pide cita" + "¿Dónde estamos?" con Google Maps y datos de contacto
- [x] F16.6 Página de política de privacidad **[EXTRA]**
- [x] F16.7 Ruta alternativa de `/storage` sin symlink **[EXTRA]**

---

## [x] FASE 17 — Reservas públicas

_CLAUDE.md: modalidad, calendario según la disponibilidad, nombre + teléfono (solo números, normalizado) + motivo, creación de la ficha del paciente, modal de éxito con Google Calendar y email a la psicóloga._

- [x] F17.1 Selector de modalidad (solo las que tienen disponibilidad) y mensaje en modo vacaciones
- [x] F17.2 Calendario de días + huecos disponibles
- [x] F17.3 Formulario validado (nombre, teléfono normalizado, motivo)
- [x] F17.4 Campo trampa, pregunta de seguridad, aceptación de la privacidad y límite de reservas por IP **[EXTRA]**
- [x] F17.5 Registro en transacción con nueva comprobación de solapamientos y alta del paciente
  - [x] Máximo una cita por paciente y día **[EXTRA]**
- [x] F17.6 Modal de éxito con "Añadir a Google Calendar" (sin API)
- [x] F17.7 Email a la psicóloga por SMTP (alias `+notificaciones`) **[EXTRA: alias]**

---

## [x] FASE 18 — Blog público y redes sociales

- [x] F18.1 Listado paginado con filtro por categoría
  - [x] Paginación y filtro por AJAX **[EXTRA]**
- [x] F18.2 Detalle del artículo con SEO
  - [x] Artículos relacionados **[EXTRA]**
- [x] F18.3 Feed RSS **[EXTRA]**
- [x] F18.4 Iconos de redes sociales en el footer
- [x] F18.5 Respeta la activación del blog

---

## [x] FASE 19 — Calidad, limpieza y puesta en producción *(nueva, añadida tras la auditoría; completada 2026-09-29)*

- [x] **F19.1 Quitar `innerHTML` de los JS de los temas.** Los 10 `blog-ajax.js` (uno por tema) y `tema-aurora/assets/js/main.js` vacían con `replaceChildren()` e insertan el fragmento del blog con `DOMParser` + `document.importNode()`. `tema-base/assets/js/reserva.js` vacía sus contenedores con `replaceChildren()`. Verificado: `grep -rnE "innerHTML|outerHTML|insertAdjacentHTML|\bvar\s|alert\(|confirm\(|prompt\(" themes public/js` sin resultados.
- [x] **F19.2 Código muerto y ficheros de andamiaje eliminados**
  - [x] `TemaController::actualizarLogo()` / `preview()`, sus rutas (`dashboard.temas.logo`, `dashboard.temas.preview`) y `dashboard/temas/preview.blade.php` (sin referencias; el logo ya lo gestiona `LogoController` y la previsualización usa `?preview_theme`). Imports (`Profile`, `ImagenOptimizer`, `Storage`) limpiados del controlador.
  - [x] `welcome.blade.php`, `public/placeholder.blade.php`, `vite.config.js`, `tailwind.config.js`, `postcss.config.js`, `package.json`, `resources/css/`, `resources/js/` y `node_modules/` eliminados (no quedaba ningún `@vite` fuera de `welcome.blade.php`).
  - [x] Scripts de un solo uso en `database/seeders/` (`scaffold-themes.php`, `patch-navs.php`, `apply-phrases.php`, `gen-sql.php`) eliminados — eran scripts sueltos (sin `namespace`), no clases `Seeder`, y apuntaban a una ruta `cms/` que ya no existe.
  - [x] **[EXTRA]** Import muerto de `InstallerService` en `routes/web.php` eliminado (detectado por el linter del editor).
  - [x] `public/sw.js` y el bloque de `dashboard/layout.blade.php` que lo registra: **se mantienen**, decisión deliberada — siguen limpiando el service worker antiguo de navegadores que ya lo tuvieran registrado en desarrollo; retirarlos ahora dejaría el SW atascado para siempre en esos navegadores.
- [x] **F19.3 Tests automáticos.** `phpunit.xml` configurado con SQLite en memoria (aislado de la BD real `cms_app`). El aislamiento de `storage/app/installed.lock` se completó el 2026-09-30 (nota de más arriba) — en esta fase todavía se creaba y borraba a mano antes/después de cada `php artisan test`. 36 tests en verde (`php artisan test`):
  - `tests/Unit/Support/PhoneHelperTest.php`, `tests/Unit/Services/CitaServiceSlotsTest.php` (los dos ejemplos exactos de CLAUDE.md).
  - `tests/Feature/Auth/LoginTest.php`, `tests/Feature/RouteProtectionTest.php` (recorre `Route::getRoutes()` para todas las `dashboard.*`).
  - `tests/Feature/Disponibilidad/ReservaServiceTest.php`, `tests/Feature/Public/ReservaPublicaTest.php`, `tests/Feature/ProteccionDatos/PdfTest.php`, `tests/Feature/SeoTest.php`, `tests/Feature/ThemePreviewTest.php`.
  - **[EXTRA] Correcciones necesarias para poder escribir los tests:** `database/factories/UserFactory.php` usaba los campos (`name`, `email_verified_at`) del `User` por defecto de Laravel, no los reales (`nombre`, `apellidos`, `telefono`) — corregido.
  - **[EXTRA] Bug real corregido:** `ReservaService::periodosVacacionesCargados()` cacheaba los periodos de vacaciones en una variable `static` (persiste entre peticiones en `php artisan serve`/colas/Octane); un periodo añadido o borrado no se aplicaba hasta reiniciar el proceso. Cambiado a una propiedad de instancia (`$this->periodosCache`).
- [x] **F19.4 SEO técnico.** `public/robots.txt` estático (bloqueaba nada) sustituido por `GET /robots.txt` (`Public\SeoController@robots`): bloquea `/panel-psicologa`, `/acceso-psicologa`, `/instalacion`, `/recuperar-pwd` y enlaza el sitemap. `GET /sitemap.xml` (`Public\SeoController@sitemap`) genera las URLs activas según el modo y las funcionalidades, más los artículos publicados. `<link rel="canonical">` (`resources/views/_shared/canonical.blade.php`) incluido en el `<head>` de los 11 temas.
- [x] **F19.5 `.env.example`** reescrito: `APP_NAME=PsicoCMS`, `es`/`Europe/Madrid`/`es_ES`, `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync`, `DB_CONNECTION=mysql` con comentario de que el asistente los rellena.
- [x] **F19.6 README propio** (instalación en XAMPP, asistente, despliegue en hosting compartido, `.htaccess` de raíz **[EXTRA, nuevo]**, copias de seguridad, Gmail, cómo crear un tema y cómo ejecutar los tests).
- [x] **F19.7 `?preview_theme` restringido a sesión iniciada.** `ThemeManager::previewSlug()`/`previewMode()` comprueban `auth()->check()` antes de leer la query. Cubierto por `ThemePreviewTest`.
- [x] **F19.8 Ficheros de seguimiento.** Decisión documentada en `CLAUDE.md` (Otras consideraciones): `project-map.md` sustituye a `tareas.md`.

**Nota de entorno (no es código del proyecto):** al levantar `php artisan serve` para verificar la fase, MariaDB no arrancaba (tabla de sistema `mysql.db` marcada `crashed`, y tras repararla, una tabla interna de phpMyAdmin con una página InnoDB corrupta abortaba el arranque). Se reparó `mysql.db` con `aria_chk` y, al seguir dando "table is full", se reconstruyó con el esquema oficial de MariaDB conservando sus filas; se sacaron de `data/` los ficheros corruptos de `phpmyadmin.pma__recent` (ajenos a la app); se volvió a crear el usuario `cms_app` con la misma contraseña que ya tenía en `.env`. La base de datos `cms_app` de la aplicación no se tocó. Verificado con `php artisan serve`: `/`, `/blog`, `/preguntas-frecuentes`, `/pide-cita`, `/politica-de-privacidad`, `/robots.txt`, `/sitemap.xml` → 200; `/panel-psicologa` sin sesión → 302 a `/acceso-psicologa`.
