<?php
// public/conductores/create.php
// CRUD - Crear nuevo conductor

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../src/Repository/PdoConductorRepository.php';
require_once __DIR__ . '/../../src/Model/Conductor.php';

AuthMiddleware::setSecurityHeaders();
AuthMiddleware::requireAuth();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        AuthMiddleware::validateCsrfToken($_POST['csrf_token'] ?? '');

        // Validación server-side
        $cedula      = trim(filter_input(INPUT_POST, 'cedula',      FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $nombres     = trim(filter_input(INPUT_POST, 'nombres',     FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $apellidos   = trim(filter_input(INPUT_POST, 'apellidos',   FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $telefono    = trim(filter_input(INPUT_POST, 'telefono',    FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $email       = trim(filter_input(INPUT_POST, 'email',       FILTER_SANITIZE_EMAIL) ?? '');
        $licTipo     = trim(filter_input(INPUT_POST, 'licencia_tipo',FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $licNum      = trim(filter_input(INPUT_POST, 'licencia_num', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $fechaVenc   = trim(filter_input(INPUT_POST, 'fecha_venc_lic',FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $estado      = trim(filter_input(INPUT_POST, 'estado',       FILTER_SANITIZE_SPECIAL_CHARS) ?? 'activo');

        if (!preg_match('/^\d{10,13}$/', $cedula))       $errors[] = 'La cédula debe tener 10-13 dígitos.';
        if (mb_strlen($nombres) < 2)                     $errors[] = 'Nombres obligatorios (mín. 2 caracteres).';
        if (mb_strlen($apellidos) < 2)                   $errors[] = 'Apellidos obligatorios (mín. 2 caracteres).';
        if (!preg_match('/^09\d{8}$/', $telefono))       $errors[] = 'Teléfono debe ser formato 09XXXXXXXX.';
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
        if (!in_array($licTipo, ['A','B','C','D','E','F'])) $errors[] = 'Tipo de licencia inválido.';
        if (empty($licNum))                              $errors[] = 'Número de licencia obligatorio.';

        if (empty($errors)) {
            $conductor = new Conductor(
                null, $cedula, $nombres, $apellidos, $telefono,
                $email, $licTipo, $licNum,
                $fechaVenc ?: null, $estado
            );

            $repo = new PdoConductorRepository();
            $id   = $repo->create($conductor);

            $_SESSION['flash_success'] = "Conductor #{$id} creado exitosamente.";
            header('Location: ' . APP_URL . '/public/conductores/index.php');
            exit;
        }

    } catch (\RuntimeException $e) {
        $errors[] = $e->getMessage();
    } catch (\PDOException $e) {
        $errors[] = 'Error al guardar. Verifique que la cédula y licencia no estén duplicadas.';
        error_log('[SGROAS] PDOException create conductor: ' . $e->getMessage());
    }
}

$csrfToken = AuthMiddleware::generateCsrfToken();
$pageTitle = 'Nuevo Conductor';
include __DIR__ . '/../../views/layouts/header.php';
?>

<div class="d-flex align-items-center gap-2 mb-3">
    <a href="<?= APP_URL ?>/public/conductores/index.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="fw-bold mb-0"><i class="bi bi-person-plus me-2 text-primary"></i>Nuevo Conductor</h4>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $e): ?>
            <div><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm" style="max-width: 720px;">
<div class="card-body">
<form method="POST" action="" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label fw-semibold">Cédula *</label>
            <input type="text" class="form-control" name="cedula" maxlength="13"
                   value="<?= htmlspecialchars($_POST['cedula'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Nombres *</label>
            <input type="text" class="form-control" name="nombres" maxlength="80"
                   value="<?= htmlspecialchars($_POST['nombres'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Apellidos *</label>
            <input type="text" class="form-control" name="apellidos" maxlength="80"
                   value="<?= htmlspecialchars($_POST['apellidos'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Teléfono *</label>
            <input type="text" class="form-control" name="telefono" maxlength="15" placeholder="09XXXXXXXX"
                   value="<?= htmlspecialchars($_POST['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" class="form-control" name="email" maxlength="150"
                   value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Estado *</label>
            <select class="form-select" name="estado">
                <?php foreach (['activo','inactivo','suspendido'] as $opt): ?>
                    <option value="<?= $opt ?>"
                        <?= (($_POST['estado'] ?? 'activo') === $opt) ? 'selected' : '' ?>>
                        <?= ucfirst($opt) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Tipo Licencia *</label>
            <select class="form-select" name="licencia_tipo">
                <?php foreach (['A','B','C','D','E','F'] as $tipo): ?>
                    <option value="<?= $tipo ?>"
                        <?= (($_POST['licencia_tipo'] ?? '') === $tipo) ? 'selected' : '' ?>>
                        Tipo <?= $tipo ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-5">
            <label class="form-label fw-semibold">N° de Licencia *</label>
            <input type="text" class="form-control" name="licencia_num" maxlength="20"
                   value="<?= htmlspecialchars($_POST['licencia_num'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Vencimiento Licencia</label>
            <input type="date" class="form-control" name="fecha_venc_lic"
                   value="<?= htmlspecialchars($_POST['fecha_venc_lic'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
    </div>

    <hr class="my-3">
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i>Guardar Conductor
        </button>
        <a href="<?= APP_URL ?>/public/conductores/index.php" class="btn btn-outline-secondary">
            Cancelar
        </a>
    </div>
</form>
</div>
</div>

<?php include __DIR__ . '/../../views/layouts/footer.php'; ?>
