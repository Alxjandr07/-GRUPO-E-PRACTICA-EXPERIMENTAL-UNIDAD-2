<?php
// src/Model/Conductor.php
// Modelo de datos para la entidad Conductor (SGROAS)

declare(strict_types=1);

class Conductor
{
    public function __construct(
        private ?int    $id            = null,
        private string  $cedula        = '',
        private string  $nombres       = '',
        private string  $apellidos     = '',
        private string  $telefono      = '',
        private string  $email         = '',
        private string  $licencia_tipo = '',   // Tipo: A, B, C, D, E
        private string  $licencia_num  = '',
        private ?string $fecha_venc_lic = null,
        private string  $estado        = 'activo',
        private ?string $created_at    = null
    ) {}

    // Getters
    public function getId(): ?int            { return $this->id; }
    public function getCedula(): string      { return $this->cedula; }
    public function getNombres(): string     { return $this->nombres; }
    public function getApellidos(): string   { return $this->apellidos; }
    public function getTelefono(): string    { return $this->telefono; }
    public function getEmail(): string       { return $this->email; }
    public function getLicenciaTipo(): string{ return $this->licencia_tipo; }
    public function getLicenciaNum(): string { return $this->licencia_num; }
    public function getFechaVencLic(): ?string { return $this->fecha_venc_lic; }
    public function getEstado(): string      { return $this->estado; }
    public function getCreatedAt(): ?string  { return $this->created_at; }
    public function getNombreCompleto(): string
    {
        return $this->nombres . ' ' . $this->apellidos;
    }

    // Setters
    public function setCedula(string $v): void       { $this->cedula = $v; }
    public function setNombres(string $v): void      { $this->nombres = $v; }
    public function setApellidos(string $v): void    { $this->apellidos = $v; }
    public function setTelefono(string $v): void     { $this->telefono = $v; }
    public function setEmail(string $v): void        { $this->email = $v; }
    public function setLicenciaTipo(string $v): void { $this->licencia_tipo = $v; }
    public function setLicenciaNum(string $v): void  { $this->licencia_num = $v; }
    public function setFechaVencLic(?string $v): void{ $this->fecha_venc_lic = $v; }
    public function setEstado(string $v): void       { $this->estado = $v; }

    /**
     * Crea un Conductor desde un array asociativo (resultado de PDO).
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id:             isset($data['id']) ? (int)$data['id'] : null,
            cedula:         $data['cedula']         ?? '',
            nombres:        $data['nombres']        ?? '',
            apellidos:      $data['apellidos']      ?? '',
            telefono:       $data['telefono']       ?? '',
            email:          $data['email']          ?? '',
            licencia_tipo:  $data['licencia_tipo']  ?? '',
            licencia_num:   $data['licencia_num']   ?? '',
            fecha_venc_lic: $data['fecha_venc_lic'] ?? null,
            estado:         $data['estado']         ?? 'activo',
            created_at:     $data['created_at']     ?? null,
        );
    }
}
