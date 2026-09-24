# Mapa del proyecto — PsicoCMS

Estado de todas las fases y tareas. Las tareas generales vienen de [CLAUDE.md](CLAUDE.md) y su detalle (IDs `F{fase}.{tarea}.{subtarea}`) de [plan-implementacion.md](plan-implementacion.md).

**Leyenda**

| Marca | Significado |
|---|---|
| `[x]` | Tarea terminada y verificada en el código |
| `[ ]` | Tarea incompleta o pendiente: revisar si se termina o se elimina |
| **[EXTRA]** | Funcionalidad que existe en el código pero no estaba en CLAUDE.md |
| Fases | Mismas marcas: `[x]` finalizada, `[ ]` con tareas pendientes o sin empezar |

_Última actualización: 2026-09-23 (reconstruido por auditoría del código)._

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
| 19 | Calidad, limpieza y producción *(nueva)* | [ ] Pendiente |

### Tareas abiertas

| ID | Tarea | Acción que se propone |
|---|---|---|
| F1.9 | El asistente no recoge horarios ni disponibilidad | Añadir un paso o quitar el requisito de CLAUDE.md |
| F19.1 | `innerHTML` en los JS de los temas | Terminar |
| F19.2 | Código muerto y ficheros de andamiaje | Eliminar |
| F19.3 | No hay tests automáticos | Terminar |
| F19.4 | `robots.txt`, `sitemap.xml` y canonical | Terminar |
| F19.5 | `.env.example` con `en`/`UTC` | Terminar |
| F19.6 | README propio del proyecto | Terminar |
| F19.7 | `?preview_theme` accesible a cualquier visitante | Terminar |
| F19.8 | `prompts.md` / `tareas.md` que pide CLAUDE.md | Decidir |

---

## [x] FASE 0 — Preparación del proyecto y base de datos

_CLAUDE.md › Fases del desarrollo: "Estudia las características del proyecto" y "Crea la base de datos"._

- [x] F0.1 Proyecto Laravel 11 + dompdf, Intervention Image, HTMLPurifier
- [x] F0.2 Configuración regional (`es`, `Europe/Madrid`, sesiones en BD) en `.env` *(falta en `.env.example`, ver F19.5)*
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

## [ ] FASE 4 — Inicio, disponibilidad y gestión de citas

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

## [ ] FASE 19 — Calidad, limpieza y puesta en producción *(nueva, añadida tras la auditoría)*

- [ ] F19.1 Quitar `innerHTML` de los JS de los temas (`blog-ajax.js` ×10, `tema-aurora/main.js`, `tema-base/reserva.js`)
- [ ] F19.2 Borrar código muerto
  - [ ] `TemaController::actualizarLogo()` / `preview()`, sus rutas y `dashboard/temas/preview.blade.php`
  - [ ] `welcome.blade.php`, `public/placeholder.blade.php`, Vite/Tailwind/PostCSS, `resources/css` y `resources/js`
  - [ ] Scripts de un solo uso en `database/seeders/` (`scaffold-themes`, `patch-navs`, `apply-phrases`, `gen-sql`)
  - [ ] `public/sw.js` + bloque que da de baja el service worker (cuando ya no haga falta)
- [ ] F19.3 Tests automáticos (login, protección de rutas, solapamientos, huecos, reserva pública, PDF)
- [ ] F19.4 SEO técnico: `robots.txt` que bloquee el panel, `sitemap.xml` y canonical
- [ ] F19.5 `.env.example` con `es` / `Europe/Madrid`
- [ ] F19.6 README propio del proyecto (instalación, despliegue y cómo crear temas)
- [ ] F19.7 Limitar `?preview_theme` a sesiones autenticadas
- [ ] F19.8 Ficheros de seguimiento `prompts.md` / `tareas.md` que pide CLAUDE.md
