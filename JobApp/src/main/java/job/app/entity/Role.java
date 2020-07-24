package job.app.entity;

import javax.persistence.Entity;
import javax.persistence.Id;

@Entity(name = "roles")
public class Role {

	@Id
	private String authority;

	public Role() {
	}

	public Role(String authority) {
		super();
		this.authority = authority;
	}

	public String getAuthority() {
		return authority;
	}

	public void setAuthority(String authority) {
		this.authority = authority;
	}

	@Override
	public String toString() {
		return authority;
	}

}
