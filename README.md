# PsicoCMS

CMS a medida para psicólogas independientes: web pública administrable (con 11 temas visuales), sistema de reservas de citas, blog, y un panel privado para gestionar pacientes, historias clínicas, disponibilidad, calendario y toda la información pública de la consulta.

Aplicación monolítica en Laravel, sin frameworks de JavaScript ni herramientas de build (HTML5, CSS3 y JavaScript nativos).

---

## Requisitos

- PHP 8.2 o superior, con las extensiones: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd` o `imagick`, `dom`, `ctype`, `curl`.
- MySQL o MariaDB.
- Composer.
- Servidor web (Apache/XAMPP) o el servidor embebido de PHP.

No hace falta Node.js ni npm: no hay build de assets.

---

## Instalación en local (XAMPP)

1. Copia el proyecto dentro de `htdocs/` (o crea un virtual host apuntando a la carpeta `public/`).
2. Instala las dependencias de PHP:
   ```bash
   composer install
   ```
3. Crea el fichero de entorno y la clave de la aplicación:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Crea una base de datos vacía en MySQL/MariaDB (puede hacerse desde phpMyAdmin; no hace falta crear tablas, eso lo hace el asistente).
5. Arranca el servidor:
   ```bash
   php artisan serve
   ```
   o usa el virtual host de XAMPP apuntando a `public/`.
6. Abre la URL de la aplicación en el navegador. Como todavía no está instalada, redirige automáticamente a `/instalacion`.

### Asistente de instalación (`/instalacion`)

El asistente, en 6 pasos, deja la aplicación lista para usar:

1. **Base de datos**: host, puerto, nombre, usuario y contraseña. El botón "Probar conexión" comprueba los datos antes de continuar. Al avanzar, crea la base de datos si no existe, escribe la configuración en `.env` y ejecuta las migraciones.
2. **Cuenta de acceso**: nombre, apellidos, email, teléfono y contraseña (los tres primeros datos, junto con la contraseña, son el login de la psicóloga en `/acceso-psicologa`).
3. **Datos públicos**: eslogan, teléfono y email de citas, nº de colegiado, sobre mí y dirección de la consulta.
4. **Servicios, especialidades y planes y precios** (online y presencial).
5. **Tema visual**: elegir uno de los temas disponibles y el formato (landing o multipágina).
6. **Foto de perfil** (opcional, preferiblemente sin fondo).

Al terminar, crea la marca `storage/app/installed.lock`, enlaza `storage:link` y deja la sesión iniciada en el panel (`/panel-psicologa`). Mientras no exista esa marca, cualquier URL redirige a `/instalacion`; una vez instalada, `/instalacion` deja de estar accesible.

Toda la información del asistente puede ampliarse o corregirse después desde el panel.

---

## Despliegue en producción / hosting compartido

1. Sube todo el proyecto **incluyendo `vendor/`** (composer no siempre está disponible en hostings compartidos; si lo está, basta con `composer install --no-dev --optimize-autoloader`).
2. Si el hosting permite cambiar el document root, apúntalo a la carpeta `public/`. Si no lo permite, deja el fichero `.htaccess` de la raíz del proyecto (ya incluido), que redirige las peticiones a `public/`.
3. Copia `.env.example` a `.env`, genera la clave (`php artisan key:generate`) y ajusta `APP_URL`, `APP_ENV=production` y `APP_DEBUG=false`.
4. Da permisos de escritura a `storage/` y `bootstrap/cache/`.
5. Entra por la URL pública: el asistente de instalación arranca automáticamente.
6. Tras instalar, comprueba que `public/storage` existe como enlace simbólico a `storage/app/public`. Si el hosting no permite symlinks, la aplicación sirve esos ficheros igualmente por una ruta alternativa (`/storage/{ruta}`), sin necesitar el enlace.
7. Opcional, para ganar rendimiento: `php artisan config:cache`, `php artisan route:cache` y `php artisan view:cache` (recuerda limpiarlas con `php artisan optimize:clear` cada vez que cambies `.env` o rutas).

### Copias de seguridad

Haz copia periódica de:
- La base de datos completa.
- La carpeta `storage/app` (fotos, avatar, adjuntos de historias clínicas e imágenes subidas).

---

## Notificaciones por email (Gmail)

Desde el panel, en **Configuración → Email y notificaciones**, se configura una cuenta de Gmail para avisar de las nuevas reservas. Necesita una **contraseña de aplicación** (no la contraseña normal de la cuenta):

1. Activa la verificación en dos pasos en la cuenta de Google.
2. Entra en "Contraseñas de aplicación" dentro de la configuración de seguridad de Google.
3. Crea una para "PsicoCMS" y copia los 16 caracteres en el panel.

La contraseña se guarda cifrada en la base de datos.

---

## Cómo crear un tema visual nuevo

Los temas viven en `themes/<slug>/` y son totalmente independientes entre sí (cada uno con su propio CSS; no hay herencia de vistas entre temas). La forma más simple de crear uno es duplicar un tema existente (por ejemplo `themes/tema-base/`) y ajustarlo:

```
themes/<slug>/
├── theme.json                  (manifiesto del tema)
├── captura.jpg                 (miniatura para la pantalla de temas del panel)
├── assets/
│   ├── css/                    (todo el CSS del tema, propio y no compartido)
│   ├── js/                     (JS nativo del tema)
│   └── img/                    (imágenes por defecto del tema)
└── views/
    ├── layout.blade.php        (head, SEO, cabecera y pie comunes)
    ├── landing.blade.php       (modo landing: una sola página)
    ├── multipage/              (una vista por página en modo multipágina)
    └── partials/                (secciones reutilizadas por landing y multipage)
```

`theme.json` obligatorio:

```json
{
    "slug": "mi-tema",
    "name": "Nombre visible del tema",
    "description": "Una frase que lo describe.",
    "supports": ["landing", "multipage"],
    "color_palette": { "primary": "#...", "secondary": "#...", "accent": "#...", "bg": "#...", "bg_alt": "#...", "text": "#...", "text_light": "#..." },
    "image_slots": ["hero", "sobre-mi", "servicios-bg", "blog-bg"],
    "preview_colors": ["#...", "#...", "#...", "#..."]
}
```

Un tema aparece automáticamente en el panel (**Mi web → Temas**) en cuanto tiene un `theme.json` válido: no hace falta registrarlo en ningún otro sitio. Los assets se sirven de forma segura por `/theme-assets/{slug}/{ruta}` (helper `theme_asset()` en las vistas). Usa siempre `frase()`, `imagen_sitio()` y los modelos correspondientes para el contenido: ningún texto fijo debe ir escrito directamente en las vistas del tema.

---

## Tests automáticos

```bash
php artisan test
```

Los tests usan SQLite en memoria (configurado en `phpunit.xml`) y no tocan la base de datos real ni el `storage/app/installed.lock` del proyecto instalado. Cubren: login (y su límite de intentos), protección de las rutas del panel, cálculo de huecos de disponibilidad (duración y descanso entre sesiones), solapamientos, periodos de vacaciones y modo vacaciones, reserva pública (con su anti-spam), y la generación de los PDF de protección de datos.

---

## Documentación del proyecto

- [CLAUDE.md](CLAUDE.md) — especificación funcional (qué construir).
- [plan-implementacion.md](plan-implementacion.md) — arquitectura real y desglose por fases (cómo está construido).
- [project-map.md](project-map.md) — estado actual de cada fase y tarea.
- [prompts.md](prompts.md) — historial de peticiones del proyecto.
