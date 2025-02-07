<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="assets/style.css"> 
</head>
<body>
    <div class="container">
        <div class="col-4 offset-4">
            <a href="<?= base_url('login') ?>" class="go-back-btn">Login</a>
            <?php echo form_open(base_url('forgot_password')); ?>

                <h2 class="text-center">Forgot Password</h2>   
                
                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger">
                        <?= session('error') ?>
                    </div>
                <?php endif; ?>
        
                <div class="form-group">
                    <input type="email" class="form-control" placeholder="Enter your registration Email" required="required" name="email">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Send Reset Token</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</body>
</html>
