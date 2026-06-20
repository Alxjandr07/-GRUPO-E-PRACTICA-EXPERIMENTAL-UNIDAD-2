# -GRUPO-E-PRACTICA-EXPERIMENTAL-UNIDAD-2
# SGROAS — Sistema de Gestión de Recursos Operativos

## Descripción del proyecto

Este repositorio contiene el trabajo realizado para la Práctica Experimental de la Unidad 2 de la asignatura Aplicaciones Web. El proyecto consistió en desarrollar el sistema SGROAS mediante dos tecnologías del lado del servidor: PHP y Java con Spring Boot.

El trabajo fue realizado de manera colaborativa por los tres integrantes del equipo. Para completar la práctica se distribuyeron actividades relacionadas con el desarrollo de las aplicaciones, la investigación bibliográfica, la aplicación de controles de seguridad, las pruebas funcionales y la elaboración del informe técnico.

## Integrantes y actividades realizadas

### Álava Alvarado Jean Pierre

Participó principalmente en la elaboración, organización y edición del informe técnico del proyecto. Se encargó de estructurar el documento en LaTeX, preparar la portada, el índice, la introducción, la descripción del sistema, los resultados obtenidos, la tabla comparativa, el análisis de la práctica y las conclusiones.

También organizó e incorporó las capturas de las interfaces desarrolladas en PHP y Spring Boot, el resultado del análisis realizado con PHPStan y el enlace del repositorio. Además, revisó la presentación de las referencias bibliográficas en formatos APA e IEEE y preparó la documentación final para su entrega.

### Rivera Suárez Jonathan Javier

Participó principalmente en la investigación y elaboración del fundamento teórico correspondiente a las herramientas de programación web del lado del servidor.

Realizó la revisión bibliográfica sobre programación del lado del servidor, PERL, PHP, Java, Servlets, JSP, Spring Boot y ASP.NET Core. También colaboró en la búsqueda, revisión y corrección de las fuentes utilizadas, incorporando citas y referencias bibliográficas en formato APA.

Su aporte permitió complementar la parte práctica del proyecto con información académica sobre el funcionamiento, las características, las ventajas y las limitaciones de las tecnologías estudiadas.

### Tejada Bajaña Luis Alejandro

Participó principalmente en el desarrollo, configuración y prueba de las aplicaciones web de SGROAS.

Colaboró en la implementación de las versiones desarrolladas con PHP y Java con Spring Boot. Estas aplicaciones incluyen el registro de usuarios, inicio de sesión, cierre de sesión, protección de rutas privadas, dashboard y módulo CRUD para gestionar conductores.

También participó en la ejecución de las aplicaciones, la comprobación de las funcionalidades y la obtención de las capturas utilizadas como evidencia en el informe.

## Trabajo realizado en conjunto

Además de las actividades específicas, los tres integrantes participaron en la revisión general del proyecto, la validación de los requisitos de la práctica y la organización del repositorio.

Como equipo se realizaron las siguientes actividades:

* Revisión de las indicaciones de la práctica experimental.
* Desarrollo y comprobación de las aplicaciones web.
* Implementación de autenticación y protección de rutas.
* Desarrollo del CRUD de conductores.
* Aplicación de controles de seguridad recomendados por OWASP.
* Revisión del funcionamiento de PHP y Spring Boot.
* Investigación de tecnologías del lado del servidor.
* Elaboración del informe técnico.
* Organización de capturas, código fuente y documentación.
* Publicación del proyecto en GitHub.

## Funcionalidades del sistema

Las dos versiones de SGROAS incluyen:

* Registro de usuarios.
* Inicio de sesión mediante correo electrónico y contraseña.
* Cierre de sesión.
* Protección de rutas privadas.
* Dashboard con información resumida.
* Registro de conductores.
* Consulta y búsqueda de conductores.
* Edición de información.
* Desactivación de registros.
* Validación de formularios.
* Acceso seguro a la base de datos.

## Controles de seguridad

Durante el desarrollo se implementaron los siguientes controles:

* Prevención de ataques XSS mediante saneamiento de salidas.
* Protección CSRF en formularios.
* Almacenamiento seguro de contraseñas mediante funciones de hash.
* Consultas preparadas con PDO en PHP.
* Uso de Spring Data JPA en Java.
* Protección de rutas para usuarios autenticados.
* Gestión segura de sesiones.
* Cabeceras HTTP de seguridad.
* Validación de datos recibidos desde formularios.

## Tecnologías utilizadas

### Implementación en PHP

La versión PHP utiliza:

* PHP 8.
* PDO.
* Consultas preparadas.
* Sesiones PHP.
* `password_hash()`.
* `password_verify()`.
* Tokens CSRF.
* HTML y CSS.

### Implementación en Java

La versión Java utiliza:

* Java.
* Spring Boot.
* Spring Security.
* Spring Data JPA.
* Thymeleaf.
* BCrypt.
* Servidor Tomcat embebido.

## Análisis estático

Se realizó una validación del modelo `Conductor.php` utilizando PHPStan en nivel 5. La herramienta revisó los tipos de datos, parámetros, métodos y valores retornados del archivo seleccionado.

El análisis finalizó sin reportar errores en el nivel utilizado.

## Documentación elaborada

El informe técnico del proyecto contiene:

* Portada institucional.
* Índice.
* Introducción.
* Fundamento teórico.
* Revisión bibliográfica.
* Descripción del sistema.
* Tecnologías utilizadas.
* Funcionalidades implementadas.
* Controles de seguridad.
* Capturas de las aplicaciones.
* Resultado de PHPStan.
* Tabla comparativa entre PHP y Spring Boot.
* Análisis de la práctica.
* Resultados obtenidos.
* Conclusiones.
* Referencias en formato APA e IEEE.
