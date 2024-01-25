<?php 
class Booking_model extends CI_Model {
  
	  public function check_create_days() {
		  // check that the day stubs have been created - if not, create them
		$now = strtotime("now");
		$end_date = strtotime("+90 days");
		while (date("Y-m-d", $now) != date("Y-m-d", $end_date)) {
			$day_index = date("w", $now);
			if ($day_index == 0 || $day_index == 6) {
				$val = date("Y-m-d", $now);
				$day_of_year = date("Y", $now) . date("z", strtotime($val));
				$query = $this->db->query("SELECT * FROM `day` WHERE `date`='$val'");
				if($query->num_rows() == 0) {
					$this->db->insert('day', array('date'=>$val, 'day_of_year'=>$day_of_year));
				}
			}
			$now = strtotime(date("Y-m-d", $now) . "+1 day");
		}		
	}
	
	public function days() {
	    date_default_timezone_set('Australia/Adelaide');
		$from_date = date("Y-m-d",  strtotime('0 days'));
	
		$to_date =  date("Y-m-d", strtotime('+1 month'));
		$query = $this->db->query("SELECT * FROM `day` WHERE `date` BETWEEN '$from_date' AND '$to_date' ORDER BY `date`");
		$result = array(); 
		$previous = new DateTime("2000-01-01");
		$counter = 0;
	//	$result[] = "<optgroup label='Weekend'>";
		$day_of_weekend = 0;
		$weekend_labels = array("This Weekend","The One After","The One after that","4 Weekends Away","Planning Well Ahead");
		foreach($query->result() as $row) {
			$date_val = $row->date;
			$current = new DateTime($date_val);
			$days_diff = $current->diff($previous);
			if($days_diff->days > 1) {
				$result[] = "</optgroup>";
				$result[] = "<optgroup label='" . $weekend_labels[$counter] . "'>";
				$counter++;
				$day_of_weekend = 0;
			} else {
				$day_of_weekend++;
			}
			$close = false;
	//		$value = $row->id;
			$value = date("Y", strtotime($row->date)) . date("z", strtotime($row->date));
			$day_of_week = date('l', strtotime($row->date));
			// for the purpose of grouping flying days, move the start of the week to wednesday
			$week_number = date('W', strtotime($row->date) - strtotime('+4 days'));
			$title = date('l', strtotime($row->date)) . ' ' . date("d/m/Y", strtotime($row->date));
			$result[] = "<option value='$value' day='$day_of_week' day_of_weekend='$day_of_weekend' counter='$counter'>$title</option>";
			$previous = new DateTime($date_val);
		}
		$result[] = "</optgroup>";
		return $result;
	}
	
	public function booking_days() {
	    date_default_timezone_set('Australia/Adelaide');
		$from_date = date("Y-m-d",  strtotime('-2 days'));
		$query = $this->db->query("SELECT * FROM `day` WHERE `date` > '$from_date' ORDER BY `date`");
		return $query->result();
	}
	
	public function end_days() {
		$from_date = date("Y-m-d",  strtotime('-1 days'));
		$to_date =  date("Y-m-d", strtotime('+1 month'));
		$query = $this->db->query("SELECT * FROM `day` WHERE `date` BETWEEN '$from_date' AND '$to_date' ORDER BY `date`");
		$result = array(); 
		$previous = new DateTime("2000-01-01");
		$counter = 0;
	//	$result[] = "<optgroup label='Weekend'>";
		$day_of_weekend = 0;
		$weekend_labels = array("This Weekend","The One After","The One after that","4 Weekends Away","Planning Well Ahead");
		foreach($query->result() as $row) {
			$date_val = $row->date;
			$current = new DateTime($date_val);
			$days_diff = $current->diff($previous);
			if($days_diff->days > 1) {
				$counter++;
				$day_of_weekend = 0;
			} else {
				$day_of_weekend++;
			}
			$close = false;
			$value = date("Y", strtotime($row->date)) . date("z", strtotime($row->date));
			$day_of_week = date('l', strtotime($row->date));
			// for the purpose of grouping flying days, move the start of the week to wednesday
			$week_number = date('W', strtotime($row->date) - strtotime('+4 days'));
			$title = date('l', strtotime($row->date)) . ' ' . date("d/m/Y", strtotime($row->date));
			$result[] = "<option value='$value' day='$day_of_week' day_of_weekend='$day_of_weekend' counter='$counter'>$title</option>";
			$previous = new DateTime($date_val);
		}
		return $result;
}

