package job.app.repository;

import org.springframework.data.jpa.repository.JpaRepository;

import job.app.entity.Description;

public interface DescriptionRepo extends JpaRepository<Description, Integer> {

}
