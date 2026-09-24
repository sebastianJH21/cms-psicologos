<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordRecoveryController;
use App\Http\Controllers\Dashboard\Blog\ArticuloController;
use App\Http\Controllers\Dashboard\Blog\BlogUploadController;
use App\Http\Controllers\Dashboard\Blog\CategoriaBlogController;
use App\Http\Controllers\Dashboard\CalendarioController;
use App\Http\Controllers\Dashboard\CitaController;
use App\Http\Controllers\Dashboard\Configuracion\ConfiguracionGeneralController;
use App\Http\Controllers\Dashboard\Configuracion\EmailNotificacionesController;
use App\Http\Controllers\Dashboard\Configuracion\PerfilController;
use App\Http\Controllers\Dashboard\Configuracion\RedesSocialesController;
use App\Http\Controllers\Dashboard\Configuracion\PlanController;
use App\Http\Controllers\Dashboard\Configuracion\ServicioController;
use App\Http\Controllers\Dashboard\Configuracion\TerapiaController;
use App\Http\Controllers\Dashboard\DisponibilidadController;
use App\Http\Controllers\Dashboard\PeriodoVacacionesController;
use App\Http\Controllers\Dashboard\EventoController;
use App\Http\Controllers\Dashboard\FaqController;
use App\Http\Controllers\Dashboard\HistoriaController;
use App\Http\Controllers\Dashboard\HomeController as DashboardHomeController;
use App\Http\Controllers\Dashboard\PerfilPrivadoController;
use App\Http\Controllers\Dashboard\PacienteController;
use App\Http\Controllers\Dashboard\ProteccionDatosController;
use App\Http\Controllers\Dashboard\ImagenesController;
use App\Http\Controllers\Dashboard\TemaController;
use App\Http\Controllers\Install\WizardController;
use App\Http\Controllers\Public\BlogController as PublicBlogController;
use App\Http\Controllers\Public\CitaController as PublicCitaController;
use App\Http\Controllers\Public\FaqController as PublicFaqController;
use App\Http\Controllers\Public\HomeController as PublicHomeController;
use App\Http\Controllers\Public\PrivacidadController as PublicPrivacidadController;
use App\Http\Controllers\Public\ServiciosController as PublicServiciosController;
use App\Http\Controllers\Public\SobreMiController as PublicSobreMiController;
use App\Http\Controllers\Public\StoragePublicController;
use App\Http\Controllers\Public\ThemeAssetsController;
use App\Services\InstallerService;
use Illuminate\Support\Facades\Route;

// Tema assets (imágenes, CSS, JS, etc)
Route::get('/theme-assets/{slug}/{path}', [ThemeAssetsController::class, 'serve'])
    ->where('slug', '[a-z0-9-]+')
    ->where('path', '.+')
    ->name('theme.asset');

// Fallback de storage público: solo se ejecuta si el servidor web no
// encuentra el fichero físico (symlink public/storage ausente o roto).
Route::get('/storage/{path}', [StoragePublicController::class, 'serve'])
    ->where('path', '.+')
    ->name('storage.public');

// Web pública
Route::get('/', [PublicHomeController::class, 'index'])->name('public.home');
Route::middleware('installed')->group(function () {
    Route::get('/sobre-mi', [PublicSobreMiController::class, 'index'])->name('public.sobre-mi');
    Route::get('/servicios', [PublicServiciosController::class, 'index'])->name('public.servicios');
    Route::get('/blog', [PublicBlogController::class, 'index'])->name('public.blog');
    Route::get('/blog/rss.xml', [PublicBlogController::class, 'rss'])->name('public.blog.rss');
    Route::get('/blog/{slug}', [PublicBlogController::class, 'show'])->name('public.blog.show');
    Route::get('/preguntas-frecuentes', [PublicFaqController::class, 'index'])->name('public.faq');
    Route::get('/pide-cita', [PublicCitaController::class, 'index'])->name('public.cita');
    Route::get('/politica-de-privacidad', [PublicPrivacidadController::class, 'index'])->name('public.privacidad');
});

Route::middleware('installed')->prefix('reservas')->name('public.reservas.')->group(function () {
    Route::get('slots', [PublicCitaController::class, 'slots'])->name('slots');
    Route::get('dias', [PublicCitaController::class, 'diasDisponibles'])->name('dias');
    Route::post('crear', [PublicCitaController::class, 'reservar'])->name('crear');
});

Route::middleware('not_installed')->prefix('instalacion')->name('install.')->group(function () {
    Route::get('/', [WizardController::class, 'index'])->name('index');
    Route::get('/paso/{n}', [WizardController::class, 'show'])->whereNumber('n')->name('step.show');
    Route::post('/paso/{n}', [WizardController::class, 'process'])->whereNumber('n')->name('step.process');
    Route::post('/test-db', [WizardController::class, 'testDb'])->name('test-db');
});

