<meta name="google-signin-client_id" content="<?php echo get_option('googleid'); ?>">

<script>
    window.fbAsyncInit = function () {
        FB.init({
            appId: '<?php echo get_option('fbid'); ?>',
            cookie: true,  // enable cookies to allow the server to access
                           // the session
            xfbml: true,  // parse social plugins on this page
            version: 'v2.2' // use version 2.2
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
    function userLogOut() {
        FB.getLoginStatus(function (response) {
            if (response && response.status === 'connected') {
                FB.logout(function (response) {
                    jQuery.ajax({
                        url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=user-logout',
                        type: "POST",
                        data: {
                            test: 'ololo'
                        },
                        success: function (response) {
                            console.log(response);
                            window.location.reload();

                        }
                    });
                });
            }
        });
        var auth2 = gapi.auth2.getAuthInstance();

        auth2.signOut().then(function () {
            jQuery.ajax({
                url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=user-logout',
                type: "POST",
                data: {
                    test: 'ololo'
                },
                success: function (response) {
                    console.log(response);
                    window.location.reload();

                }
            });
            console.log('User signed out.');
        });

    }
    function onLoad() {
        gapi.load('auth2', function () {
            gapi.auth2.init();
        });
    }

</script>
<div class="sr-content">
    <div class="row sr-list-menu">
        <div class="col-md-12">
            <a href="<?php echo get_home_url() ?>/sr-user">My Details</a>
            <a href="<?php echo get_home_url() ?>/sr-favorites">Favorites</a>
            <a href="<?php echo get_home_url() ?>/sr-search-fav">Saved Search</a>
            <a onclick="userLogOut()" href="<?php echo get_bloginfo('url') ?>/sr-logout">Logout</a>
        </div>
    </div>
    <div style="clear: both"></div>
    <form action="" method="get">
        <div class="sr_options margin-top-20">
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <label for="">Username</label>
                    <input type="text" name="u_name" value="<?= $user[0]['u_name'] ?>" class="form-control">
                </div>
                <div class="col-md-6 col-sm-6">
                    <label for="">Full name</label>
                    <input type="text" name="full_name" value="<?= $user[0]['full_name'] ?>" class="form-control">
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-6 col-sm-6">
                    <label for="">Your Phone:</label>
                    <input type="text" name="phone" value="<?= $user[0]['u_phone'] ?>" class="form-control">
                </div>
            </div>

            <div class="row margin-top-15">
                <div class="col-md-8 col-sm-8">
                </div>
                <div class="col-md-4 col-sm-4">
                    <label for="">&nbsp;</label>
                    <input type="submit" name="user-save"
                           id="user-save"
                           class="button-primary form-control" value="Save"/>
                </div>
            </div>
        </div>
    </form>
</div>
<script src="https://apis.google.com/js/platform.js?onload=onLoad" async defer></script>