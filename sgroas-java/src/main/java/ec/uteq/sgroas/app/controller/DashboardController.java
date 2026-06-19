package ec.uteq.sgroas.app.controller;

import ec.uteq.sgroas.app.service.ConductorService;
import org.springframework.security.core.Authentication;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;

@Controller
public class DashboardController {

    private final ConductorService conductorService;

    public DashboardController(ConductorService conductorService) {
        this.conductorService = conductorService;
    }

    @GetMapping("/dashboard")
    public String dashboard(Model model, Authentication auth) {
        model.addAttribute("total",   conductorService.contarTotal());
        model.addAttribute("activos", conductorService.contarActivos());
        model.addAttribute("usuario", auth.getName());
        return "dashboard";
    }

    @GetMapping("/")
    public String index() {
        return "redirect:/dashboard";
    }
}
