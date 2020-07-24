package job.app.repository;

import java.util.List;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;

import job.app.entity.Job;

public interface JobRepo extends JpaRepository<Job, Integer> {

	
	@Query("from Job j where j.position like %?1% OR j.companyName like %?1%")
	public List<Job> searchJob(String word);
		
	public List<Job> findAllByOrderByDateAsc();	
	
	public List<Job> findAllByOrderByCompanyNameAsc();	
	
	public List<Job> findAllByOrderByPositionAsc();	
	
	@Query("from Job j where j.description.response = 1")
	public List<Job> findAllResponses();	
	
	@Query("from Job j where j.description.response = 0")
	public List<Job> findAllNoResponses();	
}
