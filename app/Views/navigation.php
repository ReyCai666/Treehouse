<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Navigation Bar</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/navStyle.css'); ?>">
</head>
<body>
	<div class="navbar">
		<div class="navbar-header">
			<h2 class="logo">UQ Treehouse</h2>
		</div>
		<ul class="navbar-nav">
			<?php if (session()->get('username') || isset($_COOKIE['username'])) { ?>
				<?php if (session()->get('username') != null) { ?>
					<li class="nav-item"><a href="<?= base_url("user_profile") ?>"><?= session()->get('username') ?></a></li>
				<?php }; ?>

				<?php if (session()->get('username') == null && isset($_COOKIE['username'])) { ?>
					<li class="nav-item"><a href="<?= base_url("user_profile") ?>"><?= $_COOKIE['username'] ?></a></li>
				<?php }; ?>
			<?php } else { ?>
				<li class="nav-item"><a href="<?= base_url("session_expired") ?>">Login</a></li>
			<?php }; ?>
			<li class="nav-item"><a href="<?= base_url("discussion_forum") ?>">Discussion Forum</a>
			<li class="nav-item"><a href="<?= base_url("course_review") ?>">Course Review</a></li>
			<?php if (session()->get('username') || isset($_COOKIE['username'])) { ?>
				<li class="nav-item"><a href="<?php echo base_url(); ?>login/logout"> Logout </a></li>
			<?php } else { ?>
				<!-- nothing is displayed if session expired. -->
			<?php } ?>

		</ul>
	</div>
</body>
</html>
