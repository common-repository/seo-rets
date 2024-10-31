<?php /*<p>Thanks for signing up! An email has been sent to <?php echo htmlentities($_POST['email'])?> for verification. You must click the link inside the email before you login.</p> FIXME*/ ?>
<p>Thanks for signing up! An email has been sent to <?php echo htmlentities($_POST['email'])?> for verification. <a href="<?php echo get_bloginfo('url')?>/sr-login">Click here</a> to login.</p>
