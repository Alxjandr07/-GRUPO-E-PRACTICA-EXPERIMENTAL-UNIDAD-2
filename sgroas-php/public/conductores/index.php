<?php
// public/conductores/index.php
// CRUD - Listado de conductores con búsqueda (ruta protegida)

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../src/Repository/PdoConductorRepository.php';

AuthMiddleware::setSecurityHeaders();
AuthMiddleware::requireAuth();

$repo = new PdoConductorRepository();
$conductores = [];
$searchTerm  = '';

try {
    // Búsqueda o listado completo
    $searchTerm  = trim(filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
    $conductores = $searchTerm !== ''
        ? $repo->search($searchTerm)
        : $repo->findAll();
} catch (\PDOException $e) {
    error_log('[SGROAS] Error listado conductores: ' . $e->getMessage());
}

// Leer mensajes flash de sesión
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error']   ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$pageTitle = 'Conductores';
include __DIR__ . '/../../views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>Conductores</h4>
    <a href="<?= APP_URL ?>/public/conductores/create.php" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Nuevo Conductor
    </a>
</div>

<!-- Mensajes flash -->
<?php if ($flashSuccess): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-1"></i>
        <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if ($flashError): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle me-1"></i>
        <?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Formulario de búsqueda -->
<form method="GET" action="" class="mb-3">
    <div class="input-group input-group-sm" style="max-width: 380px;">
        <input type="text" class="form-control" name="q"
               value="<?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') ?>"
               placeholder="Buscar por nombre, apellido o cédula...">
        <button type="submit" class="btn btn-outline-primary">
            <i class="bi bi-search"></i>
        </button>
        <?php if ($searchTerm): ?>
            <a href="?" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
        <?php endif; ?>
    </div>
</form>

<!-- Tabla de conductores -->
<div class="table-responsive">
    <table class="table table-hover table-bordered align-middle small">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Cédula</th>
                <th>Nombre Completo</th>
                <th>Teléfono</th>
                <th>Licencia</th>
                <th>Venc. Licencia</th>
                <th>Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($conductores)): ?>
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                    No se encontraron conductores.
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($conductores as $i => $c): ?>
            <tr>
                <td class="text-muted"><?= $i + 1 ?></td>
                <!-- XSS: htmlspecialchars en todas las salidas -->
                <td><?= htmlspecialchars($c->getCedula(), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($c->getNombreCompleto(), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($c->getTelefono(), ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <span class="badge bg-secondary">
                        <?= htmlspecialchars($c->getLicenciaTipo(), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <?= htmlspecialchars($c->getLicenciaNum(), ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td><?= htmlspecialchars($c->getFechaVencLic() ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <span class="badge badge-<?= htmlspecialchars($c->getEstado(), ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars(ucfirst($c->getEstado()), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </td>
                <td class="text-center">
                    <a href="<?= APP_URL ?>/public/conductores/edit.php?id=<?= (int)$c->getId() ?>"
                       class="btn btn-sm btn-outline-primary me-1" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <!-- Delete: POST con CSRF para evitar CSRF via GET -->
                    <form method="POST" action="<?= APP_URL ?>/public/conductores/delete.php"
                          class="d-inline"
                          onsubmit="return confirm('¿Confirma desactivar este conductor?')">
                        <input type="hidden" name="csrf_token"
                               value="<?= htmlspecialchars(AuthMiddleware::generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="id" value="<?= (int)$c->getId() ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<p class="text-muted small">Total: <?= count($conductores) ?> registro(s)</p>

<?php include __DIR__ . '/../../views/layouts/footer.php'; ?>
