# Improvements

Documento de mejoras del desarrollo.

Este archivo contiene mejoras funcionales, de UX, rendimiento, estructura
o mantenimiento sobre funcionalidades que actualmente funcionan.

Las mejoras no deben interpretarse como correcciones de errores, salvo que
se indique explícitamente.

## Instrucciones para el agente

- Revisar cada mejora antes de implementarla.
- Mantener las funcionalidades existentes que no estén relacionadas con
  la mejora.
- Evitar introducir cambios innecesarios en otras partes del sistema.
- Mantener los patrones y arquitectura existentes del proyecto.
- Priorizar soluciones simples y coherentes con el desarrollo actual.
- No implementar una mejora basándose en suposiciones cuando el requerimiento
  no sea suficientemente claro.
- Verificar que la mejora no afecte negativamente funcionalidades existentes.
- Marcar una mejora como completada únicamente después de verificarla.
- No romper funcionalidades que ya existen y estan funcionado.

---

## IMP-001 — Poder crear un mensaje personalizado de whatsApp

**Estado:** Implementado, pendiente de validación  
**Prioridad:** Alta  
**Página:** `/panel-psicologa/configuracion/`  
**Sección:** configuracion

### Mejora aplicada

- **Nuevo:** `app/Services/WhatsappService.php` — plantilla por defecto (la que ya se
  usaba, ahora con variables), `renderizar()`, `mensajeConfirmacion(Cita)` y
  `urlConfirmacion(Cita)`. Variables disponibles: `{nombre}`, `{psicologa}`,
  `{modalidad}`, `{fecha}`, `{hora}`.
- `app/helpers.php`: `whatsapp_confirmacion_url()` (el helper que ya usaban las
  3 vistas de citas) ahora delega en el servicio en vez de tener el mensaje
  escrito a mano — así las vistas no necesitaron ningún cambio.
- **Nueva pantalla** `/panel-psicologa/configuracion/whatsapp`
  (`WhatsappController`, `WhatsappRequest`): textarea con la plantilla,
  lista de variables disponibles y vista previa en vivo (JS, sin `innerHTML`)
  con datos de ejemplo. Enlace añadido al menú "Configuración" y a la página
  de Ayuda.
- Guardado en `settings` (grupo `whatsapp`, clave `whatsapp.plantilla_confirmacion`);
  si no se ha configurado nada, se usa la plantilla por defecto.
- Admite emojis sin ningún cambio adicional (la base de datos ya usa `utf8mb4`
  en todas las tablas, incluida `settings`).
- **El calendario también usa la plantilla** (no estaba pedido explícitamente,
  pero el modal de detalle de una cita en `/panel-psicologa/calendario`
  construía el mismo mensaje de confirmación por su cuenta, en JavaScript, con
  el texto fijo de siempre — se habría quedado desactualizado frente a la
  plantilla nueva). Ahora el feed `calendario/eventos` incluye la URL ya
  calculada con `whatsapp_confirmacion_url()` y `calendario.js` solo la usa,
  sin reconstruir el mensaje. Esto dejó sin uso la variable `psicologa` que se
  pasaba a `CalendarioConfig`; se quitó de `CalendarioController@index` y de
  la vista.
- **No se ha tocado** (son enlaces de WhatsApp sin relación con confirmar una
  cita): el enlace "Escribir por WhatsApp" de la ficha del paciente (sin
  mensaje prerrelleno), el campo "WhatsApp" de Redes sociales, y el botón de
  WhatsApp de contacto de la web pública.
- Tests nuevos: `tests/Feature/Configuracion/WhatsappPlantillaTest.php` (la
  página muestra la plantilla por defecto, se guarda una plantilla con emoji,
  se rechaza una plantilla vacía, el enlace de una cita usa la plantilla
  guardada con las variables sustituidas, y sin teléfono no se genera enlace).
  44 tests en verde.

### Ampliación (2026-09-30): selector de emojis

A petición del usuario, tras validar la primera versión, se añadió una forma
fácil de escoger emojis sin tener que salir del teclado del sistema:

- `WhatsappService::EMOJIS_SUGERIDOS` — 32 emojis pensados para una consulta
  de psicología (caritas y saludos, calendario/hora/ubicación, corazones de
  colores, naturaleza y celebración).
