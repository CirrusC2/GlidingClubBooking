<div class="container">
<?php

  $success_flashData = $this->session->flashdata('success_flashData');
    $error_flashData = $this->session->flashdata('error_flashData');
    $warning_flashData = $this->session->flashdata('warning_flashData');

    if ($success_flashData !== NULL) {
        echo "<div class='alert alert-success' role='alert'>$success_flashData</div>";
    } else {
        echo "<!-- NO SUCCESS FLASHDATA -->";
    }

    if ($error_flashData !== NULL) {
        echo '<div class="alert alert-danger" role="alert">'.$error_flashData.'</div>';
    } else {
        echo "<!-- NO DANGER FLASHDATA -->";
    }

    if ($warning_flashData !== NULL) {
        echo '<div class="alert alert-warning" role="alert">'.$warning_flashData.'</div>';
    } else {
        echo "<!-- NO WARNING FLASHDATA -->";
    }

?>
    
    <?= form_open() ?>
    <div class='card'>
        <div class='card-header'><img src="../assets/img/augc_logo.png" style='width:300px' /></div>
        <div class='card-body'>
            
        <!--div class='alert alert-success'><strong>Welcome to AUGC Bookings</strong><hr>If you had access to the previous application, please sign in using your <strong>Email Address</strong> and password.</div-->
        <div class="form-group">
            <label>Email address</label>
            <input type="email" name="email" value="<?= set_value('email'); ?>" class="form-control <?= (form_error('email') == "" ? '':'is-invalid') ?>" placeholder="Enter Email"> 
            <?= form_error('email'); ?>            
        </div>      
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" value="<?= set_value('password'); ?>" class="form-control <?= (form_error('password') == "" ? '':'is-invalid') ?>" placeholder="Password">
            <?= form_error('password'); ?> 
        </div>
      
        <button type="submit" class="btn btn-primary">Login</button>
        <div style='margin-top:5px;'>
            <small>Forgot Password? <a href='forgot'>click here</a></small><br>
            <small>Not Registered? <a href='registration'>click here</a></small>
        </div>
    <?= form_close() ?>
        </div>
    </div>
</div>