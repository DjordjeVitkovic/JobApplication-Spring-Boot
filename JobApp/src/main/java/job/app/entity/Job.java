package job.app.entity;

import javax.persistence.CascadeType;
import javax.persistence.Entity;
import javax.persistence.FetchType;
import javax.persistence.GeneratedValue;
import javax.persistence.GenerationType;
import javax.persistence.Id;
import javax.persistence.JoinColumn;
import javax.persistence.ManyToOne;
import javax.persistence.OneToOne;

@Entity
public class Job {
	
	@Id
	@GeneratedValue(strategy = GenerationType.IDENTITY)
	private int id;
	private String position;
	private String companyName;
	private String companyLink;
	private String date;
	private String extraInfo;
	
	@OneToOne(cascade = CascadeType.ALL, mappedBy = "job", fetch = FetchType.EAGER)
	private Description description;
	
	@ManyToOne(cascade = {CascadeType.DETACH,CascadeType.MERGE,CascadeType.PERSIST,CascadeType.REFRESH})
	@JoinColumn(name = "sourceId", referencedColumnName = "id")
	private WhereIFound source;
	
	public Job() {
	}

	

	public Job(String position, String companyName, String companyLink, String date, String extraInfo,
			Description description) {
		super();
		this.position = position;
		this.companyName = companyName;
		this.companyLink = companyLink;
		this.date = date;
		this.extraInfo = extraInfo;
		this.description = description;
	}



	public int getId() {
		return id;
	}

	public void setId(int id) {
		this.id = id;
	}

	public String getCompanyName() {
		return companyName;
	}

	public void setCompanyName(String companyName) {
		this.companyName = companyName;
	}

	public String getCompanyLink() {
		return companyLink;
	}

	public void setCompanyLink(String companyLink) {
		this.companyLink = companyLink;
	}

	public String getDate() {
		return date;
	}

	public void setDate(String date) {
		this.date = date;
	}

	public String getExtraInfo() {
		return extraInfo;
	}

	public void setExtraInfo(String extraInfo) {
		this.extraInfo = extraInfo;
	}

	public Description getDescription() {
		return description;
	}

	public void setDescription(Description description) {
		this.description = description;
	}

	


	public WhereIFound getSource() {
		return source;
	}



	public void setSource(WhereIFound source) {
		this.source = source;
	}



	public String getPosition() {
		return position;
	}



	public void setPosition(String position) {
		this.position = position;
	}
	
	
	

}
