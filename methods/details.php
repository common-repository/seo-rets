<?php
$type = $wp_query->query['sr_type'];


$tmp = $wp_query->query['sr_tmp'];
$tmpdtval = array('community', 'overview', 'features', 'map', 'video');

$request = $this->api_request("get_listings", array(
    'type' => $type,
    'query' => array(
        'boolopr' => 'AND',
        'conditions' => array(
            array(
                'field' => 'mls_id',
                'operator' => '=',
                'value' => $wp_query->query['sr_mls']
            )
        )
    )
));

if (!$tmp) {
    $extraFieldsTemplate = get_option('sr_templates_extra');
    if ($extraFieldsTemplate['show_related_properties'] == 'true') {
        if (!empty($request->result[0]->subdivision)) {
            $conditions[] = array(
                'field' => 'subdivision',
                'operator' => 'LIKE',
                'value' => $request->result[0]->subdivision,
                'loose' => '1'
            );
            $conditions[] = array(
                'field' => 'mls_id',
                'operator' => '<>',
                'value' => $request->result[0]->mls_id
            );
        }
        if (!empty($request->result[0]->price)) {
            $price10part = $request->result[0]->price / 100 * 10;
            $conditions[] = array(
                'field' => 'price',
                'operator' => '>=',
                'loose' => '1',
                'value' => $request->result[0]->price - $price10part
            );
            $conditions[] = array(
                'field' => 'price',
                'operator' => '<=',
                'loose' => '1',
                'value' => $request->result[0]->price + $price10part
            );
        }
        if ($extraFieldsTemplate['rp_zipcode'] == 'true' && !empty($request->result[0]->zip)) {
            $conditions[] = array(
                'field' => 'zip',
                'operator' => '=',
                'value' => $request->result[0]->zip
            );
        }
        if ($extraFieldsTemplate['rp_bedrooms'] == 'true' && !empty($request->result[0]->bedrooms)) {
            $conditions[] = array(
                'field' => 'bedrooms',
                'operator' => '>=',
                'value' => $request->result[0]->bedrooms
            );
        }

        if (!empty($conditions)) {
            $addRequest = $this->api_request("get_listings", array(
                'type' => $type,
                'query' => array(
                    'boolopr' => 'AND',
                    'conditions' => $conditions
                ),
                'limit' => array(
                    'range' => 3,
                    'offset' => 0
                )
            ));
        }
    }
}

$server_name = $this->feed->server_name;
$match = array();
if (preg_match("/^([a-zA-Z]+)\\.([a-zA-Z]+)$/", $type, $match)) {
    $server_name = $match[1];
}

if (count($request->result) !== 0) {


    add_action('wp_head', array($this, 'put_meta_info'), 0);

    $page_name = "Listing Details";

    if ($this->template_settings['type'] == "all") {
        wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['all-value'])), "post_meta");
    } else {
        wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['every-values'][$page_name])), "post_meta");
    }


    $listing = $request->result[0];
    $this->detail_result = $listing;
    $photo_dir = "http://img.seorets.com/" . $server_name;
	$listings = $this->get_session_data('listings');
    $listings = $listings ? $listings : array();
    $listings[] = array(
        'mls' => $listing->mls_id,
        'type' => $type
    );

    while (count($listings) > 10) {
        array_shift($listings);
    }

    $this->set_session_data('listings', $listings);

    ob_start();
    eval("?>{$this->feed->disclaimer}");
    $disclaimer = ob_get_clean();


    if ($this->feed->powered_by == null) {
        $powered_by = 'Powered By <a href="http://seorets.com/" target="_blank">SEO RETS</a>';
    } elseif ($this->feed->powered_by !== '') {
        $powered_by = $this->feed->powered_by;
        $powered_by_link = $this->feed->powered_by_link;
        $powered_by = 'Powered by <a href="' . $this->feed->powered_by_link . '">' . $this->feed->powered_by . '</a>';
    } else {
        $powered_by = 'Powered By <a href="http://seorets.com/" target="_blank">SEO RETS</a>';
    }

    $sr = $this;
    $map_settings = get_option('sr-map');
    $map_width = isset($map_settings['width']) ? $map_settings['width'] : 600;
    $map_height = isset($map_settings['height']) ? $map_settings['height'] : 350;
    $currentPage->post_content = $this->include_return('templates/listing.php', get_defined_vars()) . "<br />" . $disclaimer . '<br/>' . $powered_by;
    $seodata = get_option('sr_seodata');
    $title = isset($seodata['title']) ? $seodata['title'] : $this->seo_defaults['title'];
    $currentPage->post_title = $this->parse_seo_data($title);
//    $currentPage->post_meta = 'asd';
    $this->popup_options = get_option('sr_popup');


    if (isset($this->popup_options['status']) && $this->popup_options['status'] == "enabled" && (count($listings) >= intval($this->popup_options['num'])) && !$this->get_session_data('registered')) {
        wp_enqueue_style('sr-contact');
        add_action('wp_head', array($this, 'show_popup'));
    }

    $currentPage->guid = get_bloginfo('url') . $sr->listing_to_url($listing, $type);
//    echo "<pre>";
//    print_r($currentPage);
//    echo "</pre>";
} else {


    add_action('wp', array($this, 'put_404_header'), 0);

    add_action('wp_head', array($this, 'put_meta_info'), 0);

    $page_name = "Listing Details";

    if ($this->template_settings['type'] == "all") {
        wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['all-value'])), "post_meta");
    } else {
        wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['every-values'][$page_name])), "post_meta");
    }
    $currentPage->post_content = "<p>The Listing you requested does not exist or was removed. Please go back and try again, or use the form below to search for the listing you were looking for.</p>" . do_shortcode("[sr-search]");
    $currentPage->post_title = "Listing not found";
}
