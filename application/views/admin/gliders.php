<?php

$glider_query = $this->db->query("SELECT * FROM `gliders_meta`");
	$glider_array = array();
	if($glider_query->num_rows() > 0) {
		foreach($glider_query->result() as $glider) {
			if($glider->unserviceable_start != '' && $glider->unserviceable_start != 0) {
				$us_from = date("d/m/Y", strtotime($glider->unserviceable_start));
			} else {
				$us_from = "<span style='opacity:0.3'>N/A</span>";
			}
			if($glider->unserviceable_end != '' && $glider->unserviceable_end != 0) {
				$us_to = date("d/m/Y", strtotime($glider->unserviceable_end));
			} else {
				$us_to = "<span style='opacity:0.3'>N/A</span>";
			}
			if($glider->airworthy == 1) {
				$aw = "<span class='badge badge-success'>Airworthy</span>";
			} else {
				$aw = "<span class='badge badge-danger'>Unserviceable</span>";
			}
			$glider_array[] = "<tr><td>$glider->title<br><small>$glider->description</small></td><td>$aw</td><td>$glider->airworthy_comment</td><td>$us_from</td><td>$us_to</td></tr>";
		
		}
	}
	
?>

<div class='container'>
    <div class='alert alert-warning'><strong>Note </strong>- return to service dates for unairworthy aircraft are an estimate</div>
	<h3>Glider Status</h4>
	<table class='table table-foo footable toggle-square-filled toggle-medium'>
		<thead>
			<th>Glider</th>
			<th>Status</th>
			<th data-breakpoints='all'>Info</th>
			<th data-breakpoints='all'>U/S from</th>
			<th data-breakpoints='all'>Until</th>
		</thead>
		<tbody>
			<?php echo implode(PHP_EOL, $glider_array); ?>
		</tbody>
	</table>
</div>