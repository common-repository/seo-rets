<?php
$sr = $seo_rets_plugin;

if (!$sr->api_key) return '';

$mls = $sr->get_session_data('listings');
if (!is_array($mls)) {
	$mls = array();
}

if (!defined("DONOTCACHEPAGE")) {//support for WP Super Cache
	define("DONOTCACHEPAGE",true);
}

$mls = array_slice(array_reverse($mls), 0, 10, true);//get up to the last 10 listings and preserve order
$listings = $sr->mlsid_cache($mls);//get the listings from the cache or fetch if needed

foreach ($mls as $listing) {
	$key = serialize($listing);
	$result = $listings[$key];
	echo $result->address.", ".$result->city.", ".$sr->long_state($result->state).": ".get_bloginfo('wpurl').$sr->listing_to_url($result, $listing['type'])."\n";
} 
