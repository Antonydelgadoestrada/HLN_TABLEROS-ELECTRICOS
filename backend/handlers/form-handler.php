<?php
/**
 * Manejador de formulario de contacto
 * POST /backend/handlers/form-handler.php
 */

require_once __DIR__ . '/../config/config.php';

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Método no permitido');
}

// Verificar CSRF token (si usas sesiones)
// session_start();
// if (!isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
//     jsonResponse(false, 'Token de seguridad inválido');
// }

try {
    // Obtener datos del formulario
    $nombre = sanitizeInput($_POST['nombre'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $telefono = sanitizeInput($_POST['telefono'] ?? '');
    $empresa = sanitizeInput($_POST['empresa'] ?? '');
    $asunto = sanitizeInput($_POST['asunto'] ?? '');
    $mensaje = sanitizeInput($_POST['mensaje'] ?? '');

    // Validaciones
    $errores = [];

    if (empty($nombre) || strlen($nombre) < 3) {
        $errores[] = 'El nombre debe tener al menos 3 caracteres';
    }

    if (empty($email) || !isValidEmail($email)) {
        $errores[] = 'Email inválido';
    }

    if (empty($telefono) || !isValidPhone($telefono)) {
        $errores[] = 'Teléfono inválido';
    }

    if (empty($asunto) || strlen($asunto) < 5) {
        $errores[] = 'El asunto debe tener al menos 5 caracteres';
    }

    if (empty($mensaje) || strlen($mensaje) < 10) {
        $errores[] = 'El mensaje debe tener al menos 10 caracteres';
    }

    // Si hay errores, retornar
    if (!empty($errores)) {
        jsonResponse(false, implode(', ', $errores));
    }

    // Guardar en base de datos
    $db = Database::getInstance();
    
    $stmt = $db->prepare('
        INSERT INTO contactos (nombre, email, telefono, empresa, asunto, mensaje, fecha_creacion, ip_address, estado)
        VALUES (:nombre, :email, :telefono, :empresa, :asunto, :mensaje, NOW(), :ip, :estado)
    ');

    $stmt->execute([
        ':nombre' => $nombre,
        ':email' => $email,
        ':telefono' => $telefono,
        ':empresa' => $empresa,
        ':asunto' => $asunto,
        ':mensaje' => $mensaje,
        ':ip' => $_SERVER['REMOTE_ADDR'],
        ':estado' => 'nuevo'
    ]);

    // Enviar email (opcional)
    sendContactEmail($nombre, $email, $asunto, $mensaje);

    // Loguear acción
    logAction('Formulario de contacto', "Email: $email");

    // Respuesta de éxito
    jsonResponse(true, 'Gracias por contactarnos. En breve nos comunicaremos con usted.');

} catch (Exception $e) {
    if (DEBUG_MODE) {
        jsonResponse(false, 'Error: ' . $e->getMessage());
    } else {
        logAction('Error en formulario', $e->getMessage());
        jsonResponse(false, 'Error al procesar el formulario. Intente más tarde.');
    }
}

/**
 * Enviar email de contacto
 */
function sendContactEmail($nombre, $email, $asunto, $mensaje) {
    // Usar mail() de PHP (requiere servidor SMTP configurado)
    // O usar PHPMailer/SwiftMailer para más control
    
    $emailBody = "
    <html>
        <head>
            <title>Nuevo contacto</title>
        </head>
        <body>
            <h2>Nuevo mensaje de contacto</h2>
            <p><strong>Nombre:</strong> {$nombre}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Asunto:</strong> {$asunto}</p>
            <p><strong>Mensaje:</strong></p>
            <p>{$mensaje}</p>
        </body>
    </html>";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . $email . "\r\n";

    // Descomentar para enviar emails
    // mail(EMPRESA_EMAIL, "Nuevo contacto: " . $asunto, $emailBody, $headers);
}
?>
