<?php
/**
 * API para obtener contactos (solo para administrador)
 * GET /backend/handlers/get-contacts.php
 */

require_once __DIR__ . '/../config/config.php';

// TODO: Implementar autenticación de administrador
// Por ahora solo desarrollo

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, 'Método no permitido');
}

try {
    $db = Database::getInstance();
    
    // Obtener últimos contactos
    $stmt = $db->prepare('
        SELECT id, nombre, email, telefono, empresa, asunto, fecha_creacion, estado
        FROM contactos
        ORDER BY fecha_creacion DESC
        LIMIT 50
    ');
    
    $stmt->execute();
    $contactos = $stmt->fetchAll();

    jsonResponse(true, 'Contactos obtenidos', $contactos);

} catch (Exception $e) {
    logAction('Error al obtener contactos', $e->getMessage());
    jsonResponse(false, 'Error al obtener contactos');
}
?>
