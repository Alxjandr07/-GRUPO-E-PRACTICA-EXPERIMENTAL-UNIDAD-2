<?php
// config/database.php
// Configuración de conexión a la base de datos usando PDO

declare(strict_types=1);

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');          // PostgreSQL 15 (igual que el proyecto SGROAS)
define('DB_NAME', 'sgroas_db');
define('DB_USER', 'root');
define('DB_PASS', 'root');   // Cambiar en producción
define('DB_CHARSET', 'utf8');

// Configuración de sesión segura
define('SESSION_NAME', 'SGROAS_SESSION');
define('SESSION_LIFETIME', 3600);   // 1 hora
define('APP_URL', 'http://localhost/sgroas-php');
define('APP_ENV', 'development');   // 'production' en producción
