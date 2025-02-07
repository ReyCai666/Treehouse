<!DOCTYPE html>
<html>
<head>
	<title>Email Verification</title>
</head>
<body>
	<h1>Email Verification</h1>
	<?php if (isset($error)): ?>
		<div><?= esc($error) ?></div>
	<?php endif; ?>

	<form method="post" action="<?= base_url('register/verify') ?>">
		<label for="verification_code">Verification Code:</label>
		<input type="text" id="verification_code" name="verification_code"><br>
		<button type="submit">Verify</button>
	</form>
</body>
</html>
