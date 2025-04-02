<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bookings extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index() {
		if (empty($this->session->userdata('USER_ID'))) {
            redirect('user/login');
        }
		$this->load->model('Booking_model', 'BookingModel');
		$this->load->model('User_model', 'UserModel');
		$data['reg_key'] = $this->UserModel->registration_key();
		$data['bookings'] = $this->BookingModel->get_bookings();
		$data['days'] = $this->BookingModel->booking_days();
		$data['page_title'] = "AUGC Member Bookings";
		$this->load->view('_Layout/home/header.php', $data);
		$this->load->view('user/bookings');
		$this->load->view('_Layout/home/footer.php');
	}
	
	public function email_summary() {
	    $this->load->model('Booking_model', 'BookingModel');
	    if (empty($this->session->userdata('USER_ID'))) {
            redirect('user/login');
        }
		$email_content = $this->BookingModel->form_email();
		$subject = "AUGC Flying List";
		$to = $this->input->post('email');
		$from = getenv('EMAIL_FROM');
		// send email
	//	$this->load->config('email');
        $this->load->library('email');
        $this->email->set_mailtype("html");
        $this->email->from($from);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($email_content);

        if ($this->email->send()) {
            $this->session->set_flashdata('success_flashData', 'Summary Email Sent.');
        } else {
            show_error($this->email->print_debugger());
        }
        redirect('');
	}
	
	public function del($booking_id) {
		$this->load->model('Booking_model', 'BookingModel');
		$booking = $this->BookingModel->get_booking($booking_id);
		if (empty($this->session->userdata('USER_ID'))) {
            redirect('user/login');
        }
		if($this->session->userdata('USER_ID') != $booking->member_id) {
			redirect(base_url());
		}
		$this->db->query("DELETE FROM `booking` WHERE `id`='$booking_id'");
		redirect(base_url());
	}
	
	public function edit_booking($booking_id) {
		if (empty($this->session->userdata('USER_ID'))) {
            redirect('user/login');
        } else {
			$member_id = $this->session->userdata('USER_ID');
		}
	    $this->load->model('User_model', 'UserModel');
		if($this->UserModel->get_member($member_id)->guest_bookings == 1) {
		    // the person making this booking can add guest bookings
		    $data['guest'] = true;
		} else {
		    $data['guest'] = false;
		}
		$this->load->model('Booking_model', 'BookingModel');
		$data['booking'] = $this->BookingModel->get_booking($booking_id);
		$data['days'] = $this->BookingModel->days();
		$data['end_days'] = $this->BookingModel->end_days();
		$data['gliders'] = $this->BookingModel->get_gliders();
        $this->form_validation->set_rules('date', 'Booking Date', 'required');
		$this->form_validation->set_rules('end_date', 'Booking End Date', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $data['page_title'] = "Make a Booking";
            $this->load->view('_Layout/home/header.php', $data);
            $this->load->view("booking/edit");
            $this->load->view('_Layout/home/footer.php');
        } else {   
			$insert = array();
			$insert['member_id'] = $member_id;
			$insert['day_id_start'] = $this->input->post('date');
			$insert['day_id_end'] = $this->input->post('end_date');
			$insert['accommodation'] = $this->input->post('accommodation');
			$two_seater = $this->input->post('two_seater');
			$transport = $this->input->post('transport');
			$insert['seats'] = $this->input->post('spare_seats');
			$pickup_location = $this->input->post('pickup_location');
			$pickup_other = $this->input->post('pickup_other');
			$insert['comment'] = $this->input->post('comment');
			$insert['glider_id'] = $this->input->post('glider_booking');
			$insert['guest_booking'] = $this->input->post('guest_booking');
			if($insert['guest_booking'] == 1) {
			    $insert['guest_first'] = $this->input->post('guest_first');
			    $insert['guest_last'] = $this->input->post('guest_last');
			    $insert['guest_weight'] = $this->input->post('guest_weight');
			    $duplicate_check = false;
			} else {
			    $insert['guest_first'] = '';
			    $insert['guest_last'] = '';
			    $insert['guest_weight'] = '';
		//	    $duplicate_check = $this->BookingModel->duplicate_booking_check(0, $member_id, $insert['day_id_start'], $insert['day_id_end']);
			}
		//	if($duplicate_check) {	
		//		$this->session->set_flashdata('error_flashData', 'You have already created a booking that includes this period');
		//		redirect(base_url("bookings/edit_booking/$booking_id"));
		//	}
			if($two_seater == 'both') {
				$insert['two_seater'] = 1;
				$insert['instructor'] = 1;
			} elseif ($two_seater == 'two_seater') {
				$insert['two_seater'] = 1;
				$insert['instructor'] = 0;
			} else {
				$insert['two_seater'] = 0;
				$insert['instructor'] = 0;
			}
			if($transport == 'spare') {
			    $insert['collect_from'] = '';
				if($pickup_location == 'other') {
					$insert['pickup_from'] = $pickup_other;
				} else {
					$insert['pickup_from'] = $pickup_location;
				}
			} elseif ($transport == 'need') {
			    $insert['pickup_from'] = '';
			    $insert['seats'] = 0;
				if($pickup_location == 'other') {
					$insert['collect_from'] = $pickup_other;
				} else {
					$insert['collect_from'] = $pickup_location;
				}
			} else {
			    $insert['pickup_from'] = '';
			    $insert['collect_from'] = '';
			    $insert['seats'] = 0;
			}
			
			date_default_timezone_set('Australia/Adelaide');
			$insert['created'] = date('Y-m-d h:i:s a', time());
			$insert['updated'] = date('Y-m-d h:i:s a', time());
			$result = $this->BookingModel->update_booking($booking_id, $insert);
			echo "RESULT IS $result";
            if ($result == TRUE) {
                $this->session->set_flashdata('success_flashData', 'Booking Updated.');
                redirect('');
            } else {
                $this->session->set_flashdata('error_flashData', 'Invalid Booking.');
                redirect(base_url());
            }
        }
    }
	
	
	  public function make($day_id = NULL) {
		  if (empty($this->session->userdata('USER_ID'))) {
            redirect('user/login');
        } else {
			$member_id = $this->session->userdata('USER_ID');
		}
		
		if($day_id != NULL) {
			$date_query = $this->db->query("SELECT * FROM `day` WHERE `id`='$day_id'");
			if($date_query->num_rows()) {
				$day_id = $date_query->row()->day_of_year;
			} else {
				$day_id = NULL;
			}
		} else {
			$day_id = NULL;
		}
		$this->load->model('Booking_model', 'BookingModel');
		$this->load->model('User_model', 'UserModel');
		if($this->UserModel->get_member($member_id)->guest_bookings == 1) {
		    // the person making this booking can add guest bookings
		    $data['guest'] = true;
		} else {
		    $data['guest'] = false;
		}
		$data['days'] = $this->BookingModel->days();
		$data['end_days'] = $this->BookingModel->end_days();
		$data['gliders'] = $this->BookingModel->get_gliders();
		$data['day_id'] = $day_id;
        $this->form_validation->set_rules('date', 'Booking Date', 'required');
		$this->form_validation->set_rules('end_date', 'Booking End Date', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $data['page_title'] = "Make a Booking";
            $this->load->view('_Layout/home/header.php', $data);
            $this->load->view("booking/make");
            $this->load->view('_Layout/home/footer.php');
        } else {   
			$insert = array();
			$insert['member_id'] = $member_id;
			$insert['day_id_start'] = $this->input->post('date');
			$insert['day_id_end'] = $this->input->post('end_date');
			$insert['accommodation'] = $this->input->post('accommodation');
			$insert['guest_booking'] = $this->input->post('guest_booking');
			$two_seater = $this->input->post('two_seater');
			$transport = $this->input->post('transport');
			$insert['seats'] = $this->input->post('spare_seats');
			$pickup_location = $this->input->post('pickup_location');
			$pickup_other = $this->input->post('pickup_other');
			$insert['glider_id'] = $this->input->post('glider_booking');
			$insert['comment'] = $this->input->post('comment');
			if($insert['guest_booking'] == 1) {
			    $insert['guest_first'] = $this->input->post('guest_first');
			    $insert['guest_last'] = $this->input->post('guest_last');
			    $insert['guest_weight'] = $this->input->post('guest_weight');
			    $duplicate_check = false;
			} else {
			    $insert['guest_first'] = '';
			    $insert['guest_last'] = '';
			    $insert['guest_weight'] = '';
			    $duplicate_check = $this->BookingModel->duplicate_booking_check(0, $member_id, $insert['day_id_start'], $insert['day_id_end']);
			}
			if($duplicate_check) {	
				$this->session->set_flashdata('error_flashData', 'You have already created a booking that includes this period');
				redirect(base_url('bookings/make'));
			}
			if($two_seater == 'both') {
				$insert['two_seater'] = 1;
				$insert['instructor'] = 1;
			} elseif ($two_seater == 'two_seater') {
				$insert['two_seater'] = 1;
				$insert['instructor'] = 0;
			} else {
				$insert['two_seater'] = 0;
				$insert['instructor'] = 0;
			}
			if($transport == 'spare') {
				if($pickup_location == 'other') {
					$insert['pickup_from'] = $pickup_other;
				} else {
					$insert['pickup_from'] = $pickup_location;
				}
			} elseif ($transport == 'need') {
				if($pickup_location == 'other') {
					$insert['collect_from'] = $pickup_other;
				} else {
					$insert['collect_from'] = $pickup_location;
				}
			} 
			date_default_timezone_set('Australia/Adelaide');
			$insert['created'] = date('Y-m-d h:i:s a', time());
			$insert['updated'] = date('Y-m-d h:i:s a', time());
			$result = $this->BookingModel->insert_booking($insert);
//			echo "RESULT IS $result";
            if ($result == TRUE) {
                $this->session->set_flashdata('success_flashData', 'Booking Created.');
                redirect('');
            } else {
                $this->session->set_flashdata('error_flashData', 'Invalid Booking.');
                redirect(base_url());
            }
        }
    }
	public function admin() {
		
	}
	
}
