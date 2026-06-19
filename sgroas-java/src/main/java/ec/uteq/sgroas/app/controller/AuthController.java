package ec.uteq.sgroas.app.controller;

import ec.uteq.sgroas.app.dto.RegistroDto;
import ec.uteq.sgroas.app.service.UsuarioService;
import jakarta.validation.Valid;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.validation.BindingResult;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

@Controller
@RequestMapping("/auth")
public class AuthController {

    private final UsuarioService usuarioService;

    public AuthController(UsuarioService usuarioService) {
        this.usuarioService = usuarioService;
    }

    // GET /auth/login
    @GetMapping("/login")
    public String loginPage(
            @RequestParam(value = "error",  required = false) String error,
            @RequestParam(value = "logout", required = false) String logout,
            Model model) {

        if (error != null)  model.addAttribute("error",  "Credenciales incorrectas.");
        if (logout != null) model.addAttribute("logout", "Sesión cerrada exitosamente.");
        return "auth/login";
    }

    // GET /auth/register
    @GetMapping("/register")
    public String registerPage(Model model) {
        model.addAttribute("registroDto", new RegistroDto());
        return "auth/register";
    }

    // POST /auth/register
    @PostMapping("/register")
    public String register(
            @Valid @ModelAttribute("registroDto") RegistroDto dto,
            BindingResult result,
            RedirectAttributes redirectAttrs,
            Model model) {

        // Validar que las contraseñas coincidan
        if (!dto.getPassword().equals(dto.getPassword2())) {
            result.rejectValue("password2", "passwords.mismatch", "Las contraseñas no coinciden.");
        }

        // Verificar email duplicado
        if (usuarioService.existeEmail(dto.getEmail())) {
            result.rejectValue("email", "email.exists", "El correo ya está registrado.");
        }

        if (result.hasErrors()) {
            return "auth/register";
        }

        try {
            usuarioService.registrar(dto.getNombre(), dto.getEmail(), dto.getPassword(), "operador");
            redirectAttrs.addFlashAttribute("success", "Cuenta creada. Puede iniciar sesión.");
            return "redirect:/auth/login";
        } catch (Exception e) {
            model.addAttribute("error", "Error al registrar. Intente más tarde.");
            return "auth/register";
        }
    }
}
