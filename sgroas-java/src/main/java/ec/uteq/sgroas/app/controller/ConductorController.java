package ec.uteq.sgroas.app.controller;

import ec.uteq.sgroas.app.entity.Conductor;
import ec.uteq.sgroas.app.service.ConductorService;
import jakarta.validation.Valid;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.validation.BindingResult;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;
import java.util.List;

@Controller
@RequestMapping("/conductores")
public class ConductorController {

    private final ConductorService conductorService;

    public ConductorController(ConductorService conductorService) {
        this.conductorService = conductorService;
    }

    // GET /conductores — Listado
    @GetMapping
    public String index(@RequestParam(value = "q", required = false) String q, Model model) {
        List<Conductor> conductores = (q != null && !q.isBlank())
            ? conductorService.buscar(q)
            : conductorService.listarTodos();

        model.addAttribute("conductores", conductores);
        model.addAttribute("q", q);
        return "conductores/index";
    }

    // GET /conductores/nuevo — Formulario crear
    @GetMapping("/nuevo")
    public String nuevo(Model model) {
        model.addAttribute("conductor", new Conductor());
        model.addAttribute("accion", "Nuevo");
        return "conductores/form";
    }

    // POST /conductores/nuevo — Guardar nuevo
    @PostMapping("/nuevo")
    public String guardar(
            @Valid @ModelAttribute("conductor") Conductor conductor,
            BindingResult result,
            RedirectAttributes redirectAttrs,
            Model model) {

        if (result.hasErrors()) {
            model.addAttribute("accion", "Nuevo");
            return "conductores/form";
        }

        try {
            conductorService.guardar(conductor);
            redirectAttrs.addFlashAttribute("flashSuccess", "Conductor creado exitosamente.");
            return "redirect:/conductores";
        } catch (Exception e) {
            model.addAttribute("error", "Error al guardar. Verifique cédula y licencia duplicadas.");
            model.addAttribute("accion", "Nuevo");
            return "conductores/form";
        }
    }

    // GET /conductores/{id}/editar — Formulario editar
    @GetMapping("/{id}/editar")
    public String editar(@PathVariable Long id, Model model, RedirectAttributes redirectAttrs) {
        return conductorService.buscarPorId(id).map(conductor -> {
            model.addAttribute("conductor", conductor);
            model.addAttribute("accion", "Editar");
            return "conductores/form";
        }).orElseGet(() -> {
            redirectAttrs.addFlashAttribute("flashError", "Conductor no encontrado.");
            return "redirect:/conductores";
        });
    }

    // POST /conductores/{id}/editar — Actualizar
    @PostMapping("/{id}/editar")
    public String actualizar(
            @PathVariable Long id,
            @Valid @ModelAttribute("conductor") Conductor conductor,
            BindingResult result,
            RedirectAttributes redirectAttrs,
            Model model) {

        if (result.hasErrors()) {
            model.addAttribute("accion", "Editar");
            return "conductores/form";
        }

        try {
            conductor.setId(id);
            conductorService.guardar(conductor);
            redirectAttrs.addFlashAttribute("flashSuccess", "Conductor actualizado correctamente.");
            return "redirect:/conductores";
        } catch (Exception e) {
            model.addAttribute("error", "Error al actualizar.");
            model.addAttribute("accion", "Editar");
            return "conductores/form";
        }
    }

    // POST /conductores/{id}/eliminar — Soft delete
    @PostMapping("/{id}/eliminar")
    public String eliminar(@PathVariable Long id, RedirectAttributes redirectAttrs) {
        conductorService.desactivar(id);
        redirectAttrs.addFlashAttribute("flashSuccess", "Conductor desactivado correctamente.");
        return "redirect:/conductores";
    }
}
