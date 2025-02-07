<html>

<head>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/style.css">
    <!-- <script src="<?php echo base_url(); ?>assets/js/jquery-3.6.0.min.js"></script> -->
    <!-- <script src="<?php echo base_url(); ?>assets/js/bootstrap.js"></script> -->
</head>

    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand">UQ Treehouse</a>
            <?php if (session()->get('username')) { ?>
                <a class="mx-4" href="<?php echo base_url(); ?>login/logout"> Logout </a>
            <?php } else { ?>
                <button class="signup-btn" onclick="location.href='<?php echo base_url(); ?>register'">Sign up now</button>
            <?php } ?>
        </nav>
        

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var closebtn = document.querySelector(".error-closebtn");
                if (closebtn) {
                closebtn.onclick = function () {
                    var errorPopup = this.parentElement;
                    errorPopup.style.display = "none";
                };
                }
            });
        </script>

    </body>
</html>
