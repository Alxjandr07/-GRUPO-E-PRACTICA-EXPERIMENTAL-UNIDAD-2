package ec.uteq.sgroas.app.dto;

import jakarta.validation.constraints.*;

public class RegistroDto {

    @NotBlank(message = "El nombre es obligatorio")
    @Size(min = 3, max = 100, message = "El nombre debe tener entre 3 y 100 caracteres")
    private String nombre;

    @NotBlank(message = "El email es obligatorio")
    @Email(message = "Email inválido")
    private String email;

    @NotBlank(message = "La contraseña es obligatoria")
    @Pattern(
        regexp = "^(?=.*[A-Z])(?=.*\\d).{8,}$",
        message = "La contraseña debe tener mínimo 8 caracteres, una mayúscula y un número"
    )
    private String password;

    @NotBlank(message = "Confirme la contraseña")
    private String password2;

    // Getters y Setters
    public String getNombre() { return nombre; }
    public void setNombre(String nombre) { this.nombre = nombre; }

    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }

    public String getPassword() { return password; }
    public void setPassword(String password) { this.password = password; }

    public String getPassword2() { return password2; }
    public void setPassword2(String password2) { this.password2 = password2; }
}
