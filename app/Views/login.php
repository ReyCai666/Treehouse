<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link rel="stylesheet" href="assets/style.css"> 
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body style="background-image: url('<?php echo base_url('assets/pictures/login.jpg'); ?>');">

  <div class="container">
    <div class="col-4 offset-4">

      <?php echo form_open(base_url().'login/check_login', array('id' => 'demo-form')); ?>
        <h2 class="text-center">Login</h2>       
        <div class="form-group">
          <input type="text" class="form-control" placeholder="User Name" required="required" name="username">
        </div>
        <div class="form-group">
          <input type="password" class="form-control" placeholder="Password" required="required" name="password">
        </div>
        <div class="error-message <?php if (!empty($error)) { echo 'show'; } ?>">
          <?php echo $error; ?>
        </div>

        <div class="form-group">
          <div class="g-recaptcha" data-sitekey="6Lc2O-QlAAAAAHOtgZkvxMK0mTXWSqkqLXzk1Ibn"></div>
          <button type="submit" id="loginButton" class="btn btn-primary btn-block" disabled>Log in</button>
        </div>
        
        <div class="form-group remember-me">
          <label class="float-left form-check-label"><input type="checkbox" name="remember"> Remember me</label>
        </div>    
        <span class="float-right">
          <a href="<?php echo base_url('forgot_password'); ?>" class="forgot-password-link">Forgot Password?</a>
        </span>
      <?php echo form_close(); ?>

    </div>
  </div>

  <script>
    function recaptchaUnticked() {
      document.getElementById("loginButton").disabled = true;
    }

    function recaptchaTicked() {
      document.getElementById("loginButton").disabled = false;
    }

    document.querySelector('.g-recaptcha').setAttribute('data-callback', 'recaptchaTicked');
    document.querySelector('.g-recaptcha').setAttribute('data-expired-callback', 'recaptchaUnticked');
 </script>

</body>
</html>
