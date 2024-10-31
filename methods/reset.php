<?php

$page_name = "Password Reset";
if ( $this->template_settings['type'] == "all" ) {
	wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['all-value'])), "post_meta");
} else {
	wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['every-values'][$page_name])), "post_meta");
}

$index = $this->get_session_data('user_index');


if ( $index ) {
	header("Location: /sr-favorites");
	exit;
}


$users = get_option('sr_users');

foreach ( $users as $n => $user ) {
	if ( $user['forgot'] == $_GET['key'] ) {
		$theuser = $user;
		break;
	}
}

if ( isset($theuser) ) {
	if ( isset($_POST['submit']) ) {
	
		if ( $_POST['password'] !== $_POST['password-again'] ) {
			$errors[] = "Passwords do not match";
		}
	
		if ( strlen($_POST['password']) < 5 ) {
			$errors[] = "Password must be at least 5 characters long";
		}
		
		if ( strlen($_POST['password']) > 255 ) {
			$errors[] = "Password too long";
		}
	
		if ( count($errors) == 0 ) {
			unset($theuser['forgot']);
			$theuser['password'] = md5($theuser['salt'] . $_POST['password']);
			$users[$index] = $theuser;
			update_option('sr_users', $users);
			
			$currentPage->post_title = 'Reset Password';
			$currentPage->post_content = '<p>Your password has been reset. <a href="/sr-login">Click here</a> to login.</p>';
	
		} else {
			$currentPage->post_title = 'Reset Password';
			$currentPage->post_content = $this->include_return('methods/includes/reset/form.php', get_defined_vars());
		}
	} else {
		$currentPage->post_title = 'Reset Password';
		$currentPage->post_content = $this->include_return('methods/includes/reset/form.php', get_defined_vars());
	}
} else {
	$currentPage->post_title = 'Reset Password';
	$currentPage->post_content = "<p>This page has expired.</p>";
}


