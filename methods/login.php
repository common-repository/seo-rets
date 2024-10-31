<?php
if (!$this->api_key) {
    $currentPage->post_title = 'Search Results';
    $currentPage->post_content = 'You must activate the SEO RETS plugin.';
} else {
    $sr = $seo_rets_plugin;

    $page_name = "User Login";
    if ($this->template_settings['type'] == "all") {
        wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['all-value'])), "post_meta");
    } else {
        wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['every-values'][$page_name])), "post_meta");
    }
    function sr_login($users)
    {
        if ($users) {
            foreach ($users as $index => $user) {
                $password = md5($user['salt'] . $_POST['password']);
                $user['index'] = $index;
                if ($_POST['email'] === $user['u_email'] && $user['password'] === $password /*&& !isset($user['verify']) FIXME */) return $user;
            }
        }
        return false;
    }

    $user_index = $this->get_session_data('user_index');
    if ($user_index !== false) {

        header("Location: " . get_bloginfo('url') . "/sr-user");

        exit;
    } else {
        if (isset($_POST['submit'])) {
            $errors = array();
//            $users = get_option('sr_users');
            $sql = "SELECT * FROM  $this->wpdb_sr_users";
            $res = SEO_RETS_Plugin::wpdbSelectResults($sql);
            $array = json_decode(json_encode($res), True);
//            echo "<pre>";
//            print_r($array);
//            echo "</pre>";
            $user = sr_login($array);
            if (!$user) {
                $errors[] = 'Wrong username or password';
            }
            if (count($errors) == 0) {
                date_default_timezone_set("US/Central");
                $this->set_session_data('user_index', $user['id']);
                $index = $this->get_session_data('user_index');
                $add = $this->get_session_data('add_later');
//                $field = array(
//                    'last_login' => date("Y-m-d H:i:s")
//                );
//                $where = array(
//                    'id' => $index
//                );
//                $srb = SEO_RETS_Plugin::wpdbUpdateRow('wp_sr_users', $field, $where);
//                echo $srb;
//                $users[$index]['last_login'] = date("Y-m-d H:i:s");
//                update_option('sr_users', $users);
                if ($add) {
                    $this->set_session_data('add_later', false);
                    header("Location: " . get_bloginfo('url') . "/sr-favorites?add=" . $add);
                } else {
                    header("Location: " . get_bloginfo('url') . "/sr-favorites");
                }
                exit;
            } else {
                $currentPage->post_title = 'Login';
                $currentPage->post_content = $this->include_return('methods/includes/login/form.php', get_defined_vars());
            }
        } else {
            $currentPage->post_title = 'Login';
            $currentPage->post_content = $this->include_return('methods/includes/login/form.php', get_defined_vars());
        }
    }

}