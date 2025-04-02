<?php 

if($guest) { // person signed in has access to create guest bookings
    $guest_field = "
    <div class='card card-padded'>
        <div class='card-header h6'>Guest Booking</div>
        <div class='card-body'><div class='btn-group btn-group-toggle' data-toggle='buttons' style='flex-wrap:wrap'>
		    <label class='btn btn-light active'><input type='radio' name='guest_booking' id='option1' autocomplete='off' checked value='0'> This booking is for me</label>
		    <label class='btn btn-light'><input type='radio' name='guest_booking' id='option2' autocomplete='off' value='1'> This is a guest booking</label>
		</div>
		<div id='guest_details' style='margin-top:5px; display:none'>
    		<div class='form-group'>
    			<label>Guest First Name</label>
    			<input type='text' class='form-control' id='guest_first' name='guest_first' value='' placeholder='first name' />
    		</div>
    		<div class='form-group'>
    			<label>Guest Last Name</label>
    			<input type='text' class='form-control' id='guest_last' name='guest_last' value='' placeholder='last name' />
    		</div>
    		<div class='form-group'>
    			<label>Guest Weight <small>in kilograms</small></label>
    			<input type='number' class='form-control' id='guest_weight' name='guest_weight' value='' />
    		</div>
    	</div>
	</div>
	</div>";
} else {
    $guest_field = '';
}

$glider_select = array();
$sorry = array();
foreach($gliders as $glider) {
	$title = $glider->title;
	$airworthy = $glider->airworthy;
	if($airworthy) {
		$glider_select[] = "<label class='btn btn-light'><input type='radio' name='glider_booking' id='option1' autocomplete='off' value='$title'> $title</label>";
	} else {
		$glider_select[] = "<label class='btn btn-light sorry' glider_id='$title' style='opacity:0.5'><input type='radio' class='sorry' name='glider_booking' autocomplete='off' value='$title' title='not airworthy' disabled> $title</label>";
		$available_date = date("d/m/Y", strtotime($glider->unserviceable_end));
		$sorry[] = "<div style='display:none; margin-top:5px;' id='date_$title' class='alert alert-danger'>Sorry - the $title is unavailable until the $available_date</div>";
	}
}
$script = '';
if($day_id != NULL) {
	// day ID has been set, so use JQuery to change select box 
	$script = "<script>
	$(document).ready(function() {
		$('#date').val('$day_id').change();
	});
	</script>
	";
}

?>


