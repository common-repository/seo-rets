<?php
$sr = $seo_rets_plugin;
$plugin_title = $sr->admin_title;
$plugin_id = $sr->admin_id;

if ( isset($_POST['submit']) ) {

    if ( $_POST['banner'] || $_POST['logo'] || $_POST['name'] || $_POST['phone'] ) {
        $logo_name = pathinfo(parse_url($_POST['logo'], PHP_URL_PATH), PATHINFO_BASENAME);
        $banner_name = pathinfo(parse_url($_POST['banner'], PHP_URL_PATH), PATHINFO_BASENAME);

        $banner_mime = '';
        $logo_mime = '';

        $logo = base64_encode($sr->http_request($_POST['logo'], NULL, $logo_mime));
        $banner = base64_encode($sr->http_request($_POST['banner'], NULL, $banner_mime));

        update_option('sr_logo_image',
            array(
                'name' => $logo_name,
                'mime' => $logo_mime,
                'url' => $_POST['logo']
            )
        );

        update_option('sr_banner_image',
            array(
                'name' => $banner_name,
                'mime' => $banner_mime,
                'url' => $_POST['banner']
            )
        );

        $response = json_decode($sr->http_request($sr->api_url, array(
            'api-key' => $sr->api_key,
            'request' => json_encode(array("method" => "upload_branding", "parameters" => array(
                'logo' => array(
                    'name' => $logo_name,
                    'mime' => $logo_mime
                ),
                'banner' => array(
                    'name' => $banner_name,
                    'mime' => $banner_mime
                ),
                'pdf' => array(
                    'name' => $_POST['name'],
                    'phone' => $_POST['phone']
                )
            ))),
            'logo' => $logo,
            'banner' => $banner
        )));

        $sr->refresh_feed();

        if ( $response->result == 1 ) {
            echo '<div id="setting-error-settings_updated" class="updated settings-error">
<p><strong>Settings Updated.</strong></p></div>';
        } else {
            echo '<div id="setting-error-settings_updated" class="updated settings-error">
<p><strong>Failed to upload image, please contact <?php echo $plugin_title ?> support.</strong></p></div>';
        }
    }
}

$logo = get_option('sr_logo_image');
$banner = get_option('sr_banner_image');

?>
<script type="text/javascript">

    jQuery(function($) {
        var input_box;

        var upload_media = function(element) {
            input_box = element;
            tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        };


        window.send_to_editor = function(html) {
            var url = jQuery('img', html).attr('src');

            if ( (typeof url) === 'undefined' ) {
                url = jQuery(html).attr('href');
            }

            input_box.val(url);
            tb_remove();
        }

        jQuery(".upload").click(function() {
            upload_media(jQuery(this));
        });
    });

</script>

<div class="wrap">
    <div id="icon-tools" class="icon32"></div>
    <h2><?php echo $plugin_title ?> :: Plugin Branding</h2>
    <div class="tool-box">
        <form action="" method="post">
            <p>You can use the form below to customize the images used by listing alert emails, and dynamic listing PDFs. Remember to edit the images to match the resolution listed.</p>
            <h3>PDF Settings</h3>
            <table class="form-table">
                <tbody>
                <tr valign="top">
                    <th scope="row">
                        <label for="name">PDF Name</label>
                    </th>
                    <td>
                        <input name="name" type="text" class="regular-text ltr" value="<?=htmlentities($sr->feed->pdf_name)?>">
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="phone">PDF Phone</label>
                    </th>
                    <td>
                        <input name="phone" type="text" class="regular-text ltr" value="<?=htmlentities($sr->feed->pdf_phone)?>">
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="logo">PDF Logo <span class="description">(170x100 Pixels)</span></label>
                    </th>
                    <td>
                        <input name="logo" type="text" class="regular-text ltr upload" value="<?=htmlentities($logo['url'])?>">
                    </td>
                </tr>
                </tbody>
            </table>

            <h3>Email Settings</h3>

            <table class="form-table">
                <tbody>

                <tr valign="top">
                    <th scope="row">
                        <label for="banner">Email Banner <span class="description">(600x120 Pixels)</span></label>
                    </th>
                    <td>
                        <input name="banner" type="text" class="regular-text ltr upload" value="<?=htmlentities($banner['url'])?>">
                    </td>
                </tr>
                </tbody>
            </table>
            <p><br /><input type="submit" class="button button-primary" name="submit" value="Save Images"></p>

        </form>
        <?php



        ?>


    </div>
</div>
