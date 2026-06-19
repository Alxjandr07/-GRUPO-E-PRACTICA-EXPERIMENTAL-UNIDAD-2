<?php
// public/register.php
// Registro de usuarios con password_hash(PASSWORD_ARGON2ID) (OE1 + OWASP A02)

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../src/Repository/UsuarioRepository.php';

AuthMiddleware::setSecurityHeaders();
AuthMiddleware::initSession();

if (isset($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/public/dashboard.php');
    exit;
}

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Validar CSRF
        AuthMiddleware::validateCsrfToken($_POST['csrf_token'] ?? '');

        // 2. Recoger y validar inputs server-side
        $nombre    = trim(filter_input(INPUT_POST, 'nombre',   FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $email     = trim(filter_input(INPUT_POST, 'email',    FILTER_SANITIZE_EMAIL) ?? '');
        $password  = $_POST['password']  ?? '';
        $password2 = $_POST['password2'] ?? '';

        if (empty($nombre) || mb_strlen($nombre) < 3) {
            $errors[] = 'El nombre debe tener al menos 3 caracteres.';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Ingrese un correo electrónico válido.';
        }
        // Política de contraseña: mínimo 8 caracteres, una mayúscula, un número
        if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
            $errors[] = 'La contraseña debe tener mínimo 8 caracteres, una mayúscula y un número.';
        }
        if ($password !== $password2) {
            $errors[] = 'Las contraseñas no coinciden.';
        }

        if (empty($errors)) {
            $repo = new UsuarioRepository();

            if ($repo->emailExists($email)) {
                $errors[] = 'El correo ya está registrado.';
            } else {
                // OWASP A02: Almacenamiento seguro con Argon2id
                // Argon2id es resistente a ataques GPU y side-channel
                $hash = password_hash($password, PASSWORD_ARGON2ID, [
                    'memory_cost' => 65536, // 64 MB
                    'time_cost'   => 4,     // 4 iteraciones
                    'threads'     => 1,
                ]);

                $repo->create($nombre, $email, $hash, 'operador');
                $success = 'Cuenta creada exitosamente. Puede iniciar sesión.';
            }
        }

    } catch (\RuntimeException $e) {
        $errors[] = $e->getMessage();
    } catch (\PDOException $e) {
        $errors[] = 'Error al registrar. Intente más tarde.';
        error_log('[SGROAS] PDOException register: ' . $e->getMessage());
    }
}

$csrfToken = AuthMiddleware::generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | SGROAS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: linear-gradient(135deg, #1a3a5c 0%, #0d1f33 100%); min-height: 100vh; }
        .card { border: none; border-radius: 1rem; box-shadow: 0 10px 30px rgba(0,0,0,.4); }
        .btn-register { background-color: #57B9FF; border: none; }
        .btn-register:hover { background-color: #3aa0f0; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-4">
<div class="container" style="max-width: 450px;">
    <div class="text-center mb-4">
        <i class="bi bi-bus-front-fill fs-1 text-white"></i>
        <h2 class="text-white mt-2 fw-bold">SGROAS</h2>
    </div>

    <div class="card p-4">
        <h5 class="card-title fw-bold mb-3">Crear cuenta</h5>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-1"></i>
                <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                <a href="<?= APP_URL ?>/public/login.php">Iniciar sesión</a>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger py-2">
                <?php foreach ($errors as $e): ?>
                    <div><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <div class="mb-3">
                <label for="nombre" class="form-label fw-semibold">Nombre completo</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" id="nombre" name="nombre"
                           value="<?= htmlspecialchars($_POST['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="Juan Pérez" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email"
                           value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="correo@sgroas.ec" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Mínimo 8 chars, 1 mayúscula, 1 número" required>
                </div>
                <div class="form-text">Mínimo 8 caracteres, una mayúscula y un número.</div>
            </div>

            <div class="mb-4">
                <label for="password2" class="form-label fw-semibold">Confirmar contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control" id="password2" name="password2"
                           placeholder="Repita la contraseña" required>
                </div>
            </div>

            <button type="submit" class="btn btn-register text-white w-100 fw-semibold">
                <i class="bi bi-person-plus me-2"></i>Registrarse
            </button>
        </form>

        <hr class="my-3">
        <div class="text-center">
            <a href="<?= APP_URL ?>/public/login.php" class="text-decoration-none small">
                ¿Ya tiene cuenta? Inicie sesión
            </a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
