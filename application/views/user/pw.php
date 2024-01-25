<div class="container">

    <!-- Include Flash Data File -->
    <?php $this->load->view('_FlashAlert/flash_alert.php') ?>
    
    <?= form_open() ?>
        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="password" value="<?= set_value('password'); ?>" class="form-control <?= (form_error('password') == "" ? '':'is-invalid') ?>" placeholder="Please choose a new Password">
            <?= form_error('password'); ?> 
        </div>
        <div class="form-group">
            <label>Password Confirmation</label>
            <input type="password" name="passconf" value="<?= set_value('passconf'); ?>" class="form-control <?= (form_error('passconf') == "" ? '':'is-invalid') ?>" placeholder="Password Confirmation">
            <?= form_error('passconf'); ?> 
        </div>
        <button type="submit" class="btn btn-primary">Update Password</button>
        <?= form_close() ?>
    
</div>
