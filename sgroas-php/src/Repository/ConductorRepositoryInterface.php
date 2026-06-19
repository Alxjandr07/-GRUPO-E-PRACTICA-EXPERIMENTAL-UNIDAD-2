<?php
// src/Repository/ConductorRepositoryInterface.php
// Interfaz del patrón Repository para desacoplar lógica de acceso a datos

declare(strict_types=1);

interface ConductorRepositoryInterface
{
    /**
     * Obtiene todos los conductores.
     * @return Conductor[]
     */
    public function findAll(): array;

    /**
     * Busca un conductor por su ID.
     */
    public function findById(int $id): ?Conductor;

    /**
     * Busca un conductor por su cédula.
     */
    public function findByCedula(string $cedula): ?Conductor;

    /**
     * Inserta un nuevo conductor y retorna su ID generado.
     */
    public function create(Conductor $conductor): int;

    /**
     * Actualiza los datos de un conductor existente.
     * Retorna true si se modificó al menos una fila.
     */
    public function update(Conductor $conductor): bool;

    /**
     * Elimina (o desactiva) un conductor por ID.
     * Retorna true si se eliminó al menos una fila.
     */
    public function delete(int $id): bool;

    /**
     * Busca conductores por nombre o apellido (búsqueda parcial).
     * @return Conductor[]
     */
    public function search(string $term): array;
}
