<!-- views/layouts/header.php -->
<!-- Layout principal reutilizable con Bootstrap 5 y color scheme SGROAS (#57B9FF) -->
<?php
// XSS: htmlspecialchars en todas las variables de salida (OWASP A03)
$pageTitle = htmlspecialchars($pageTitle ?? 'SGROAS', ENT_QUOTES, 'UTF-8');
$currentUser = AuthMiddleware::getCurrentUser();
$userName = htmlspecialchars($currentUser['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
$userRol  = htmlspecialchars($currentUser['rol']    ?? '', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> | SGROAS</title>
    <!-- Bootstrap 5 CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Estilos personalizados SGROAS -->
    <style>
        :root {
            --sgroas-blue: #57B9FF;
            --sgroas-dark: #1a3a5c;
        }
        .navbar-brand, .nav-link:hover { color: var(--sgroas-blue) !important; }
        .btn-primary { background-color: var(--sgroas-blue); border-color: var(--sgroas-blue); }
        .btn-primary:hover { background-color: #3aa0f0; border-color: #3aa0f0; }
        .sidebar { min-height: calc(100vh - 56px); background-color: var(--sgroas-dark); }
        .sidebar .nav-link { color: rgba(255,255,255,.75); }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: var(--sgroas-blue); }
        .table-hover tbody tr:hover { background-color: rgba(87,185,255,.08); }
        .badge-activo   { background-color: #198754; }
        .badge-inactivo { background-color: #6c757d; }
        .badge-suspendido { background-color: #dc3545; }
    </style>
</head>
<body>
<!-- Navbar superior -->
<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand fw-bold">
        <i class="bi bi-bus-front-fill me-2" style="color:var(--sgroas-blue)"></i>SGROAS
    </span>
    <div class="d-flex align-items-center gap-3">
        <span class="text-white-50 small">
            <i class="bi bi-person-circle me-1"></i><?= $userName ?>
            <span class="badge bg-secondary ms-1"><?= $userRol ?></span>
        </span>
        <a href="<?= APP_URL ?>/public/logout.php" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-box-arrow-right me-1"></i>Salir
        </a>
    </div>
</nav>

<div class="container-fluid">
<div class="row">
    <!-- Sidebar -->
    <nav class="col-md-2 sidebar py-3 d-none d-md-block">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="<?= APP_URL ?>/public/dashboard.php">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= APP_URL ?>/public/conductores/index.php">
                    <i class="bi bi-person-badge me-2"></i>Conductores
                </a>
            </li>
        </ul>
    </nav>

    <!-- Contenido principal -->
    <main class="col-md-10 ms-sm-auto px-4 py-3">
