<?php defined('BASEPATH') OR exit('No direct script access allowed');
class User extends CI_Controller {
    public function __construct() {
        parent::__construct();
        // $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<p class="invalid-feedback">', '</p>');
        $this->load->model('User_model', 'UserModel');
        $this->load->model('Const_model', 'ConstModel');
        
        // Set security headers
        $this->output->set_header('X-Frame-Options: DENY');
        $this->output->set_header('X-XSS-Protection: 1; mode=block');
        $this->output->set_header('X-Content-Type-Options: nosniff');
        $this->output->set_header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        
        // Only verify CSRF for POST requests
        if ($this->input->method() === 'post') {
            if ($this->input->post($this->config->item('csrf_token_name')) !== $this->input->cookie($this->config->item('csrf_cookie_name'))) {
                show_error('The action you have requested is not allowed.');
            }
        }
    }
    /**
     * User Registration
     */
    public function registration() {
        
        $reg_key = $this->UserModel->registration_key();
        
        $this->form_validation->set_rules('first', 'First Name', 'required');
		$this->form_validation->set_rules('last', 'Last Name', 'required');
        $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email|is_unique[members.email]', [
            'is_unique' => 'The %s already exists. Please use a different email',
		]); // // Unique Field
        $this->form_validation->set_rules('phone', 'Phone Number', 'required|min_length[10]|max_length[10]|numeric');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');
        $this->form_validation->set_rules('reg_key', 'Registration Key', 'required');
        if ($this->form_validation->run() == FALSE) {
            $data['page_title'] = $this->ConstModel->clubshortname() . " Member Registration";
            $data['reg_key'] = $reg_key;
            $this->load->view('_Layout/home/header.php', $data);
            $this->load->view("user/registration");
            $this->load->view('_Layout/home/footer.php');
        } else {   
            if(strtolower($this->input->post('reg_key')) !== $reg_key) {
                 $this->session->set_flashdata('error_flashData', 'The entered registration key is incorrect. Please contact a club member.');
                redirect('User/registration');
            } 
			date_default_timezone_set($this->ConstModel->timezone());
            $insert_data = array(
                'first' => $this->input->post('first', TRUE),
                'last' => $this->input->post('last', TRUE),
                'email' => $this->input->post('email', TRUE),
                'phone' => $this->input->post('phone', TRUE),
				'postal_address' => $this->input->post('postal_address', TRUE),
                'password' => password_hash($this->input->post('password', TRUE), PASSWORD_BCRYPT),
                'is_active' => 1,
				'admin' => 0,
                'created' => date('Y-m-d h:i:s a', time()),
                'updated' =>date('Y-m-d h:i:s a', time())
            );
            $this->load->model('User_model', 'UserModel');
            $result = $this->UserModel->insert_user($insert_data);
            if ($result == TRUE) {
                $this->session->set_flashdata('success_flashData', 'You have registered successfully.');
                redirect('User/login');
            } else {
                $this->session->set_flashdata('error_flashData', 'Invalid Registration.');
                redirect('User/registration');
            }
        }
    }
    
       public function edit_member($member_id) {
        $data['member'] = $this->UserModel->get_member($member_id);
        $data['q'] = $this->UserModel->get_qualifications($member_id);
        $data['all_quals'] = $this->UserModel->get_unselected_qualifications($member_id);
        $data['member_id'] = $member_id;
        $this->form_validation->set_rules('first', 'First Name', 'required');
		$this->form_validation->set_rules('last', 'Last Name', 'required');
        $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email', [
            'is_unique' => 'The %s already exists. Please use a different email',
		]); // // Unique Field
        $this->form_validation->set_rules('phone', 'Phone Number', 'required|min_length[10]|max_length[10]|numeric');
    //    $this->form_validation->set_rules('password', 'Password', 'required');
    //    $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');
        if ($this->form_validation->run() == FALSE) {
            $data['page_title'] = getenv('CLUB_SHORTNAME') . " Member Edit";
            $this->load->view('_Layout/home/header.php', $data);
            $this->load->view("user/edit");
            $this->load->view('_Layout/home/footer.php');
        } else {   
			date_default_timezone_set($this->ConstModel->timezone());
            $insert_data = array(
                'first' => $this->input->post('first', TRUE),
                'last' => $this->input->post('last', TRUE),
                'email' => $this->input->post('email', TRUE),
                'phone' => $this->input->post('phone', TRUE),
				'postal_address' => $this->input->post('postal_address', TRUE),
                'updated' =>date('Y-m-d h:i:s a', time())
            );
            if($this->input->post('password') != '') {
                if($this->input->post('password') != $this->input->post('passconf')) {
                     $this->session->set_flashdata('error_flashData', "Password & Password confirmation don't match.");
                    redirect("User/edit_member/$member_id");
                } else {
                    $insert_data['password'] = password_hash($this->input->post('password', TRUE), PASSWORD_BCRYPT);
                }
            }
            $this->db->where('id', $member_id);
            $result = $this->db->update('members', $insert_data);
  //          $result = $this->UserModel->insert_user($insert_data);
	//		echo "RESULT IS $result";
            if ($result == TRUE) {
                $this->UserModel->log($this->session->userdata('USER_ID'), "Member " . $insert_data['email'] . " updated");
                $this->session->set_flashdata('success_flashData', 'Member Updated Successfully.');
                redirect('admin/panel?tab=member-list');
            } else {
                $this->session->set_flashdata('error_flashData', 'Invalid Registration.');
                redirect('User/edit/$member_id');
            }
        }
    }

 

	public function login() {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');
        if ($this->form_validation->run() == FALSE) {
            $data['page_title'] = "User Login";
            $this->load->view('_Layout/home/header.php', $data); // Header File
            $this->load->view("user/login");
            $this->load->view('_Layout/home/footer.php'); // Footer File
        } else {
            $login_data = array(
                'email' => $this->input->post('email', TRUE),
                'password' => $this->input->post('password', TRUE),
            );
            /**
             * Load User Model
             */
            $this->load->model('User_model', 'UserModel');
            $result = $this->UserModel->check_login($login_data);
            if (!empty($result['status']) && $result['status'] === TRUE) {
                if($result['data']->is_active == 1) {
    				$this->load->model('Booking_model', 'BookingModel');
    				$this->BookingModel->check_create_days();
    				$user_id = $result['data']->id;
                    $session_array = array(
                        'USER_ID'  => $result['data']->id,
                        'USER_FIRSTNAME'  => $result['data']->first,
    					'USER_LASTNAME' => $result['data']->last,
                        'USER_EMAIL' => $result['data']->email,
                        'IS_ACTIVE'  => $result['data']->is_active,
    					'IS_ADMIN' => $result['data']->admin
                    );
                    // user logged in successfully, so update last_login 
                    $login = $this->UserModel->record_login($user_id);
                    $this->session->set_userdata($session_array);
                    $this->session->set_flashdata('success_flashData', 'Login Success');
                    redirect(base_url());
                } else {
                    $this->session->set_flashdata('error_flashData', 'Your booking account is current disabled.');
                    redirect('User/login');
                }
            } else {
                $this->session->set_flashdata('error_flashData', 'Invalid Email/Password.');
                redirect('User/login');
            }
        }
    }
    
    public function ext_login() {
         $login_data = array(
                'email' => $this->input->post('Username', TRUE),
                'password' => $this->input->post('Password', TRUE),
            );
            /**
             * Load User Model
             */
            $this->load->model('User_model', 'UserModel');
            $result = $this->UserModel->check_login($login_data);
            if (!empty($result['status']) && $result['status'] === TRUE) {
                if($result['data']->is_active == 1) {
    				$this->load->model('Booking_model', 'BookingModel');
    				$this->BookingModel->check_create_days();
                    $session_array = array(
                        'USER_ID'  => $result['data']->id,
                        'USER_FIRSTNAME'  => $result['data']->first,
    					'USER_LASTNAME' => $result['data']->last,
                        'USER_EMAIL' => $result['data']->email,
                        'IS_ACTIVE'  => $result['data']->is_active,
    					'IS_ADMIN' => $result['data']->admin
                    );
                    $this->session->set_userdata($session_array);
                    $this->session->set_flashdata('success_flashData', 'Login Success');
                    redirect(base_url());
                } else {
                    $this->session->set_flashdata('error_flashData', 'Your booking account is current disabled.');
                    redirect('User/login');
                }
            } else {
                $this->session->set_flashdata('error_flashData', 'Invalid Email/Password.');
                redirect('User/login');
            }
    }
    
    public function forgot() {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        if ($this->form_validation->run() == FALSE) {
            $data['page_title'] = "Forgot Password";
            $this->load->view('_Layout/home/header.php', $data); // Header File
            $this->load->view("user/forgot");
            $this->load->view('_Layout/home/footer.php'); // Footer File
        } else {
            $login_data = array(
                'email' => $this->input->post('email', TRUE),
            );
            $this->load->model('User_model', 'UserModel');
            $result = $this->UserModel->check_email($login_data);
            if ($result['status']) {
				// email address exists, create token and email to user
				$token = md5(uniqid(mt_rand()));
				$this->db->where('id', $result['id']);
				$update = $this->db->update('members', array('password_reset_token'=>$token));
				$this->load->library('email');
				$this->email->from($this->ConstModel->clubemail(), $this->ConstModel->clubshortname() . ' Bookings');
                $this->email->to($this->input->post('email'));
                $this->email->subject($this->ConstModel->clubshortname() . ' Bookings - Reset Password');
                $this->email->message("<a href='" . base_url('User/reset_password/' . $token) . "'>Click Here</a> to reset your password");
                $this->email->set_mailtype("html");
                $this->email->send();
            }
            $this->session->set_flashdata('success_flashData', '<strong>Thank You.</strong><br>If your email address exists in our system, you will receive an email containing instructions on resetting your password.');
            redirect('User/login');
        }
    }
    
    public function reset_password($token) {
        $query = $this->db->query("SELECT * FROM `members` WHERE `password_reset_token`='$token'");
        if($query->num_rows() == 1) {
            $result['data'] = $query->row();
            $session_array = array(
                'TEMP_USER_ID'  => $result['data']->id
            );
            $this->session->set_userdata($session_array);
            $this->session->set_flashdata('success_flashData', 'Please enter a new password');
            redirect(base_url("user/edit_password/" . $result['data']->id));
        } else {
            $remove_sessions = array('USER_ID', 'USERNAME','USER_EMAIL','IS_ACTIVE', 'USER_NAME');
            $this->session->unset_userdata($remove_sessions);
            redirect('User/login');
        }
    }
    
    public function remove_qual($row_id, $member_id) {
        $query = $this->db->query("DELETE FROM `quals` WHERE `id`='$row_id'");
        if($query) {
            redirect("User/edit_member/$member_id");
        }
    }
    
    public function add_qual($member_id) {
        $qual_id = $this->input->post('add_qual');
        if($qual_id != '') {
            $insert = $this->db->insert("quals", array('member_id'=>$member_id, 'qual_id'=>$qual_id));
            if($insert) {
                redirect("User/edit_member/$member_id");
            }
        }
    }
    
    /**
     * User Logout
     */
    public function logout() {
        /**
         * Remove Session Data
         */
        $remove_sessions = array('USER_ID', 'USERNAME','USER_EMAIL','IS_ACTIVE', 'USER_NAME');
        $this->session->unset_userdata($remove_sessions);
        redirect('User/login');
    }
    
    public function delete_member($member_id) {
        $user = $this->UserModel->get_member($member_id)->email;
        $query = $this->db->query("DELETE FROM `members` WHERE `id`='$member_id'");
        $query = $this->db->query("DELETE FROM `quals` WHERE `member_id`='$member_id'");
        $this->UserModel->log($this->session->userdata('USER_ID'), "Member $user deleted");
        redirect(base_url("admin/panel?tab=member-list"));
    }
    
    public function edit_profile($member_id) {
        if (empty($this->session->userdata('USER_ID'))) {
            redirect('user/login');
        } else {
			$member_id = $this->session->userdata('USER_ID');
		}
        $data['member'] = $this->UserModel->get_member($member_id);
        $data['q'] = $this->UserModel->get_qualifications($member_id);
        $data['all_quals'] = $this->UserModel->get_unselected_qualifications($member_id);
        $data['member_id'] = $member_id;
        $this->form_validation->set_rules('first', 'First Name', 'required');
		$this->form_validation->set_rules('last', 'Last Name', 'required');
        $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email', [
            'is_unique' => 'The %s already exists. Please use a different email',
		]); // // Unique Field
        $this->form_validation->set_rules('phone', 'Phone Number', 'required|min_length[10]|max_length[10]|numeric');
    //    $this->form_validation->set_rules('password', 'Password', 'required');
    //    $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');
        if ($this->form_validation->run() == FALSE) {
            $data['page_title'] = $this->ConstModel->clubshortname() . " Member Edit";
            $this->load->view('_Layout/home/header.php', $data);
            $this->load->view("user/edit");
            $this->load->view('_Layout/home/footer.php');
        } else {   
			date_default_timezone_set($this->ConstModel->timezone());
            $insert_data = array(
                'first' => $this->input->post('first', TRUE),
                'last' => $this->input->post('last', TRUE),
                'email' => $this->input->post('email', TRUE),
                'phone' => $this->input->post('phone', TRUE),
				'postal_address' => $this->input->post('postal_address', TRUE),
			//	'admin' => 0,
                'updated' =>date('Y-m-d h:i:s a', time())
            );
            if($this->input->post('password') != '') {
                $insert_data['password'] = password_hash($this->input->post('password', TRUE), PASSWORD_BCRYPT);
                
            }
            $this->db->where('id', $member_id);
            $result = $this->db->update('members', $insert_data);
  //          $result = $this->UserModel->insert_user($insert_data);
//			echo "RESULT IS $result";
            if ($result == TRUE) {
                $this->UserModel->log($this->session->userdata('USER_ID'), "Member " . $insert_data['email'] . " updated");
                $this->session->set_flashdata('success_flashData', 'Profile Updated Successfully.');
                redirect("user/edit_profile/$member_id");
            } else {
                $this->session->set_flashdata('error_flashData', 'Invalid Registration.');
                redirect('User/edit/$member_id');
            }
        }
    }
    
    public function edit_password($member_id) {
        if (empty($this->session->userdata('TEMP_USER_ID'))) {
            redirect('user/login');
        } else {
            $old_id = $member_id;
			$member_id = $this->session->userdata('TEMP_USER_ID');
			if($old_id != $member_id) {
			    $this->session->set_flashdata('danger_flashData', 'Not Today Champ.');
                redirect("user/login");
			}
		}
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');
        if ($this->form_validation->run() == FALSE) {
            $data['page_title'] = getenv('CLUB_SHORTNAME') . " Password Reset";
            $this->load->view('_Layout/home/header.php', $data);
            $this->load->view("user/pw");
            $this->load->view('_Layout/home/footer.php');
        } else {   
			date_default_timezone_set($this->ConstModel->timezone());
            if($this->input->post('password') != '') {
                $insert_data['password'] = password_hash($this->input->post('password', TRUE), PASSWORD_BCRYPT);
                $insert_data['password_reset_token'] = '';
                $insert_data['updated'] = date('Y-m-d h:i:s a', time());
            }
            $this->db->where('id', $member_id);
            $result = $this->db->update('members', $insert_data);
            if ($result == TRUE) {
                $this->session->set_flashdata('success_flashData', 'Password Reset - Please Login.');
                redirect("user/login");
            } else {
                $this->session->set_flashdata('error_flashData', 'Please Try Again');
                redirect('User/login');
            }
        }
    }
    
    /**
     * User Panel
     */
    public function panel() {
        if (empty($this->session->userdata('USER_ID'))) {
            redirect('user/login');
        }
        $data['page_title'] = "Welcome to the " . $this->ConstModel->clubshortname() . " Booking System";
        $data['reg_key'] = $this->UserModel->registration_key();
        $this->load->view('_Layout/home/header.php', $data); // Header File
        $this->load->view("user/bookings", $data);
        $this->load->view('_Layout/home/footer.php'); // Footer File
    }
    
  
    
}