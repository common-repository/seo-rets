<meta name="google-signin-client_id" content="356743512790-tj23ahr24a8vv0p9jsdsrcvsbodngcd1.apps.googleusercontent.com">

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
<div class="row sr-list-menu">
    <div class="col-md-12">
        <a href="<?php echo get_home_url() ?>/sr-user">My Details</a>
        <a href="<?php echo get_home_url() ?>/sr-favorites">Favorites</a>
        <a href="<?php echo get_home_url() ?>/sr-search-fav">Saved Search</a>
        <!--		<button onclick="FB.logout()"></button>-->
        <a onclick="userLogOut()" href="<?php echo get_bloginfo('url') ?>/sr-logout">Logout</a>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        &nbsp;
    </div>
</div>
<?php
$sql = "SELECT * FROM wp_sr_favorites WHERE user_id = " . $index;
$res = SEO_RETS_Plugin::wpdbSelectResults($sql);
$sr = $this;

if (count($res) > 0):
    if (isset($templates['css'])) {
        ?>
        <?php
        wp_enqueue_style('sr_method_display', $this->css_resources_dir . 'methods/display.css');
        wp_add_inline_style('sr_method_display', $templates['css']);
        wp_print_styles(array('sr_method_display'));
        ?>
        <!--        <style type="text/css">-->
        <!--            --><?php //echo $templates['css'];
        ?>

        <!--        </style>-->
        <?php
    }
    foreach ($res as $index => $favorite):

        $server_name = $this->feed->server_name;
        $match = array();
        if (preg_match("/^([a-zA-Z]+)\\.([a-zA-Z]+)$/", $favorite->mtype, $match)) {
            $server_name = $match[1];
        }
        $photo_dir = "http://img.seorets.com/" . $server_name;

        $request = $this->api_request('get_listings', array(
            'type' => $favorite->mtype,
            'query' => array(
                'boolopr' => 'AND',
                'conditions' => array(
                    array(
                        'field' => 'mls_id',
                        'operator' => '=',
                        'value' => $favorite->mls
                    )
                )
            ),
            'limit' => array(
                'range' => 1,
                'offset' => 0
            )
        ));


        $l = $request->result[0];
        $l->city2 = preg_replace('/\s/', '+', $l->city);
        $url = $this->listing_to_url($l, $favorite->type);
        $templates = get_option('sr_templates');
        $type = $favorite->mtype;
        if ($l->mls_id) {
            echo '<div class="sr-fav"><a href="' . get_bloginfo('url') . '/sr-favorites?remove=' . $favorite->id . '" style="float:right;color:red;">Remove</a>';

            if (isset($templates['result'])) {
                if (isset($templates['css'])) {
                    ?>

                    <?php echo $templates['css']; ?>
                    <?php
                } else {
                    include($sr->resp_css);
                }
                eval('?>' . $templates['result']);
            } else {
                include($this->server_plugin_dir . '/resources/defaults/template-result.php');
            }


            echo '<div style="clear:both"></div></div>';
        }
    endforeach;

else: ?>
    <p>You haven't saved any listings yet.</p>
<?php endif; ?>


<script src="https://apis.google.com/js/platform.js?onload=onLoad" async defer></script>