<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Session expired page</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/style.css'); ?>">
</head>

<body>
	<header>
		<h1>Your session is expired, please login again</h1>
	</header>
    <a href="<?php echo base_url('login'); ?>" class="login-link">Log in again</a>
</body>
</html>
