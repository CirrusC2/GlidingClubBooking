<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center py-4">
                    <img src="<?= base_url(getenv('CLUB_LOGO_URL')) ?>" class="img-fluid mb-3" style="max-width: 300px;" alt="Club Logo">
                    <h4 class="mb-0">Member Login</h4>
                </div>
                <div class="card-body p-4">
                    <?php
                    $success_flashData = $this->session->flashdata('success_flashData');
                    $error_flashData = $this->session->flashdata('error_flashData');
                    $warning_flashData = $this->session->flashdata('warning_flashData');

                    if ($success_flashData !== NULL) {
                        echo "<div class='alert alert-success' role='alert'>
                                <i class='fas fa-check-circle me-2'></i>$success_flashData
                              </div>";
                    }

                    if ($error_flashData !== NULL) {
                        echo "<div class='alert alert-danger' role='alert'>
                                <i class='fas fa-exclamation-circle me-2'></i>$error_flashData
                              </div>";
                    }

                    if ($warning_flashData !== NULL) {
                        echo "<div class='alert alert-warning' role='alert'>
                                <i class='fas fa-exclamation-triangle me-2'></i>$warning_flashData
                              </div>";
                    }
                    ?>
                    
                    <?= form_open('', ['class' => 'needs-validation']) ?>
                        <div class="mb-4">
                            <label class="form-label">Email address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" value="<?= set_value('email'); ?>" 
                                    class="form-control <?= (form_error('email') == "" ? '':'is-invalid') ?>" 
                                    placeholder="Enter Email">
                            </div>
                            <?= form_error('email'); ?>            
                        </div>      
                        <div class="mb-4">
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
                      
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i> Login
                            </button>
                        </div>

                        <div class="text-center mt-4">
                            <div class="mb-2">
                                <a href="<?= base_url('user/forgot') ?>" class="text-decoration-none">
                                    <i class="fas fa-key me-1"></i> Forgot Password?
                                </a>
                            </div>
                            <div>
                                <a href="<?= base_url('user/registration') ?>" class="text-decoration-none">
                                    <i class="fas fa-user-plus me-1"></i> Not Registered? Create Account
                                </a>
                            </div>
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
    padding: 1.5rem;
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

.is-invalid {
    border-color: #dc3545;
}

.is-invalid:focus {
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.alert {
    border: none;
    border-radius: 8px;
    padding: 1rem 1.5rem;
}

a {
    color: #0d6efd;
}

a:hover {
    color: #0a58ca;
}
</style>