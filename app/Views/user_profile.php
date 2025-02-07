<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>User Profile</title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/userProfile.css'); ?>">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
	<header>
		<h2 class="welcome-message">Welcome! <?php echo $user['username']; ?>!</h2>
	</header>

	<div class="container">
		<h1>User Profile</h1>
		<div class="profile-avatar">
			<img src="<?php echo base_url($user['profile_pic']); ?>" alt="Profile Picture" style="width: 128px; height: 128px; object-fit: cover;">
		</div>

		<?= form_open_multipart(base_url() . 'user_profile/upload') ?>
			<div class="dropzone">
				<input type="file" name="profile_pic"  id="profile_pic" size="20" accept="image/*">
				<p>Drag and drop a file here, or click to browse</p>
			</div>
			<button type="submit">Upload</button>
		</form>

		<div class="form-group">
			<label class="label-text">ID:</label>
			<span class="editable" data-field="username"><?php echo $user['id']; ?></span>
		</div>

		<?php if (session()->has('error')): ?>
                    <div class="alert alert-danger" style="color: red;">
                        <?= session('error') ?>
                    </div>
					<?php
        				// Clear the flashdata after 5 seconds
        				header("Refresh: 5; URL=".base_url('user_profile'));
    				?>
		<?php endif; ?>

		<div class="form-group">
			<label class="label-text">Username:</label>
			<span class="editable" data-field="username"><?php echo $user['username']; ?></span>
			<img class="edit-icon" src="<?php echo base_url('assets/edit-icon.png'); ?>" alt="Edit Icon">
			<form class="update-username-form hidden" action="<?php echo base_url('user_profile/updateUsername'); ?>" method="post">
				<input type="text" name="new_username" value="<?php echo $user['username']; ?>" />
				<button type="submit">Save</button>
				<button type="button" class="cancel">Cancel</button>
        	</form>
		</div>

		<div class="form-group">
		 <label class="label-text">Verified Email: </label>
			<span class="editable" data-field="email"><?php echo $user['email']; ?></span>
		</div>
		<div class="form-group">
			<label class="label-text">About me:</label>
			<span class="editable" data-field="bio"><?php echo $user['bio']; ?></span>
			<img class="edit-icon" src="<?php echo base_url('assets/edit-icon.png'); ?>" alt="Edit Icon">
			<form class="update-username-form hidden" action="<?php echo base_url('user_profile/updateBio'); ?>" method="post">
				<textarea name="new_bio" rows="5" cols="50"> <?php echo $user['bio']; ?></textarea>
				<button type="submit">Save</button>
				<button type="button" class="cancel">Cancel</button>
        	</form>
		</div>

		<div class="form-group">
			<label class="label-text">Account Status: </label>
			<img class="verified-icon" src="<?php echo base_url('assets/verified-icon.png'); ?>" alt="Edit Icon">
		</div>

		<form action="<?php echo base_url('update_username'); ?>" method="post" class="hidden">
			<div class="form-group">
				<button type="submit" class="edit-btn">Edit Profile</button>
			</div>
		</form>
	</div>

	<script>
		$(document).ready(function () {
			const dropzone = $(".dropzone");

			dropzone.on("dragover", function (e) {
				e.preventDefault();
				e.stopPropagation();
				$(this).css("background-color", "#efefef");
			});

			dropzone.on("dragleave", function (e) {
				e.preventDefault();
				e.stopPropagation();
				$(this).css("background-color", "transparent");
			});

			dropzone.on("drop", function (e) {
				e.preventDefault();
				e.stopPropagation();
				$(this).css("background-color", "transparent");

				const files = e.originalEvent.dataTransfer.files;
				$("#profile_pic")[0].files = files;
			});

			// display the input field when user click edit button
			$('.edit-icon').on('click', function () {
				const editable = $(this).siblings('.editable');
				const updateForm = $(this).siblings('.update-username-form');
				const editIcon = $(this).siblings('.edit-icon');
				editIcon.addClass('hidden');
				editable.addClass('hidden');
				updateForm.removeClass('hidden');
			});

			// Hide the input field when user click save
			$('.update-username-form').on('submit', function() {
				const editable = $(this).siblings('.editable');
                const editIcon = $(this).siblings('.edit-icon');
                editable.removeClass('hidden');
                editIcon.removeClass('hidden');
                $(this).addClass('hidden');
			})
			// close the input field when user click cancel
			$('.cancel').on('click', function() {
				const updateForm = $(this).closest('.update-username-form');
				const editable = updateForm.siblings('.editable');
				const editIcon = updateForm.siblings('.edit-icon');
				editable.removeClass('hidden');
				editIcon.removeClass('hidden');
				updateForm.addClass('hidden'); 
				<?php if (session()->has('error')): ?>
        			<?php session()->remove('error'); ?>
    			<?php endif; ?>
			})
		});
	</script>
</body>
</html>
