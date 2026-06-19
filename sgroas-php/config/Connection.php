<?php
// config/Connection.php
// Clase Singleton para manejo de conexión PDO

declare(strict_types=1);

require_once __DIR__ . '/database.php';

class Connection
{
    private static ?PDO $instance = null;

    // Constructor privado: patrón Singleton
    private function __construct() {}

    /**
     * Retorna la instancia única de PDO.
     * Lanza PDOException si la conexión falla.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            DB_HOST,
            DB_PORT,
            DB_NAME
        );

            self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,  // Prepared statements reales
                PDO::ATTR_PERSISTENT         => false,
            ]);

            // Forzar codificación UTF-8
            self::$instance->exec("SET NAMES 'UTF8'");
        }

        return self::$instance;
    }

    // Prevenir clonación del Singleton
    private function __clone() {}

    // Prevenir deserialización del Singleton
    public function __wakeup(): void
    {
        throw new \RuntimeException('Cannot unserialize a singleton.');
    }
}
