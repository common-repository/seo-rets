<?php
$sr = $seo_rets_plugin;

if (!defined("DONOTCACHEPAGE")) {//support for WP Super Cache
	define("DONOTCACHEPAGE",true);
}

if ( !$sr->api_key ) return '<p class="sr-error">You must activate the SEO RETS plugin before using shortcodes.</p>';
if ( !$sr->is_type_valid($params['type']) ) return '<p class="sr-error">Shortcode parameter "type" not set or invalid.</p>';

$type = $params['type'];
unset($params['type']);
$limit = isset($params['limit']) ? ((intval($params['limit']) == 0 ) ? 3 : intval($params['limit'])) : 3;
$silent = isset($params['silent']) && strtolower($params['silent']) != "no";
$only = isset($params['onlymylistings']) && strtolower($params['onlymylistings']) != "no";
$order = isset($params['order']) ? explode(":", $params['order']) : array();
$widgetize = isset($params['widgetize']) && strtolower($params['widgetize']) != "no";

if ( !isset($order) || count($order) != 2 ) {
	$order = NULL;
} else {
	$save = $order;
	$order = array(array(
		'field' => $save[0],
		'order' => $save[1]
	));
}

$params = $sr->filter_params($params, $type);

$conditions = $sr->build_conditions($params);

if ( !is_array($conditions) ) {
	$conditions = array();
}

$query = array(
	'boolopr' => 'AND',
	'conditions' => $conditions
);

$prioritization = get_option('sr_prioritization');
$prioritization = ($prioritization === false) ? array() : $prioritization;


$query = $sr->prioritize(array(
	'type' => $type,
	'order' => $order,
	'query' => array(
		'boolopr' => 'AND',
		'conditions' => $conditions
	)
), $prioritization);

if ( $only && count($prioritization) > 0 ) {
	array_pop($query);
}

$count = $sr->api_request('get_listings', array(
	'query' => $query,
	'count' => true
));

$listings = array();

if ($count->count > 0) {
	if ( $limit >= $count->count ) {
		$offset = 0;
	} else {
		$max = $count->count - $limit;
		$offset = rand(0, $max);
	}

	$request = $sr->api_request("get_listings", array(
		'query' => $query,
		'limit' => array(
			'range' => $limit,
			'offset' => $offset
		)
	));

	$listings = $request->result;
}

include($sr->server_plugin_dir . "/templates/results.php");
