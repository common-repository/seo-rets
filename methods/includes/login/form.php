<?php

$text_settings = get_option('sr-plugintext');

if ($text_settings) {
    $text_settings_put = $text_settings;
} else {
    $text_settings_put = $this->text_defaults;
}

?>

<p><?php eval("?>" . $text_settings_put['login']) ?></p>
<?php if (isset($errors) && count($errors) > 0): foreach ($errors as $error): ?>
    <p style="color: red;">* <?php echo $error ?></p>
<?php endforeach; endif; ?>
<?php if (get_option('googleid')) { ?>
    <meta name="google-signin-scope" content="profile email">
    <meta name="google-signin-client_id"
          content="<?php echo get_option('googleid'); ?>">
    <script src="https://apis.google.com/js/platform.js" async defer></script>

<div class="row margin-top-10">
    <div class="g-signin2 col-md-4 col-sm-4" data-onsuccess="onSignIn" data-theme="light"></div>
    <script>
        function onSignIn(googleUser) {
            // Useful data for your client-side scripts:
            var profile = googleUser.getBasicProfile();
            jQuery.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=google-users',
                type: "POST",
                data: {
                    name: profile.getName(),
                    email: profile.getEmail(),
                    uid: profile.getId(),
                    fullname: profile.getName()
                },
                success: function (response) {
                    console.log(response);
                    location.reload();
                }
            });
//            console.log("ID: " + profile.getId()); // Don't send this directly to your server!
//            console.log("Name: " + profile.getName());
//            console.log("Image URL: " + profile.getImageUrl());
//            console.log("Email: " + profile.getEmail());

            // The ID token you need to pass to your backend:
//            var id_token = googleUser.getAuthResponse().id_token;
//            console.log("ID Token: " + id_token);
        }

    </script>
</div>
<?php } ?>
<?php if (get_option('fbid')) { ?>
    <script>
        jQuery(document).ready(function () {

            jQuery('#fbLoginBtn').click(function (e) {
                e.preventDefault();
                FB.login(function (response) {
                    console.log(response);
                    statusChangeCallback(response);
                }, {
                    scope: 'email',
                    return_scopes: true
                });

            });
        });
        function fbLogout() {
            FB.logout(function (response) {
                //Do what ever you want here when logged out like reloading the page
                window.location.reload();
            });
        }
        function statusChangeCallback(response) {
            console.log('statusChangeCallback');
            console.log(response);
            if (response.status === 'connected') {
                testAPI();
            }
        }
        function checkLoginState() {
            FB.getLoginStatus(function (response) {
                statusChangeCallback(response);
            });
        }

        window.fbAsyncInit = function () {
            FB.init({
                appId: '<?php echo get_option('fbid'); ?>',
                cookie: true,  // enable cookies to allow the server to access
                               // the session
                xfbml: true,  // parse social plugins on this page
                version: 'v2.2' // use version 2.2
            });
            FB.getLoginStatus(function (response) {
                statusChangeCallback(response);
            });

        };
        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        function testAPI() {
            console.log('Welcome!  Fetching your information.... ');
            FB.api('/me', {fields: 'email,last_name,first_name,id,name'}, function (response) {
                console.log(response);
                jQuery.ajax({
                    url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=fb-users',
                    type: "POST",
                    data: {
                        name: response.first_name + ' ' + response.last_name,
                        email: response.email,
                        uid: response.id,
                        fullname: response.name
                    },
                    success: function (response) {
                        console.log(response);
                        location.reload();
                    }
                });
            });
        }
    </script>

    <div class="row margin-top-10">
        <div class="col-md-4 col-sm-4">
            <div class="btn-group-justified">
                <a href="#" class="btn btn-facebook" id="fbLoginBtn"><span class="fa fa-facebook pull-left"></span><span
                        class="signinFBText">Sign In with Facebook</span></a>
            </div>
        </div>
    </div>
    <div class="row margin-top-15">
        <div class="col-md-4 col-sm-4 text-align-center">
            <span>OR</span>
        </div>
    </div>
<?php } ?>
<form action="" method="post">

    <div class="row">
        <div class="col-md-4 col-sm-4">
            <label for="">Email:</label>
            <input type="text" name="email" class="form-control"
                   value="<?php echo empty($_POST['email']) ? '' : htmlentities($_POST['email']) ?>"/>
        </div>
    </div>
    <div class="row margin-top-5">
        <div class="col-md-4 col-sm-4">
            <label for="">Password:</label>
            <input type="password" name="password" class="form-control"/>
        </div>
    </div>
    <div class="row margin-top-5">
        <div class="col-md-4 col-sm-4">
            <input type="submit" name="submit" value="Login"/>
        </div>
    </div>
</form>
<br/>
<!--<button onclick="fbLogout()">Logout</button>-->
<p><a href="<?php echo get_bloginfo('url') ?>/sr-signup">Sign Up</a> | <a
        href="<?php echo get_bloginfo('url') ?>/sr-forgot">Forgot Password</a></p>

