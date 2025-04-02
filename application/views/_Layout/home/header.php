<?php
$admin = false;
if(isset($this->session->userdata['USER_ID'])) {
	if($this->session->userdata['IS_ADMIN'] == 1) {
		$admin = 1;
	} 
} 


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= getenv('PAGE_TITLE') ?: getenv('SITE_TITLE') ?: 'Gliding Club Bookings' ?></title>
    <link rel="stylesheet" href="<?= base_url("assets/css/bootstrap.min.css"); ?>">
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
	<link href="<?= base_url("assets/css/footable.bootstrap.css"); ?>" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
	<script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#"><?= getenv('SITE_TITLE') ?></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="<?= base_url() ?>">Home <span class="sr-only">(current)</span></a>
            </li>
            </ul>
            <div class="form-inline my-2 my-lg-0">
                <?php if (!empty($this->session->userdata('USER_ID')) && $this->session->userdata('USER_ID') > 0) { ?>
                <a href="<?= base_url('User/edit_profile/') . $this->session->userdata['USER_ID']; ?>" class="btn btn-info my-2 my-sm-0">My Profile</a> &nbsp;
                    <!-- User isLogin -->
				<?php if($admin) { ?>
					<a href="<?= base_url('Admin/Panel') ?>" class="btn btn-success my-2 my-sm-0">Admin</a> &nbsp;
					<?php
				} ?>
                    <a href="<?= base_url('bookings') ?>" class="btn btn-primary my-2 my-sm-0">Bookings</a> &nbsp;
					<a href="<?= base_url('bookings/Make') ?>" class="btn btn-warning my-2 my-sm-0">Make a Booking</a> &nbsp;
					<a href="<?= base_url('Admin/gliders') ?>" class="btn btn-info my-2 my-sm-0">Glider Status</a> &nbsp;
					<a href="<?= base_url('Admin/library') ?>" class="btn btn-light my-2 my-sm-0">Document Library</a> &nbsp;
					
                    <a href="<?= base_url('User/logout') ?>" class="btn btn-danger my-2 my-sm-0">Logout</a>
                <?php } else { ?>
                    <!-- User not Login -->
                    <a href="<?= base_url('User/registration') ?>" class="btn btn-info my-2 my-sm-0">Register</a> &nbsp;
                    <a href="<?= base_url('User/login') ?>" class="btn btn-success my-2 my-sm-0">Login</a>
                <?php } ?>
            </div>
        </div>
    </nav>
    <br>