package job.app.repository;

import org.springframework.data.jpa.repository.JpaRepository;

import job.app.entity.WhereIFound;

public interface WhereIFoundRepo extends JpaRepository<WhereIFound, Integer> {

		public WhereIFound findById(int id);
}
