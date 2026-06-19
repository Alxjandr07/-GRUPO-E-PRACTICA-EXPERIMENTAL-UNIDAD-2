package ec.uteq.sgroas.app.service;

import ec.uteq.sgroas.app.entity.Conductor;
import ec.uteq.sgroas.app.repository.ConductorRepository;
import org.springframework.stereotype.Service;
import java.util.List;
import java.util.Optional;

@Service
public class ConductorService {

    private final ConductorRepository conductorRepository;

    public ConductorService(ConductorRepository conductorRepository) {
        this.conductorRepository = conductorRepository;
    }

    public List<Conductor> listarTodos() {
        return conductorRepository.findAllByOrderByApellidosAscNombresAsc();
    }

    public Optional<Conductor> buscarPorId(Long id) {
        return conductorRepository.findById(id);
    }

    public List<Conductor> buscar(String term) {
        return conductorRepository.search(term);
    }

    public Conductor guardar(Conductor conductor) {
        return conductorRepository.save(conductor);
    }

    /**
     * Soft delete: cambia estado a inactivo (no elimina el registro).
     */
    public void desactivar(Long id) {
        conductorRepository.findById(id).ifPresent(c -> {
            c.setEstado("inactivo");
            conductorRepository.save(c);
        });
    }

    public long contarActivos() {
        return conductorRepository.countByEstado("activo");
    }

    public long contarTotal() {
        return conductorRepository.count();
    }

    public boolean existeCedula(String cedula) {
        return conductorRepository.existsByCedula(cedula);
    }

    public boolean existeLicencia(String licenciaNum) {
        return conductorRepository.existsByLicenciaNum(licenciaNum);
    }
}
