<?php
//update_option('sr_users', false);
if (!$this->api_key) {
    $currentPage->post_title = 'Search Results';
    $currentPage->post_content = 'You must activate the SEO RETS plugin.';
} else {
    global $seo_rets_plugin;
    $sr = $seo_rets_plugin;

    $page_name = "User Signup";
    if ($this->template_settings['type'] == "all") {
        wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['all-value'])), "post_meta");
    } else {
        wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['every-values'][$page_name])), "post_meta");
    }


    $index = $this->get_session_data('user_index');

    if ($index) {
        header("Location: " . get_bloginfo('url') . "/sr-favorites");
        exit;
    }

    $users = get_option("sr_users");

    function check_email_address($email)
    {

        if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) return false;

        $email_array = explode("@", $email);
        $local_array = explode(".", $email_array[0]);

        for ($i = 0; $i < sizeof($local_array); $i++) {
            if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) return false;
        }

        if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
            $domain_array = explode(".", $email_array[1]);

            if (sizeof($domain_array) < 2) return false;

            for ($i = 0; $i < sizeof($domain_array); $i++) {
                if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) return false;
            }
        }

        return true;
    }

    function sr_email_exists($email, $users)
    {

        if ($users) {
            foreach ($users as $user) {
                if ($email === $user['email']) return true;
            }
        }

        return false;
    }

    function sr_is_unique_key($users, $key)
    {

        if ($users) {
            foreach ($users as $user) {
                if ($key == $user['verify'] || $key == $user['salt'] || $key == $user['forgot']) return false;
            }
        }

        return true;
    }

    function sr_rand($users)
    {

        do {
            $salt = '';

            for ($n = 0; $n < 20; $n++) {
                $salt .= chr(rand(65, 90));
            }

        } while (!sr_is_unique_key($users, $salt));

        return $salt;
    }


    if ($this->get_session_data('user_key')) {
        header("Location: " . get_bloginfo('url') . "/sr-login");
        exit;
    }

    if ($_POST['submit']) {

        $errors = array();

        if (!check_email_address($_POST['email'])) {
            $errors[] = "Invalid email address";
        }

        if ($_POST['password'] !== $_POST['password-again']) {
            $errors[] = "Passwords do not match";
        }

        if (strlen($_POST['password']) < 5) {
            $errors[] = "Password must be at least 5 characters long";
        }

        if (!$_POST['full-name']) {
            $errors[] = "You must enter a name";
        }

        if (strlen($_POST['full-name']) > 255) {
            $errors[] = "Name too long";
        }

        if (sr_email_exists($_POST['email'], $users)) {
            $errors[] = "That email address already exists";
        }

        if (strlen($_POST['email']) > 255) {
            $errors[] = "Email too long";
        }

        if (strlen($_POST['password']) > 255) {
            $errors[] = "Password too long";
        }

        if (count($errors) == 0) {
            global $wpdb;

            $salt = sr_rand($users);
            $verify = sr_rand($users);
            date_default_timezone_set("US/Central");

            $users = array(
                'name' => $_POST['full-name'],
                'email' => $_POST['email'],
                'password' => md5($salt . $_POST['password']),
                'salt' => $salt,
                'verify' => $verify,
                'favorites' => array(),
                'last_login' => date("Y-m-d H:i:s")
            );

            $users2 = array(
                'id' => '',
                'u_name' => empty($_POST['full-name']) ? ' ' : $_POST['full-name'],
                'u_email' => empty($_POST['email']) ? ' ' : $_POST['email'],
                'u_phone' => ' ',
                'u_city' => ' ',
                'full_name' => empty($_POST['full-name']) ? ' ' : $_POST['full-name'],
                'sign_up' => date("Y-m-d H:i:s"),
                'password' => md5($salt . $_POST['password']),
                'salt' => $salt,
                'verify' => $verify
            );


//            update_option('sr_users', $users);
            $vewr = SEO_RETS_Plugin::wpdbInsertRow($this->wpdb_sr_users, $users2);


            $to = $_POST['email'];
            $subject = 'Verfiy Your New Account on ' . $_SERVER['SERVER_NAME'];
            $message = 'Hello ' . $_POST['full-name'] . ",\n\nWe've created your account on " . get_bloginfo('url') . "/. Before you can login you must verify your account by clicking the link below.\n\n" . get_bloginfo('url') . '/sr-verify?key=' . $verify;
            $headers = 'From: no-reply@' . $_SERVER['SERVER_NAME'];

            SEO_RETS_Plugin::sendEmail($to, $subject, $message, $headers);


            $currentPage->post_title = 'Sign Up';
            $currentPage->post_content = $this->include_return('methods/includes/signup/complete.php', get_defined_vars());
        } else {
            $currentPage->post_title = 'Sign Up';
            $currentPage->post_content = $this->include_return('methods/includes/signup/form.php', get_defined_vars());
        }
    } else {
        $currentPage->post_title = 'Sign Up';
        $currentPage->post_content = $this->include_return('methods/includes/signup/form.php', get_defined_vars());
    }

}