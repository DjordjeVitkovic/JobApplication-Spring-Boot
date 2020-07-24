package job.app.repository;

import org.springframework.data.jpa.repository.JpaRepository;

import job.app.entity.User;

public interface UserRepository extends JpaRepository<User, String>{

	public User findByUsername(String username);
	
	
}
