<?php
// public/dashboard.php
// Página principal post-login (ruta protegida)

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../src/Repository/PdoConductorRepository.php';

AuthMiddleware::setSecurityHeaders();
AuthMiddleware::requireAuth(); // Protección de ruta

$repo        = new PdoConductorRepository();
$conductores = $repo->findAll();
$total       = count($conductores);
$activos     = count(array_filter($conductores, fn($c) => $c->getEstado() === 'activo'));

$pageTitle = 'Dashboard';
include __DIR__ . '/../views/layouts/header.php';
?>

<h4 class="fw-bold mb-4"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</h4>

<!-- Tarjetas de resumen -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <div class="fs-2 fw-bold text-primary"><?= $total ?></div>
                <div class="text-muted small"><i class="bi bi-person-badge me-1"></i>Conductores registrados</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <div class="fs-2 fw-bold text-success"><?= $activos ?></div>
                <div class="text-muted small"><i class="bi bi-check-circle me-1"></i>Conductores activos</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <div class="fs-2 fw-bold text-warning"><?= $total - $activos ?></div>
                <div class="text-muted small"><i class="bi bi-pause-circle me-1"></i>Inactivos/Suspendidos</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <div class="fs-2 fw-bold" style="color:#57B9FF">SGROAS</div>
                <div class="text-muted small"><i class="bi bi-shield-check me-1"></i>Sistema activo</div>
            </div>
        </div>
    </div>
</div>

<!-- Acceso rápido -->
<div class="d-flex gap-2 mb-3">
    <a href="<?= APP_URL ?>/public/conductores/index.php" class="btn btn-primary">
        <i class="bi bi-person-badge me-1"></i>Gestionar Conductores
    </a>
    <a href="<?= APP_URL ?>/public/conductores/create.php" class="btn btn-outline-primary">
        <i class="bi bi-plus-circle me-1"></i>Nuevo Conductor
    </a>
</div>

<?php include __DIR__ . '/../views/layouts/footer.php'; ?>
