<?php
$sr = $this;
if (!$sr->api_key) {
    $currentPage->post_title = 'Search Results';
    $currentPage->post_content = 'You must activate the SEO RETS plugin.';
} else {
    $page_name = "Search";
    if ($this->template_settings['type'] == "all") {
        wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['all-value'])), "post_meta");
    } else {
        wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['every-values'][$page_name])), "post_meta");
    }
    $currentPage->post_title = 'Search Results';
    $currentPage->post_content = '';
    // Figure out if this is a legacy form processor request or the new version
    $get_vars = $this->parse_url_to_vars();
    if ($get_vars != NULL) { // We can say that the only required variable to be set is conditions in new request format, so we'll assume that's what this request is
        if (is_array($get_vars->q->c)) {
            $get_vars->p = isset($get_vars->p) ? intval($get_vars->p) : 10; // Default to 10 per page if request doesn't specify
            $get_vars->g = isset($get_vars->g) ? intval($get_vars->g) : 1;
            $conditions = $this->convert_to_api_conditions($get_vars->q);
            $prioritization = get_option('sr_prioritization');
            $prioritization = ($prioritization === false) ? array() : $prioritization;

            $query = array(
                "type" => $get_vars->t,
                "query" => $conditions
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
            $srfq = get_option('sr_fq');
            if ($srfq != "false") {
                $fields = array(
                    "mls_id" => true,
                    "address" => true,
                    "seo_url" => true,
                    "photos" => true,
                    "city" => true,
                    "office_name" => true,
                    "sqft" => true,
                    "ppsqft" => true,
                    "lot_sqft" => true,
                    "bedrooms" => true,
                    "baths_full" => true,
                    "baths" => true,
                    "price" => true,
                    "state" => true,
                    "zip" => true,
                    "coll_name" => true
                );
                $response = $this->api_request("get_listings", array(
                    'query' => $newquery,
                    'fields' => $fields,
                    'limit' => array(
                        'range' => $get_vars->p,
                        'offset' => ($get_vars->g - 1) * $get_vars->p
                    )
                ));
            } else {
                $response = $this->api_request("get_listings", array(
                    'query' => $newquery,
                    'limit' => array(
                        'range' => $get_vars->p,
                        'offset' => ($get_vars->g - 1) * $get_vars->p
                    )
                ));
            }
            $listings = $response->result;
            $type = $get_vars->t;
            $listing_html = $this->include_return('templates/results.php', get_defined_vars());
            if ($this->ucp == "true") {
                $pagination_html = $this->pagination_text_html($get_vars, $get_vars->g, ceil($response->count / $get_vars->p), $response->count);
            } else {
                $pagination_html = $this->pagination_html($get_vars, $get_vars->g, ceil($response->count / $get_vars->p), $response->count);
            }
            $currentPage->post_content .= do_shortcode($this->include_return("templates/refinementform.php", array('query' => $get_vars)));
            $currentPage->post_content .= $pagination_html . $listing_html . $pagination_html;
//            
        } else {
            $currentPage->post_content .= 'Error: Invalid Request';
        }
    } else {
        if (isset($_GET['conditions'])) {
            $conditions = array();
            foreach ($_GET['conditions'] as $condition) {
                if (isset($condition['value']) && $condition['value'] != "") {
                    if (is_array($condition['value'])) {
                        $newcondition['boolopr'] = "OR";
                        $newcondition['conditions'] = array();
                        foreach ($condition['value'] as $value) {
                            if ($value == "") {
                                continue;
                            }
                            $split = explode(",", $value);
                            if (count($split) > 1 && $condition['operator'] == "LIKE") {
                                foreach ($split as $v) {
                                    $subcondition = array('field' => $condition['field'], 'operator' => $condition['operator'], 'value' => $v);
                                    if (isset($condition['loose'])) {
                                        $subcondition['loose'] = $condition['loose'];
                                    }
                                    $newcondition['conditions'][] = $subcondition;
                                }
                            } else {
                                $subcondition = array('field' => $condition['field'], 'operator' => $condition['operator'], 'value' => $value);
                                if (isset($condition['loose'])) {
                                    $subcondition['loose'] = $condition['loose'];
                                }
                                $newcondition['conditions'][] = $subcondition;
                            }
                        }
                        if (count($newcondition['conditions']) > 0) {
                            $conditions[] = $newcondition;
                        }
                    } else {
                        $conditions[] = $condition;
                    }
                }
            }
            $_GET['perpage'] = isset($_GET['perpage']) ? $_GET['perpage'] : 10;
            $new_request = array(
                'q' => $sr->convert_to_search_conditions(array(
                    'boolopr' => '1',
                    'conditions' => $conditions
                )),
                't' => $_GET['type'],
                'p' => (int)$_GET['perpage']
            );
            if (isset($_GET['order_wp_sux'])) {
                $new_request['o'] = array();
                foreach (array_values($_GET['order_wp_sux']) as $a_order) $new_request['o'][] = array('f' => $a_order['field'], 'o' => (strtolower($a_order['order']) == "desc") ? 0 : 1);
            }
            $new_request['q']['b'] = 1;
            $new_request_json = json_encode($new_request);
            header("Location: " . home_url() . "/sr-search?" . urlencode(base64_encode($new_request_json)));
            exit;
        } else {
            $currentPage->post_content = 'It looks like there is a problem with your permalink structure. Please set to "/%postname%".';
            $currentPage->post_title = 'Error';
        }
    }
}
