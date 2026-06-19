<?php
// src/Middleware/AuthMiddleware.php
// Protección de rutas y gestión segura de sesiones (OE1 + OWASP A07)

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

class AuthMiddleware
{
    /**
     * Inicializa la sesión con configuración segura.
     * Debe llamarse ANTES de session_start() en cualquier página.
     */
    public static function initSession(): void
    {
        // Flags de cookie: HttpOnly + Secure + SameSite Strict (OWASP)
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'domain'   => '',
            'secure'   => (APP_ENV === 'production'), // true en producción (HTTPS)
            'httponly' => true,                        // Previene acceso via JS (XSS)
            'samesite' => 'Strict',                    // Previene CSRF
        ]);

        session_name(SESSION_NAME);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Verifica si el usuario está autenticado.
     * Si no lo está, redirige al login.
     */
    public static function requireAuth(): void
    {
        self::initSession();

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . APP_URL . '/public/login.php');
            exit;
        }

        // Regenerar ID de sesión periódicamente para prevenir session fixation
        if (!isset($_SESSION['last_regenerated'])) {
            $_SESSION['last_regenerated'] = time();
        } elseif (time() - $_SESSION['last_regenerated'] > 300) { // cada 5 min
            session_regenerate_id(true); // true = destruir sesión anterior
            $_SESSION['last_regenerated'] = time();
        }

        // Verificar inactividad
        if (isset($_SESSION['last_activity'])
            && (time() - $_SESSION['last_activity']) > SESSION_LIFETIME) {
            self::logout();
        }

        $_SESSION['last_activity'] = time();
    }

    /**
     * Cierra la sesión de forma segura.
     */
    public static function logout(): void
    {
        self::initSession();

        // 1. Destruir datos de sesión
        $_SESSION = [];

        // 2. Eliminar cookie de sesión
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        // 3. Destruir la sesión en el servidor
        session_destroy();

        header('Location: ' . APP_URL . '/public/login.php');
        exit;
    }

    /**
     * Genera un token CSRF y lo almacena en sesión.
     * Retorna el token para incluir en el formulario.
     */
    public static function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Valida el token CSRF del formulario enviado.
     * Lanza RuntimeException si el token es inválido.
     */
    public static function validateCsrfToken(string $token): void
    {
        if (empty($_SESSION['csrf_token'])
            || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            throw new \RuntimeException('Token CSRF inválido. Posible ataque CSRF.');
        }
        // Regenerar token después de validarlo (one-time use)
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    /**
     * Aplica cabeceras de seguridad HTTP (OWASP).
     * Llamar al inicio de cada respuesta.
     */
    public static function setSecurityHeaders(): void
    {
        // Prevenir clickjacking
        header('X-Frame-Options: DENY');
        // Prevenir MIME sniffing
        header('X-Content-Type-Options: nosniff');
        // XSS Protection (legacy browsers)
        header('X-XSS-Protection: 1; mode=block');
        // Content Security Policy: solo recursos propios
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; font-src 'self' https://cdn.jsdelivr.net;");
        // HSTS (solo en producción con HTTPS)
        if (APP_ENV === 'production') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }

    /**
     * Obtiene el usuario actual de la sesión.
     * Retorna array con id, nombre, email o null si no autenticado.
     */
    public static function getCurrentUser(): ?array
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        return [
            'id'     => $_SESSION['user_id'],
            'nombre' => $_SESSION['user_nombre'] ?? '',
            'email'  => $_SESSION['user_email']  ?? '',
            'rol'    => $_SESSION['user_rol']     ?? 'operador',
        ];
    }
}
