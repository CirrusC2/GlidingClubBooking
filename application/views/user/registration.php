<div class="container">

    <!-- Include Flash Data File -->
    <?php $this->load->view('_FlashAlert/flash_alert.php') ?>
    
    <?= form_open() ?>
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first" value="<?= set_value('first'); ?>" class="form-control <?= (form_error('first') == "" ? '':'is-invalid') ?>" placeholder="Enter First Name">
            <?= form_error('first'); ?>        
        </div>
		<div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last" value="<?= set_value('last'); ?>" class="form-control <?= (form_error('last') == "" ? '':'is-invalid') ?>" placeholder="Enter Last Name">
            <?= form_error('last'); ?>        
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" value="<?= set_value('email'); ?>" class="form-control <?= (form_error('email') == "" ? '':'is-invalid') ?>" placeholder="Enter Email"> 
            <?= form_error('email'); ?>            
        </div>
        <div class="form-group">
            <label>Phone Number</label>
            <input type="tel" name="phone" value="<?= set_value('phone'); ?>" class="form-control <?= (form_error('phone') == "" ? '':'is-invalid') ?>" placeholder="Enter Phone Number">  
            <?= form_error('phone'); ?>           
        </div>
		<div class="form-group">
            <label>Postal Address</label>
            <input type="text" name="postal_address" value="<?= set_value('postal_address'); ?>" class="form-control <?= (form_error('postal_address') == "" ? '':'is-invalid') ?>" placeholder="Enter Postal Address">  
            <?= form_error('postal_address'); ?>           
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" value="<?= set_value('password'); ?>" class="form-control <?= (form_error('password') == "" ? '':'is-invalid') ?>" placeholder="Password">
            <?= form_error('password'); ?> 
        </div>
        <div class="form-group">
            <label>Password Confirmation</label>
            <input type="password" name="passconf" value="<?= set_value('passconf'); ?>" class="form-control <?= (form_error('passconf') == "" ? '':'is-invalid') ?>" placeholder="Password Confirmation">
            <?= form_error('passconf'); ?> 
        </div>
        <div class="form-group">
            <label>Registration Key</label>
            <input type="text" name="reg_key" class="form-control" placeholder="Enter Registration Key (supplied by club member)">
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    <?= form_close() ?>

</div>
<br>