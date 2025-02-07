<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>
</head>
<body>
    <div class="container">
        <div class="col-4 offset-4">
            <?php if(session()->has('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <?php if (isset($validation)): ?>
                <div class="alert alert-danger">
                    <?= $validation->listErrors() ?>
                </div>
            <?php endif; ?>
            
            <?php if(session()->has('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('forgot_password/reset_password') ?>">
                <h2 class="text-center">Reset Password</h2>   
            
                <div class="form-group">
                    <input type="text2" class="form-control" placeholder="Enter Your Reset Token" required="required" name="token">
                </div>
                <div class="form-group">
                        <input type="password" class="form-control" placeholder="Password" required="required" name="password">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Confirm Password" required="required" name="confirm_password">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</body>
<script>
    // password strength
    $(document).ready(function() {
        const $passwordInput = $('input[name="password"]');
        const $passwordStrengthDiv = $('<div class="password-strength" style="color: red; display: none;"></div>');
        $passwordStrengthDiv.insertAfter($passwordInput); // password strength HTML appear only after user input.

        function updateStrength() {
            const password = $passwordInput.val();
            // The password strength should be hide when user is not typing.
            if (password.length == 0) {
                $passwordStrengthDiv.hide();
                return;
            }
            
            // zxcvbn low-budget password strength detection library
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
            case 4: // customized strength level for "very strong". 
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
</html>