<style>
.card-padded {
	margin-bottom:3px;
}
</style>
<div class="container">

    <!-- Include Flash Data File -->
    <?php $this->load->view('_FlashAlert/flash_alert.php') ?>
    <?= form_open() ?>
    <?php echo $guest_field; ?>
	<!-- BOOKING DATES -->
	<div class='card card-padded'>
		<div class='card-header h6'>Booking Dates</div>
		<div class='card-body'>
			<div class="form-group">
				<label>Arriving</label>
				<select class='form-control' name='date' id='date' value='<?= set_value('date'); ?>'>
					<option value=''>Select Booking Date(s)</option>
					<?= implode(PHP_EOL, $days); ?>
				</select>
				<?= form_error('date'); ?>
			</div>
			<div class="form-group" style='display:none' id='end_date_chooser'>
				<label>Departing</label>
				<select class='form-control' name='end_date' id='end_date' value='<?= set_value('end_date'); ?>'>
					<option value=''>Select Booking Date(s)</option>
					<?= implode(PHP_EOL, $end_days); ?>
				</select>
				<?= form_error('end_date'); ?>
				<div class='form-group' style='margin-top:5px;'>
					<label>Comment / Plans</label>
					<input type='text' class='form-control' name='comment' value='<?= set_value('comment'); ?>' placeholder='plans?' />
				</div>
			</div>
		</div>
	</div>
	<!-- -->
	<!-- COMMENT -->
	<div class='card card-padded' id='accomm_select' style='display:none'>
		<div class='card-header h6'>Accommodation</div>
		<div class='card-body'>
			<div class='form-group'>
				<div class="btn-group btn-group-toggle" data-toggle="buttons" style='flex-wrap:wrap'>
				  <label class="btn btn-light active"><input type="radio" name="accommodation" id="option1" autocomplete="off" checked value='no'> No Accommodation Required</label>
				  <label class="btn btn-light"><input type="radio" name="accommodation" id="option2" autocomplete="off" value='bunkhouse'> Bunkhouse Accommodation</label>
				  <label class="btn btn-light"><input type="radio" name="accommodation" id="option3" autocomplete="off" value='unit'> Self-Contained Unit</label>
				</div>
			</div>
		</div>
	</div>
	<!-- ACCOMODATION -->
	<div class='card card-padded' id='accomm_select' style='display:none'>
		<div class='card-header h6'>Accommodation</div>
		<div class='card-body'>
			<div class='form-group'>
				<div class="btn-group btn-group-toggle" data-toggle="buttons" style='flex-wrap:wrap'>
				  <label class="btn btn-light active"><input type="radio" name="accommodation" id="option1" autocomplete="off" checked value='no'> No Accommodation Required</label>
				  <label class="btn btn-light"><input type="radio" name="accommodation" id="option2" autocomplete="off" value='bunkhouse'> Bunkhouse Accommodation</label>
				  <label class="btn btn-light"><input type="radio" name="accommodation" id="option3" autocomplete="off" value='unit'> Self-Contained Unit</label>
				</div>
			</div>
		</div>
	</div>
	<!-- -->
	<!-- 2-SEATER & INSTRUCTION -->
	<div class='card card-padded'>
		<div class='card-header h6'>2-Seater & Instruction</div>
		<div class='card-body'>
			<div class='form-group'>
				<div class="btn-group btn-group-toggle" data-toggle="buttons" style='flex-wrap:wrap'>
				  <label class="btn btn-light active"><input type="radio" name="two_seater" id="none" autocomplete="off" checked value='no'> No 2-Seater or Instructor Required</label>
				  <label class="btn btn-light"><input type="radio" name="two_seater" id="option2" autocomplete="off" value='two_seater'> 2-Seater Required</label>
				  <label class="btn btn-light"><input type="radio" name="two_seater" id="option3" autocomplete="off" value='both'> 2-Seater & Instructor Required</label>
				</div>
			</div>
		</div>
	</div>
	<!-- -->
	<!-- TRANSPORT -->
	<div class='card card-padded'>
		<div class='card-header h6'>Transport</div>
		<div class='card-body'>
			<div class='form-group'>
				<div class="btn-group btn-group-toggle" data-toggle="buttons" style='flex-wrap:wrap'>
				  <label class="btn btn-light active"><input type="radio" name="transport" id="option1" autocomplete="off" value='organised' checked> I'm organised</label>
				  <label class="btn btn-light"><input type="radio" name="transport" id="option2" autocomplete="off" value='need'> I need a lift</label>
				  <label class="btn btn-light"><input type="radio" name="transport" id="option3" autocomplete="off" value='spare'> I have spare seats</label>
				</div>
			</div>
			<div class='form-group spare_select' style='display:none'>
				<label>How Many Spare Seats?</label>
				<input type='number' name='spare_seats' id='spare_seats' class='form-control' value='0' min='0' max='6' step='1' />
			</div>
			<div class='form-group pickup_select' style='display:none'>
				<label>Pickup from Where?</label>
				<div class="btn-group btn-group-toggle" data-toggle="buttons" style='flex-wrap:wrap'>
					<label class="btn btn-light active"><input type="radio" name="pickup_location" id="option1" autocomplete="off" value='<?= getenv('PICKUP_LOCATION_1') ?>' checked><?= getenv('PICKUP_LOCATION_1_LABEL') ?></label>
					<!--<label class="btn btn-light"><input type="radio" name="pickup_location" id="option2" autocomplete="off" value='<?= getenv('PICKUP_LOCATION_2') ?>'> <?= getenv('PICKUP_LOCATION_2_LABEL') ?></label>-->
					<label class="btn btn-light"><input type="radio" name="pickup_location" id="option3" autocomplete="off" value='other'> Other</label>
				</div>
			</div>
			<div class='form-group specify_other' style='display:none'>
				<label>Specify Other</label>
				<input type='text' name='pickup_other' class='form-control' />
			</div>
		</div>
	</div>
	<div class='card card-padded'>
		<div class='card-header h6'>I intend to fly the...</div>
		<div class='card-body'>
			<div class='form-group'>
				<div class="btn-group btn-group-toggle" data-toggle="buttons" style='flex-wrap:wrap'>
				<label class='btn btn-light active'><input type='radio' name='glider_booking' id='option0' autocomplete='off' value='0' checked> N/A</label>
					<?php echo implode(PHP_EOL, $glider_select); ?>
				</div>
				<?php echo implode(PHP_EOL, $sorry); ?>
			</div>
	</div>
        <button type="submit" class="btn btn-primary">Save Booking</button>
    <?= form_close() ?>
</div>
<br>

<script>
$('#date').change(function() {
	$('#end_date').children('option').show();
	var weekend_id = $("#date option:selected").attr('counter');
	var day_of_week = $("#date option:selected").attr('day');
	var value = $("#date").val();
	$('#end_date_chooser').show();
	$('#end_date').find('option[counter!="' + weekend_id + '"]').hide();
	$('#end_date').val(value);
});

$('input[name=guest_booking').change(function() {
    var guest_booking = $(this).val();
    if(guest_booking == 1) {
        $('#guest_details').show();
    } else {
        $('#guest_details').hide();
        $('#guest_first').val('');
        $('#guest_last').val('');
        $('#guest_weight').val('');
    }
    
})

$('#end_date').change(function() {
	var end = $(this).val();
	var start = $('#date').val();
	if(end == start) {
		$('#accomm_select').hide();
	} else {
		$('#accomm_select').show();
	}
});

$('input[name=transport').change(function() {
	var val = $(this).val();
	if(val == 'spare') {
		$('#spare_seats').attr('min','1');
		$('.spare_select').show();
		$('.pickup_select').show();
	} else if (val == 'need') {
		$('#spare_seats').attr('min','0');
		$('#spare_seats').val('0');
		$('.spare_select').hide();
		$('.pickup_select').show();
	} else {
		$('#spare_seats').attr('min','0');
		$('#spare_seats').val('0');
		$('.spare_select').hide();
		$('.pickup_select').hide();
	}
});

$('input[name=pickup_location').change(function() {
	var val = $(this).val();
	if(val == 'other') {
		$('.specify_other').show();
	} else {
		$('.specify_other').val('').hide();
	}
});

$(document).ready(function() {
	$('#spare_seats').inputSpinner();
});

$('.sorry').click(function() {
	var glider = $(this).attr("glider_id");
	var elem = $('#date_' + glider);
	$(elem).fadeIn('fast', function(){
        $(elem).delay(3000).fadeOut(); 
    });
});
</script>
<?php echo $script; ?>
