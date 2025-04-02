<?php 

if(isset($q)) {
    if($q) {
        $quals = implode(PHP_EOL, $q);
    } else {
        $quals = "<div class='alert alert-danger'>This member has no listed qualifications</div>";
    }
} else {
    $quals = "<div class='alert alert-danger'>This member has no listed qualifications</div>";
}

?>

<div class="container">

    <!-- Include Flash Data File -->
    <?php $this->load->view('_FlashAlert/flash_alert.php') ?>
    
    <?= form_open('user/edit_member/' . $member_id, array('autocomplete'=>'false')) ?>
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first" value="<?php echo $member->first; ?>" class="form-control <?= (form_error('first') == "" ? '':'is-invalid') ?>" placeholder="Enter First Name">
            <?= form_error('first'); ?>        
        </div>
		<div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last" value="<?php echo $member->last; ?>" class="form-control <?= (form_error('last') == "" ? '':'is-invalid') ?>" placeholder="Enter Last Name">
            <?= form_error('last'); ?>        
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" value="<?php echo $member->email; ?>" class="form-control <?= (form_error('email') == "" ? '':'is-invalid') ?>" placeholder="Enter Email"> 
            <?= form_error('email'); ?>            
        </div>
        <div class="form-group">
            <label>Phone Number</label>
            <input type="tel" name="phone" value="<?php echo $member->phone; ?>" class="form-control <?= (form_error('phone') == "" ? '':'is-invalid') ?>" placeholder="Enter Phone Number">  
            <?= form_error('phone'); ?>           
        </div>
		<div class="form-group">
            <label>Postal Address</label>
            <input type="text" name="postal_address" value="<?php echo $member->postal_address; ?>" class="form-control <?= (form_error('postal_address') == "" ? '':'is-invalid') ?>" placeholder="Enter Postal Address">  
            <?= form_error('postal_address'); ?>           
        </div>
        <div class="form-group">
            <label>Password <small>leave blank for no change</small></label>
            <input type="password" name="password" value="<?= set_value('password'); ?>" class="form-control <?= (form_error('password') == "" ? '':'is-invalid') ?>" placeholder="Password">
            <?= form_error('password'); ?> 
        </div>
        <div class="form-group">
            <label>Password Confirmation <small>leave blank for no change</small></label>
            <input type="password" name="passconf" value="<?= set_value('passconf'); ?>" class="form-control <?= (form_error('passconf') == "" ? '':'is-invalid') ?>" placeholder="Password Confirmation">
            <?= form_error('passconf'); ?> 
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <?= form_close() ?>
        <hr>
        <h5>Current Member Qualifications</h5>
        <?php echo $quals; ?>
        <h5>Add Qualification</h5>
        <div class='form-group'>
            <div class='form-inline'>
                <?= form_open('user/add_qual/' . $member_id) ?>
                    <select class='form-control' name='add_qual'>
                        <option value=''>select qualification to add</option>
                        <?php echo implode(PHP_EOL, $all_quals); ?>
                    </select>
                    <button class='btn btn-success' type='submit'>add qualification</button>
                <?= form_close() ?>
            </div>
        </div>
</div>
<br>

<script>
    $('.qual').on('closed.bs.alert', function () {
        var row_id = $(this).attr('qual_row');
        var url = '<?php echo base_url('user/remove_qual/'); ?>' + row_id + '/<?php echo $member_id; ?>';
        window.location.replace(url);
  // do something…
})
</script>