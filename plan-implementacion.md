# Plan de implementación — PsicoCMS

> **Destinatario:** agente de IA (Claude Code con Claude Sonnet) que debe construir PsicoCMS desde cero, fase a fase.
> **Fuente de requisitos:** [CLAUDE.md](CLAUDE.md). Este plan detalla cada fase de CLAUDE.md, la divide en tareas y subtareas con identificadores estables (`F{fase}.{tarea}.{subtarea}`) y define los criterios de aceptación.
> **Estado del proyecto:** ver [project-map.md](project-map.md) (usa los mismos identificadores).
>
> Este plan se ha reconstruido a partir del código existente (ingeniería inversa). Las funcionalidades que existen en el código pero no estaban en CLAUDE.md se marcan con la etiqueta **[EXTRA]**. Las que están incompletas o tienen errores se marcan con **[PENDIENTE]**.

---

## Índice

1. [Reglas de trabajo para el agente](#1-reglas-de-trabajo-para-el-agente)
2. [Stack y dependencias](#2-stack-y-dependencias)
3. [Arquitectura y estructura de carpetas](#3-arquitectura-y-estructura-de-carpetas)
4. [Modelo de datos](#4-modelo-de-datos)
5. [Claves de configuración (`settings`)](#5-claves-de-configuración-settings)
6. [Mapa de rutas](#6-mapa-de-rutas)
7. [Componentes reutilizables](#7-componentes-reutilizables)
8. [Fases de implementación](#8-fases-de-implementación) (Fase 0 a Fase 19)
9. [Optimizaciones recomendadas](#9-optimizaciones-recomendadas)
10. [Checklist de verificación por fase](#10-checklist-de-verificación-por-fase)

---

## 1. Reglas de trabajo para el agente

- Comunicarse con el usuario en **español**. Todos los textos visibles de la web, en español.
- Trabajar **una fase cada vez**. Al terminar una fase: parar, resumir lo hecho, actualizar `project-map.md` y esperar a que el usuario lo pruebe.
- No romper funcionalidades anteriores. Antes de cerrar una fase, repasar el checklist de la [sección 10](#10-checklist-de-verificación-por-fase).
- Ante una duda: releer CLAUDE.md y este plan. Si sigue la duda, preguntar. Si es algo menor, elegir la opción más simple que no rompa nada.
- Guardar cada prompt nuevo del usuario en `prompts.md` (según CLAUDE.md).

### Reglas de código (obligatorias en todas las fases)

| Ámbito | Regla |
|---|---|
| JS | Solo `let`/`const`, nunca `var`. |
| JS | Prohibido `alert`, `confirm` y `prompt`. Todo feedback se muestra en el DOM con modales o avisos propios. |
| JS | Prohibido `innerHTML`. Crear nodos con `document.createElement` + `appendChild`/`textContent`. Para vaciar un contenedor: `el.replaceChildren()`. Para insertar HTML de servidor: `DOMParser` + `importNode`. |
| JS | `event.preventDefault()` en todos los `submit` y `click` que no deban navegar. |
| JS | JavaScript nativo, sin frameworks. Un fichero por pantalla en `public/js/dashboard/`. |
| CSS | CSS nativo. `html { font-size: 10px }` y medidas en `rem`. Flexbox/Grid. |
| CSS | No mezclar CSS: dashboard en `public/css/dashboard/*.css`, cada tema en `themes/<slug>/assets/css/`. |
| HTML | Semántico (`header`, `nav`, `main`, `section`, `article`, `aside`, `footer`). |
| UX | Mensaje de confirmación tras cada acción (flash). Estado vacío ("empty state") amable en cada listado sin datos. Modal propio de confirmación para borrados. |
| Iconos | Font Awesome local (`public/vendor/fontawesome`). |
| Seguridad | Todas las rutas del panel bajo `/panel-psicologa` con middleware `auth`. CSRF en todos los formularios y `fetch`. Validación con FormRequest. HTML enriquecido saneado con HTMLPurifier. |
| PHP | Lógica de negocio en `app/Services`. Controladores finos. Validación en `app/Http/Requests`. |
| Estilo | Código legible, pocos comentarios, nombres en español coherentes con el dominio (cita, paciente, historia…). |

---

## 2. Stack y dependencias

| Capa | Tecnología |
|---|---|
| Backend | PHP 8.2+, Laravel 11 (monolito) |
| BD | MySQL / MariaDB |
| Frontend | HTML5, CSS3 nativo, JavaScript nativo |
| PDF | `barryvdh/laravel-dompdf` ^3.1 |
| Imágenes | `intervention/image` ^3.11 (redimensionar + convertir a WebP) |
| Saneado HTML | `mews/purifier` ^3.4 (config en `config/purifier.php`) |
| Editor WYSIWYG | Jodit 4.7.6 copiado en `public/vendor/jodit/` (desde cdnjs, `es2021/jodit.min.{css,js}`) |
| Calendario | Calendar.js (calendarjs.com) + `lemonade.min.js` copiados en `public/vendor/calendarjs/` |
| Iconos | Font Awesome copiado en `public/vendor/fontawesome/` (css + webfonts) |
| Email | Symfony Mailer (el que usa Laravel) con transporte SMTP creado en tiempo de ejecución a partir de los ajustes |

Entorno `.env`: `APP_LOCALE=es`, `APP_TIMEZONE=Europe/Madrid`, `SESSION_DRIVER=database`.

> No se usan Vite ni Tailwind. Los ficheros de andamiaje de Laravel (`vite.config.js`, `tailwind.config.js`, `resources/css`, `resources/js`, `welcome.blade.php`) sobran (ver F19).

---

## 3. Arquitectura y estructura de carpetas

```
app/
  Http/
    Controllers/
      Auth/            LoginController, PasswordRecoveryController
      Install/         WizardController
      Dashboard/       Home, Cita, Calendario, Evento, Disponibilidad, PeriodoVacaciones,
                       Paciente, Historia, ProteccionDatos, Faq, Imagenes, Logo, Tema,
                       Frases, FrasesPublicas, Notificaciones, Buscador, Ayuda, PerfilPrivado
        Blog/          Articulo, CategoriaBlog, BlogUpload
        Configuracion/ Perfil, Servicio, Terapia, Plan, RedesSociales,
                       EmailNotificaciones, ConfiguracionGeneral
      Public/          Home, SobreMi, Servicios, Blog, Faq, Cita, Privacidad,
                       ThemeAssets, StoragePublic
    Middleware/        EnsureInstalled, EnsureNotInstalled, RedirectIfAuthenticated, OnlyLocal
    Requests/          (un FormRequest por formulario, agrupados como los controladores)
  Mail/                NuevaCitaMail
  Models/              User, Profile, Setting, Servicio, Terapia, PlanPrecio, Disponibilidad,
                       PeriodoVacaciones, Cita, Evento, Paciente, Historia, HistoriaArchivo,
                       Faq, CategoriaBlog, Articulo
  Policies/            CitaPolicy
  Rules/               NoOverlap
  Services/            InstallerService, CitaService, ReservaService, PacienteService,
                       ThemeManager, ImagenOptimizer, ImagenSlotResolver,
                       PdfPlantillaRenderer, SlugService
  Support/             PhoneHelper
  View/Composers/      SidebarComposer, HeaderComposer, PublicLayoutComposer
  helpers.php          theme_asset(), theme_image(), phrase(), whatsapp_confirmacion_url(), logo_data()
public/
  css/{auth,install,dashboard}/   css/wysiwyg.css
  js/{auth,install,dashboard}/
  vendor/{fontawesome,jodit,calendarjs}/
resources/views/
  install/  auth/  dashboard/  emails/  feeds/  public/  vendor/pagination/  _shared/
themes/
  <slug>/theme.json
  <slug>/assets/{css,js,img}/
  <slug>/views/layout.blade.php, landing.blade.php
  <slug>/views/multipage/{home,sobre-mi,servicios,blog,blog-articulo,blog-fragment,faq,cita}.blade.php
  <slug>/views/partials/{nav,footer,section-hero,section-sobre-mi,section-servicios,
                         section-terapias,section-planes,section-blog,section-faq,section-cita}.blade.php
storage/app/installed.lock        (marca de instalación completada)
```

### Decisiones de arquitectura

- **Usuario único.** Solo existe la psicóloga (tabla `users`, 1 fila). No hay registro público. `User::first()` es "la psicóloga".
- **Datos públicos** en la tabla `profile` (1 fila, `Profile::singleton()`); el nombre y los apellidos públicos salen de `users`.
- **Ajustes** en la tabla `settings` clave/valor (valor JSON) agrupados por `group`. Acceso con `Setting::get($key, $default)` / `Setting::set($key, $value, $group)`. `get()` devuelve el valor por defecto si la BD aún no existe, para que el asistente pueda arrancar.
- **Temas plug and play.** `ThemeManager` lee `themes/*/theme.json`. `AppServiceProvider` registra el namespace de vistas `theme::` apuntando a `themes/<activo>/views` y asigna `PublicLayoutComposer` a `theme::*`. Los assets de un tema se sirven con `/theme-assets/{slug}/{path}` (`ThemeAssetsController`, con control de path traversal).
- **Instalación.** `installed.lock` en `storage/app`. Middleware `installed` (redirige a `/instalacion` si falta) y `not_installed` (bloquea el asistente tras instalar).
- **Ficheros privados** (historias y avatar) en el disco `local` (no público), servidos por rutas autenticadas. Ficheros públicos (foto, blog, imágenes de tema, logo) en el disco `public`.

---

## 4. Modelo de datos

| Tabla | Campos principales | Notas |
|---|---|---|
| `users` | nombre, apellidos, email (único), telefono (único), password (bcrypt), theme_preference (`light`/`dark`), primary_color (def. `#2c4a7e`), avatar_path, remember_token | Una sola fila. |
| `sessions`, `cache`, `jobs`, `password_reset_tokens` | estándar de Laravel | |
| `profile` | slogan, telefono_publico, email_publico, numero_colegiado, sobre_mi (HTML), direccion, lat, lng, foto_path | Una sola fila. |
| `settings` | key (PK), value (JSON), group, updated_at | Ver sección 5. |
| `servicios` | titulo, descripcion, icono, orden, activo | |
| `terapias` | titulo, icono, descripcion, orden, activo | "Especialidades" en la interfaz. |
| `planes_precios` | tipo (`online`/`presencial`), nombre, descripcion, precio, duracion_min, orden | |
| `disponibilidades` | modalidad, dia_semana (0=domingo…6), hora_inicio, hora_fin, activa | Único (modalidad, dia_semana, hora_inicio). Cada fila es un hueco reservable. |
| `periodos_vacaciones` | fecha_inicio, fecha_fin | |
| `pacientes` | nombre, apellidos, dni (único, nullable), telefono (único, normalizado), email, fecha_nacimiento, genero, direccion, motivo_inicial, notas, origen (`publica`/`manual`), softDeletes | El teléfono es el identificador de negocio. |
| `citas` | paciente_id (FK nullOnDelete), nombre_provisional, telefono_provisional, email_provisional, modalidad, fecha_inicio, fecha_fin, motivo, estado (`pendiente`,`confirmada`,`cancelada`,`realizada`,`no_asistio`), notas_internas, origen, softDeletes | Índices en fecha_inicio, estado y paciente_id. |
| `eventos` | titulo, descripcion, color, fecha_inicio, fecha_fin, all_day, ubicacion | **[EXTRA]** Eventos del calendario que no son citas. |
| `historias` | paciente_id (FK cascade), fecha_sesion, titulo, contenido (HTML), softDeletes | |
| `historia_archivos` | historia_id (FK cascade), nombre_original, ruta, tipo (`imagen`/`pdf`), mime_type, tamanio | |
| `faqs` | pregunta, respuesta, orden, activa | |
| `categorias_blog` | nombre, slug (único), descripcion, orden | |
| `articulos` | categoria_id (FK nullOnDelete), titulo, slug (único), extracto, contenido, imagen_path, estado (`borrador`/`publicado`/`archivado`), published_at, meta_title, meta_description, softDeletes | |

Seeders: `CategoriasBlogSeeder` (Ansiedad, Depresión, Autoestima, Relaciones, Mindfulness, Trauma) y `DemoDataSeeder` (datos de prueba: 50 pacientes, 200 citas, 100 historias, artículos, FAQs, servicios, especialidades).

---

## 5. Claves de configuración (`settings`)

| Grupo | Claves |
|---|---|
| theme | `active_theme`, `theme_mode` (`landing`/`multipage`) |
| branding | `branding.logo_path`, `branding.logo_icon` |
| disponibilidad | `duracion_sesion_presencial_min`, `duracion_sesion_online_min`, `hora_apertura_manana`, `hora_cierre_manana`, `hora_apertura_tarde`, `hora_cierre_tarde`, `descanso_activo_presencial`, `descanso_min_presencial`, `descanso_activo_online`, `descanso_min_online`, `modo_vacaciones`, `mensaje_vacaciones`, `dias_adelante` (todas con prefijo `disponibilidad.`). Heredadas: `duracion_sesion_min`, `hora_apertura`, `hora_cierre` |
| features | `features.blog_enabled`, `features.reservas_enabled`, `features.faq_enabled`, `features.servicios_enabled`, `features.sobre_mi_enabled` |
| mail | `mail.smtp_host`, `mail.smtp_port`, `mail.smtp_user`, `mail.smtp_password` (cifrada con `Crypt`), `mail.from_address`, `mail.notif_enabled` |
| social | `social.{facebook,instagram,linkedin,twitter,youtube,tiktok,whatsapp}` |
| phrases | `phrases.*` (25 claves: hero, about, servicios, especialidades, planes, blog, faq, cita) |
| proteccion_datos | `proteccion_datos.plantilla_html` |
| notifications | `notifications.bookings_last_seen_at` |

---

## 6. Mapa de rutas

**Públicas**

| Método | URL | Nombre | Notas |
|---|---|---|---|
| GET | `/` | public.home | Landing o home multipágina según `theme_mode`. Redirige a `/instalacion` si no está instalado. |
| GET | `/sobre-mi`, `/servicios`, `/preguntas-frecuentes`, `/pide-cita` | public.* | En modo landing redirigen a `/#ancla`. Devuelven 404 si la funcionalidad está desactivada. |
| GET | `/blog`, `/blog/{slug}`, `/blog/rss.xml` | public.blog* | |
| GET | `/politica-de-privacidad` | public.privacidad | **[EXTRA]** |
| GET | `/reservas/dias`, `/reservas/slots` | public.reservas.* | JSON de disponibilidad. También los usa el panel. |
| POST | `/reservas/crear` | public.reservas.crear | JSON |
| GET | `/theme-assets/{slug}/{path}` | theme.asset | |
| GET | `/storage/{path}` | storage.public | Alternativa si falta el symlink. |

**Instalación / acceso**

| Método | URL | Middleware |
|---|---|---|
| GET/POST | `/instalacion`, `/instalacion/paso/{n}`, `POST /instalacion/test-db` | not_installed |
| GET | `/instalacion/completado` | — |
| GET/POST | `/acceso-psicologa` | installed, guest_psicologa |
| POST | `/logout` | auth |
| GET/POST | `/recuperar-pwd` | only_local **[EXTRA]** |

**Panel** (`/panel-psicologa/*`, middleware `installed` + `auth`, nombres `dashboard.*`): inicio, notificaciones, buscador, ayuda, perfil-privado, disponibilidad (+ periodos-vacaciones), citas (resource + estado), calendario (+ eventos, crear, actualizar, eventos-extra), pacientes (resource + buscar, restaurar, citas, historias anidadas, proteccion-datos.pdf), historias (listado general), configuracion/{perfil, general, redes-sociales, email-notificaciones, servicios, terapias, planes, proteccion-datos}, preferencias/tema, imagenes, logo, frases-publicas, frases/{seccion}, temas, blog/{articulos, categorias, upload-imagen}, faqs (+ reordenar).

---

## 7. Componentes reutilizables

Constrúyelos en la Fase 3 y úsalos en todas las pantallas del panel:

| Componente | Fichero | Uso |
|---|---|---|
| Layout del panel | `resources/views/dashboard/layout.blade.php` | `@section('title')`, `@section('content')`, `@push('styles')`, `@push('scripts')` |
| Sidebar | `dashboard/partials/sidebar.blade.php` + `SidebarComposer` | Menú definido como array (items y grupos desplegables) |
| Header | `dashboard/partials/header.blade.php` + `HeaderComposer` | Buscador, ayuda, campana de notificaciones, tema, avatar |
| Breadcrumbs | `dashboard/partials/breadcrumbs.blade.php` | |
| Mensajes flash | `dashboard/partials/flash.blade.php` + `js/dashboard/flash.js` | `success` / `error` con cierre automático |
| Modal de confirmación | `dashboard/partials/modal-confirm.blade.php` + `js/dashboard/modal.js` | Cualquier `form[data-confirm]` abre el modal en lugar de enviarse |
| Modal de tema | `dashboard/partials/modal-tema.blade.php` + `js/dashboard/theme-toggle.js` | Claro/oscuro + color primario |
| Editor Jodit | `dashboard/blog/partials/editor-jodit.blade.php` + `js/dashboard/jodit-init.js` | `textarea[data-wysiwyg]` |
| Selector de iconos | `dashboard/configuracion/partials/icon-picker.blade.php` + `js/dashboard/icon-picker.js` | Servicios, especialidades, logo |
| Paginación | `vendor/pagination/dashboard.blade.php` | |
| Formulario de frases | `dashboard/partials/frases-form.blade.php` | Frases por sección |
| CSS base | `css/dashboard/{base,forms,tables,cards,modals,pagination,responsive,dark-theme}.css` | Variables CSS (`--color-primary`…) |

---

## 8. Fases de implementación

Formato de cada tarea: **ID — título**. Archivos: ficheros principales. "Criterio de aceptación": condición para dar la tarea por buena.

---

### FASE 0 — Preparación del proyecto y base de datos

**Objetivo:** tener el esqueleto de Laravel listo, con dependencias, assets de terceros y la BD base.

- **F0.1 — Crear el proyecto Laravel 11** con PHP 8.2 y las dependencias de la sección 2.
  **Criterio de aceptación:** `composer install` sin errores; `php artisan about` funciona.
- **F0.2 — Configuración regional:** `APP_LOCALE=es`, `APP_TIMEZONE=Europe/Madrid`, `SESSION_DRIVER=database`, también en `.env.example`.
- **F0.3 — Copiar librerías de terceros** a `public/vendor/`: Font Awesome (desde la carpeta de fuentes del tema base), Jodit 4.7.6 y Calendar.js.
- **F0.4 — Migraciones base:** `users` (con los campos de la sección 4), `sessions`, `cache`, `jobs`, `profile`, `settings`.
- **F0.5 — Modelos base:** `User`, `Profile` (con `singleton()`), `Setting` (`get`/`set` con casting JSON y protección si no hay BD).
- **F0.6 — `app/helpers.php`** registrado en `composer.json > autoload.files`.
- **F0.7 — `PhoneHelper::normalize()`:** quita espacios y todo lo que no sea dígito y conserva el `+` inicial. Devuelve `null` si queda vacío.
- **F0.8 — `ImagenOptimizer`:** recibe un `UploadedFile`, lo redimensiona a un ancho máximo, lo convierte a WebP con la calidad indicada y lo guarda en el disco `public`. Devuelve la ruta relativa.

**Criterio de aceptación:** La app arranca, las migraciones corren y los helpers están disponibles.

---

### FASE 1 — Asistente de instalación

**Objetivo:** asistente de 6 pasos que configura la BD, crea la cuenta única, recoge los datos públicos y el tema, y deja a la psicóloga dentro del panel.

- **F1.1 — Infraestructura del asistente**
  - F1.1.1 `InstallerService`: `testConnection()`, `createDatabaseIfNotExists()`, `writeEnv()` (escapando valores), `applyDbConfig()` (config en tiempo de ejecución), `runMigrations()`, `isInstalled()`, `markInstalled()` (`storage/app/installed.lock`).
  - F1.1.2 Middlewares `installed` y `not_installed` (alias en `bootstrap/app.php`).
  - F1.1.3 `WizardController` con estado en sesión (`max_step`), que impide saltarse pasos; vistas `install/layout`, `install/partials/progress` (barra de progreso) y `css/install/install.css`, `js/install/install.js`.
- **F1.2 — Paso 1: base de datos.** Host, puerto, nombre de BD, usuario y contraseña. Botón "Probar conexión" por AJAX (`POST /instalacion/test-db`) **[EXTRA]**. Al enviar: probar conexión → crear BD → escribir `.env` → migrar. Errores claros en cada fallo.
- **F1.3 — Paso 2: cuenta de acceso.** Nombre, apellidos, email, teléfono (normalizado), contraseña + confirmación (`Hash::make`). Impedir una segunda cuenta.
- **F1.4 — Paso 3: datos públicos.** Eslogan, teléfono y email para citas, nº de colegiado (opcional), sobre mí, dirección de la consulta.
- **F1.5 — Paso 4: servicios, especialidades y planes.** Listas dinámicas (añadir/quitar filas) de servicios, especialidades (terapias) y planes y precios (tipo online/presencial, nombre, precio, duración).
- **F1.6 — Paso 5: tema visual.** Tarjetas con los temas de `ThemeManager::all()` y selector de modo landing/multipágina.
- **F1.7 — Paso 6: foto de perfil.** Subida opcional, con el aviso "preferiblemente sin fondo". Se optimiza con `ImagenOptimizer` (1200px, WebP).
- **F1.8 — Cierre de la instalación:** activar las 5 funcionalidades (`features.*`), `markInstalled()`, `storage:link` (tolerante a enlaces rotos o carpetas vacías subidas por FTP), `optimize:clear`, login automático con "recordarme" y página `/instalacion/completado` con botón al panel.
- **F1.9 — [PENDIENTE] Horarios y disponibilidad en el asistente.** CLAUDE.md pide recoger "horarios y disponibilidad" en el asistente. Hoy no hay paso para ello: se configura después en el panel (F4.3), y la lista de pasos del inicio (F4.1.3) lo recuerda. **Decisión pendiente:** añadir un paso 4b sencillo (duraciones + franjas + días) que reutilice `CitaService::generarSlots()`, o quitar el requisito de CLAUDE.md.

**Criterio de aceptación:** Con una BD vacía, el asistente deja la web instalada y la psicóloga dentro del panel. Tras instalar, `/instalacion` ya no es accesible.

---

### FASE 2 — Login seguro

**Objetivo:** acceso con email + teléfono + contraseña en `/acceso-psicologa`.

- **F2.1 — `LoginRequest`:** los 3 campos obligatorios; el teléfono se normaliza; checkbox `remember`.
- **F2.2 — `LoginController@store`:** buscar el usuario por email **y** teléfono, y después `Auth::attempt(email, password, remember)`. `session()->regenerate()`. Mensaje genérico si falla.
- **F2.3 — Límite de intentos [EXTRA]:** 5 intentos por usuario e IP y 20 por IP cada 15 min (`RateLimiter`). Registro en log de fallos y bloqueos.
- **F2.4 — Logout:** invalidar la sesión y regenerar el token. Redirigir a `/acceso-psicologa`.
- **F2.5 — Middleware `guest_psicologa`** (redirige al panel si ya hay sesión) y `redirectGuestsTo(route('login'))`.
- **F2.6 — Vista de login** (`auth/layout`, `auth/login`, `css/auth/login.css`, `js/auth/login.js`) con mostrar/ocultar contraseña.
- **F2.7 — Recuperar contraseña solo en local [EXTRA]:** `/recuperar-pwd` (middleware `only_local`, 404 en producción) valida email + teléfono y fija una contraseña nueva.

**Criterio de aceptación:** Sin sesión, cualquier URL del panel redirige al login. Con "recordarme", la sesión sobrevive a cerrar el navegador.

---

### FASE 3 — Layout del panel y menú

**Objetivo:** el esqueleto visual del panel, en el que se apoyan las demás pantallas.

- **F3.1 — Grupo de rutas** `/panel-psicologa` con `auth` y nombres `dashboard.*`.
- **F3.2 — Layout** (sección 7): sidebar izquierda, header y contenido. Variables CSS con el color primario del usuario inyectado en `:root`.
- **F3.3 — Menú lateral** (`SidebarComposer`):
  - Accesos directos: Inicio, Citas, Calendario, Pacientes, Historias.
  - Grupo **Blog**: Artículos, Categorías.
  - Grupo **Gestión Web**: Información pública, Servicios, Especialidades, Planes y precios, Preguntas frecuentes, Frases públicas, Imágenes, Logo, Temas.
  - Grupo **Configuración**: Disponibilidad, Protección de datos, Email y notificaciones, Redes sociales, General.
  - Grupos desplegables (`sidebar.js`) que se abren solos si contienen la ruta activa.
- **F3.4 — Header:** buscador, botón Ayuda, campana con contador, botón de tema y avatar/nombre (enlaces que se completan en F14/F15).
- **F3.5 — Componentes comunes:** flash, modal de confirmación, breadcrumbs, paginación y footer.
- **F3.6 — Responsive:** en móvil la sidebar se abre como panel deslizante con fondo oscuro (`responsive.css`).

**Criterio de aceptación:** Todas las pantallas del panel comparten el layout; el menú marca la sección activa y funciona en móvil.

---

### FASE 4 — Inicio, disponibilidad y gestión de citas

#### F4.1 — Página de inicio del panel
- F4.1.1 Tarjetas de estadísticas: citas que quedan hoy, pacientes, artículos publicados y sesiones del mes (datos reales).
- F4.1.2 Próximas citas de hoy (hasta 5) con enlace al detalle, y resumen semanal de la disponibilidad presencial/online.
- F4.1.3 **[EXTRA]** Lista de "primeros pasos" con porcentaje de avance: instalación, disponibilidad, info pública, foto, servicios, FAQ, SMTP y primer artículo; cada paso enlaza a su pantalla.

#### F4.2 — Modelo de citas
- F4.2.1 Migración `citas` y modelo `Cita` (constantes `MODALIDADES`, `ESTADOS`, accessors `estado_label`, `modalidad_label`, relación `paciente`).
- F4.2.2 `CitaService`: duraciones por modalidad, descanso, modo vacaciones, `existeSolapamiento()`, `crear()`, `actualizar()`, `cancelar()` (estado `cancelada` + soft delete) y `generarSlots()`.
- F4.2.3 Regla `NoOverlap` (ignora las canceladas y las eliminadas, y admite `ignoreId`).
- F4.2.4 `CitaPolicy`.

#### F4.3 — Configuración de disponibilidad (`/panel-psicologa/disponibilidad`)
- F4.3.1 Duración de sesión **presencial** y **online** (minutos), por separado.
- F4.3.2 Horario de apertura y cierre. **[EXTRA]** En dos franjas, **mañana** y **tarde**, en lugar de una sola entrada/salida.
- F4.3.3 Cuadrícula de 7 días × huecos por modalidad (`partials/slots-grid`): se marca con clic. Cada hueco dura lo mismo que la sesión y el paso entre huecos es duración + descanso.
- F4.3.4 Descanso entre sesiones por modalidad: interruptor + minutos. Ejemplo: 50 + 10 → 9:00, 10:00…; 50 + 0 → 9:00, 9:50…
- F4.3.5 Aviso en pantalla cuando cambian la duración, el descanso o los horarios: hay que volver a marcar los huecos (`disponibilidad.js`).
- F4.3.6 Guardado: se borran y recrean las filas de `disponibilidades` dentro de una transacción. Mensaje de confirmación.
- F4.3.7 **Modo vacaciones** (interruptor) + **[EXTRA]** mensaje personalizado que se muestra en la web pública.
- F4.3.8 **Periodos de vacaciones:** alta/baja de rangos con `<input type="date">` (`PeriodoVacacionesController`). Se muestra **al lado** del panel de modo vacaciones.
- F4.3.9 **[EXTRA]** Días de antelación reservables (`dias_adelante`, mínimo 7, 60 por defecto).

#### F4.4 — Cálculo de disponibilidad (`ReservaService`)
- F4.4.1 `diasDisponibles(modalidad)`: días de los próximos N con huecos configurados, sin contar los que caen en periodos de vacaciones.
- F4.4.2 `slotsDisponibles(modalidad, fecha)`: nada si está en modo vacaciones, si la fecha ya pasó o si cae en un periodo de vacaciones; quita los huecos que ya han pasado y los que se solapan con citas no canceladas (incluidas las que vienen de días anteriores).
- F4.4.3 Endpoints JSON `/reservas/dias` y `/reservas/slots`, compartidos por la web pública y el panel.

#### F4.5 — Gestión de citas (CRUD)
- F4.5.1 Listado paginado (15) con filtros: próximas/pasadas, modalidad, estado, fechas desde/hasta y búsqueda por nombre o teléfono. Filtrado por AJAX sin recargar (`citas.js`). **[EXTRA]**
- F4.5.2 Crear/editar: nombre, teléfono, email, modalidad, fecha/hora, motivo, estado y notas internas. **[EXTRA]** Modal con calendario de huecos disponibles para elegir la fecha (`cita-form.js`, usa `/reservas/*`).
- F4.5.3 Detalle de la cita.
- F4.5.4 **[EXTRA]** Cambio rápido de estado desde el listado/detalle (`PATCH citas/{cita}/estado`, `cita-estado.js`).
- F4.5.5 **[EXTRA]** Botón "Confirmar por WhatsApp" con mensaje prerrellenado (`whatsapp_confirmacion_url()`).
- F4.5.6 Borrar = cancelar (soft delete) con modal de confirmación.
- F4.5.7 **[PENDIENTE] Duración por modalidad en citas manuales.** `StoreCitaRequest`, `UpdateCitaRequest`, `CalendarioController@index` y `Public\CitaController@datosVista` calculan `fecha_fin` con `CitaService::duracionSesion()`, que lee la clave heredada `disponibilidad.duracion_sesion_min`. Ninguna pantalla escribe esa clave, así que las citas manuales duran siempre 60 min aunque la psicóloga haya configurado otra duración. **Arreglo:** calcular `fecha_fin` con `duracionPresencial()`/`duracionOnline()` según la `modalidad` enviada (backend), pasar las dos duraciones al JS del formulario y del calendario, y recalcular el fin al cambiar la modalidad. Después, eliminar `duracionSesion()` y `calcularFechaFin()`.

**Criterio de aceptación:** La psicóloga configura la disponibilidad y los huecos se reflejan en el panel y en la web. No se pueden crear citas solapadas. Los periodos de vacaciones bloquean los días.

---

### FASE 5 — Calendario del panel

- **F5.1 — Vista** `/panel-psicologa/calendario` con Calendar.js, traducida al español. Vistas mes/semana/día **[EXTRA: la vista elegida se guarda en `localStorage`]**, navegación y selector de mes.
- **F5.2 — Feed JSON** `calendario/eventos?start&end`: citas no canceladas con color por modalidad y estado + eventos extra.
- **F5.3 — Crear cita desde el calendario:** modal con buscador de pacientes, selector de huecos disponibles y creación/vinculación automática del paciente (`POST calendario/citas`).
- **F5.4 — Modal de detalle** de la cita, con cambio de estado y enlace a la ficha (`PATCH calendario/citas/{cita}`).
- **F5.5 — [EXTRA] Eventos extra** (tabla `eventos`): crear, editar y borrar eventos personales (título, descripción, color, todo el día, ubicación) desde el calendario (`EventoController`).
- **F5.6 — [EXTRA] Leyenda de colores** por modalidad y estado.

**Criterio de aceptación:** Las citas creadas en cualquier sitio aparecen en el calendario, y las creadas en el calendario respetan los solapamientos.

---

### FASE 6 — Blog y editor WYSIWYG

- **F6.1 — Categorías:** migración, modelo, CRUD (sin vista de detalle), slug automático (`SlugService`, único), orden. Seeder con 6 categorías por defecto.
- **F6.2 — Artículos:** CRUD con título, slug, extracto, contenido, imagen destacada (optimizada), categoría, estado (borrador/publicado/archivado), fecha de publicación **[EXTRA]** y SEO: meta title y meta description **[EXTRA]**. Listado con búsqueda y filtros. Vista previa de detalle en el panel.
- **F6.3 — Jodit:** copiado en local, inicializado en `textarea[data-wysiwyg]` (`jodit-init.js`) en español. Se usa en artículos, sobre mí, historias y la plantilla de protección de datos. `css/wysiwyg.css` para el contenido.
- **F6.4 — [EXTRA] Subir imágenes dentro del editor** (`POST blog/upload-imagen`), optimizadas.
- **F6.5 — [EXTRA] Saneado del HTML** con HTMLPurifier en los mutators de `Articulo` (contenido y extracto).
- **F6.6 — Scope `publicados()`:** estado publicado y `published_at <= now`.

**Criterio de aceptación:** Artículos con imagen y categoría; el HTML guardado está saneado.

---

### FASE 7 — Pacientes

- **F7.1 — Migración y modelo `Paciente`:** teléfono único y normalizado, DNI **[EXTRA]**, fecha de nacimiento, género, dirección, motivo inicial, notas, origen, soft deletes, `scopeBuscar()` y `nombre_completo`.
- **F7.2 — `PacienteService`:** `findOrCreateByPhone()` con `lockForUpdate` (si el paciente estaba en la papelera, lo restaura) **[EXTRA]**, crear, actualizar, eliminar, restaurar.
- **F7.3 — CRUD de pacientes:** listado paginado con búsqueda y filtro por origen (AJAX), alta manual, edición y detalle con las últimas citas y las 3 historias más recientes.
- **F7.4 — [EXTRA] Papelera** de pacientes (soft delete + restaurar).
- **F7.5 — [EXTRA] Historial de citas** del paciente (`pacientes/{paciente}/citas`), incluidas las canceladas.
- **F7.6 — Buscador de pacientes** (`GET pacientes/buscar`, JSON). Al elegir uno en el formulario de cita se rellenan los datos.
- **F7.7 — Crear el paciente al crear la cita:** si no se elige un paciente existente, `findOrCreateByPhone` con el teléfono de la cita y vinculación de `paciente_id` (formulario de citas y calendario).

**Criterio de aceptación:** Una reserva pública o manual siempre acaba vinculada a un único paciente, identificado por su teléfono normalizado.

---

### FASE 8 — Historias clínicas

- **F8.1 — Modelos** `Historia` y `HistoriaArchivo` (cascada al borrar).
- **F8.2 — CRUD anidado** en `pacientes/{paciente}/historias`: fecha de la sesión, título, contenido con Jodit y adjuntos múltiples (JPG, PNG, WebP o PDF, máx. 10 MB).
- **F8.3 — Adjuntos privados:** guardados en el disco `local` (fuera de `public`) y servidos por una ruta autenticada (`verArchivo`). Las imágenes se optimizan. Se pueden borrar uno a uno.
- **F8.4 — [EXTRA] Listado general** de historias (`/panel-psicologa/historias`) con búsqueda por paciente, accesible desde el menú.
- **F8.5 — Vista de detalle** con galería de imágenes y enlaces a los PDF.

**Criterio de aceptación:** Ningún adjunto clínico es accesible sin sesión.

---

### FASE 9 — Documento de protección de datos (PDF)

- **F9.1 — Plantilla por defecto** RGPD/LOPDGDD sembrada por migración en `proteccion_datos.plantilla_html`.
- **F9.2 — Pantalla de configuración** con Jodit y la lista de variables disponibles (`{{nombre}}`, `{{apellidos}}`, `{{dni}}`, `{{telefono}}`, `{{email}}`, `{{direccion}}`, `{{fecha}}`, `{{psicologa_nombre}}`, `{{psicologa_email}}`, `{{psicologa_telefono}}`, `{{psicologa_colegiado}}` **[EXTRA]**).
- **F9.3 — `PdfPlantillaRenderer`:** sustituye las variables con los valores escapados; `rellenarVacio()` pone líneas en blanco.
- **F9.4 — Descargar la plantilla vacía** en PDF (dompdf).
- **F9.5 — Botón en el detalle del paciente** para descargar el PDF relleno con sus datos.

---

### FASE 10 — Preguntas frecuentes

- **F10.1 — CRUD** de FAQ (pregunta, respuesta, activa).
- **F10.2 — [EXTRA] Reordenar arrastrando** (`POST faqs/reordenar`, `faqs.js`).

---

### FASE 11 — Información pública (lo que se rellenó en el asistente)

- **F11.1 — Perfil público:** eslogan, teléfono y email públicos, nº de colegiado, sobre mí (Jodit), dirección, **[EXTRA]** latitud/longitud para el mapa, foto (subir/eliminar).
- **F11.2 — Servicios:** CRUD con icono (**[EXTRA]** selector visual de iconos), orden y activo.
- **F11.3 — Especialidades (terapias):** CRUD con icono **[EXTRA]**, orden y activo.
- **F11.4 — Planes y precios:** CRUD por tipo online/presencial.

---

### FASE 12 — Temas visuales

- **F12.1 — `ThemeManager`:** descubre los temas por su `theme.json` (`slug`, `name`, `description`, `supports`, `color_palette`, `image_slots`, `preview_colors`) y activa tema + modo.
- **F12.2 — Namespace `theme::`** y `PublicLayoutComposer` (profile, user, features, social, themeSlug, themeMode).
- **F12.3 — `ThemeAssetsController`** para servir los assets de `themes/<slug>/assets` de forma segura.
- **F12.4 — Temas:** CLAUDE.md pide 5. **[EXTRA]** Hay 11: `tema-base` (Tierra Cálida, réplica del tema visual base), `tema-aurora`, `tema-bold`, `tema-clinica`, `tema-minimal`, `tema-modern`, `tema-natural`, `tema-organico`, `tema-sage`, `tema-violeta`, `tema-warm`. Todos con las mismas vistas: `landing`, `multipage/*` y `partials/section-*`.
- **F12.5 — Pantalla de temas:** tarjetas con paleta, botón de activar en landing o multipágina, previsualización en pestaña nueva (`/?preview_theme=slug&preview_mode=…`) con los datos reales, y botón "¿Quieres un diseño personalizado? Pídemelo aquí" → `https://victorroblesweb.es/contacto`.

---

### FASE 13 — Imágenes de la web

- **F13.1 — `ImagenSlotResolver`:** resuelve la imagen de cada hueco (`hero`, `sobre-mi`, `servicios-bg`, `blog-bg`) con esta prioridad: imagen subida compartida (`theme-overrides/shared`) → subida antigua por tema → imagen por defecto del tema.
- **F13.2 — Pantalla de imágenes:** vista previa de cada hueco, subir (optimizada según el hueco) y restaurar la imagen por defecto.
- **F13.3 — [EXTRA] Logo y favicon** (`/panel-psicologa/logo`): elegir imagen, icono de Font Awesome o ninguno; se usa en los temas (`logo_data()`) y como favicon (`_shared/favicon`).

---

### FASE 14 — Apariencia del panel y activación de funcionalidades

- **F14.1 — Modal de tema del panel:** claro/oscuro + selector de color primario. **[EXTRA]** 11 colores en lugar de 8 (incluye el azul por defecto `#2c4a7e`).
- **F14.2 — Persistencia en BD** (`users.theme_preference`, `users.primary_color`) mediante `POST preferencias/tema` (AJAX). Se aplica en el layout antes de pintar la página, sin parpadeo. `dark-theme.css`.
- **F14.3 — Activar/desactivar funcionalidades** (`/configuracion/general`): blog, reservas, FAQ, servicios, sobre mí. **[EXTRA]** Cambio inmediato por AJAX (`PATCH general/feature`). La web pública oculta la sección, la quita del menú y devuelve 404 en su URL.

---

### FASE 15 — Frases, redes, email, perfil privado, buscador y ayuda

- **F15.1 — Frases públicas:** pantalla con un formulario por sección (hero, sobre mí, servicios, especialidades, planes, blog, FAQ, cita). 25 claves con valores por defecto (`FrasesController::DEFAULTS`). Helper `phrase()` en los temas.
- **F15.2 — Redes sociales:** Facebook, Instagram, LinkedIn, X/Twitter, YouTube, TikTok y WhatsApp (URL validada). Se muestran en el footer de los temas.
- **F15.3 — Email y notificaciones:** host, puerto, usuario, contraseña (cifrada con `Crypt`; si se deja vacía, se conserva la anterior), remitente, interruptor de activación y mini-tutorial para Gmail (verificación en 2 pasos + contraseña de aplicación).
- **F15.4 — Perfil privado:** nombre, apellidos, email y teléfono privados, cambio de contraseña (opcional; mínimo 10 caracteres con números y mayúsculas/minúsculas, más confirmación) y avatar. **[EXTRA]** El avatar se guarda en privado, se sirve con una ruta autenticada y se redimensiona con GD.
- **F15.5 — Buscador global:** página de resultados agrupados (pacientes, citas, historias, artículos, FAQ) con un mínimo de 2 caracteres y 20 resultados por grupo.
- **F15.6 — Ayuda:** tutorial en texto por secciones (inicio, citas, calendario, disponibilidad, pacientes, historias, blog, gestión web, protección de datos, email, redes, general, apariencia, notificaciones, buscador). El botón del header tiene efecto hover.
- **F15.7 — Botón "Ver tu web"** en la sidebar (pestaña nueva).
- **F15.8 — [EXTRA] Notificaciones de nuevas reservas:** campana en el header con el contador de reservas públicas no vistas (`HeaderComposer`) y página `notificaciones` que separa nuevas y vistas y marca todas como vistas.

---

### FASE 16 — Web pública: estructura y páginas

- **F16.1 — Layout de tema** con SEO: `<title>`, meta description, Open Graph, JSON-LD `LocalBusiness` **[EXTRA]**, favicon, logo, nav y footer.
- **F16.2 — Nav** con enlaces que dependen del modo (anclas en landing, URLs en multipágina) y de las funcionalidades activas. Botones de **llamar**, **WhatsApp** y **pedir cita**. Email en el footer o en contacto.
- **F16.3 — Modo landing:** una sola página con todas las secciones y scroll suave.
- **F16.4 — Modo multipágina:** `/` (inicio reducido), `/sobre-mi`, `/servicios` (servicios + especialidades + planes), `/blog`, `/preguntas-frecuentes`, `/pide-cita`. En modo landing esas URLs redirigen a su ancla.
- **F16.5 — Página "Pide cita":** a un lado, el formulario de reserva; al otro, "¿Dónde estamos?" con dirección, mapa de Google Maps embebido (por lat/lng o por dirección), teléfono y email (`public/_cita_contacto`).
- **F16.6 — [EXTRA] Política de privacidad** pública (`/politica-de-privacidad`) generada con los datos de la psicóloga.
- **F16.7 — [EXTRA] Ruta alternativa de `/storage`** para hostings en los que el symlink no funciona.

---

### FASE 17 — Reservas públicas

- **F17.1 — Selector de modalidad** presencial/online (solo las que tienen disponibilidad). Si está en modo vacaciones, se muestra el mensaje en lugar del formulario.
- **F17.2 — Calendario de días disponibles** (`/reservas/dias`) y lista de huecos (`/reservas/slots`) (`reserva.js` / `cita-form.js` del tema).
- **F17.3 — Formulario:** nombre (obligatorio, solo letras), teléfono (obligatorio, al menos 9 dígitos, normalizado) y motivo (opcional).
- **F17.4 — [EXTRA] Anti-spam y legalidad:** campo trampa (`website`), pregunta de seguridad (suma cifrada con caducidad de 1 h), aceptación de la política de privacidad y límite de 3 reservas por hora por IP (desactivado en local).
- **F17.5 — `ReservaService::registrar()`** en una transacción: vuelve a comprobar los solapamientos, crea o recupera el paciente por teléfono y **[EXTRA]** impide más de una cita por paciente y día.
- **F17.6 — Modal de éxito** con el resumen de la cita y un botón "Añadir a Google Calendar" (enlace `calendar.google.com/calendar/render?action=TEMPLATE…`, sin API).
- **F17.7 — Email a la psicóloga** (`NuevaCitaMail`, vista `emails/nueva-cita`) con un transporte SMTP creado con los ajustes guardados. Se envía a la propia cuenta con el alias `+notificaciones` **[EXTRA]**. Si falla, se registra en el log y la reserva no se interrumpe.

---

### FASE 18 — Blog público y redes sociales

- **F18.1 — Listado del blog** paginado (9) con filtro por categoría. **[EXTRA]** Paginación y filtro por AJAX con `blog-fragment` (`blog-ajax.js`).
- **F18.2 — Detalle del artículo** con **[EXTRA]** artículos relacionados de la misma categoría y SEO del artículo (meta title/description, og:image).
- **F18.3 — [EXTRA] Feed RSS** (`/blog/rss.xml`).
- **F18.4 — Iconos de redes sociales** en el footer de todos los temas (solo las que tienen URL).
- **F18.5 — Respetar `features.blog_enabled`** (404 y enlaces ocultos).

---

### FASE 19 — Calidad, limpieza y puesta en producción *(añadida tras la auditoría)*

Tareas que el código actual no cumple o que conviene cerrar antes de dar el proyecto por terminado.

- **F19.1 — [PENDIENTE] Quitar `innerHTML` de los JS de los temas** (norma de CLAUDE.md): `themes/*/assets/js/blog-ajax.js`, `themes/tema-aurora/assets/js/main.js`, `themes/tema-base/assets/js/reserva.js`. Usar `replaceChildren()` para vaciar y `DOMParser` + `importNode` para insertar el fragmento del blog.
- **F19.2 — [PENDIENTE] Borrar código muerto:**
  - `TemaController::actualizarLogo()` y `preview()`, rutas `dashboard.temas.logo` / `dashboard.temas.preview` y la vista `dashboard/temas/preview.blade.php` (el logo lo gestiona `LogoController`; la previsualización usa `?preview_theme`).
  - `resources/views/welcome.blade.php`, `resources/views/public/placeholder.blade.php`, `resources/css`, `resources/js`, `vite.config.js`, `tailwind.config.js`, `postcss.config.js` y las dependencias npm sin uso.
  - Scripts de un solo uso en `database/seeders/` (`scaffold-themes.php`, `patch-navs.php`, `apply-phrases.php`, `gen-sql.php`, que además apuntan a una ruta antigua `cms/`): moverlos a `scripts/` o eliminarlos.
  - `public/sw.js` y el bloque del layout que da de baja el service worker: mantenerlos solo mientras queden navegadores con el SW antiguo registrado y quitarlos después.
- **F19.3 — [PENDIENTE] Tests automáticos** (hoy solo existen los `ExampleTest`): feature tests de login (3 campos + límite de intentos), protección de rutas del panel, solapamientos (`NoOverlap`, `ReservaService::registrar`), cálculo de huecos con descanso y vacaciones, `PhoneHelper`, reserva pública (captcha, honeypot, 1 cita por día) y PDF de protección de datos.
- **F19.4 — [PENDIENTE] SEO técnico:** `robots.txt` con `Disallow: /panel-psicologa`, `/acceso-psicologa` y `/instalacion`; `sitemap.xml` dinámico (páginas activas + artículos publicados); `<link rel="canonical">`.
- **F19.5 — [PENDIENTE] `.env.example`** con `APP_LOCALE=es`, `APP_TIMEZONE=Europe/Madrid` y `APP_FAKER_LOCALE=es_ES` (hoy trae `en`/`UTC`). Opcional: que el asistente también los escriba en el `.env`.
- **F19.6 — [PENDIENTE] README del proyecto** (sustituir el de Laravel): requisitos, instalación, asistente, despliegue (symlink de storage, permisos, `APP_URL`), cómo añadir un tema nuevo (estructura de `theme.json` + vistas obligatorias) y cómo cargar los datos de prueba.
- **F19.7 — [PENDIENTE] Restringir `?preview_theme`** a sesiones autenticadas (hoy cualquier visitante puede ver otro tema añadiendo el parámetro).
- **F19.8 — [PENDIENTE] Ficheros de seguimiento** que exige CLAUDE.md: `prompts.md` (y `tareas.md`, ahora sustituido por `project-map.md`; decidir y actualizar CLAUDE.md).

---

## 9. Optimizaciones recomendadas

Mejoras opcionales (no bloquean). El agente debe proponerlas antes de aplicarlas.

1. **Caché de `settings`.** `Setting::get()` lanza una consulta por clave y una página pública lee más de 30. Cargar todos los ajustes una vez por petición (o `Cache::rememberForever` que se invalide en `set()`).
2. **Eventos extra que bloqueen huecos.** Hoy un evento personal del calendario no impide reservar a esa hora. Añadir una opción "bloquea disponibilidad" y tenerla en cuenta en `slotsDisponibles()` y `NoOverlap`.
3. **Botón "Enviar email de prueba"** en Email y notificaciones, para validar el SMTP sin esperar a una reserva.
4. **Email de notificación en cola** (`ShouldQueue`), para que la respuesta de la reserva no dependa del SMTP.
5. **Unificar `User::first()` / `Profile::first()`** en un servicio o view composer compartido (hoy se repite en varios controladores).
6. **Borrar las claves heredadas** `disponibilidad.duracion_sesion_min`, `hora_apertura` y `hora_cierre` con una migración, una vez resuelto F4.5.7.
7. **Normalizar el teléfono en `Cita`** con un mutator, para no depender de que cada FormRequest lo haga.
8. **Partials comunes a los temas.** Los 11 temas repiten nav, footer y sección de cita casi iguales. Sacar la lógica (URLs, teléfono limpio, WhatsApp) a un view composer o componente Blade y dejar en cada tema solo el marcado.
9. **CSP y cabeceras de seguridad** (`X-Frame-Options`, `Referrer-Policy`) con un middleware.
10. **Pedir la contraseña actual** en el perfil privado antes de cambiar la contraseña, el email o el teléfono (son las credenciales de acceso).

---

## 10. Checklist de verificación por fase

Antes de dar por cerrada cualquier fase:

- [ ] Las rutas nuevas del panel están bajo `/panel-psicologa` y con `auth`.
- [ ] Los formularios tienen FormRequest con mensajes en español.
- [ ] Hay mensaje flash tras cada acción y empty state en cada listado.
- [ ] Los borrados usan el modal de confirmación propio.
- [ ] No hay `var`, `alert`/`confirm`/`prompt` ni `innerHTML` en el JS nuevo.
- [ ] El CSS nuevo está en su fichero de sección, en `rem`, y se ve bien en móvil y en modo oscuro.
- [ ] Siguen funcionando: login, crear cita manual, reserva pública, calendario y cambio de tema.
- [ ] Si la funcionalidad usa disponibilidad: respeta la duración por modalidad, el descanso, el modo vacaciones y los periodos de vacaciones.
- [ ] `project-map.md` actualizado.
