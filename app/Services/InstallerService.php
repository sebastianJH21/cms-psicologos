<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;
use PDOException;
use Throwable;

class InstallerService
{
    public function testConnection(string $host, int $port, string $username, string $password): array
    {
        try {
            $dsn = "mysql:host={$host};port={$port}";
            new PDO($dsn, $username, $password, [PDO::ATTR_TIMEOUT => 5]);
            return ['ok' => true];
        } catch (PDOException $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function createDatabaseIfNotExists(string $host, int $port, string $username, string $password, string $database): array
    {
        try {
            $dsn = "mysql:host={$host};port={$port}";
            $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $safe = str_replace('`', '', $database);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$safe}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            return ['ok' => true];
        } catch (PDOException $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function writeEnv(array $values): void
    {
        $path = base_path('.env');
        $content = file_get_contents($path);

        foreach ($values as $key => $value) {
            $escaped = $this->escapeEnvValue($value);
            $pattern = "/^{$key}=.*$/m";
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$escaped}", $content);
            } else {
                $content .= "\n{$key}={$escaped}";
            }
        }

        file_put_contents($path, $content);
    }

    public function applyDbConfig(string $host, int $port, string $database, string $username, string $password): void
    {
        Config::set('database.connections.mysql.host', $host);
        Config::set('database.connections.mysql.port', $port);
        Config::set('database.connections.mysql.database', $database);
        Config::set('database.connections.mysql.username', $username);
        Config::set('database.connections.mysql.password', $password);
        DB::purge('mysql');
        DB::reconnect('mysql');
    }

    public function runMigrations(): array
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            return ['ok' => true, 'output' => Artisan::output()];
        } catch (Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function isInstalled(): bool
    {
        // En tests se asume siempre instalado: así las pruebas del panel y de
        // la web pública no dependen de si el test concreto ya ha creado una
        // `User`, y nunca leen ni escriben storage/app/installed.lock (el
        // real, compartido con el entorno de desarrollo).
        if (app()->environment('testing')) {
            return true;
        }

        if (file_exists($this->lockPath())) {
            return true;
        }

        // Autorrecuperación: la marca es un fichero físico y puede perderse
        // (copia del proyecto, limpieza manual, etc.) sin que la base de datos
        // deje de tener la instalación real. Si ya existe la cuenta de la
        // psicóloga, la instalación es válida: se regenera la marca para no
        // volver a pedir el asistente con datos reales ya configurados.
        try {
            if (Schema::hasTable('users') && User::query()->exists()) {
                $this->markInstalled();
                return true;
            }
        } catch (Throwable) {
            // Sin conexión a la base de datos todavía (antes del paso 1 del
            // asistente): no se puede comprobar, se asume no instalado.
        }

        return false;
    }

    public function markInstalled(): void
    {
        $path = $this->lockPath();
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($path, now()->toIso8601String());

        if (!app()->environment('testing')) {
            $this->writeEnv(['APP_INSTALLED' => 'true']);
        }
    }

    /**
     * Ruta de la marca de instalación. En entorno de tests usa una ruta propia
     * (storage/framework/testing/) para no leer ni escribir nunca el fichero
     * real del proyecto en desarrollo (storage/app/installed.lock).
     */
    private function lockPath(): string
    {
        if (app()->environment('testing')) {
            return storage_path('framework/testing/installed.lock');
        }

        return storage_path('app/installed.lock');
    }

    private function escapeEnvValue(string $value): string
    {
        if ($value === '' || preg_match('/\s|"|#/', $value)) {
            return '"' . str_replace('"', '\"', $value) . '"';
        }
        return $value;
    }
}