	public function get_member_quals_html($member_id)  {
		$return = array();
		$query = $this->db->query("SELECT * FROM `quals` WHERE `member_id`='$member_id'");
		if ($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				$qual_id = $row->qual_id;
				$qual_query = $this->get_qual_row($qual_id);
				if($qual_query) {
					$return[] = "<div class='alert alert-dark alert-narrow'>$qual_query->title</div>";
					
				}
			}
			return implode(PHP_EOL, $return);
		} else {
			return "<div class='alert alert-light'>no qualifications listed</div>";
		}
	}
	
	public function get_gliders() {
		$query = $this->db->query("SELECT * FROM `gliders_meta`");
		if($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	public function get_booking($booking_id) {
		$query = $this->db->query("SELECT * FROM `booking` WHERE `id`='$booking_id'");
		if($query->num_rows() > 0) {
			return $query->row();
		} else {
			return false;
		}
	}
	
	public function get_qual_row($id) {
		$query = $this->db->query("SELECT * FROM `quals_meta` WHERE `id`='$id'");
		if($query->num_rows() > 0) {
			return $query->row();
		}
	}

	 public function insert_booking($userData) {
        return $this->db->insert('booking', $userData);
    }
	
	 public function update_booking($booking_id, $userData) {
		$this->db->where("id", $booking_id);
        return $this->db->update('booking', $userData);
    }
	
	public function duplicate_booking_check($booking_id, $member_id, $this_booking_start, $this_booking_end) {
		$return = false;
		$query = $this->db->query("SELECT * FROM `booking` WHERE `member_id`='$member_id' AND `guest_booking` != 1");
		if($query->num_rows() == 0) {
			return false;
		} else {
			foreach($query->result() as $row) {
				$stored_booking_start = $row->day_id_start;
				$stored_booking_end = $row->day_id_end;
				$stored_booking_id = $row->id;
				if($stored_booking_id != $booking_id) {
					if($this_booking_start >= $stored_booking_start) {
						if($this_booking_end <= $stored_booking_end) {
							$return = true;
						}
					}
				}
			}
		}
		return $return;
	}
	
	public function get_bookings() {
		$from_date = date("Y-m-d",  strtotime('-1 days'));
		$to_date =  date("Y-m-d", strtotime('+7 days'));
		$result = array();
		$query = $this->db->query("SELECT * FROM `day` WHERE `date` BETWEEN '$from_date' AND '$to_date' ORDER BY `date`");
		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				$day_id = $row->id;
				$booking_query = $this->db->query("SELECT * FROM `booking` WHERE '$day_id' BETWEEN `day_id_start` AND `day_id_end`");
				foreach($booking_query->result() as $row) {
					$result[] = $row;
				}
			}
		} else {
			$result = false;;
		}
		return $result;
	}
	
	public function get_bookings_for_day_id($day_id) {
		$query = $this->db->query("SELECT * FROM `booking` WHERE '$day_id' BETWEEN `day_id_start` AND `day_id_end`");
		if($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	public function is_member_new($member_id) {
	    $member_details = $this->get_member_details($member_id);
	    date_default_timezone_set('Australia/Adelaide');
	    $now_date =  strtotime(date("Y-m-d h:i:s a", time()));
	    $create_date = strtotime($member_details->created);
	    $diff = ($now_date - $create_date) / 86400; // returns the number of days since account was created
	    if($diff < 14) {
	        return 1;
	    } else {
	        return 0;
	    }
	}
	
	public function get_glider_title($glider_id) {
	if($glider_id == '') {
		return "<span style='opacity:0.3'>N/A</span>";
	} else {
		$query = $this->db->query("SELECT * FROM `gliders_meta` WHERE `id`='$glider_id'");
		if($query->num_rows()) {
			return $query->row()->title;
		}
	}
}

	
	public function get_member_details($member_id) {
		$query = $this->db->query("SELECT * FROM `members` WHERE `id`='$member_id'");
		return $query->row();
	}
	
	public function summary_check($user_id) {
	    $query = $this->db->query("SELECT * FROM `members` WHERE `id`='$user_id'");
	    if($query->num_rows() != 0) {
	        if($query->row()->email_summary) {
	            return 1;
	        } else {
	            return 0;
	        }
	    } else {
	        return 0;
	    }	
	    
	}
	
	public function form_email() {
	    $email_content = array();
	    // GET HEADER CONTENT & PREPEND
	    $header_content = $this->input->post('header_content');
	    if($header_content == '') {
	        $header_content = '';
	    } else {
	        $header_content = $this->input->post('header_content');
	    }
	    
	    $email_content[] = "<p style='text-align:left; padding:5px;'>$header_content</p><hr>";
	    
	    $bookings = $this->get_bookings();
		$days = $this->booking_days();
	    
	    if(isset($_GET['we'])) {
	        $weekend = $_GET['we'];
        } else {
        	$weekend = 1;
        }
        $previous = new DateTime("2000-01-01");
        $counter = 0;
        $display = array();
        $modals = array();
        foreach($days as $day) {
        	$date_val = $day->date;
        	$day_val = date("Y", strtotime($day->date)) . date("z", strtotime($day->date));
        	$day_id = $day->id;
        	$day_comment = $day->comment;
        	$current = new DateTime($date_val);
        	$days_diff = $current->diff($previous)->days;
        	if($days_diff != 1) {
        		$counter++;
        		$day_of_weekend = 0;
        	} else {
        		$day_of_weekend++;
        	}
        	if($counter == $weekend) {
        		$display_date = date("d/m/Y", strtotime($date_val));
        		$display_day = date("l", strtotime($date_val));
        		$bookings = $this->BookingModel->get_bookings_for_day_id($day_val);
        		$display[$day_id]['day'] = $display_day;
        		$display[$day_id]['date'] = $display_date;
        		$display[$day_id]['comment'] = $day_comment;
        		$display[$day_id]['bookings'] = $bookings;
        	}
        	$previous = new DateTime($date_val);
        }
        foreach($display as $key=>$value) {
        	$email_content[] =  "<h3>" . $value['day'] . ' ' . $value['date'] . " <a href='" . base_url("bookings/make/$key") . "' class='btn btn-light btn-sm' style='border: 1px solid #DDD'>book</a></h3>";
        	$email_content[] =  $value['comment'];
        	$email_content[] =  "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"3\" bgcolor=\"#EEE\" style=\"border:1px solid #DDD\">";
        	if($value['bookings']) {
        		$email_content[] =  "<thead style='color:#666; font-size:11px; font-weight:normal'>
        		<th style='border-bottom:1px solid #CCC'>name</th>
        		<th style='border-bottom:1px solid #CCC'>comment</th>
        		<th class='hide_col' style='border-bottom:1px solid #CCC'>accom</th>
        		<th class='hide_col' style='border-bottom:1px solid #CCC'>glider</th>
        		<th style='border-bottom:1px solid #CCC'>transport</th>
        		<th style='border-bottom:1px solid #CCC'>training</th>
        		</thead>";
        	}
        	$email_content[] =  "<tbody>";
        	// loop through bookings, if any
        	if($value['bookings']) {
        	foreach($value['bookings'] as $booking) {
        	    $member = $this->BookingModel->get_member_details($booking->member_id);
        	    if($booking->guest_booking == 1) {
        	        $first = $booking->guest_first;
        	        $last = $booking->guest_last;
        	        $weight = $booking->guest_weight;
        	        if($weight != '' && $weight != 0) {
        	            $suffix = " <small>guest (" . $weight . "kg)</small>";
        	        } else {
        	            $suffix = " <small>guest</small>";
        	        }
        	        $member_id = -1;
        	        $member_badge = '';
        	    } else {
        	        $first = $member->first;
        	        $last = $member->last;
        	        $suffix = "";
        	        $member_id = $booking->member_id;
        	        if($this->BookingModel->is_member_new($member_id)) {
        	            $member_badge = "<span class='badge badge-danger' style='position:relative; top:-8px; left:-5px;'>new member</span>";
        	        } else {
        	            $member_badge = '';
        	        }
        	    }
        	    
        		$glider = $this->BookingModel->get_glider_title($booking->glider_id);
        		$ts = '';
        		$email_content[] =  "<tr>
        				<td>$first $last" . $suffix . " $member_badge</td>
        				<td>$booking->comment</td>
        				<td class='hide_col'>" . $this->na($booking->accommodation) . "</td>
        				<td class='hide_col'>" . $this->glider($glider) . "</td>
        				<td>" . $this->transport($booking) . "</td>
        				<td>";
        		if($booking->two_seater == 0 && $booking->instructor == 0) {
        			$ts .=  "<span style='opacity:0.3'>N/A</span>";
        		}
        		if($booking->two_seater == 1) {
        			$ts .=  "Two-Seater";
        		}
        		if($booking->instructor == 1) {
        			$ts .=  " & Instructor";
        		}
        		$email_content[] = $ts;
        		$email_content[] =  "</td>";
        		$email_content[] =  "</tr>";
        	
        	}
        	} else {
        		$email_content[] =  "<tr><td><h4>no bookings yet</h4></td></tr>";
        	}
        	$email_content[] =  "</tbody>";
        	$email_content[] =  "</table>";
        }
        return $this->email_boilerplate(implode(PHP_EOL, $email_content));
	}
	
	public function na($input) {
	if($input == '') {
		return "<span style='opacity:0.3'>N/A</span>";
	} else if ($input == 'no') {
		return "<span style='opacity:0.3'>N/A</span>";
	} else {
		return ucfirst($input);
	}
}

    public function glider($glider) {
        if($glider) {  
            return $glider;
        } else {
            return "<span style='opacity:0.3'>N/A</span>";
        }
    
    }

    public function transport($booking)  {
    	$pickup_from = $booking->pickup_from;
    	$collect_from = $booking->collect_from;
    	$seats = $booking->seats;
    	if($pickup_from != "") {
    		return "Can collect from " . ucfirst($pickup_from) . "<span class='badge badge-primary'>$seats seats</span>";
    	} else if ($collect_from !="") {
    		return "Please pickup from " . ucfirst($collect_from);
    	} else {
    		return "<span style='opacity:0.3'>N/A</span>";
    	}
    }
    
    public function email_boilerplate($code) {
        return "
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html lang=\"en\">
<head>
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">

  <title></title>

  <style type=\"text/css\">
  table {
      font-family:verdana;
      font-size:13px;
  }
     @media only screen and (max-width: 900px) {
        .hide_col {
            display: none;
        }
    }
 
  </style>    
</head>
<body style=\"margin:0; padding:0; background-color:#FFF;\">
  <center>
    <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"#FFF\">
        <tr>
            <td align=\"center\" valign=\"top\">
                $code
            </td>
        </tr>
    </table>
  </center>
</body>
</html>";
    }
}