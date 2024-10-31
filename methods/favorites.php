<?php

if (!$this->api_key) {
    $currentPage->post_title = 'Search Results';
    $currentPage->post_content = 'You must activate the SEO RETS plugin.';
} else {
    $page_name = "User Favorites";
    if ($this->template_settings['type'] == "all") {
        wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['all-value'])), "post_meta");
    } else {
        wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['every-values'][$page_name])), "post_meta");
    }
    $users = get_option('sr_users');
    $sql = "SELECT * FROM $this->wpdb_sr_users";
    $res = SEO_RETS_Plugin::wpdbSelectResults($sql);
    $index = $this->get_session_data('user_index');
    function sr_add_favorite($users, $index, $table)
    {
        $parts = explode(",", $_GET['add'], 2);
        $sql = "SELECT * FROM $table WHERE user_id =" . $index;
        $users = SEO_RETS_Plugin::wpdbSelectResults($sql);
        $user = json_decode(json_encode($users), True);

        foreach ($user as $favorite) {
            if ($favorite['mls'] == $parts[0]) return false;
        }
        date_default_timezone_set("US/Central");
        $saveFav = array(
            'id' => '',
            'user_id' => $index,
            'mls' => empty($parts[0]) ? ' ' : $parts[0],
            'mtype' => empty($parts[1]) ? ' ' : $parts[1],
            'last_views' => date("Y-m-d H:i:s"),
            'views' => 1,
            'add_times' => date("Y-m-d H:i:s")
        );
        SEO_RETS_Plugin::wpdbInsertRow($table, $saveFav);
    }

    if (isset($_GET['add'])) {
        if ($index !== false) {
            $parts = explode(",", $_GET['add'], 2);

            $request = $this->api_request('get_listings', array(
                'type' => $parts[1],
                'query' => array(
                    'boolopr' => 'AND',
                    'conditions' => array(
                        array(
                            'field' => 'mls_id',
                            'operator' => '=',
                            'value' => $parts[0]
                        )
                    )
                ),
                'limit' => array(
                    'range' => 1,
                    'offset' => 0
                )
            ));
            $url = $this->listing_to_url($request->result[0], $parts[1]);
            SEO_RETS_Plugin::lead_alert("{$users[$index]['name']} <{$users[$index]['email']}> listing: " . site_url() . $url, "Favorites");
            sr_add_favorite($users, $index, $this->wpdb_sr_favorites);

            $sql_getListings = "SELECT * FROM $this->wpdb_sr_stat_mls WHERE mls=" . $parts[0];
            $res_getListings = $this->wpdbSelectResults($sql_getListings);

            $ar = $res_getListings[0];
            $statOptAdd = array(
                'id' => '',
                'stat_id' => $ar->id,
                'mtype' => 'Fav',
                'time' => date("Y-m-d H:i:s")
            );
            $this->wpdbInsertRow($this->wpdb_sr_stat_option, $statOptAdd);
            header("Location: " . get_bloginfo('url') . "/sr-favorites");
            exit;
        } elseif (!$this->new_session) {//Only add later if we are sure that they support cookies
            $this->set_session_data('add_later', $_GET['add']);
        }
    }
    if ($index !== false) {
        $sql = "SELECT * FROM $this->wpdb_sr_users WHERE id =" . $index;
        $users = SEO_RETS_Plugin::wpdbSelectResults($sql);
        $user = json_decode(json_encode($users), True);

        if (isset($_GET['remove'])) {
            $del = array(
                'id' => $_GET['remove']
            );
            SEO_RETS_Plugin::wpdbDeleteRow($this->wpdb_sr_favorites, $del);
            header("Location: " . get_bloginfo('url') . "/sr-favorites");
            exit;
        }
        $currentPage->post_title = $user[0]['u_name'] . '\'s Favorites';
        $currentPage->post_content = $this->include_return('methods/includes/favorites/display.php', get_defined_vars());
    } else {
        header("Location: " . get_bloginfo('url') . "/sr-login");
        exit;
    }

}
