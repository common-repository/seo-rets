<?php
$sr = $seo_rets_plugin;
//error_reporting(E_ALL);
if (!defined("DONOTCACHEPAGE")) {//support for WP Super Cache
    define("DONOTCACHEPAGE", true);
}


if (!$sr->api_key) return '<p class="sr-error">You must activate the SEO RETS plugin.</p>';
if (!$sr->is_type_valid($params['type'])) return '<p class="sr-error">Shortcode parameter "type" not set or invalid.</p>';

$validation = array_key_exists('object', $params);
if (!$validation) {
    return '<p class="sr-error">Shortcode parameter "object" not set or invalid.</p>';
}

$type = $params['type'];
$object = $params['object'];
unset($params['type']);
unset($params['object']);


$perpage = isset($params['perpage']) ? ((intval($params['perpage']) == 0) ? 10 : intval($params['perpage'])) : 10;
$only = isset($params['onlymylistings']) && strtolower($params['onlymylistings']) != "no";
$page = ($wp_query->query_vars['page'] == 0) ? 1 : $wp_query->query_vars['page'];
$order = isset($params['order']) ? explode(":", $params['order']) : array();
$widgetize = isset($params['widgetize']) && strtolower($params['widgetize']) != "no";

$cond = array(
    'type' => $type,
    'object' => $object,
    'conditions' => $params
);

if ($only && count($prioritization) > 0) {
    array_pop($query);
}

$request = $this->api_request("get_list", $cond);
if ($request->count == 0) {
    echo 'Sorry, but there is no data for your shortcode';
    exit;
}



$sr->recoveringListData($request);

usort($request->result,array("SEO_RETS_Plugin","compareStrings"));

$count = $request->count;

//

include($sr->server_plugin_dir . "/templates/list.php");



