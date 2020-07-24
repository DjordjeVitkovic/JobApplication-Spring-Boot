package job.app.entity;

import javax.persistence.CascadeType;
import javax.persistence.Entity;
import javax.persistence.GeneratedValue;
import javax.persistence.GenerationType;
import javax.persistence.Id;
import javax.persistence.JoinColumn;
import javax.persistence.ManyToOne;
import javax.persistence.OneToOne;

@Entity
public class Description {

	@Id
	@GeneratedValue(strategy = GenerationType.IDENTITY)
	private int id;
	private boolean response;
	private String interviewDate;
	
	
	
	@OneToOne(cascade = CascadeType.ALL)
	@JoinColumn(name = "jobId", referencedColumnName = "id")
	private Job job;
	
	
	public Description() {
	}

	public Description(boolean response, String interviewDate) {
		super();
		this.response = response;
		this.interviewDate = interviewDate;
	}

	public int getId() {
		return id;
	}

	public void setId(int id) {
		this.id = id;
	}

	public boolean getResponse() {
		return response;
	}

	public void setResponse(boolean response) {
		this.response = response;
	}

	public String getInterviewDate() {
		return interviewDate;
	}

	public void setInterviewDate(String interviewDate) {
		this.interviewDate = interviewDate;
	}

	

	public Job getJob() {
		return job;
	}

	public void setJob(Job job) {
		this.job = job;
	}
	

	@Override
	public String toString() {
		return "Description [id=" + id + ", response=" + response + ", interviewDate=" + interviewDate + 
				", job=" + job + "]";
	}
	
}
