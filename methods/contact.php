<?php

$options = get_option('sr_popup');

$title   = isset($_GET['title']) ? htmlentities($_GET['title']) : ($options['title'] ? htmlentities($options['title']) : htmlentities($this->popup_defaults['title']));
$sub     = isset($_GET['sub']) ? htmlentities($_GET['sub']) : ($options['sub'] ? htmlentities($options['sub']) : htmlentities($this->popup_defaults['sub']));
$btn     = isset($_GET['btn']) ? htmlentities($_GET['btn']) : ($options['btn'] ? htmlentities($options['btn']) : htmlentities($this->popup_defaults['btn']));
$css     = isset($_GET['css']) ? $_GET['css'] : ($options['css'] ? $options['css'] : $this->popup_defaults['css']);
$success = isset($_GET['success']) ? htmlentities($_GET['success']) : ($options['success'] ? htmlentities($options['success']) : htmlentities($this->popup_defaults['success']));
$error   = isset($_GET['err']) ? htmlentities($_GET['err']) : ($options['error'] ? htmlentities($options['error']) : htmlentities($this->popup_defaults['error']));
$email   = isset($options['email']) ? $options['email'] : $this->popup_defaults['email'];


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title><?php echo $title?></title>
    <?php ?>
        <?php
        wp_enqueue_style('sr_method_contact',$this->css_resources_dir.'methods/contact.css');
        wp_add_inline_style('sr_method_contact',$css);
        wp_print_styles(array('sr_method_contact'));

        wp_enqueue_script('jquery');
        wp_print_scripts(array('jquery'));
        ?>
<!--    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->
    <!-- Need to include library for this iframe so the parenr url can be accessed -->
</head>

<body>
<?php

$errors = false;

if ( isset($_POST['submit']) ) {
    if ( !$_POST['name'] || !$_POST['phone'] ) {
        $errors = $error;
    }
}

if ( isset($_POST['submit']) && ($errors === false) ):
    if ( count($_GET) == 0 ) {
        $this->set_session_data('registered', true);
        //$this->lead_alert("{$_POST['name']} <{$_POST['email']}>", "Lead Popup");
        //This is redundant, they already get an email for this

        $domain = $_SERVER['SERVER_NAME'];

        if (substr($domain,0,4)=="www."){$domain=substr($domain,4);}

        $to      = $email;
        $subject = 'New Lead - ' . get_bloginfo('wpurl');
        $message = 'Name: ' . $_POST['name'] . "\r\n";
        $message .= 'Phone: ' . $_POST['phone'] . "\r\n";

        if ( $_POST['email'] ) {
            $message .= 'Email: ' . $_POST['email'] . "\r\n";
        }

        $message .= 'URL viewed when signing up: ' . $_POST['hidden'] . "\r\n";

        if ( $_POST['p_type'] ) {
            $message .= 'Interested in ' .$_POST['p_type'] . "\r\n";
        }

        if ( $_POST['price_min'] && $_POST['price_max'] ) {
            $message .= 'Price Range $' . $_POST['price_min'] . ' to $' . $_POST['price_max'] . "\r\n";
        }

        $headers = 'From: lead@' . $domain;

        SEO_RETS_Plugin::sendEmail($to, $subject, $message, $headers);

        if ( ($key = get_option("sr-mailchimptoken")) && ($list = get_option("sr-mailchimplist")) ) {
            require_once($this->server_plugin_dir."/includes/MCAPI.class.php");
            $mailchimp = new MCAPI($key, true);
            $mailchimp->listSubscribe($list, $_POST['email'], array("NAME" => $_POST['name'], "OPTIN_IP" => $_SERVER['REMOTE_ADDR'], "OPTIN_TIME" => date("Y-m-d H:i:s")), 'html', false, true);
        }
    }
    ?>
<h3 style="color:green;">Thanks, <?php echo htmlentities($_POST['name'])?></h3>
<h3><?php echo $success?></h3>
<p style="text-align:center;font-size:12px;"><a href="javascript:window.parent.close_popup();">Click here</a> to close.</p>
    <?php elseif($options['showCustom']=='true'): ?>
    <?php
    wp_enqueue_style('sr_method_contact',$this->css_resources_dir.'methods/contact.css');
    wp_add_inline_style('sr_method_contact',$options['css']);
    wp_print_styles('sr_method_contact');
    ?>
    <?php eval('?>' .  $options['customHtml']); ?>
    <?php else: ?>
<h1 class="sr-contact-h1"><?php echo $title?></h1>
<h3 class="sr-contact-h3"><?php echo $sub?></h3>
<form  class="sr-contact-form" action="" method="post">
    <table>
        <tr>
            <td>Your Name <span style="color:red;">*</span></td>
        </tr>
        <tr>
            <td><input type="text" name="name" value="<?php echo  isset($_POST['name']) ? htmlentities($_POST['name']) : "" ?>" /></td>
        </tr>
        <tr>
            <td>Your Phone Number <span style="color:red;">*</span></td>
        </tr>
        <tr>
            <td><input type="text" name="phone" value="<?php echo  isset($_POST['phone']) ? htmlentities($_POST['phone']) : "" ?>" /></td>
        </tr>
        <tr>
            <td>Your Email</td>
        </tr>
        <tr>
            <td><input type="text" name="email" value="<?php echo  isset($_POST['email']) ? htmlentities($_POST['email']) : "" ?>" /></td>
        </tr>
        <tr>
            <td>Price Range</td>
        </tr>
        <tr>
            <td>
                <input type="text" name="price_min" placeholder="Min Price" class="contact_price contact_price_min"/><input type="text" name="price_max" placeholder="Max Price" class="contact_price"/>
            </td>
        </tr>
        <tr>
            <?php if ($options['showType']=='true'){?>
            <td style="padding-top:20px;">
                <div id="houseType">
                    <div class="popupType">Interested In:</div>
                    <div class="popupTypeBuying"><p>Buying </p><input type="radio" value="Buying" name="p_type"/></div>
                    <div class="popupTypeRenting"> <p>Renting </p><input type="radio" name="p_type" value="Renting"/></div>
                    <div style="clear:both"></div>
                </div>
            </td>
            <?php }?>
        </tr>
        <tr>
            <td><input type="hidden" name="hidden" value="" id="hidden_parent"/>
                <script>
                    var parent_url = parent.location.href;
                    jQuery("#hidden_parent").val(parent_url);
                </script>
            </td>
        </tr>
    </table>

    <p style="color:red;text-align:center;font-weight:bold;font-size:12px;"><?php echo $errors?></p>

    <div id="sr-button">
        <div id="sr-button-left"></div>

        <div id="sr-button-center">
            <input type="submit" name="submit" value="<?php echo $btn?>" />
        </div>
        <div id="sr-button-right"></div>
    </div>
</form>

    <?php if ( count($_GET) > 0 ): ?>
    <p style="text-align:center;font-size:12px;">This is a preview. No message will be sent. <a href="javascript:window.parent.close_popup();">Click here</a> to close.</p><?php endif; ?>
    <?php endif; ?>
<div style="clear:both"></div>
</body>

</html>

<?php
// So WordPress won't render another page
//exit;
?>
