<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view('includes/head'); ?>
    </head>
    <body>
    	<?php $background_url = get_file_uri('files/system/mtje_industria2.jpg'); ?>
        <?php
        if (get_setting("show_background_image_in_signin_page") === "yes") {
            //$background_url = get_file_uri('files/system/sigin-background-image.jpg');
            ?>
            <style type="text/css">
               /* body {background-image: url('<?php echo $background_url; ?>'); background-size:cover}*/
				body{
					width:100%;
					/*background-image:url('https://images.pexels.com/photos/5065/forest-big-aerial-area.jpg');*/
					background-image: url('<?php echo $background_url; ?>');
					background-size:cover;
					left:0;
					top: 0;
				}
            </style>
        <?php } ?>

        <div class="signin-box">
            <?php
            if (isset($form_type) && $form_type == "request_reset_password") {
                $this->load->view("signin/reset_password_form");
            } else if (isset($form_type) && $form_type == "new_password") {
                $this->load->view('signin/new_password_form');
            } else {
                $this->load->view("signin/signin_form");
            }
            ?>
        </div>
    </body>
</html>
<style type="text/css">
	body{
		width:100%;
		/*background-image:url('https://images.pexels.com/photos/5065/forest-big-aerial-area.jpg');*/
		background-image: url('<?php echo $background_url; ?>');
		background-size:cover;
		left:0;
		top: 0;
		background-position:center;
	}
</style>