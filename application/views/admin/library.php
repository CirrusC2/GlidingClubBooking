<?php

    
    // document library
    $library_table = array();
    $library_query = $this->db->query("SELECT * FROM `library` ORDER BY `date` DESC");
    if($library_query->num_rows() > 0) {
        foreach($library_query->result() as $row) {
            $member = $this->UserModel->get_member($row->member_id)->first . ' ' . $this->UserModel->get_member($row->member_id)->last;
            $display_date = date('d/m/Y', strtotime($row->date));
            $library_table[] = "<tr>
                                    <td>$row->title <small>$row->description</small></td>
                                    <td>
                                        <a class='btn btn-light' href='$row->url' target='_blank'><i class='fa fa-download'></i> download</a>
                                    </td>
                                    <td>$member</td>
                                    <td>$display_date</td>
                                </tr>";
        }
    }
?>
<div class='container'>
 <table class='table table-foo' data-filtering='true' data-paging='true' data-sorting='true' data-paging-size="20">
                <thead><th>Document</th><th>&nbsp;</th><th data-breakpoints='all'>Added By</th><th data-breakpoints='all'>Date</th></thead>
            	<tbody>
                	<?php echo implode(PHP_EOL, $library_table); ?>
            	</tbody>
        	</table>
        	
        	</div>