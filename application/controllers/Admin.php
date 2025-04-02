<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Admin extends CI_Controller {
    public function __construct() {
        parent::__construct();
        // $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<p class="invalid-feedback">', '</p>');
        $this->load->model('Booking_model', 'BookingModel');
        $this->load->model('User_model', 'UserModel');
        
        // Set security headers
        $this->output->set_header('X-Frame-Options: DENY');
        $this->output->set_header('X-XSS-Protection: 1; mode=block');
        $this->output->set_header('X-Content-Type-Options: nosniff');
        $this->output->set_header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        
        // Enable CSRF protection for POST requests
        if ($this->input->method() === 'post') {
            $this->security->csrf_verify();
        }
    }
 
    public function panel() {
        if (!$this->session->userdata('IS_ADMIN')) {
            redirect('user/login');
        }
		$data['page_title'] = getenv('CLUB_SHORTNAME') . " Bookings - Admin";
		$this->load->view('_Layout/home/header.php', $data);
		$this->load->view("admin/menu");
		$this->load->view('_Layout/home/footer.php');
    }
	
	public function add_days() {
	    if (!$this->session->userdata('IS_ADMIN')) {
            redirect('user/login');
        }
		$date_val = $this->input->post('add_days');
		$day_of_year = date("Y", strtotime($date_val)) . date("z", strtotime($date_val));
		$insert = array('date'=>$date_val, 'day_of_year'=>$day_of_year, 'manually_added'=>1);
		$result = $this->db->insert("day", $insert);
		if($result) {
		    $this->UserModel->log($this->session->userdata('USER_ID'), "day $date_val added");
			redirect(base_url("admin/panel"));
		} else {
			 echo "ERROR";
		}
	}
	
	public function add_qual_meta() {
	    if (!$this->session->userdata('IS_ADMIN')) {
            redirect('user/login');
        }
        
        // Validate input
        $this->form_validation->set_rules('title', 'Title', 'required|trim|max_length[100]|xss_clean');
        $this->form_validation->set_rules('desc', 'Description', 'trim|max_length[500]|xss_clean');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect(base_url("admin/panel?tab=qualifications"));
            return;
        }
        
        $data['title'] = $this->input->post('title', TRUE);
        $data['desc'] = $this->input->post('desc', TRUE);
        
        $insert = $this->db->insert('quals_meta', $data);
        if($insert) {
            $this->UserModel->log($this->session->userdata('USER_ID'), "qualification " . $data['title'] . " added");
            $this->session->set_flashdata('success', "Qualification added successfully.");
            redirect(base_url("admin/panel?tab=qualifications"));
        } else {
            $this->session->set_flashdata('error', "Failed to add qualification.");
            redirect(base_url("admin/panel?tab=qualifications"));
        }
	}
	
	public function del_qual_meta($id) {
	    if (!$this->session->userdata('IS_ADMIN')) {
            redirect('user/login');
        }
        $this->UserModel->log($this->session->userdata('USER_ID'), "qualification deleted");
	    $this->db->where('id', $id);
	    $this->db->delete('quals_meta');
	    $this->db->where('qual_id', $id);
	    $this->db->delete('quals');
	    redirect(base_url("admin/panel?tab=qualifications"));
	}
	
	public function remove_day() {
	    if (!$this->session->userdata('IS_ADMIN')) {
            redirect('user/login');
        }
	    $day_id = $this->input->post("remove_day");
	    $this->db->where('id', $day_id);
	    $this->db->delete('day');
	    $this->UserModel->log($this->session->userdata('USER_ID'), "day $day_id removed");
	    redirect(base_url("admin/panel?tab=flying-days"));
	}
	
	public function suspend($member_id) {
	    if (!$this->session->userdata('IS_ADMIN')) {
            redirect('user/login');
        }
	    $this->db->where('id', $member_id);
	    $query = $this->db->update("members", array('is_active'=>0));
	    $user = $this->UserModel->get_member($member_id)->email;
	    $this->UserModel->log($this->session->userdata('USER_ID'), "member $user suspended");
	    redirect(base_url("admin/panel?tab=member-list"));
	}
	
	public function get_comment($day_id) {
	    $this->db->where('id', $day_id);
	    $query = $this->db->get('day');
	    if($query->num_rows() > 0) {
	        echo $query->row()->comment;
	    } 
	}
	
	public function add_document() {
	    if (!$this->session->userdata('IS_ADMIN')) {
            redirect('user/login');
        }
	    $config['upload_path'] = 'uploads/';
        $config['allowed_types'] = 'pdf|doc|docx|txt|rtf|jpg|jpeg|png|gif';
        $config['max_size'] = 10240; // 10MB
        $config['encrypt_name'] = TRUE;
        $config['remove_spaces'] = TRUE;
        $config['detect_mime'] = TRUE;
	    $this->load->library('upload', $config);
        if (!$this->upload->do_upload()) {
            $error = array('error' => $this->upload->display_errors());
            $this->session->set_flashdata('warning_flashData', $error['error']);
    	    redirect(base_url("admin/panel?tab=library"));
    	} else {
    	    $data = $this->upload->data();
    	    $file_name = $this->upload->data('file_name');  
    	    $insert['url'] = base_url('uploads/' . $file_name);
    	    $insert['title'] = $this->input->post('title');
    	    $insert['description'] = $this->input->post('description');
    	    $insert['date'] = date('Y-m-d');
    	    $insert['member_id'] = $this->session->userdata("USER_ID");
    	    $result = $this->db->insert("library", $insert);
    	    if($result) {
    	        $this->session->set_flashdata('success_flashData', "Document Added Successfully.");
    	        $this->UserModel->log($this->session->userdata('USER_ID'), "document " . $insert['title'] . " added to library");
    	        redirect(base_url("admin/panel?tab=library"));
    	    } else {
    	        $this->session->set_flashdata('warning_flashData', "Document failed to upload.");
    	        redirect(base_url("admin/panel?tab=library"));
    	    }
    	}
	}
	
	public function library() {
	    $data['page_title'] = getenv('CLUB_SHORTNAME') . " Bookings - Document Library";
		$this->load->view('_Layout/home/header.php', $data);
		$this->load->view("admin/library");
		$this->load->view('_Layout/home/footer.php');
	}
	
	public function delete_document($doc_id) {
	    if (!$this->session->userdata('IS_ADMIN')) {
            redirect('user/login');
        }
	    $this->db->where('id', $doc_id);
	    $this->db->delete('library');
	    if($this->db->affected_rows() > 0) {
	        $this->session->set_flashdata('warning_flashData', "Document Deleted Successfully.");
	        $this->UserModel->log($this->session->userdata('USER_ID'), "document deleted from library");
	        redirect(base_url("admin/panel?tab=library"));
	    }
	}
	
	public function day_comment() {
    	if (!$this->session->userdata('IS_ADMIN')) {
            redirect('user/login');
        }
        
        // Validate input
        $this->form_validation->set_rules('comment_day_id', 'Day ID', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('day_comment_value', 'Comment', 'trim|max_length[1000]|xss_clean');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect(base_url("admin/panel?tab=flying-days"));
            return;
        }
        
        $day_id = $this->input->post('comment_day_id', TRUE);
        $comment = $this->input->post('day_comment_value', TRUE);
        
        $this->db->where('id', $day_id);
        $update = $this->db->update("day", array('comment' => $comment));
        
        if($update) {
            $this->UserModel->log($this->session->userdata('USER_ID'), "day $day_id comment edited");
            $this->session->set_flashdata('success', "Day comment updated successfully.");
        } else {
            $this->session->set_flashdata('error', "Failed to update day comment.");
        }
        
        redirect(base_url("admin/panel?tab=flying-days"));
    }
	
	
	public function unsuspend($member_id) {
		if (!$this->session->userdata('IS_ADMIN')) {
            redirect('user/login');
        }
	    $this->db->where('id', $member_id);
	    $query = $this->db->update("members", array('is_active'=>1));
	    $user = $this->UserModel->get_member($member_id)->email;
	    $this->UserModel->log($this->session->userdata('USER_ID'), "member $user un-suspended");
	    redirect(base_url("admin/panel?tab=member-list"));
	}
	
	public function edit_glider($glider_id) {
	    if (!$this->session->userdata('IS_ADMIN')) {
            redirect('user/login');
        }
        
        // Validate input
        $this->form_validation->set_rules('title', 'Title', 'required|trim|max_length[45]|xss_clean');
        $this->form_validation->set_rules('description', 'Description', 'trim|max_length[255]|xss_clean');
        $this->form_validation->set_rules('airworthy', 'Airworthy Status', 'required|in_list[0,1]');
        $this->form_validation->set_rules('airworthy_comment', 'Airworthy Comment', 'trim|max_length[500]|xss_clean');
        $this->form_validation->set_rules('unserviceable_start', 'Unserviceable Start', 'trim|valid_date');
        $this->form_validation->set_rules('unserviceable_end', 'Unserviceable End', 'trim|valid_date');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect(base_url("admin/panel?tab=glider-status"));
            return;
        }
        
        $update = array(
            'title' => $this->input->post('title', TRUE),
            'description' => $this->input->post('description', TRUE),
            'airworthy' => $this->input->post('airworthy', TRUE),
            'airworthy_comment' => $this->input->post('airworthy_comment', TRUE),
            'unserviceable_start' => $this->input->post('unserviceable_start', TRUE),
            'unserviceable_end' => $this->input->post('unserviceable_end', TRUE)
        );
        
        $this->db->where('id', $glider_id);
        $result = $this->db->update('gliders_meta', $update);
        
        if($result) {
            $this->UserModel->log($this->session->userdata('USER_ID'), "glider " . $update['title'] . " edited");
            $this->session->set_flashdata('success', "Glider updated successfully.");
        } else {
            $this->session->set_flashdata('error', "Failed to update glider.");
        }
        
        redirect(base_url("admin/panel?tab=glider-status"));
    }
	
	public function gliders() {
		  if (empty($this->session->userdata('USER_ID'))) {
            redirect('user/login');
        } else {
			$member_id = $this->session->userdata('USER_ID');
		}
		$data['page_title'] = getenv('CLUB_SHORTNAME') . " Bookings - Gliders";
		$this->load->view('_Layout/home/header.php', $data);
		$this->load->view("admin/gliders");
		$this->load->view('_Layout/home/footer.php');
    }
	
	public function clear($glider_id) {
	    if (!$this->session->userdata('IS_ADMIN')) {
            redirect('user/login');
        }
		$update['unserviceable_start'] = '';
		$update['unserviceable_end'] = '';
		$this->db->where('id', $glider_id);
		$update = $this->db->update('gliders_meta', $update);
		if($update) {
			redirect(base_url("admin/panel?tab=glider-status"));
		} else {
			echo "ERROR!!!";
		}
	}

	public function add_glider() {
		// Validate input
		$this->form_validation->set_rules('title', 'Title', 'required|trim|max_length[45]');
		$this->form_validation->set_rules('description', 'Description', 'trim|max_length[255]');
		$this->form_validation->set_rules('airworthy', 'Airworthy Status', 'required|in_list[0,1]');

		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect('admin/panel?tab=glider-status');
			return;
		}

		// Prepare data for insertion
		$data = array(
			'title' => $this->input->post('title'),
			'description' => $this->input->post('description'),
			'airworthy' => $this->input->post('airworthy'),
			'airworthy_comment' => '',
			'unserviceable_start' => NULL,
			'unserviceable_end' => NULL
		);

		// Insert the new glider
		$this->db->insert('gliders_meta', $data);

		if ($this->db->affected_rows() > 0) {
			$this->UserModel->log($this->session->userdata('USER_ID'), "Added new glider: " . $this->input->post('title'));
			$this->session->set_flashdata('success', 'Glider added successfully');
		} else {
			$this->session->set_flashdata('error', 'Failed to add glider');
		}

		redirect('admin/panel?tab=glider-status');
	}

	public function delete_user($user_id) {
	    if (!$this->session->userdata('IS_ADMIN')) {
            redirect('user/login');
        }
	    $this->db->where('id', $user_id);
	    $this->db->delete('users');
	    if($this->db->affected_rows() > 0) {
	        $this->session->set_flashdata('warning_flashData', "User Deleted Successfully.");
	        $this->UserModel->log($this->session->userdata('USER_ID'), "user deleted");
	        redirect(base_url("admin/panel?tab=users"));
	    }
	}
}