- `WhatsappService::ENLACE_MAS_EMOJIS` — enlace a una lista externa más
  completa (`https://es.piliapp.com/emoji/list/`, pedido explícitamente por
  el usuario), por si los 32 sugeridos no bastan.
- En `/panel-psicologa/configuracion/whatsapp`: una rejilla de botones con
  los emojis sugeridos (uno por botón) y, debajo, el enlace "¿Buscas más
  emojis? Ver lista completa" (`target="_blank"`, `rel="noopener"`).
- `public/js/dashboard/whatsapp-plantilla.js`: al pulsar un emoji se inserta
  en la posición del cursor dentro del textarea (no al final) y se actualiza
  la vista previa al momento; sin `innerHTML`, solo `textContent` y
  `textarea.setSelectionRange()`.
- Test añadido: la página muestra los 32 emojis y el enlace externo. 45 tests
  en verde.


### Objetivo

Crear una plantilla de mensaje de whatsApp personalizado para enviar a los usuarios para confirmar la cita.

### Comportamiento actual

Lo boton para enviar un mensaje por whatsApp al cliente estan "https://api.whatsapp.com/send/?phone=3002584847&text=Hola+Camilo%2C+soy+Sebastian+Camilo+Jimenez+Hernandez.+Te+escribo+para+confirmar+tu+cita+Presencial+del+01%2F10%2F2026+a+las+13%3A00.+%C2%BFMe+confirmas+que+podr%C3%A1s+asistir%3F+%C2%A1Gracias%21&type=phone_number&app_absent=0", y no se puede modificar.


### Mejora solicitada
Tener una plantilla personaliza que se pueda modificar desde la configuración del sitio, y que los botones que lleven a whatsApp relacionado a la visat de citas "/panel-psicologa/citas o /panel-psicologa/citas/XX" que muestras un boton para enviar un mensaje al usuario para confirmar la cita, carguen el mensaje de la plantilla.

La pantilla debe poder agregar emojis porque hay psicologos que les gusta hacer su mensaje mas animado.

### Comportamiento esperado

al hacer clic en un boton de citas para enviar un mensaje de confirmación al usuario, el mensaje debe contener la plantilla creada

---

## IMP-002 — Dashboar de facturación y registro de citas pagadas.
**Estado:** Implementado, pendiente de validación
**Prioridad:** Alta  
**Página:** `/panel-psicologa/facturacion/`  
**Sección:** facturacion

### Mejora aplicada

- **Nuevo módulo separado** `App\Http\Controllers\Dashboard\Facturacion\*` (`FacturacionController`,
  `GastoController`, `IngresoManualController`), `App\Services\FacturacionService` y los modelos
  `Gasto`/`IngresoManual`, todos independientes de los controladores/servicios de citas — pensado para
  poder añadir más adelante una sección de pago por internet sin tocar el resto del módulo.
- **Citas:** nuevas columnas `pagado` (booleano), `monto` (importe, `decimal(10,2)`) y `pagado_at` en
  `citas` (migración aditiva, no destructiva). `Cita::puede_marcarse_pagada` (accesor) es `true` solo si
  el estado es `confirmada` o `realizada`, igual que pide la mejora. Nueva ruta
  `PATCH /panel-psicologa/citas/{cita}/pago` y un panel "Pago" en el detalle de la cita para fijar el
  importe y marcarla pagada/pendiente. El importe **no se borra** al desmarcar el pago (se conserva para
  poder seguir usándolo en el recordatorio).
- **Listado de citas:** nueva columna "Pago" (Pagada / Pendiente / "—" si la cita no está confirmada ni
  realizada) y un interruptor de filtro "Solo pendientes de pago" (`pendiente_pago=1`) junto a los
  filtros existentes.
- **Plantilla de WhatsApp para recordar el pago:** ampliado `WhatsappService` con
  `plantillaPago()`/`mensajeRecordatorioPago()`/`urlRecordatorioPago()` y la variable `{monto}`. Se edita
  en la misma pantalla `/panel-psicologa/configuracion/whatsapp`, ahora con dos plantillas (confirmación
  y recordatorio de pago), cada una con su propio selector de emojis y vista previa. El botón "Recordar
  pago" (listado de citas, detalle de la cita y panel de facturación) solo aparece si la cita tiene un
  importe asignado y sigue sin pagarse.
