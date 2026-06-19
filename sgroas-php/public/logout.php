<?php
// public/logout.php
// Cierre de sesión seguro

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Middleware/AuthMiddleware.php';

AuthMiddleware::setSecurityHeaders();
// logout() ya incluye initSession() y redirect al login
AuthMiddleware::logout();
