<?php
/**
 * Archivo de configuración global
 */

// Definir entorno (development | production)
define('APP_ENV', 'development');
define('DEBUG_MODE', APP_ENV === 'development');

// Headers de seguridad
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\'; style-src \'self\' \'unsafe-inline\';');

// Errores
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../../logs/error.log');
}

// Crear carpeta de logs si no existe
if (!is_dir(__DIR__ . '/../../logs')) {
    mkdir(__DIR__ . '/../../logs', 0755, true);
}

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS
ini_set('session.cookie_samesite', 'Strict');

// Zona horaria
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Cargar clase de base de datos
require_once __DIR__ . '/Database.php';

/**
 * Validar y sanitizar entrada de usuario
 */
function sanitizeInput($input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validar email
 */
function isValidEmail($email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validar teléfono (formato básico)
 */
function isValidPhone($phone): bool {
    return preg_match('/^[\d\s\-\+\(\)]{10,}$/', $phone);
}

/**
 * Respuesta JSON segura
 */
function jsonResponse($success, $message, $data = null) {
    header('Content-Type: application/json; charset=utf-8');
    
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Log de acciones
 */
function logAction($action, $details = '') {
    $logFile = __DIR__ . '/../../logs/actions.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $logMessage = "[$timestamp] IP: $ip | Acción: $action | Detalles: $details\n";
    
    error_log($logMessage, 3, $logFile);
}
?>
