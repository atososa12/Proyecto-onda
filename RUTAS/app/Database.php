<?php
// app/Database.php
class Database {
    private static ?PDO $conn = null;

    private static function loadEnv(string $path): void {
        if (!file_exists($path)) return;
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (str_starts_with(trim($line), '#')) continue;
            [$k, $v] = array_pad(explode('=', $line, 2), 2, '');
            $_ENV[trim($k)] = trim($v);
        }
    }

    public static function get(): PDO {
        if (self::$conn) return self::$conn;

        // Cargar .env en la raíz del proyecto
        $root = dirname(__DIR__);
        self::loadEnv($root.'/.env');

        $host = $_ENV['DB_HOST']      ?? '127.0.0.1';
        $port = $_ENV['DB_PORT']      ?? '3306';
        $db   = $_ENV['DB_NAME']      ?? 'TransporteHistorico';
        $user = $_ENV['DB_USER']      ?? 'root';
        $pass = $_ENV['DB_PASSWORD']  ?? '';
        $ch   = $_ENV['DB_CHARSET']   ?? 'utf8mb4';

        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$ch}";
        $opts = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        self::$conn = new PDO($dsn, $user, $pass, $opts);
        return self::$conn;
    }
}
