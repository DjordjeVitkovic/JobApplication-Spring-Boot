package job.app;

import java.util.List;
import java.util.Optional;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.DeleteMapping;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.ModelAttribute;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.ResponseBody;

import job.app.entity.Description;
import job.app.entity.Job;
import job.app.entity.User;
import job.app.entity.WhereIFound;
import job.app.repository.DescriptionRepo;
import job.app.repository.JobRepo;
import job.app.repository.UserRepository;
import job.app.repository.WhereIFoundRepo;

@Controller
@RequestMapping("")
public class ApplicationController {
	
	@Autowired
	private JobRepo jobRepo;
	@Autowired
	private DescriptionRepo descriptionRepo;
	@Autowired
	private WhereIFoundRepo whereIFoundRepo;
	@Autowired
	private UserRepository userRepo;

	@GetMapping("/back")
	public String getBackSide(Model model) {
		
		List<Job> list = jobRepo.findAll();
		
		model.addAttribute("jobs", list);
		
		return "back";
	}
	@GetMapping("/back/{username}")
	@ResponseBody
	public User getUser(@PathVariable(name = "username") String username, Model model) {
		
		User u = userRepo.findByUsername(username);
		
		return u;
	}
	
	@PostMapping("/back/add")
	@ResponseBody
	public User addUser(@RequestBody User user, Model model) {
		
		String passwordEncode = new BCryptPasswordEncoder().encode(user.getPassword());

		user.setEnabled(true);
		user.setPassword(passwordEncode);
		
		userRepo.save(user);
		
		return user;
	}
	
	@RequestMapping("/delete/{id}")
	public String deleteJob(@PathVariable(name = "id") int id) {
		
		jobRepo.deleteById(id);
		
		return "redirect:/back";
	}
	
	@RequestMapping("/updateJob/{id}")
	public String updateJob(@PathVariable("id") int id,@RequestParam("desc") String interviewDate, @RequestParam(value = "response", required = false) boolean response) {
		
		Job job = jobRepo.getOne(id);
		
		if(response == job.getDescription().getResponse()) {
			
		}else {
			job.getDescription().setResponse(!job.getDescription().getResponse());

		}
		
		job.getDescription().setInterviewDate(interviewDate);
		
		jobRepo.save(job);
	
		return "redirect:/back";
	}
	
	@GetMapping("/search")
	public String searchJob(@RequestParam ("word") String word, Model model) {
		
		model.addAttribute("jobs", jobRepo.searchJob(word));
		
		return "back";
	}
	
	@GetMapping("/sort")
	public String sortJobs(@RequestParam(name = "num") int num, Model model) {
		
		if(num ==1) {
			model.addAttribute("jobs", jobRepo.findAllByOrderByDateAsc());
			
		}else if(num == 2) {
			model.addAttribute("jobs",jobRepo.findAllByOrderByCompanyNameAsc());
		}else if(num == 3 ) {
			model.addAttribute("jobs", jobRepo.findAllByOrderByPositionAsc());
		}else if(num == 4) {
			model.addAttribute("jobs", jobRepo.findAllResponses());
		}else if(num == 5) {
			model.addAttribute("jobs", jobRepo.findAllNoResponses());
		}
		
		
		return "back";
	}
	
	
	
	
	
	
	
	
	
	
	
	
/* FRONT
 * FRONT 
 * FRONT
 * FRONT
 * FRONT
 * FRONT
 * */		
	
	@GetMapping("")
	public String getFrontSide(Model model) {
		
		model.addAttribute("job", new Job());
		model.addAttribute("soruseList", whereIFoundRepo.findAll());
		
		return "front";
	}
	
	@PostMapping("/saveJob")
	public String saveJob(@ModelAttribute Job job) {
		
		Description d = new Description();
		d.setJob(job);
		descriptionRepo.save(d);
		job.setSource(whereIFoundRepo.findById(Integer.parseInt(job.getSource().toString())));
		jobRepo.save(job);
		
		return "redirect:/back";
	}
	
}
