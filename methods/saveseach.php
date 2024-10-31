<?php

if (!$this->api_key) {
    $currentPage->post_title = 'Search Results';
    $currentPage->post_content = 'You must activate the SEO RETS plugin.';
} else {
    $sr = $seo_rets_plugin;

    $page_name = "User Favorites";
    if ($this->template_settings['type'] == "all") {
        wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['all-value'])), "post_meta");
    } else {
        wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['every-values'][$page_name])), "post_meta");
    }

    $users = get_option('sr_users');
    $index = $this->get_session_data('user_index');

    function sr_save_search($index, $add, $table)
    {
        date_default_timezone_set("US/Central");
        $saveSearch = array(
            'id' => '',
            'user_id' => $index,
            'base64link' => empty($add) ? ' ' : $add,
            'add_times' => date("Y-m-d H:i:s")
        );
        SEO_RETS_Plugin::wpdbInsertRow($table, $saveSearch);
    }

    if (isset($_GET['add'])) {
        if ($index !== false) {
            sr_save_search($index, $_GET['add'], $this->wpdb_sr_savesearch);
            header("Location: " . get_bloginfo('url') . "/sr-search-fav");
            exit;
        } elseif (!$this->new_session) {//Only add later if we are sure that they support cookies
            $this->set_session_data('add_later', $_GET['add']);
        }
    }
    if ($index !== false) {

//        $user = $users[$index];
        $sql = "SELECT * FROM $this->wpdb_sr_users  WHERE id =" . $index;
        $users = SEO_RETS_Plugin::wpdbSelectResults($sql);
        $user = json_decode(json_encode($users), True);


        if (isset($_GET['remove'])) {
//            update_option('sr_users', $users);
            $del = array(
                'id' => $_GET['remove']
            );
            SEO_RETS_Plugin::wpdbDeleteRow($this->wpdb_sr_savesearch, $del);
            header("Location: " . get_bloginfo('url') . "/sr-search-fav");
            exit;
        }

        $currentPage->post_title = $user[0]['u_name'] . '\'s Saved Search';
        $currentPage->post_content = $this->include_return('methods/includes/savesearch/display.php', get_defined_vars());

    } else {
        header("Location: " . get_bloginfo('url') . "/sr-login");
        exit;
    }

}
