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


if (count($mls) > 0) {
	$last = array_pop($mls);
	$results = $sr->mlsid_cache(array($last));
	if (count($results) > 0) {
		$r = $results[serialize($last)];
		echo $r->address.", ".$r->city.", ".$sr->long_state($r->state)."\n";
	} else {
		echo "Property not found";
	}
} else {
	echo "No property found";
}
