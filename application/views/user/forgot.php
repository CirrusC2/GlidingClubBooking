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
        <div class="form-group">
            <label>Enter Email address</label>
            <input type="email" name="email" value="<?= set_value('email'); ?>" class="form-control <?= (form_error('email') == "" ? '':'is-invalid') ?>" placeholder="Enter Email"> 
            <?= form_error('email'); ?>            
        </div>      
        <button type="submit" class="btn btn-primary">Submit</button>
    <?= form_close() ?>
</div>