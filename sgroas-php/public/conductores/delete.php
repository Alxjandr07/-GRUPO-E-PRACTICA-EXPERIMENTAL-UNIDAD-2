<?php
// public/conductores/delete.php
// CRUD - Eliminar (soft delete) conductor
// Solo acepta POST para prevenir eliminaciones via GET (CSRF protection)

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../src/Repository/PdoConductorRepository.php';

AuthMiddleware::setSecurityHeaders();
AuthMiddleware::requireAuth();

// Solo permitir método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $_SESSION['flash_error'] = 'Método no permitido.';
    header('Location: ' . APP_URL . '/public/conductores/index.php');
    exit;
}

try {
    // Validar CSRF antes de cualquier operación destructiva
    AuthMiddleware::validateCsrfToken($_POST['csrf_token'] ?? '');

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        throw new \InvalidArgumentException('ID inválido.');
    }

    $repo = new PdoConductorRepository();

    // Verificar que el conductor existe antes de eliminar
    $conductor = $repo->findById((int)$id);
    if (!$conductor) {
        throw new \RuntimeException('Conductor no encontrado.');
    }

    // Soft delete: cambia estado a 'inactivo' (no borra el registro físicamente)
    $repo->delete((int)$id);
    $_SESSION['flash_success'] = "Conductor '{$conductor->getNombreCompleto()}' desactivado.";

} catch (\RuntimeException | \InvalidArgumentException $e) {
    $_SESSION['flash_error'] = $e->getMessage();
} catch (\PDOException $e) {
    $_SESSION['flash_error'] = 'Error al desactivar el conductor.';
    error_log('[SGROAS] PDOException delete conductor: ' . $e->getMessage());
}

header('Location: ' . APP_URL . '/public/conductores/index.php');
exit;
