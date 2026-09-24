<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
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
        return file_exists(storage_path('app/installed.lock'));
    }

    public function markInstalled(): void
    {
        $dir = storage_path('app');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($dir . DIRECTORY_SEPARATOR . 'installed.lock', now()->toIso8601String());
        $this->writeEnv(['APP_INSTALLED' => 'true']);
    }

    private function escapeEnvValue(string $value): string
    {
        if ($value === '' || preg_match('/\s|"|#/', $value)) {
            return '"' . str_replace('"', '\"', $value) . '"';
        }
        return $value;
    }
}
