<?php

$page_name = "Verify User";
if ( $this->template_settings['type'] == "all" ) {
	wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['all-value'])), "post_meta");
} else {
	wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['every-values'][$page_name])), "post_meta");
}


function sr_is_verified() {
	$users = get_option('sr_users');

	if ( $users ) {
		foreach ( $users as $index => $user ) {
			if ( isset($user['verify']) && $user['verify'] == $_GET['key'] ) {
				unset($users[$index]['verify']);
				update_option('sr_users', $users);
				// TODO notify api of user
				return $user;
			}
		}
	}
	
	return false;
}

$vuser = sr_is_verified();

if ( $vuser ) {
	if ( ($key = get_option("sr-mailchimptoken")) && ($list = get_option("sr-mailchimplist")) ) {
			require_once($this->server_plugin_dir . "/includes/MCAPI.class.php");
			$mailchimp = new MCAPI($key, true);
			$mailchimp->listSubscribe($list, $_GET['email'], array("NAME" => $_GET['sr-name'], "OPTIN_IP" => $_SERVER['REMOTE_ADDR'], "OPTIN_TIME" => date("Y-m-d H:i:s")), 'html', false, true);
		}
	$currentPage->post_title = 'Verify Account';
	$currentPage->post_content = $this->include_return('methods/includes/verify/success.php', get_defined_vars());
} else {
	$currentPage->post_title = 'Verify Account';
	$currentPage->post_content = $this->include_return('methods/includes/verify/fail.php', get_defined_vars());
}