Route::get('/instalacion/completado', [WizardController::class, 'completed'])->name('install.completed');

// Recuperación de contraseña (solo local)
Route::middleware('only_local')->group(function () {
    Route::get('/recuperar-pwd', [PasswordRecoveryController::class, 'show'])->name('recuperar-pwd.show');
    Route::post('/recuperar-pwd', [PasswordRecoveryController::class, 'recover'])->name('recuperar-pwd.recover');
});

Route::middleware('installed')->group(function () {
    Route::middleware('guest_psicologa')->group(function () {
        Route::get('/acceso-psicologa', [LoginController::class, 'show'])->name('login');
        Route::post('/acceso-psicologa', [LoginController::class, 'store'])->name('login.attempt');
    });

    Route::post('/logout', [LoginController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');

    Route::middleware('auth')->prefix('panel-psicologa')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardHomeController::class, 'index'])->name('home');

        Route::get('notificaciones', [\App\Http\Controllers\Dashboard\NotificacionesController::class, 'index'])->name('notificaciones.index');
        Route::get('buscador', [\App\Http\Controllers\Dashboard\BuscadorController::class, 'index'])->name('buscador.index');
        Route::get('ayuda', [\App\Http\Controllers\Dashboard\AyudaController::class, 'index'])->name('ayuda.index');

        Route::get('perfil-privado', [PerfilPrivadoController::class, 'edit'])->name('perfil-privado.edit');
        Route::put('perfil-privado', [PerfilPrivadoController::class, 'update'])->name('perfil-privado.update');
        Route::delete('perfil-privado/avatar', [PerfilPrivadoController::class, 'eliminarAvatar'])->name('perfil-privado.avatar.destroy');
        Route::get('perfil-privado/avatar', [PerfilPrivadoController::class, 'servirAvatar'])->name('perfil-privado.avatar');

        Route::get('disponibilidad', [DisponibilidadController::class, 'edit'])->name('disponibilidad.edit');
        Route::put('disponibilidad', [DisponibilidadController::class, 'update'])->name('disponibilidad.update');
        Route::post('disponibilidad/periodos-vacaciones', [PeriodoVacacionesController::class, 'store'])->name('periodos-vacaciones.store');
        Route::delete('disponibilidad/periodos-vacaciones/{periodo}', [PeriodoVacacionesController::class, 'destroy'])->name('periodos-vacaciones.destroy');

        Route::patch('citas/{cita}/estado', [CitaController::class, 'cambiarEstado'])->name('citas.estado');
        Route::resource('citas', CitaController::class)->parameters(['citas' => 'cita']);

        Route::get('calendario', [CalendarioController::class, 'index'])->name('calendario.index');
        Route::get('calendario/eventos', [CalendarioController::class, 'eventos'])->name('calendario.eventos');
        Route::post('calendario/citas', [CalendarioController::class, 'crear'])->name('calendario.crear');
        Route::patch('calendario/citas/{cita}', [CalendarioController::class, 'actualizar'])->name('calendario.actualizar');
        Route::post('calendario/eventos-extra', [EventoController::class, 'store'])->name('calendario.eventos-extra.store');
        Route::patch('calendario/eventos-extra/{evento}', [EventoController::class, 'update'])->name('calendario.eventos-extra.update');
        Route::delete('calendario/eventos-extra/{evento}', [EventoController::class, 'destroy'])->name('calendario.eventos-extra.destroy');

        Route::get('historias', [HistoriaController::class, 'indexGeneral'])->name('historias.index');

        Route::get('pacientes/buscar', [PacienteController::class, 'buscar'])->name('pacientes.buscar');
        Route::post('pacientes/{id}/restaurar', [PacienteController::class, 'restore'])
            ->whereNumber('id')
            ->name('pacientes.restore');
        Route::get('pacientes/{paciente}/citas', [PacienteController::class, 'citas'])
            ->name('pacientes.citas');

        Route::prefix('pacientes/{paciente}')->name('pacientes.')->group(function () {
            Route::get('historias', [HistoriaController::class, 'index'])->name('historias.index');
            Route::get('historias/crear', [HistoriaController::class, 'create'])->name('historias.create');
            Route::post('historias', [HistoriaController::class, 'store'])->name('historias.store');
            Route::get('historias/{historia}', [HistoriaController::class, 'show'])->name('historias.show');
            Route::get('historias/{historia}/editar', [HistoriaController::class, 'edit'])->name('historias.edit');
            Route::put('historias/{historia}', [HistoriaController::class, 'update'])->name('historias.update');
            Route::delete('historias/{historia}', [HistoriaController::class, 'destroy'])->name('historias.destroy');
            Route::get('historias/{historia}/archivos/{archivo}', [HistoriaController::class, 'verArchivo'])->name('historias.archivos.show');
            Route::delete('historias/{historia}/archivos/{archivo}', [HistoriaController::class, 'destroyArchivo'])->name('historias.archivos.destroy');
        });

        Route::resource('pacientes', PacienteController::class)
            ->parameters(['pacientes' => 'paciente']);

        // Protección de datos (PDF)
        Route::prefix('configuracion/proteccion-datos')->name('configuracion.proteccion-datos.')->group(function () {
            Route::get('/', [ProteccionDatosController::class, 'edit'])->name('edit');
            Route::put('/', [ProteccionDatosController::class, 'update'])->name('update');
            Route::get('descargar-vacio', [ProteccionDatosController::class, 'descargarVacio'])->name('descargar-vacio');
        });
        Route::get('pacientes/{paciente}/proteccion-datos.pdf', [ProteccionDatosController::class, 'descargarRelleno'])
            ->name('pacientes.proteccion-datos');

        // FAQs
        Route::post('faqs/reordenar', [FaqController::class, 'reordenar'])->name('faqs.reordenar');
        Route::resource('faqs', FaqController::class)->parameters(['faqs' => 'faq']);

        // Configuración pública
        Route::prefix('configuracion')->name('configuracion.')->group(function () {
            Route::get('perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
            Route::put('perfil', [PerfilController::class, 'update'])->name('perfil.update');
            Route::delete('perfil/foto', [PerfilController::class, 'eliminarFoto'])->name('perfil.foto.destroy');

            Route::get('general', [ConfiguracionGeneralController::class, 'edit'])->name('general.edit');
            Route::put('general', [ConfiguracionGeneralController::class, 'update'])->name('general.update');
            Route::patch('general/feature', [ConfiguracionGeneralController::class, 'toggleFeature'])->name('general.feature.toggle');

            Route::get('redes-sociales', [RedesSocialesController::class, 'edit'])->name('redes-sociales.edit');
            Route::put('redes-sociales', [RedesSocialesController::class, 'update'])->name('redes-sociales.update');

            Route::get('email-notificaciones', [EmailNotificacionesController::class, 'edit'])->name('email-notificaciones.edit');
            Route::put('email-notificaciones', [EmailNotificacionesController::class, 'update'])->name('email-notificaciones.update');

            Route::resource('servicios', ServicioController::class)->parameters(['servicios' => 'servicio']);
            Route::resource('terapias', TerapiaController::class)->parameters(['terapias' => 'terapia']);
            Route::resource('planes', PlanController::class)->parameters(['planes' => 'plan']);
        });

        // Preferencias de tema (AJAX)
        Route::post('preferencias/tema', [ConfiguracionGeneralController::class, 'guardarTema'])->name('preferencias.tema');

        // Imágenes públicas
        Route::get('imagenes', [ImagenesController::class, 'index'])->name('imagenes.index');
        Route::post('imagenes/{slot}', [ImagenesController::class, 'update'])->name('imagenes.update');
        Route::delete('imagenes/{slot}', [ImagenesController::class, 'restore'])->name('imagenes.restore');

        // Logo y favicon
        Route::get('logo', [\App\Http\Controllers\Dashboard\LogoController::class, 'edit'])->name('logo.edit');
        Route::put('logo', [\App\Http\Controllers\Dashboard\LogoController::class, 'update'])->name('logo.update');

        // Frases de las secciones públicas
        Route::get('frases-publicas', [\App\Http\Controllers\Dashboard\FrasesPublicasController::class, 'index'])
            ->name('frases-publicas.index');
        Route::put('frases/{seccion}', [\App\Http\Controllers\Dashboard\FrasesController::class, 'update'])
            ->whereIn('seccion', ['hero','about','servicios','especialidades','planes','blog','faq','cita'])
            ->name('frases.update');

        // Temas visuales
        Route::get('temas', [TemaController::class, 'index'])->name('temas.index');
        Route::post('temas/logo', [TemaController::class, 'actualizarLogo'])->name('temas.logo');
        Route::post('temas/{slug}/activar', [TemaController::class, 'activar'])->name('temas.activar');
        Route::get('temas/{slug}/preview', [TemaController::class, 'preview'])->name('temas.preview');

        Route::prefix('blog')->name('blog.')->group(function () {
            Route::post('upload-imagen', [BlogUploadController::class, 'imagen'])->name('upload-imagen');

            Route::resource('articulos', ArticuloController::class)
                ->parameters(['articulos' => 'articulo']);

            Route::resource('categorias', CategoriaBlogController::class)
                ->parameters(['categorias' => 'categoria'])
                ->except(['show']);
        });
    });
});
