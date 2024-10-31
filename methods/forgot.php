<?php

$page_name = "Forgot Password";
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


function sr_is_unique_key($users, $key) {
	
	if ( $users ) {
		foreach ( $users as $user ) {
			if ( $key == $user['verify'] || $key == $user['salt'] || $key == $user['forgot'] ) return false;
		}
	}
	
	return true;
}

function sr_rand($users) {

	do {
		$salt = '';

		for ( $n = 0; $n < 20; $n++ ) {
			$salt .= chr(rand(65, 90));
		}
	
	} while ( !sr_is_unique_key($users, $salt) );
	
	return $salt;
}



function sr_email_exists() {
	$users = get_option('sr_users');
	
	if ( $users ) {
		foreach ( $users as $index => $user ) {
			if ( $user['email'] == $_POST['email'] ) {
		
				$users[$index]['forgot'] = sr_rand($users);
				
				update_option('sr_users', $users);
				
				return $users[$index];
			}
		}
	}
	
	return false;
}


if ( $_POST['submit'] ) {
	
	$errors = array();
	
	$user = sr_email_exists();
	
	if ( !$user ) {
		$errors[] = "We couldn't find that email address";
	}
	
	if ( count($errors) == 0 ) {
	
		$to      = $_POST['email'];
		$subject = 'Reset Password on ' . $_SERVER['SERVER_NAME'];
		$message = 'Hello ' . $user['name'] . ",\n\nWe've recieved a request to reset your password. Click the link below to reset.\n\n" . get_bloginfo('url') . '/sr-reset?key=' . $user['forgot'];
		$headers = 'From: no-reply@' . $_SERVER['SERVER_NAME'];

        SEO_RETS_Plugin::sendEmail($to, $subject, $message, $headers);
		
	
		$currentPage->post_title = 'Forgot Password';
		$currentPage->post_content = $this->include_return('methods/includes/forgot/success.php', get_defined_vars());
	} else {
		$currentPage->post_title = 'Forgot Password';
		$currentPage->post_content = $this->include_return('methods/includes/forgot/form.php', get_defined_vars());
	}
} else {
	$currentPage->post_title = 'Forgot Password';
	$currentPage->post_content = $this->include_return('methods/includes/forgot/form.php', get_defined_vars());
}



