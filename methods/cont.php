<?php
$options = get_option('sr_popup');

$title = isset($_GET['title']) ? htmlentities($_GET['title']) : ($options['title'] ? htmlentities($options['title']) : htmlentities($this->popup_defaults['title']));
$sub = isset($_GET['sub']) ? htmlentities($_GET['sub']) : ($options['sub'] ? htmlentities($options['sub']) : htmlentities($this->popup_defaults['sub']));
$btn = isset($_GET['btn']) ? htmlentities($_GET['btn']) : ($options['btn'] ? htmlentities($options['btn']) : htmlentities($this->popup_defaults['btn']));
$css = isset($_GET['css']) ? $_GET['css'] : ($options['css'] ? $options['css'] : $this->popup_defaults['css']);
$success = isset($_GET['success']) ? htmlentities($_GET['success']) : ($options['success'] ? htmlentities($options['success']) : htmlentities($this->popup_defaults['success']));
$error = isset($_GET['err']) ? htmlentities($_GET['err']) : ($options['error'] ? htmlentities($options['error']) : htmlentities($this->popup_defaults['error']));
$email = isset($options['email']) ? $options['email'] : $this->popup_defaults['email'];
$errors = false;

if (isset($_POST['submit'])) {
    if (!$_POST['name'] || !$_POST['phone']) {
        $errors = $error;
    }
}

if (isset($_POST['submit']) && ($errors === false)){
    $this->set_session_data('registered', true);
    //$this->lead_alert("{$_POST['name']} <{$_POST['email']}>", "Lead Popup");
    //This is redundant, they already get an email for this

    $domain = $_SERVER['SERVER_NAME'];

    if (substr($domain, 0, 4) == "www.") {
        $domain = substr($domain, 4);
    }

    $to = $email;
    $subject = 'New Lead - ' . get_bloginfo('wpurl');
    $message = 'Name: ' . $_POST['name'] . "\r\n";
    $message .= 'Phone: ' . $_POST['phone'] . "\r\n";

    if ($_POST['email']) {
        $message .= 'Email: ' . $_POST['email'] . "\r\n";
    }

    $message .= 'URL viewed when signing up: ' . $_POST['hidden'] . "\r\n";

    if ($_POST['p_type']) {
        $message .= 'Interested in ' . $_POST['p_type'] . "\r\n";
    }

    if ($_POST['price_min'] && $_POST['price_max']) {
        $message .= 'Price Range $' . $_POST['price_min'] . ' to $' . $_POST['price_max'] . "\r\n";
    }

    $headers = 'From: lead@' . $domain;

    SEO_RETS_Plugin::sendEmail($to, $subject, $message, $headers);

    if (($key = get_option("sr-mailchimptoken")) && ($list = get_option("sr-mailchimplist"))) {
        require_once($this->server_plugin_dir . "/includes/MCAPI.class.php");
        $mailchimp = new MCAPI($key, true);
        $mailchimp->listSubscribe($list, $_POST['email'], array("NAME" => $_POST['name'], "OPTIN_IP" => $_SERVER['REMOTE_ADDR'], "OPTIN_TIME" => date("Y-m-d H:i:s")), 'html', false, true);
    }
    return 'da';
}