- **Panel de facturación** (`/panel-psicologa/facturacion`): tarjetas de resumen del mes (ingresos,
  gastos, neto, citas pendientes de pago), un gráfico de barras con los ingresos diarios del mes actual
  y otro con la comparativa mensual (botones "6 meses" / "último año", recarga por AJAX sin salir de la
  página), listado de citas pendientes de pago, y un listado de gastos y de ingresos manuales (alta
  directamente desde la misma página, borrado con el modal de confirmación habitual). Los gráficos son
  SVG dibujados a mano en JS nativo (sin librería externa), con tooltip al pasar el ratón y una tabla de
  datos alternativa (accesible, colapsada en un `<details>`) para quien no pueda ver el gráfico.
  **Tal y como pide la mejora, ambos gráficos solo cuentan citas con `pagado = true`** (más los ingresos
  manuales); los gastos no entran en los gráficos, solo en las tarjetas de resumen y su propio listado.
- Añadido "Facturación" como acceso directo propio en el menú lateral (no agrupado dentro de
  Configuración ni Gestión Web, para mantenerlo como módulo independiente).
- Tests nuevos: `tests/Feature/Facturacion/FacturacionTest.php` (12 tests: dashboard, marcar/desmarcar
  pago y sus validaciones, filtro de pendientes de pago, enlace de recordatorio con y sin importe, alta
  y borrado de gastos e ingresos manuales, cálculo del resumen del mes) y 3 tests nuevos en
  `WhatsappPlantillaTest` para la plantilla de recordatorio de pago. 59 tests en verde.
- **Nota técnica:** las 3 migraciones nuevas (`add_pago_a_citas_table`, `create_gastos_table`,
  `create_ingresos_manuales_table`) son aditivas — no se ha tocado ni vaciado ninguna tabla existente.
  Ya se ejecutaron (`php artisan migrate`) una vez el servicio `MySQL80` volvió a estar arrancado (ver
  nota de entorno en `project-map.md`); verificado que los datos existentes no se vieron afectados y que
  `/panel-psicologa/facturacion` carga correctamente contra la base real.

### Rediseño (2026-10-08): nueva estructura visual basada en `templates-ux/dashboard-facturacion.html`

A petición del usuario, tras validar la primera versión, se rehízo la interfaz de
`/panel-psicologa/facturacion` siguiendo la estructura UX de la plantilla de referencia
`templates-ux/dashboard-facturacion.html`, adaptada a la interfaz y a las reglas de código de
PsicoCMS. No cambia la lógica de negocio de la mejora, solo su presentación:

- **Pestañas** Resumen / Ingresos / Gastos / "Pagos en línea" (placeholder "Próximamente", igual
  que en la plantilla, acorde a la idea original de IMP-002 de dejar hueco a una pasarela de pago).
- **Resumen:** 4 tarjetas KPI (ingresos del mes con variación real vs. el mes anterior calculada
  con `serieMensual()`, gastos del mes, utilidad neta con el margen %, y "por cobrar" con un enlace
  real a `/panel-psicologa/citas?pendiente_pago=1`); dos gráficos y "Últimos movimientos" del mes
  (citas pagadas + ingresos manuales + gastos) con un filtro Todos/Ingresos/Gastos que oculta filas
  al instante (sin recargar). **Ver el ajuste de los gráficos más abajo.**
- **Ingresos / Gastos:** mini-tarjetas de resumen, listado del mes (citas pagadas y manuales en
  Ingresos; gastos con su categoría en Gastos) y, en Gastos, el desglose "Gastos por categoría" con
  barras proporcionales. Alta de gasto/ingreso con un modal propio (reutiliza el componente
  `.modal`/`.modal__*` ya existente en el proyecto, no uno nuevo) y borrado con el modal de
  confirmación habitual (`data-confirm` + `data-ajax-delete`, igual que en servicios, especialidades,
  planes y FAQ).
- **Servicio ampliado:** `FacturacionService::porCobrar()`, `movimientosMes()` (citas pagadas +
  ingresos + gastos del mes en un solo array, para alimentar las tres tablas anteriores) y
  `gastosPorCategoriaMes()`. Se quitó `FacturacionController::datos()` y su ruta AJAX — ya no hacía
  falta, el gráfico de tendencia ahora recibe directamente los 12 meses y recorta en el cliente.
