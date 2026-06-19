<?php
// src/Repository/PdoConductorRepository.php
// Implementación concreta del repositorio usando PDO con Prepared Statements
// OE2: Ninguna consulta SQL construida por concatenación de strings

declare(strict_types=1);

require_once __DIR__ . '/ConductorRepositoryInterface.php';
require_once __DIR__ . '/../Model/Conductor.php';
require_once __DIR__ . '/../../config/Connection.php';

class PdoConductorRepository implements ConductorRepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    /**
     * Obtiene todos los conductores ordenados por apellido.
     * @return Conductor[]
     */
    public function findAll(): array
    {
        // NEVER concatenation - always prepared statement
        $stmt = $this->pdo->prepare(
            'SELECT id, cedula, nombres, apellidos, telefono, email,
                    licencia_tipo, licencia_num, fecha_venc_lic, estado, created_at
             FROM conductores
             ORDER BY apellidos, nombres'
        );
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return array_map(
            fn(array $row) => Conductor::fromArray($row),
            $rows
        );
    }

    /**
     * Busca un conductor por ID.
     */
    public function findById(int $id): ?Conductor
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, cedula, nombres, apellidos, telefono, email,
                    licencia_tipo, licencia_num, fecha_venc_lic, estado, created_at
             FROM conductores
             WHERE id = :id
             LIMIT 1'
        );
        // bindValue: pasa por valor (no por referencia como bindParam)
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row !== false ? Conductor::fromArray($row) : null;
    }

    /**
     * Busca un conductor por cédula.
     */
    public function findByCedula(string $cedula): ?Conductor
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, cedula, nombres, apellidos, telefono, email,
                    licencia_tipo, licencia_num, fecha_venc_lic, estado, created_at
             FROM conductores
             WHERE cedula = :cedula
             LIMIT 1'
        );
        $stmt->bindValue(':cedula', $cedula, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row !== false ? Conductor::fromArray($row) : null;
    }

    /**
     * Inserta un nuevo conductor.
     * Retorna el ID generado por la BD.
     */
    public function create(Conductor $conductor): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO conductores
                (cedula, nombres, apellidos, telefono, email,
                 licencia_tipo, licencia_num, fecha_venc_lic, estado)
             VALUES
                (:cedula, :nombres, :apellidos, :telefono, :email,
                 :licencia_tipo, :licencia_num, :fecha_venc_lic, :estado)
             RETURNING id'
        );

        // bindParam: pasa por referencia (útil en loops; aquí bindValue también vale)
        $cedula       = $conductor->getCedula();
        $nombres      = $conductor->getNombres();
        $apellidos    = $conductor->getApellidos();
        $telefono     = $conductor->getTelefono();
        $email        = $conductor->getEmail();
        $licTipo      = $conductor->getLicenciaTipo();
        $licNum       = $conductor->getLicenciaNum();
        $fechaVenc    = $conductor->getFechaVencLic();
        $estado       = $conductor->getEstado();

        $stmt->bindParam(':cedula',        $cedula,    PDO::PARAM_STR);
        $stmt->bindParam(':nombres',       $nombres,   PDO::PARAM_STR);
        $stmt->bindParam(':apellidos',     $apellidos, PDO::PARAM_STR);
        $stmt->bindParam(':telefono',      $telefono,  PDO::PARAM_STR);
        $stmt->bindParam(':email',         $email,     PDO::PARAM_STR);
        $stmt->bindParam(':licencia_tipo', $licTipo,   PDO::PARAM_STR);
        $stmt->bindParam(':licencia_num',  $licNum,    PDO::PARAM_STR);
        $stmt->bindParam(':fecha_venc_lic',$fechaVenc, PDO::PARAM_STR);
        $stmt->bindParam(':estado',        $estado,    PDO::PARAM_STR);

        $stmt->execute();
        $row = $stmt->fetch();

        return (int) $row['id'];
    }

    /**
     * Actualiza un conductor existente.
     */
    public function update(Conductor $conductor): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE conductores
             SET cedula        = :cedula,
                 nombres       = :nombres,
                 apellidos     = :apellidos,
                 telefono      = :telefono,
                 email         = :email,
                 licencia_tipo = :licencia_tipo,
                 licencia_num  = :licencia_num,
                 fecha_venc_lic= :fecha_venc_lic,
                 estado        = :estado
             WHERE id = :id'
        );

        $stmt->bindValue(':id',           $conductor->getId(),         PDO::PARAM_INT);
        $stmt->bindValue(':cedula',       $conductor->getCedula(),     PDO::PARAM_STR);
        $stmt->bindValue(':nombres',      $conductor->getNombres(),    PDO::PARAM_STR);
        $stmt->bindValue(':apellidos',    $conductor->getApellidos(),  PDO::PARAM_STR);
        $stmt->bindValue(':telefono',     $conductor->getTelefono(),   PDO::PARAM_STR);
        $stmt->bindValue(':email',        $conductor->getEmail(),      PDO::PARAM_STR);
        $stmt->bindValue(':licencia_tipo',$conductor->getLicenciaTipo(),PDO::PARAM_STR);
        $stmt->bindValue(':licencia_num', $conductor->getLicenciaNum(), PDO::PARAM_STR);
        $stmt->bindValue(':fecha_venc_lic',$conductor->getFechaVencLic(),PDO::PARAM_STR);
        $stmt->bindValue(':estado',       $conductor->getEstado(),     PDO::PARAM_STR);

        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Elimina (soft delete: cambia estado a 'inactivo') un conductor.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE conductores SET estado = 'inactivo' WHERE id = :id"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Búsqueda por nombre o apellido con ILIKE (case-insensitive, PostgreSQL).
     * El % se agrega en PHP, no en SQL, para evitar confusión con wildcards.
     * @return Conductor[]
     */
    public function search(string $term): array
    {
        $likeTerm = '%' . $term . '%';

        $stmt = $this->pdo->prepare(
            'SELECT id, cedula, nombres, apellidos, telefono, email,
                    licencia_tipo, licencia_num, fecha_venc_lic, estado, created_at
             FROM conductores
             WHERE nombres ILIKE :term
                OR apellidos ILIKE :term
                OR cedula LIKE :term
             ORDER BY apellidos, nombres'
        );
        $stmt->bindValue(':term', $likeTerm, PDO::PARAM_STR);
        $stmt->execute();

        return array_map(
            fn(array $row) => Conductor::fromArray($row),
            $stmt->fetchAll()
        );
    }
}
