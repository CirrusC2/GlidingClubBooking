<?php

    if(isset($_GET['tab'])) {
        $tab = $_GET['tab'];
    } else {
        $tab = '';
    }
    
    if(isset($_GET['init'])) {
        $init = $_GET['init'];
    } else {
        $init = '';
    }
    
    $chooser = '';
    for ($i=65; $i<=90; $i++) {   
        $this_one = chr($i);
        $chooser .= "<a href='" . base_url("admin/panel?tab=member-list&init=$this_one") . "' class='btn btn-light' style='margin:1px'>$this_one</a>";
    }
    // add active accounts only
    $chooser .= "<a href='" . base_url("admin/panel?tab=member-list&init=active") . "' class='btn btn-light' style='margin:1px'>active</a>";
    
    // get reg key
    $reg_key = $this->UserModel->registration_key();
    echo "<!-- Debug: NEW_MEMBER_REGISTRATION_KEY = " . getenv('NEW_MEMBER_REGISTRATION_KEY') . " -->";
    echo "<!-- Debug: reg_key from model = " . $reg_key . " -->";
    
    // document library
    $library_table = array();
    $library_query = $this->db->query("SELECT * FROM `library` ORDER BY `date` DESC");
    if($library_query->num_rows() > 0) {
        foreach($library_query->result() as $row) {
            $member = $this->UserModel->get_member($row->member_id)->first . ' ' . $this->UserModel->get_member($row->member_id)->last;
            $display_date = date('d/m/Y', strtotime($row->date));
            $library_table[] = "<tr>
                                    <td>$row->title</td>
                                    <td>$row->description</td>
                                    <td>
                                        <a class='btn btn-light' href='$row->url' target='_blank'><i class='fa fa-download'></i> download</a>
                                        <a class='btn btn-danger' href='delete_document/$row->id'><i class='fa fa-trash'></i></a>
                                    </td>
                                    <td>$member</td>
                                    <td>$display_date</td>
                                </tr>";
        }
    }
    
    // activity log
    $log_table = array();
    $log_query = $this->db->query("SELECT * FROM `log` ORDER BY `id` DESC LIMIT 1000");
    if($log_query->num_rows() > 0) {
        foreach($log_query->result() as $row) {
            $log_table[] = "<tr><td>$row->datetime</td><td>$row->user</td><td>$row->action</td></tr>";
        }
    }

    // get member list
    $member_array = array();
    $modals = array();
    if($init == '') {
        $member_query = $this->db->query("SELECT * FROM `members` ORDER BY `last`");
    } else if ($init == 'active') {    
        $member_query = $this->db->query("SELECT * FROM `members` WHERE `last_login` IS NOT NULL ORDER BY `last_login`");
    } else {
        $member_query = $this->db->query("SELECT * FROM `members` WHERE `last` LIKE '" . $init . "%' ORDER BY `last`");
    }
    if($member_query->num_rows() > 0) {
        foreach($member_query->result() as $member) {
            $name = $member->last . ", " . $member->first; 
            $edit = "<a class='btn btn-info btn-sm' href='../user/edit_member/$member->id'><i class='fa fa-pencil'></i></a>";
            $delete = "<a class=\"btn btn-danger btn-sm member_del\" href='../user/delete_member/$member->id'><i class='fa fa-trash'></i></a>";
            if($member->is_active) {
                $suspend = "<a class='btn btn-warning btn-sm' href='suspend/$member->id'><i class='fa fa-toggle-on'></i></a>";
                $status = "<i class='fa fa-check'></i>";
            } else {
                $suspend = "<a class='btn btn-success btn-sm' href='unsuspend/$member->id'><i class='fa fa-toggle-off'></i></a>";
                $status = "<i style='color:red' class='fa fa-times'></i>";
            }
            if($member->phone != '') {
                $ph = "<td><a href='tel:$member->phone' class='btn btn-light'>$member->phone</a></td>";
            } else {
                $ph = "<td><small>N/A</small></td>";
            }
            $member_array[] = "<tr>
                                    <td><a href='#' class='btn btn-light' data-toggle='modal' data-target='#modal_$member->id'>$name</a></td>
                                    <td>$status</td>
                                    $ph
                                    <td><a href='mailto:$member->email' class='btn btn-light'>$member->email</a></td>
                                    <!--td><a href='#' class='btn btn-light' data-toggle='modal' data-target='#modal_$member->id'><i class='fa fa-search'></i></a></td-->
                                    <td>$edit $suspend $delete</td>
                                </tr>";
        
        $member_quals = $this->BookingModel->get_member_quals_html($member->id);
		$modals[] = "<div class='modal fade' id='modal_$member->id' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
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
        $member_array[] = "<tr><td colspan='5'>no members added yet</td></tr>";
    }
    

	$start = date("z", strtotime("now"));
	$end = date("z", strtotime("+ 90 days"));
	$offset = 0;
	$options = array();
	for($i = $start; $i <= $end; $i++) {
		$date_val = date("Y-m-d", strtotime("+ $offset days"));
		$offset++;
		$day_of_week = date('l', strtotime($date_val));
		$exist_query = $this->db->query("SELECT * FROM `day` WHERE `date`='$date_val'");
		if($exist_query->num_rows() > 0) {
			$options[] = "<option value='$date_val' disabled>$day_of_week " . date("d/m/Y", strtotime($date_val)) . " (already exists)</option>";
		} else {
			if($day_of_week != 'Saturday' && $day_of_week != 'Sunday') {
				$options[] = "<option value='$date_val'>$day_of_week " . date("d/m/Y", strtotime($date_val)) . "</option>";
			} else {
				$options[] = "<option value='$date_val' disabled>$day_of_week " . date("d/m/Y", strtotime($date_val)) . " (weekends created automatically)</option>";
			}
		}
	}
	
	$day_query = $this->db->query("SELECT * FROM `day` WHERE `manually_added`=1");
	$day_options = array();
	if($day_query->num_rows() > 0) {
	    foreach($day_query->result() as $day) {
	        $this_date = $day->date;
	        $date_val = date('d/m/Y', strtotime($day->date));
	        $day_options[] = "<option value='$day->id'>$date_val</option>";
	    }
	    $remove_string = "<select class='form-control' name='remove_day'>". implode(PHP_EOL, $day_options) . "</select><button class='btn btn-dark' type='submit'>remove this day</button>";
	} else {
	    $remove_string = 'No flying days have been added manually yet';
	}
	
	date_default_timezone_set(getenv('CLUB_TIMEZONE'));
    $yesterday_date = date("Y-m-d", strtotime("-1 days"));
	$day_comment_query = $this->db->query("SELECT * FROM `day` WHERE `date` > '$yesterday_date' ORDER BY `date`");
	$day_comment_options = array();
	if($day_comment_query->num_rows() > 0) {
	    foreach($day_comment_query->result() as $row) {
	        $date_val = date("d/m/Y", strtotime($row->date));
	        $day_of_week = date('l', strtotime($row->date));
	        $current_comment = $row->comment;
	        $disp_comment = '';
	        if($current_comment != '') {
	            if(strlen($current_comment) > 30) {
	                $abb = substr($current_comment, 0, 30) . "...";
	            } else {
	                $abb = $current_comment;
	            }
	            $disp_comment = " ($abb)";
	        }
	        $day_comment_options[] = "<option value='$row->id'>$day_of_week $date_val" . $disp_comment . "</option>";
	    }
	}
	
	$glider_query = $this->db->query("SELECT * FROM `gliders_meta` ORDER BY `title`");
	$glider_array = array();
	if($glider_query->num_rows() > 0) {
		foreach($glider_query->result() as $glider) {
			if($glider->unserviceable_start != '') {
				$us_from = date("Y-m-d", strtotime($glider->unserviceable_start));
			} else {
				$us_from = '';
			}
			if($glider->unserviceable_end != '') {
				$us_to = date("Y-m-d", strtotime($glider->unserviceable_end));
			} else {
				$us_to = '';
			}
			$title = "<label>Glider Title</label><input class='form-control' value=\"$glider->title\" name='title' type='text'>";
			$description = "<label>Description</label><input class='form-control' value=\"$glider->description\" name='description' type='text' placeholder='description (optional)' />";
			$us = "<div class='form-inline'><label>Not Airworthy from</label><input class='form-control' type='date' name='unserviceable_start' value='$us_from' /> Until<input class='form-control' type='date' name='unserviceable_end' value='$us_to' /> <a class='btn btn-light' href='clear/$glider->id'>clear</a></div>";
		    $us = "<table class='table table-sm'><thead><th>Not Airworthy From</th><th>To</th><th></th></thead><tbody><tr><td><input class='form-control' type='date' name='unserviceable_start' value='$us_from' /></td><td><input class='form-control' type='date' name='unserviceable_end' value='$us_to' /></td><td><a class='btn btn-light' href='clear/$glider->id'>clear</a></td></tr></tbody></table>";
			$us_comment = "<label for='airworthy_comment'>Comment <small>(regarding current or future airworthiness status)</small></label><input style='margin-top:3px' value=\"$glider->airworthy_comment\"type='text' class='form-control' name='airworthy_comment' placeholder='comment re airworthiness' />";
			$submit = "<button style='margin-top:3px;' class='btn btn-dark' type='submit'>Update $glider->title</button>";
			if($glider->airworthy == 1) {
				$select = "<label for='airworthy'>Current Status</label><select id='airworthy' class='form-control' name='airworthy' style='border-color:green; box-shadow: 0 0 5px rgba(0, 227, 0, 0.4);'><option value='1' selected>Airworthy</option><option value='0'>Not Airworthy</option></select>";
			} else {
				$select = "<select class='form-control' name='airworthy' style='border-color:red; box-shadow: 0 0 5px rgba(227, 0, 0, 0.4);'><option value='1'>Airworthy</option><option value='0' selected>Not Airworthy</option></select>";
			}
			$glider_array[] = "
			<div class='card' style='margin-bottom:5px'>
			    <div class='card-header h5'>$glider->title</div>
			    <div class='card-body'>
			        " . form_open('admin/edit_glider/' . $glider->id) . "
					<ul class='list-group list-group-flush'>
						<input type='hidden' name='title' value='$glider->title' />
						<li class='list-group-item'>$description</li>
						<li class='list-group-item'>$select</li>
						<li class='list-group-item'>$us_comment</li>
						<li class='list-group-item'>$us <small style='opacity:0.5'>The above date range can be used for current airworthiness status or future planning</small></li>
						<li class='list-group-item'>$submit</li>
					</ul>
				" . form_close() . "
			    </div>
		    </div>";
			
		}
	}
	$all_qualifications = $this->UserModel->get_all_qualifications();
	$qual_table = array();
	$qual_modals = array();
	foreach($all_qualifications as $row) {
	    $this_qual = $row->id;
	    $qual_query = $this->db->query("SELECT * FROM `quals` WHERE `qual_id`='$this_qual'");
	    if($qual_query->num_rows() > 0) {
	        $m = array();
	        foreach($qual_query->result() as $member_qual) {
	            $this_member = $member_qual->member_id;
	            $member_query = $this->db->query("SELECT * FROM `members` WHERE `id`='$this_member'");
	            if($member_query->num_rows() > 0) {
	                $member_row = $member_query->row();
	                $m[] = "<tr><td><a href='#' class='btn btn-light' data-toggle='modal' data-target='#modal_$this_member'>$member_row->last, $member_row->first</a></td></tr>";
	            }
	        }
            $qual_modals[] = "<div class='modal fade' id='modal_qual_$this_qual' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
				  <div class='modal-dialog' role='document'>
					<div class='modal-content'>
					  <div class='modal-header'>
						<h5 class='modal-title' id='exampleModalLabel'><small>Members with the Qualification of </small> $row->title</h5>
						<button type='button' class='close' data-dismiss='modal' aria-label='Close'>
						  <span aria-hidden='true'>&times;</span>
						</button>
					  </div>
					  <div class='modal-body'>
						<table class='table table-foo table-sm'>
							" . implode(PHP_EOL, $m) . "
						</table>
					  </div>
					  <div class='modal-footer'>
						<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
					  </div>
					</div>
				  </div>
				</div>";
			    $modal_button = "<a href='#' class='btn btn-light' data-toggle='modal' data-target='#modal_qual_$this_qual'>Click for Qualified Members</a>";	
	        } else {
	            $modal_button = "<a href='#' class='btn btn-light' style='opacity:0.4'>No Qualified Members</a>";
	        }
	        $del = "<a class='btn btn-danger btn-sm del_qual' href='del_qual_meta/$row->id'><i class='fa fa-trash'></i></a>";
	    $qual_table[] = "<tr><td>$row->title</td><td>$row->desc</td><td>$modal_button</td><td>$del</td></tr>";
	}
	

	
?>
<!-- TAB MENU -->
<div class='container'>
    <?php $this->load->view('_FlashAlert/flash_alert.php') ?>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="member-list-tab" data-toggle="tab" href="#member-list" role="tab" aria-controls="member-list" aria-selected="true">Members</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="qualifications-tab" data-toggle="tab" href="#qualifications" role="tab" aria-controls="qualifications" aria-selected="false">Qualifications</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="flying-days-tab" data-toggle="tab" href="#flying-days" role="tab" aria-controls="flying-days" aria-selected="false">Edit Flying Days</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="glider-status-tab" data-toggle="tab" href="#glider-status" role="tab" aria-controls="glider-status" aria-selected="false">Glider Status</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="library-tab" data-toggle="tab" href="#library" role="tab" aria-controls="library" aria-selected="false">Document Library</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="log-tab" data-toggle="tab" href="#log" role="tab" aria-controls="log" aria-selected="false">Activity Log</a>
        </li>
    </ul>
    <!-- PAGE CONTENT -->
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade" id="flying-days" role="tabpanel" aria-labelledby="flying-days-tab">
            <!-- Add Flying Days Form -->
            <div class='card'>
                <div class='card-header'>Add Flying Days</div>
                <div class='card-body'>
                    <?php echo form_open('admin/add_days'); ?>
                        <div class="form-group">
                            <label for='add_days'>Choose Day to Add</label>
                            <div class='input-group'>
                                <select class='form-control' id='add_days' name='add_days'><?php echo implode(PHP_EOL, $options); ?></select>
                                <div class="input-group-append">
                                    <button class='btn btn-primary' type='submit'>Add Day</button>
                                </div>
                            </div>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <br>

            <!-- Remove Flying Day Form -->
            <div class='card'>
                <div class='card-header'>Remove Manually Added Flying Day</div>
                <div class='card-body'>
                    <?php echo form_open('admin/remove_day'); ?>
                        <div class="form-group">
                            <label for='remove_day'>Choose Day to Remove</label>
                            <div class='input-group'>
                                <?php echo $remove_string; ?>
                            </div>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <br>

            <!-- Add/Edit Flying Day Comment Form -->
            <div class='card'>
                <div class='card-header'>Add / Edit Flying Day Comment</div>
                <div class='card-body'>
                    <?php echo form_open('admin/day_comment'); ?>
                        <div class="form-group">
                            <label for='day_comment'>Select Day</label>
                            <div class='input-group'>
                                <select class='form-control' id='day_comment' name='comment_day_id'>
                                    <option value=''>Select a day</option>
                                    <?php echo implode(PHP_EOL, $day_comment_options); ?>
                                </select>
                                <input class='form-control day_selected' id='day_comment_value' name='day_comment_value' type='text' placeholder='Enter comment' style='display:none' />
                                <div class="input-group-append">
                                    <button class='btn btn-success day_selected' type='submit' style='display:none'>Save Comment</button>
                                </div>
                            </div>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="glider-status" role="tabpanel" aria-labelledby="glider-status-tab">
            <div class="card">
                <div class="card-header">
                    <h3>Gliders</h3>
                </div>
                <div class="card-body">
                    <!-- Add New Glider Form -->
                    <div class="card">
                        <div class="card-header">Add New Glider</div>
                        <div class="card-body">
                            <?php echo form_open('admin/add_glider'); ?>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="title">Glider Title</label>
                                            <input class="form-control" name="title" id="title" type="text" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="description">Description</label>
                                            <input class="form-control" name="description" id="description" type="text" placeholder="Description (optional)">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="airworthy">Initial Status</label>
                                            <select class="form-control" name="airworthy" id="airworthy" required>
                                                <option value="1">Airworthy</option>
                                                <option value="0">Not Airworthy</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>&nbsp;</label>
                                            <button class="btn btn-primary btn-block" type="submit">Add Glider</button>
                                        </div>
                                    </div>
                                </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>

                    <?php
                    echo implode(PHP_EOL, $glider_array);
                    ?>
                </div>
            </div>
        </div>
        <div class="tab-pane fade active show" id="member-list" role="tabpanel" aria-labelledby="member-list-tab">
            <div class='chooser' style='padding-top:5px; padding-bottom:5px;'><?php echo $chooser; ?></div>
            <table class='table' data-filtering='true' data-paging='true' data-sorting='true'>
                <thead>
                    <th>Name</th>
                    <th>Active?</th>
                    <th data-breakpoints='all'>Phone</th>
                    <th data-breakpoints='all'>Email</th>
                    <!--th>Quals</th-->
                    <th>&nbsp;</th>
                </thead>
            	<tbody>
                	<?php echo implode(PHP_EOL, $member_array); ?>
            	</tbody>
        	</table>
        </div>
        <div class="tab-pane fade" id="qualifications" role="tabpanel" aria-labelledby="qualifications-tab">
            <!-- Add Qualification Form -->
            <div class='card'>
                <div class='card-header'>Add Qualification</div>
                <div class='card-body'>
                    <?php echo form_open('admin/add_qual_meta', array('method' => 'post', 'id' => 'add_qual_form')); ?>
                        <div class="form-group">
                            <label for='title'>Title</label>
                            <input class='form-control' name='title' id="title" placeholder='e.g. Winch Driver' required />
                        </div>
                        <div class="form-group">
                            <label for='desc'>Description</label>
                            <input class='form-control' name='desc' id="desc" placeholder='Description (optional)' />
                        </div>
                        <button class='btn btn-primary' type='submit'>Add Qualification</button>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <table class='table'>
                <thead><th>Qualification</th><th>Description</th><th></th></thead>
            	<tbody>
                	<?php echo implode(PHP_EOL, $qual_table); ?>
            	</tbody>
        	</table>
        </div>
        <div class="tab-pane fade" id="library" role="tabpanel" aria-labelledby="library-tab">
            <!-- Add Document Form -->
            <div class='card'>
                <div class='card-header'>Add Document</div>
                <div class='card-body'>
                    <?php echo form_open('admin/add_document', array('enctype' => 'multipart/form-data')); ?>
                        <div class="form-group">
                            <label for="userfile">Document File</label>
                            <input name="userfile" type="file" class="form-control-file" id="userfile" required />
                        </div>
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input class='form-control' name='title' id="title" placeholder='e.g., committee minutes' required />
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input class='form-control' name='description' id="description" placeholder='Description (optional)' />
                        </div>
                        <button class='btn btn-primary' type='submit'>Add Document</button>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <table class='table table-foo' data-filtering='true' data-paging='true' data-sorting='true'>
                <thead><th>Title</th><th>Description</th><th>&nbsp;</th><th data-breakpoints='all'>Added By</th><th data-breakpoints='all'>Date</th></thead>
            	<tbody>
                	<?php echo implode(PHP_EOL, $library_table); ?>
            	</tbody>
        	</table>
        </div>
        <!-- ACTIVITY LOG -->
        <div class="tab-pane fade" id="log" role="tabpanel" aria-labelledby="log-tab">
            <div class='card'>
                <div class='card-header'>Activity Log</div>
                <div class='card-body'>
                    <div class='card-text'>
                        <table class='table table-foo' data-filtering='true' data-paging='true' data-sorting='true' data-page-size='2' data-page-navigation=".pagination">
                            <thead><th>Date / Time</th><th>Member</th><th>Action</th></thead>
            	            <tbody>
                	            <?php echo implode(PHP_EOL, $log_table); ?>
            	            </tbody>
            	            <tfoot class="hide-if-no-paging">
                                <tr>
                                    <td colspan="3">
                                        <ul class="pagination">
                                        </ul>
                                    </td>
                                </tr>
                            </tfoot>
        	            </table>    
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <hr>
    <div class='text-center'><span style='color:gray'>New User Registration Key is </span><span style='font-weight:bold'><?php echo $reg_key; ?></span></div>
    <?php echo implode(PHP_EOL, $modals); ?>
    <?php echo implode(PHP_EOL, $qual_modals); ?>
</div>

<script>
$(document).ready(function() {
    $('.nav-tabs a[href="#<?php echo $tab; ?>"]').tab('show');
});
</script>
<script>
    $('#day_comment').change(function() {
        var day_id = $(this).val();
        if(day_id != '') {
            $('.day_selected').show();
          $.ajax({                                      
              url: '<?php echo base_url('admin/get_comment/'); ?>' + day_id,                          
              data: "",
              dataType: 'text',   
              success: function(data) {
                $('#day_comment_value').val(data);
              } 
            });
        } else {
            $('.day_selected').hide();
        }
    })
    
</script>




