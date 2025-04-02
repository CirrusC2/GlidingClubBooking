<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">New Member Registration</h4>
                </div>
                <div class="card-body">
                    <!-- Include Flash Data File -->
                    <?php $this->load->view('_FlashAlert/flash_alert.php') ?>
                    
                    <?= form_open('', ['class' => 'needs-validation']) ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first" value="<?= set_value('first'); ?>" 
                                    class="form-control <?= (form_error('first') == "" ? '':'is-invalid') ?>" 
                                    placeholder="Enter First Name">
                                <?= form_error('first'); ?>        
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last" value="<?= set_value('last'); ?>" 
                                    class="form-control <?= (form_error('last') == "" ? '':'is-invalid') ?>" 
                                    placeholder="Enter Last Name">
                                <?= form_error('last'); ?>        
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" value="<?= set_value('email'); ?>" 
                                    class="form-control <?= (form_error('email') == "" ? '':'is-invalid') ?>" 
                                    placeholder="Enter Email">
                            </div>
                            <?= form_error('email'); ?>            
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" name="phone" value="<?= set_value('phone'); ?>" 
                                    class="form-control <?= (form_error('phone') == "" ? '':'is-invalid') ?>" 
                                    placeholder="Enter Phone Number">
                            </div>
                            <?= form_error('phone'); ?>           
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Postal Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <input type="text" name="postal_address" value="<?= set_value('postal_address'); ?>" 
                                    class="form-control <?= (form_error('postal_address') == "" ? '':'is-invalid') ?>" 
                                    placeholder="Enter Postal Address">
                            </div>
                            <?= form_error('postal_address'); ?>           
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" value="<?= set_value('password'); ?>" 
                                        class="form-control <?= (form_error('password') == "" ? '':'is-invalid') ?>" 
                                        placeholder="Password">
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <?= form_error('password'); ?> 
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password Confirmation</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="passconf" value="<?= set_value('passconf'); ?>" 
                                        class="form-control <?= (form_error('passconf') == "" ? '':'is-invalid') ?>" 
                                        placeholder="Password Confirmation">
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <?= form_error('passconf'); ?> 
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Registration Key</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="text" name="reg_key" class="form-control" 
                                    placeholder="Enter Registration Key (supplied by club member)">
                            </div>
                            <div class="form-text">Don't have a registration key? Contact an existing member.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Register
                            </button>
                            <a href="<?= base_url('user/login') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-sign-in-alt me-2"></i>Already have an account? Login
                            </a>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
});
</script>

<style>
.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
    padding: 1rem 1.5rem;
}

.form-control {
    border-radius: 5px;
    padding: 0.75rem 1rem;
}

.form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}

.input-group-text {
    background-color: #f8f9fa;
    border-right: none;
}

.input-group .form-control {
    border-left: none;
}

.input-group .form-control:focus {
    border-color: #ced4da;
}

.btn-primary {
    padding: 0.75rem 1.5rem;
}

.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.form-text {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.is-invalid {
    border-color: #dc3545;
}

.is-invalid:focus {
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}
</style>