package ec.uteq.sgroas.app.repository;

import ec.uteq.sgroas.app.entity.Conductor;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;
import java.util.List;
import java.util.Optional;

@Repository
public interface ConductorRepository extends JpaRepository<Conductor, Long> {

    // Buscar por cédula
    Optional<Conductor> findByCedula(String cedula);

    // Verificar si existe cédula (para validación)
    boolean existsByCedula(String cedula);

    // Verificar si existe número de licencia
    boolean existsByLicenciaNum(String licenciaNum);

    // Búsqueda por nombre, apellido o cédula (JPQL - equivalente a ILIKE)
    @Query("SELECT c FROM Conductor c WHERE " +
           "LOWER(c.nombres) LIKE LOWER(CONCAT('%', :term, '%')) OR " +
           "LOWER(c.apellidos) LIKE LOWER(CONCAT('%', :term, '%')) OR " +
           "c.cedula LIKE CONCAT('%', :term, '%') " +
           "ORDER BY c.apellidos, c.nombres")
    List<Conductor> search(@Param("term") String term);

    // Listar ordenados por apellido
    List<Conductor> findAllByOrderByApellidosAscNombresAsc();

    // Contar por estado
    long countByEstado(String estado);
}
