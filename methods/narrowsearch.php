<?php
$sr = $this;

if (!$sr->api_key) {
    $currentPage->post_title = 'Narrow Your Search';
    $currentPage->post_content = 'You must activate the SEO RETS plugin.';
} else {

    $page_name = "Search";
    if ($this->template_settings['type'] == "all") {
        wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['all-value'])), "post_meta");
    } else {
        wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['every-values'][$page_name])), "post_meta");
    }

    $currentPage->post_title = 'Narrow Your Search';
    $currentPage->post_content = '';


    // Figure out if this is a legacy form processor request or the new version
    $get_vars = $this->parse_url_to_vars();
    if ($get_vars != NULL) { // We can say that the only required variable to be set is conditions in new request format, so we'll assume that's what this request is

        if (is_array($get_vars->q->c)) {
            $get_vars->p = isset($get_vars->p) ? intval($get_vars->p) : 10; // Default to 10 per page if request doesn't specify
            $get_vars->g = isset($get_vars->g) ? intval($get_vars->g) : 1;
            $conditions = $this->convert_to_api_conditions($get_vars->q);

//            echo "<pre>";
//            print_r($conditions);
//            echo "</pre>";

            $prioritization = get_option('sr_prioritization');
            $prioritization = ($prioritization === false) ? array() : $prioritization;

            $query = array(
                "type" => $get_vars->t,
                "query" => $conditions,
            );

            if (isset($get_vars->o) && is_array($get_vars->o)) {
                $query["order"] = array();

                foreach ($get_vars->o as $order) {
                    $query["order"][] = array(
                        "field" => $order->f,
                        "order" => $order->o == 0 ? "DESC" : "ASC"
                    );
                }
            }

            $newquery = $this->prioritize($query, $prioritization);
            $response = $this->api_request("get_listings", array(
                'query' => $newquery,
                'limit' => array(
                    'range' => 100,
                    'offset' => ($get_vars->g - 1) * 100
                )
            ));
//            echo "<pre>";
//            print_r($response);
//            echo "</pre>";
            $listings = $response->result;
            foreach ($listings as $key => $l) {
                foreach ($l->features as $f) {
                    if (!($f == "Yes" || $f == "No" || $f == "None")) {
                        $features_list[$f] = $f;
                    }
                }
            }
            asort($features_list);

            $listing_html = $this->include_return('templates/narrow.php', get_defined_vars());
            $currentPage->post_content .= $listing_html;


        }
    }
}