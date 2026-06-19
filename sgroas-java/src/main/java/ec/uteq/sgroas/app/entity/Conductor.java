package ec.uteq.sgroas.app.entity;

import jakarta.persistence.*;
import jakarta.validation.constraints.*;
import java.time.LocalDate;
import java.time.LocalDateTime;

@Entity
@Table(name = "conductores")
public class Conductor {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @NotBlank(message = "La cédula es obligatoria")
    @Pattern(regexp = "\\d{10,13}", message = "La cédula debe tener 10-13 dígitos")
    @Column(nullable = false, unique = true, length = 13)
    private String cedula;

    @NotBlank(message = "Los nombres son obligatorios")
    @Size(min = 2, max = 80)
    @Column(nullable = false, length = 80)
    private String nombres;

    @NotBlank(message = "Los apellidos son obligatorios")
    @Size(min = 2, max = 80)
    @Column(nullable = false, length = 80)
    private String apellidos;

    @NotBlank(message = "El teléfono es obligatorio")
    @Pattern(regexp = "09\\d{8}", message = "Teléfono debe ser formato 09XXXXXXXX")
    @Column(nullable = false, length = 15)
    private String telefono;

    @Email(message = "Email inválido")
    @Column(length = 150)
    private String email;

    @NotBlank(message = "El tipo de licencia es obligatorio")
    @Column(name = "licencia_tipo", nullable = false, length = 2)
    private String licenciaTipo;

    @NotBlank(message = "El número de licencia es obligatorio")
    @Column(name = "licencia_num", nullable = false, unique = true, length = 20)
    private String licenciaNum;

    @Column(name = "fecha_venc_lic")
    private LocalDate fechaVencLic;

    @Column(nullable = false, length = 15)
    private String estado = "activo";

    @Column(name = "created_at")
    private LocalDateTime createdAt = LocalDateTime.now();

    @Column(name = "updated_at")
    private LocalDateTime updatedAt = LocalDateTime.now();

    // Getter nombre completo
    public String getNombreCompleto() {
        return nombres + " " + apellidos;
    }

    @PreUpdate
    public void preUpdate() { this.updatedAt = LocalDateTime.now(); }

    // Getters y Setters
    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }

    public String getCedula() { return cedula; }
    public void setCedula(String cedula) { this.cedula = cedula; }

    public String getNombres() { return nombres; }
    public void setNombres(String nombres) { this.nombres = nombres; }

    public String getApellidos() { return apellidos; }
    public void setApellidos(String apellidos) { this.apellidos = apellidos; }

    public String getTelefono() { return telefono; }
    public void setTelefono(String telefono) { this.telefono = telefono; }

    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }

    public String getLicenciaTipo() { return licenciaTipo; }
    public void setLicenciaTipo(String licenciaTipo) { this.licenciaTipo = licenciaTipo; }

    public String getLicenciaNum() { return licenciaNum; }
    public void setLicenciaNum(String licenciaNum) { this.licenciaNum = licenciaNum; }

    public LocalDate getFechaVencLic() { return fechaVencLic; }
    public void setFechaVencLic(LocalDate fechaVencLic) { this.fechaVencLic = fechaVencLic; }

    public String getEstado() { return estado; }
    public void setEstado(String estado) { this.estado = estado; }

    public LocalDateTime getCreatedAt() { return createdAt; }
    public LocalDateTime getUpdatedAt() { return updatedAt; }
}
