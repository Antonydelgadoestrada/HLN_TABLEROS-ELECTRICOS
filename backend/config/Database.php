<?php
/**
 * Clase de conexión a base de datos con PDO
 * Singleton pattern para una única conexión
 */

class Database {
    private static ?PDO $instance = null;
    private string $host = 'localhost';
    private string $db = 'armado_tableros';
    private string $user = 'root';
    private string $password = '';
    private string $charset = 'utf8mb4';

    private function __construct() {}

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $db = new self();
            try {
                self::$instance = new PDO(
                    "mysql:host={$db->host};dbname={$db->db};charset={$db->charset}",
                    $db->user,
                    $db->password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                // En desarrollo mostrar error, en producción solo loguear
                if ($_ENV['APP_ENV'] === 'development') {
                    die('Error de conexión: ' . $e->getMessage());
                } else {
                    error_log('Database Error: ' . $e->getMessage());
                    die('Error en la base de datos. Por favor, intente más tarde.');
                }
            }
        }
        return self::$instance;
    }

    // Evitar clonación
    private function __clone() {}
    
    private function __wakeup() {}
}
?>
