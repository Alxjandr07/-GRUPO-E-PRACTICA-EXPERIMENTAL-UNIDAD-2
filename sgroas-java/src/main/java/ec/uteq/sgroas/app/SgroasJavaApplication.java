package ec.uteq.sgroas.app;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.boot.autoconfigure.domain.EntityScan;
import org.springframework.data.jpa.repository.config.EnableJpaRepositories;

@SpringBootApplication
@EntityScan("ec.uteq.sgroas.app.entity")
@EnableJpaRepositories("ec.uteq.sgroas.app.repository")
public class SgroasJavaApplication {
	public static void main(String[] args) {
		SpringApplication.run(SgroasJavaApplication.class, args);
	}
}