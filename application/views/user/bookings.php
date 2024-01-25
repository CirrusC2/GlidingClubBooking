<style>
.card-padded {
	margin-bottom:6px;
}

.alert-narrow {
	padding-top:4px;
	padding-bottom: 4px;
	margin-top:4px;
	margin-bottom:4px;
}
</style>

<?php
$this->load->model('Booking_model', 'BookingModel');

if(isset($_GET['we'])) {
	$weekend = $_GET['we'];
} else {
	$weekend = 1;
}

// is this user allowed to send weekly summary?
$user_id = $this->session->userdata('USER_ID');
$summary_form = "";
if($this->BookingModel->summary_check($user_id) == '1') {
    // add summary form
    $summary_form = "<hr>
    <button id='email_display' class='btn btn-light'>email booking summary</button>
    <div id='email_send' style='display:none; margin-top:5px;'>
        <form action='" . base_url('bookings/email_summary') . "' method='post'>
            <div class='d-flex'>
                <input type='email' value='augc-people@googlegroups.com' style='width:350px' name='email' /> 
                <input type='text' name='header_content' class='form-control mr-1' placeholder='please enter comment to send with booking summary email' />
                <button type='submit' class='btn btn-light'>Send</button>
            </div>
        </form>
    </div>
    <script>
        $('#email_display').click(function() {
          $('#email_send').toggle(); 
        });
    </script>";
} else {
    $summary_form = "<hr><p><span style='color:#999'>new member registration key is </span><span style='color:#333'>$reg_key</span></p>";
}
// ===============

$previous = new DateTime("2000-01-01");
$counter = 0;
$display = array();
$modals = array();
foreach($days as $day) {
	$date_val = $day->date;
	$day_val = date("Y", strtotime($day->date)) . date("z", strtotime($day->date));
	$day_id = $day->id;
	$day_comment = $day->comment;
	if($day_comment === '' || $day_comment === NULL) {
	    $day_comment = '';
	} else {
    	$day_comment = "<div class='alert alert-info'><i class='fa fa-comments' aria-hidden='true'></i> $day->comment</div>";
	} 
	$current = new DateTime($date_val);
    $now_date = new DateTime();
    $current_f = new DateTime($day->date);
    $now_diff = $current_f->diff($now_date);
 //   print_r($now_diff);
 //   echo "===<br>" . PHP_EOL;
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
		$display[$day_id]['days_diff'] = $now_diff;
		if(!$now_diff->invert && $now_diff->days > 0) {
		    $display[$day_id]['allow_booking'] = false;
		} else {
		    $display[$day_id]['allow_booking'] = true;
		}
	}
	$previous = new DateTime($date_val);
}
echo "<div class='container'>";
$this->load->view('_FlashAlert/flash_alert.php');

foreach($display as $key=>$value) {
	echo "<div class='card card-padded'>";
	// check not in past
	if($value['allow_booking']) {
	    echo "<div class='card-header'>" . $value['day'] . ' ' . $value['date'] . " <a href='bookings/make/$key' class='btn btn-light btn-sm' style='border: 1px solid #DDD'>book <i class='fa fa-plus'></i></a></div>";
	} else {
	    echo "<div class='card-header'>" . $value['day'] . ' ' . $value['date'] . "</div>";
	}
	echo "<div class='card-body'>";
	echo $value['comment'];
	echo "<table class='table table-sm table-foo'>";
	if($value['bookings']) {
		echo "<thead><th>name</th><th>comment</th><th data-breakpoints='all'>accommodation</th><th data-breakpoints='all'>flying</th><th data-breakpoints='all'>transport</th><th data-breakpoints='all'>training</th><th></th></thead>";
	}
	echo "<tbody>";
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
		echo "<tr>
				<td><a href='#' class='btn btn-light' data-toggle='modal' data-target='#modal_$member_id'>$first $last" . $suffix . "</a>$member_badge</td>
				<td>$booking->comment</td>
				<td>" . na($booking->accommodation) . "</td>
				<td>" . glider($glider) . "</td>
				<td>" . transport($booking) . "</td>
				<td>";
		if($booking->two_seater == 0 && $booking->instructor == 0) {
			echo "<span style='opacity:0.3'>N/A</span>";
		}
		if($booking->two_seater == 1) {
			echo "Two-Seater";
		}
		if($booking->instructor == 1) {
			echo " & Instructor";
		}
		echo "</td>";
		if($_SESSION['USER_ID'] == $member->id) {		
			echo "<td><a href='bookings/edit_booking/$booking->id' class='btn btn-light btn-sm'><i class='fa fa-pencil'></i></a> <a href='bookings/del/$booking->id' class='btn btn-dark btn-sm'><i class='fa fa-trash'></i></a></td>";
		} else {
			echo "<td>&nbsp;</td>";
		}
		echo "</tr>";
		
		$member_quals = $this->BookingModel->get_member_quals_html($member->id);
		$modals[] = "<div class='modal fade' id='modal_$booking->member_id' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
					  <div class='modal-dialog' role='document'>
						<div class='modal-content'>
						  <div class='modal-header'>
							<h5 class='modal-title' id='exampleModalLabel'><small>Member Card for</small> $member->first $member->last</h5>
							<button type='button' class='close' data-dismiss='modal' aria-label='Close'>
							  <span aria-hidden='true'>&times;</span>
							</button>
						  </div>
						  <div class='modal-body'>
							<table class='table table-foo'>
								<tr><td>Phone</td><td><a href='tel:$member->phone'>$member->phone</a></td></tr>
								<tr><td>Email</td><td><a href='mailto:$member->email'>$member->email</a></td></tr>
								<tr><td>Qualifications</td><td>$member_quals</td></tr>
							</table>
						  </div>
						  <div class='modal-footer'>
							<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
						  </div>
						</div>
					  </div>
					</div>";
	}
	} else {
		echo "<tr><td colspan='2'><h6>no bookings yet</h6></td></tr>";
	}
	echo "</tbody>";
	echo "</table>";
	
	echo "</div>";
	echo "</div>";
	echo implode(PHP_EOL, $modals);
}

$prev_weekend = $weekend - 1;
$next_weekend = $weekend + 1;
if($weekend == 1) {
	echo "<span style='opacity:0.3'><a href='#' class='btn btn-dark disabled'><i class='fa fa-arrow-circle-left'></i> previous weekend</a></span>";
} else {
	echo "<a href='?we=$prev_weekend' class='btn btn-dark'><i class='fa fa-arrow-circle-left'></i> previous weekend</a>";
}
echo "<a href='?we=$next_weekend' class='btn btn-dark float-right'>next weekend <i class='fa fa-arrow-circle-right'></i></a>";
echo $summary_form;
function na($input) {
	if($input == '') {
		return "<span style='opacity:0.3'>N/A</span>";
	} else if ($input == 'no') {
		return "<span style='opacity:0.3'>N/A</span>";
	} else {
		return ucfirst($input);
	}
}

function glider($glider) {
    if($glider) {  
        return $glider;
    } else {
        return "<span style='opacity:0.3'>N/A</span>";
    }

}

function transport($booking)  {
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


?>
</table>
</div>


