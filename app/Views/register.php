<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Page</title>
  <link rel="stylesheet" href="assets/style.css"> 
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>
</head>

<body style="background-image: url('<?php echo base_url('assets/pictures/register.jpg'); ?>');">
<div class="container register-container">
    <div class="row">
        <div class="col-4 offset-4">
            <?php echo form_open(base_url().'register'); ?>
                <h2 class="text-center">Register</h2> 
                <?php if (!empty($validation)): ?>
                    <div class="alert alert-danger">
                        <?= $validation->listErrors() ?>
                    </div>
                <?php endif; ?>      
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Email" required="required" name="email">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Username" required="required" name="username">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Password" required="required" name="password">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Confirm Password" required="required" name="confirm_password">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Join now</button>
                </div>
                <div class="clearfix">
                    <span>Already have an account? <a href="<?php echo base_url('login'); ?>" class="login-link">Log in here</a></span>
                </div>    
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const $passwordInput = $('input[name="password"]');
        const $passwordStrengthDiv = $('<div class="password-strength" style="color: red; display: none;"></div>');
        $passwordStrengthDiv.insertAfter($passwordInput);

        function updateStrength() {
            const password = $passwordInput.val();

            if (password.length == 0) {
                $passwordStrengthDiv.hide();
                return;
            }

            const result = zxcvbn(password);
            let strengthText = '';
            const hasSpecialCharacter = /[\W_]/.test(password);

            switch (result.score) {
            case 0:
                strengthText = 'Very Weak';
                break;
            case 1:
                strengthText = 'Weak';
                break;
            case 2:
                strengthText = 'Fair';
                break;
            case 3:
                strengthText = 'Strong';
                break;
            case 4:
                if (hasSpecialCharacter) {
                    strengthText = 'Very Strong';
                } else {
                    strengthText = 'Strong';
                }
                break;
            }
            $passwordStrengthDiv.text('Password strength: ' + strengthText);
            $passwordStrengthDiv.show();
        }
        $passwordInput.on('input', updateStrength);
        updateStrength();
    });
</script>





</body>
</html>
