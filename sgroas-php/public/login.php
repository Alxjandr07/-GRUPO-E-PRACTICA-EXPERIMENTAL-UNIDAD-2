<?php
// public/login.php
// Sistema de autenticación - Login (OE1 + OWASP A07)

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../src/Repository/UsuarioRepository.php';

// Aplicar cabeceras de seguridad (OWASP)
AuthMiddleware::setSecurityHeaders();
AuthMiddleware::initSession();

// Si ya está autenticado, redirigir al dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/public/dashboard.php');
    exit;
}

$errors  = [];
$success = '';

// Procesar formulario POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Validar token CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        AuthMiddleware::validateCsrfToken($csrfToken);

        // 2. Recoger y sanitizar inputs (server-side validation)
        $email    = trim(filter_input(INPUT_POST, 'email',    FILTER_SANITIZE_EMAIL) ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Ingrese un correo electrónico válido.';
        }
        if (empty($password)) {
            $errors[] = 'La contraseña es obligatoria.';
        }

        if (empty($errors)) {
            $repo    = new UsuarioRepository();
            $usuario = $repo->findByEmail($email);

            // password_verify() previene timing attacks (tiempo constante)
            if ($usuario && $usuario['activo']
                && password_verify($password, $usuario['password_hash'])) {

                // Regenerar ID de sesión al autenticarse (previene session fixation)
                session_regenerate_id(true);

                // Almacenar datos mínimos necesarios en sesión
                $_SESSION['user_id']     = $usuario['id'];
                $_SESSION['user_nombre'] = $usuario['nombre'];
                $_SESSION['user_email']  = $usuario['email'];
                $_SESSION['user_rol']    = $usuario['rol'];
                $_SESSION['last_activity']    = time();
                $_SESSION['last_regenerated'] = time();

                // Redirigir a la URL original si venía de una ruta protegida
                $redirect = $_SESSION['redirect_after_login'] ?? APP_URL . '/public/dashboard.php';
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
                exit;

            } else {
                // Mensaje genérico: no revelar si el email existe (OWASP A07)
                $errors[] = 'Credenciales incorrectas. Verifique su correo y contraseña.';
            }
        }

    } catch (\RuntimeException $e) {
        $errors[] = $e->getMessage();
    } catch (\PDOException $e) {
        $errors[] = 'Error de conexión. Intente más tarde.';
        error_log('[SGROAS] PDOException login: ' . $e->getMessage());
    }
}

// Generar token CSRF para el formulario
$csrfToken = AuthMiddleware::generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | SGROAS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: linear-gradient(135deg, #1a3a5c 0%, #0d1f33 100%); min-height: 100vh; }
        .card { border: none; border-radius: 1rem; box-shadow: 0 10px 30px rgba(0,0,0,.4); }
        .btn-login { background-color: #57B9FF; border: none; }
        .btn-login:hover { background-color: #3aa0f0; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
<div class="container" style="max-width: 420px;">
    <div class="text-center mb-4">
        <i class="bi bi-bus-front-fill fs-1 text-white"></i>
        <h2 class="text-white mt-2 fw-bold">SGROAS</h2>
        <p class="text-white-50 small">Sistema de Gestión de Recursos Operativos</p>
    </div>

    <div class="card p-4">
        <h5 class="card-title fw-bold mb-3">Iniciar Sesión</h5>

        <!-- Mostrar errores (XSS: htmlspecialchars) -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger py-2" role="alert">
                <?php foreach ($errors as $e): ?>
                    <div><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
            <!-- Token CSRF oculto en todos los formularios (OWASP A01) -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email"
                           value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="correo@sgroas.ec" required autocomplete="email">
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-semibold">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="••••••••" required autocomplete="current-password">
                </div>
            </div>

            <button type="submit" class="btn btn-login text-white w-100 fw-semibold">
                <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
            </button>
        </form>

        <hr class="my-3">
        <div class="text-center">
            <a href="<?= APP_URL ?>/public/register.php" class="text-decoration-none small">
                ¿No tiene cuenta? Regístrese aquí
            </a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