- **`GastoController`/`IngresoManualController` sin `update()`** (la plantilla de referencia tampoco
  tiene edición, solo alta y borrado); `store()`/`destroy()` admiten ahora tanto una petición normal
  (redirige de vuelta a `/panel-psicologa/facturacion?tab=…`, conservando la pestaña activa) como
  AJAX (devuelve JSON), aunque de momento los formularios de los modales son envíos normales.
- Tests nuevos/actualizados: `porCobrar()`, `movimientosMes()`, las pestañas se ven en la página, y
  el redirect con `?tab=` tras crear/eliminar. **64 tests en verde.**

### Ajuste (2026-10-08): gráficos de línea iguales a la plantilla, no de barras

El usuario indicó que el primer rediseño (gráficos de barras en CSS) seguía sin convencerle y pidió
replicar **exactamente** los gráficos y tablas de `templates-ux/dashboard-facturacion.html` (línea
con área degradada, línea de referencia punteada con su etiqueta, tooltip al pasar el cursor,
leyenda), conservando únicamente los colores del tema que elija la psicóloga. Esto reemplaza la
decisión anterior de usar barras:

- Los dos gráficos son ahora **gráficos de línea en SVG** — como en la plantilla — pero construidos
  con `document.createElementNS()` + `setAttribute()`/`appendChild()` (nunca `innerHTML`, que es como
  los construye la plantilla original; se mantiene la regla de CLAUDE.md). Cuadrícula con etiquetas
  de valor, área con degradado, línea de referencia punteada con su etiqueta (ámbar), marcadores,
  línea de cursor ("crosshair") y tooltip flotante al pasar el ratón o el dedo — igual que el diseño
  base.
- **Colores según el tema de la psicóloga:** en vez de los colores fijos de la plantilla, cada
  gráfico toma `var(--color-primary)` (Ingresos del mes) o `var(--color-info)` (Tendencia) en el
  momento de dibujarse, así que cambian solos si la psicóloga cambia el color del panel en
  "Apariencia".
