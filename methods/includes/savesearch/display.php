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
            <a onclick="userLogOut();" href="<?php echo get_bloginfo('url') ?>/sr-logout">Logout</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            &nbsp;
        </div>
    </div>

    <?php
    $sql = "SELECT * FROM wp_sr_savesearch WHERE user_id = " . $index;
    $res = SEO_RETS_Plugin::wpdbSelectResults($sql);

    $p = count($res);
    $array = [];
    foreach ($res as $k => $r) {
        $t = array(
            'link' => json_decode(base64_decode($r->base64link), true),
            'originals' => urlencode($r->base64link),
            'key' => $r->id
        );
        array_push($array, $t);
    }
    foreach ($array as $key => $ar) {
        ?>
        <div class="row margin-top-20" style="padding-bottom: 20px;border-bottom: 1px solid #ccc">
            <div class="col-md-8 col-sm-8">

                <?
                foreach ($ar['link']['q']['c'] as $c) {
                    if ($c['c']) {
                        foreach ($c['c'] as $l) {
                            ?>
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <?
                                    $minMax = str_replace('=', '', $l['o']);
                                    if ($minMax != "" && $minMax == ">") {
                                        echo 'Min ' . $l['f'];
                                    } elseif ($minMax != "" && $minMax == "<") {
                                        echo 'Max ' . $l['f'];
                                    } else {
                                        echo $l['f'];
                                    }
                                    ?>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <?= $l['v']; ?>
                                </div>
                            </div>
                            <?
                        }
                    }
                    ?>
                    <div class="row">
                        <div class="col-md-4 col-sm-4">
                            <?
                            $minMax2 = str_replace('=', '', $c['o']);
                            if ($minMax2 != "" && $minMax2 == ">") {
                                echo 'Min ' . $c['f'];
                            } elseif ($minMax2 != "" && $minMax2 == "<") {
                                echo 'Max ' . $c['f'];
                            } else {
                                echo $c['f'];
                            }
                            ?>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <?= $c['v']; ?>
                        </div>
                    </div>
                    <?
                } ?>

            </div>
            <div class="col-md-4 col-sm-4">
                <div class="row">
                    <div class="col-md-6">
                        <a target="_blank" href="/sr-search/?<?= $ar['originals']; ?>">Repeat Search</a>
                    </div>
                    <div class="col-md-6">
                        <a href="<?= get_bloginfo('url') ?>/sr-search-fav?remove=<?= $ar['key'] ?>">Remove</a>
                    </div>
                </div>
            </div>
        </div>
        <?

    } ?>
</div>
<script src="https://apis.google.com/js/platform.js?onload=onLoad" async defer></script>