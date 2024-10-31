<?php
$sr = $seo_rets_plugin;

if ( !defined("DONOTCACHEPAGE") ) {//support for WP Super Cache
	define("DONOTCACHEPAGE", true);
}

if ( !$sr->api_key ) return '<p class="sr-error">You must activate the SEO RETS plugin.</p>';
if ( !$sr->is_type_valid($params['type']) ) return '<p class="sr-error">Shortcode parameter "type" not set or invalid.</p>';

$city = $params['city'];
$info = $params['info'];


$info_data['state'];
$info_data['zip_code'];
$info_data['average_listing_price'];
$info_data['median_sales_price'];
$info_data['most_expensive_home'];
$info_data['average_price_sqft'];

echo $info_data[$info];