<?php
// src/Repository/UsuarioRepository.php
// Acceso a datos de usuarios para autenticación (OE1)

declare(strict_types=1);

require_once __DIR__ . '/../../config/Connection.php';

class UsuarioRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    /**
     * Busca un usuario por email para el proceso de login.
     * Retorna array con datos del usuario o null si no existe.
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, nombre, email, password_hash, rol, activo
             FROM usuarios
             WHERE email = :email
             LIMIT 1'
        );
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    /**
     * Registra un nuevo usuario.
     * La contraseña debe venir ya hasheada con password_hash().
     * Retorna el ID del usuario creado.
     */
    public function create(string $nombre, string $email, string $passwordHash, string $rol = 'operador'): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO usuarios (nombre, email, password_hash, rol)
             VALUES (:nombre, :email, :password_hash, :rol)
             RETURNING id'
        );

        $stmt->bindValue(':nombre',        $nombre,       PDO::PARAM_STR);
        $stmt->bindValue(':email',         $email,        PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);
        $stmt->bindValue(':rol',           $rol,          PDO::PARAM_STR);

        $stmt->execute();
        $row = $stmt->fetch();
        return (int) $row['id'];
    }

    /**
     * Verifica si el email ya existe en la BD (para el registro).
     */
    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) as total FROM usuarios WHERE email = :email'
        );
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return (int)$row['total'] > 0;
    }
}
