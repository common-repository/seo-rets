<?php

add_action('wp_head', array($this, 'put_meta_info'), 0); 

//print_r($wp_query->query);

$city = $wp_query->query['sr_city'];

$currentPage->post_content = $city;
$currentPage->post_title = "City Page";

