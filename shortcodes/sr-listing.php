<?php
$sr = $seo_rets_plugin;

if (!defined("DONOTCACHEPAGE")) {//support for WP Super Cache
	define("DONOTCACHEPAGE",true);
}

if ( !$sr->api_key ) return '<p class="sr-error">You must activate the SEO RETS plugin before using shortcodes.</p>';
if ( !$sr->is_type_valid($params['type']) ) return '<p class="sr-error">Shortcode parameter "type" not set or invalid.</p>';

$type = $params['type'];

$params = $sr->filter_params($params, $type);
$conditions = $sr->build_conditions($params);


if ( $conditions ) {
	$query = array(
		'boolopr' => 'AND',
		'conditions' => $conditions
	);
} else {
	$query = NULL;
}

$request = $sr->api_request('get_listings', array(
	'type' => $type,
	'query' => $query,
	'limit' => array(
		'range' => 1,
		'offset' => 0
	)
));

if ( count($request->result) > 0 ):
	$server_name = $this->feed->server_name;
	$match = array();
	if (preg_match("/^([a-zA-Z]+)\\.([a-zA-Z]+)$/", $type, $match)) {
		$server_name = $match[1];
	}
	$listings = $request->result;
	$listing = $listings[0];
	$photo_dir = 'http://img.seorets.com/' . $server_name;
	$map_settings = get_option('sr-map');

	$map_width = isset($map_settings['width']) ? $map_settings['width'] : 600;
	$map_height = isset($map_settings['height']) ? $map_settings['height'] : 350;

	include($sr->server_plugin_dir . "/templates/listing.php");
	 else: ?>
     <?php do_action('seo_rets_unfound_page', 'sr-listing',$params);?>
<?php endif; ?>