- **La información de los gráficos ahora es la misma que en la plantilla:** antes las dos gráficas
  combinaban citas pagadas + ingresos manuales; la plantilla las genera **solo con citas pagadas**
  (coincide con lo pedido en IMP-002: "todas las gráficas deben generarse con las citas que ya hayan
  sido pagadas"). Se añadieron `FacturacionService::citasPagadasEntre()`, `serieDiariaCitasMesActual()`
  y `serieMensualCitas()` (series solo de citas) para las gráficas; `serieMensual()`/`ingresosEntre()`
  (citas + manuales) se conservan tal cual para la tarjeta KPI "Ingresos del mes", que sí debe incluir
  los ingresos manuales. Las estadísticas sobre cada gráfico también pasaron a coincidir con las de la
  plantilla: "Cobrado en citas" / "Citas pagadas" / "Ticket promedio" (antes eran de días con ingreso).
- Las tablas (Últimos movimientos / Ingresos / Gastos) no tenían un campo "Método" de pago porque la
  aplicación no guarda esa información en ningún sitio (ni se pidió en IMP-002); se mantienen con las
  columnas que sí tienen datos reales (fecha, concepto, tipo/categoría, importe) en vez de añadir un
  campo ficticio.
- Tests: sin cambios de comportamiento de negocio en esta pasada (solo de presentación de los
  gráficos), la suite sigue en **64 tests en verde**.

### Ajuste (2026-10-08, el mismo día): los gráficos pasan de SVG a `<div>` + CSS

El usuario pidió explícitamente que los gráficos no usaran SVG, para no afectar el rendimiento de
la aplicación, y que se construyeran con CSS y JavaScript. Se mantiene el mismo aspecto, los mismos
datos y la misma interacción que en el ajuste anterior (línea con área degradada, línea de
referencia punteada, marcadores, cursor y tooltip) — solo cambia cómo se dibuja:

- `LineChart` ya no genera un `<svg>`: calcula las mismas coordenadas de antes (en píxeles) y las
  aplica a `<div>` normales con `position: absolute`, sin `createElementNS`, sin `innerHTML` y sin
  `<canvas>`. El área bajo la línea usa `clip-path: polygon(...)` con un degradado CSS; la línea se
  dibuja con un `<div>` de 2px por cada segmento entre dos puntos, rotado con `transform: rotate()`
  (técnica clásica de "dibujar líneas con CSS"); los puntos, la cuadrícula, la línea de referencia y
  el cursor son también `<div>`/`<span>` posicionados por JavaScript.
- El color de cada gráfico se aplica con una variable CSS propia del contenedor
  (`--factu-chart-color`), fijada en JS a partir de `var(--color-primary)`/`var(--color-info)` —
  sigue respondiendo al tema de la psicóloga, igual que en el ajuste anterior.
- No cambia ningún dato ni la lógica de negocio: siguen siendo gráficos de citas pagadas únicamente,
  con los mismos cálculos de referencia y las mismas estadísticas.
- Verificado: sin ninguna coincidencia de `<svg>`, `createElementNS`, `innerHTML`, `var` ni
  `canvas` en `facturacion.js`. **64 tests en verde.**

### Ajuste (2026-10-08, el mismo día): selector de mes + más datos ficticios de prueba

El usuario pidió poder cambiar de mes en el panel para ver cómo cambian los datos, y más volumen de
datos ficticios (al menos 50 ingresos **y** 50 egresos, no 50 entre los dos — mínimo 100 filas),
repartidos desde mayo.

- **Selector de mes funcional** en la cabecera (sustituye la etiqueta fija del mes): un `<select>`
  con los últimos 12 meses que, al cambiar, recarga la página con `?mes=YYYY-MM&tab=…` (conserva la
  pestaña activa). Pasan a depender del mes elegido: las 4 tarjetas KPI, los dos gráficos (el de
  ingresos diarios y la tendencia mensual, que ahora termina en el mes elegido en vez de en "hoy"),
  "Últimos movimientos" y las pestañas Ingresos/Gastos (incluido el desglose por categoría). La
  tarjeta "Por cobrar" sigue mostrando el pendiente de pago **real, de hoy**, independientemente del
  mes que se esté consultando (tiene sentido: una cita pendiente de cobro no "pertenece" a un mes).
- `FacturacionService`: `serieDiariaCitasMesActual()` renombrado a `serieDiariaCitasDelMes(?Carbon $mes)`
  (ahora recorre el mes completo si es un mes pasado, no solo hasta "hoy"); `serieMensual()` y
  `serieMensualCitas()` aceptan un segundo parámetro `?Carbon $hasta` para que la ventana de 12 meses
  termine en el mes elegido en vez de siempre en el mes real.
- **Más datos ficticios de prueba:** nuevo seeder `database/seeders/DemoFacturacionRangoSeeder.php`
  que añade, para cada mes entre mayo y el mes actual, 20 ingresos manuales + 20 gastos + 15 citas
  pagadas (solo inserta, no toca nada existente). Sumado al lote anterior, cada mes de mayo a octubre
  queda con 18 a 29 filas de cada tipo; en total: 175 gastos, 178 ingresos manuales y 181 citas
  pagadas — por encima de lo pedido (mínimo 50 de cada, no 50 entre los dos). Verificado antes/después
  de sembrar: ningún dato existente (pacientes, usuarios) cambió de cantidad.
- Tests nuevos: cambiar de mes muestra los datos de ese mes; un valor de mes inválido en la URL no
  rompe la página (usa el mes actual). **66 tests en verde.**

### Ajuste (2026-10-08, el mismo día): paginación de las tablas y gráfica "Diario" por defecto

- **Paginación** en las tres tablas de la pantalla ("Últimos movimientos", "Ingresos de [mes]" y
  "Gastos de [mes]"): muestran como máximo 10 filas y, debajo, un selector "Mostrar 10/20/50/100" y
  los controles de página (anterior/números/siguiente). Es paginación 100% en el cliente: las filas
  ya están renderizadas por el servidor; un único componente reutilizable (`crearPaginador()` en
  `facturacion.js`) las oculta o muestra según la página y el tamaño elegidos, sin peticiones
  adicionales ni lógica de servidor nueva. El filtro Todos/Ingresos/Gastos de "Últimos movimientos"
  ahora se combina con su paginador (al cambiar el filtro, se vuelve a la página 1 del resultado
  filtrado).
- **"Gastos por categoría"** no se pagina: a partir de la 7ª categoría aparece scroll vertical dentro
  de la propia tarjeta (`max-height` + `overflow-y:auto`), como se pidió.
- **Gráfica "Ingresos del mes"**: el interruptor "Diario" queda activo por defecto al cargar la
  página (antes era "Acumulado"); "Acumulado" sigue disponible con un clic.
- Sin cambios de backend ni de datos en este ajuste. **66 tests en verde** (sin tests nuevos: es
  paginación puramente de interfaz, sobre datos que los tests de negocio ya cubren).

### Ajuste (2026-10-08, el mismo día): el paginador copia el estilo exacto de la plantilla

La primera versión de la paginación reutilizaba el componente `pagination-dash` que ya existe en el
resto del panel (píldoras con fondo de color sólido en la página activa). El usuario pidió que, en
vez de eso, tuviera el mismo estilo que el paginador de `templates-ux/dashboard-facturacion.html`
(`.pager`/`.pager-btns`): botones planos sin relleno, que solo se marcan con un borde sutil y fondo
suave en la página activa (sin color sólido), con una línea separadora encima del pie de la tabla.

- Nuevas clases propias de esta pantalla: `.factu-pager`/`.factu-pager__btn`/`.factu-pager__ellipsis`,
  que replican exactamente esos estilos con los tokens de color del proyecto (en vez de duplicar la
  plantilla, se adaptó: la plantilla usa SVG en línea para las flechas, aquí se usan iconos de Font
  Awesome, igual que en el resto de la aplicación).
- `crearPaginador()` ya no construye una lista `<ul>/<li>` (así era `pagination-dash`): ahora genera
  botones simples dentro de un `<div class="factu-pager">`, con `disabled` nativo en los extremos,
  igual que hace la plantilla de referencia.
- El pie de cada tabla (info + selector de tamaño + paginador) ahora lleva una línea divisoria encima,
  como en la plantilla.
- Verificado: ninguna referencia a `pagination-dash` queda en la pantalla de facturación. **66 tests
  en verde** (sin cambios de lógica, solo de estilos).

### Ajuste (2026-10-08, el mismo día): el scroll de "Gastos por categoría" solo en pantallas ≤1180px

El `max-height`+scroll de "Gastos por categoría" (a partir de la 7ª categoría) estaba activo siempre.
El usuario pidió que solo aplique en pantallas de 1180px de ancho o menos; en pantallas más anchas la
lista se ve completa, sin límite de altura ni scroll. Movida la regla `max-height: 21rem; overflow-y:
auto;` dentro del `@media (max-width: 1180px)` ya existente (el mismo que apila los gráficos y las
tarjetas de Gastos/Ingresos a una columna). 66 tests en verde (cambio puramente de CSS).

### Objetivo

- Crear un panel de facturación donde se vean los ingresos del mes para el psicologo en una grafica, y en otra grafica se vea el promedio de los ultimos 6 mes y el ultimo año segpun seleccione el usuario.
    - El panel de facturación debe quedar separado en un modulo con respecto a los otros modulos de la aplicación, ya que se en el futuro tendra sección de pago por internet si el psicologo lo requiere. Por lo tanto debe tener un modularidad y escalabilidad bastante alta para poder agregar nuevas funcionalidades en el modulo de facturación segpun se vayan requiriendo.
    - En la zona de cita "panel-psicologa/citas" debe existir un campo booleano para confirmar si la cita se pago o no se ha pagado, ese estado debe aparecer habilitado si la cita ya ha sido confirmada.
    - En la facturación debe poderse ingresar los gastos para complementar la facturación, porque tienes gastos de alquier de los consultorios u otros gastos que el psicologo quiera poner.
    - Debe poder agregar otros ingresos manuales en la facturación, por si necesita y complemetar la facturación.
    - Los graficos debe respetar el diseño de la aplicación, deben ayudar al psicologo a ver su reporte de ingresos mensuales, mientras mantiene una estitica limpia y minimalista en las graficas.
    - En el panel de "panel-psicologa/citas" debe poderse filtrar por las citas que estan pendientes por pagar.
    - Tambien debe tener un botón en el cual se pueda enviar un mensaje directo por whastapp al usuario para recordar el pago pendiente, pero ese menesaje tambien lo puede crear el psicologo en el panel "panel-psicologa/configuracion/whatsapp", donde podra escrbir una plantilla para recordar el pago al usuario así como se hace con la plantilla de confirmar cita.
    -Todas las graficas, deben generarse con la citas que ya hayan sido pagadas.

---


## IMP-003 - IA para subir historias clinicas actuales en un paciente especifico.
