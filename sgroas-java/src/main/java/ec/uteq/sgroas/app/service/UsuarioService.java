package ec.uteq.sgroas.app.service;

import ec.uteq.sgroas.app.entity.Usuario;
import ec.uteq.sgroas.app.repository.UsuarioRepository;
import org.springframework.security.core.authority.SimpleGrantedAuthority;
import org.springframework.security.core.userdetails.*;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import java.util.List;

@Service
public class UsuarioService implements UserDetailsService {

    private final UsuarioRepository usuarioRepository;
    private final PasswordEncoder passwordEncoder;

    public UsuarioService(UsuarioRepository usuarioRepository, PasswordEncoder passwordEncoder) {
        this.usuarioRepository = usuarioRepository;
        this.passwordEncoder   = passwordEncoder;
    }

    /**
     * Spring Security llama este método al autenticar.
     * Carga el usuario por email y retorna UserDetails.
     */
    @Override
    public UserDetails loadUserByUsername(String email) throws UsernameNotFoundException {
        Usuario usuario = usuarioRepository.findByEmail(email)
            .orElseThrow(() -> new UsernameNotFoundException("Usuario no encontrado: " + email));

        if (!usuario.getActivo()) {
            throw new UsernameNotFoundException("Usuario inactivo: " + email);
        }

        return new org.springframework.security.core.userdetails.User(
            usuario.getEmail(),
            usuario.getPasswordHash(),
            List.of(new SimpleGrantedAuthority("ROLE_" + usuario.getRol().toUpperCase()))
        );
    }

    /**
     * Registra un nuevo usuario con contraseña encriptada con BCrypt.
     * Spring Security PasswordEncoder usa BCrypt por defecto (equivalente
     * a password_hash() en PHP).
     */
    public Usuario registrar(String nombre, String email, String password, String rol) {
        if (usuarioRepository.existsByEmail(email)) {
            throw new RuntimeException("El correo ya está registrado.");
        }

        Usuario u = new Usuario();
        u.setNombre(nombre);
        u.setEmail(email);
        // BCrypt: equivalente a password_hash($pass, PASSWORD_BCRYPT) en PHP
        u.setPasswordHash(passwordEncoder.encode(password));
        u.setRol(rol);
        u.setActivo(true);

        return usuarioRepository.save(u);
    }

    public boolean existeEmail(String email) {
        return usuarioRepository.existsByEmail(email);
    }
}
