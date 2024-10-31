<?php
$sr = $seo_rets_plugin;

if (!defined("DONOTCACHEPAGE")) {//support for WP Super Cache
	define("DONOTCACHEPAGE",true);
}



if ( !$sr->api_key ) return 'null';
if (empty($params) || !array_key_exists('type',$params) || !$sr->is_type_valid($params['type']) ) return 'null';

$type = $params['type'];
unset($params['type']);

$perpage   = isset($params['perpage']) ? ((intval($params['perpage']) == 0 ) ? 10 : intval($params['perpage'])) : 10;
$only      = isset($params['onlymylistings']) && strtolower($params['onlymylistings']) != "no";
$page      = ($wp_query->query_vars['page'] == 0) ? 1 : $wp_query->query_vars['page'];
$order     = isset($params['order']) ? explode(":", $params['order']) : array();
$widgetize = isset($params['widgetize']) && strtolower($params['widgetize']) != "no";
$shuffle = isset($params['shuffle']);

if ( count($order) != 2 ) {
	$order = NULL;
} else {
	$save = $order;
	$order = array(array(
		'field' => $save[0],
		'order' => $save[1]
	));
}

$no_paginate = isset($params['disablepagination']);
$silent = isset($params['silent']);

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


$query = $this->prioritize(array(
	'type'  => $type,
	'order' => $order,
	'query' => array(
		'boolopr'    => 'AND',
		'conditions' => $conditions
	)
), $prioritization);

if ($only && count($prioritization) > 0) {
	array_pop($query);
}

$request = $this->api_request("get_listings", array(
	'query' => $query,
	'limit' => array(
		'range'  => $perpage,
		'offset' => (($page - 1) * $perpage)
	)
));

if ( $shuffle ) shuffle($request->result);

echo json_encode($request);