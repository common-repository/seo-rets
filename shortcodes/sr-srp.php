<?php

$sr = $seo_rets_plugin;

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
	$listings = $request->result;
	$listing = $listings[0];
	
	
	return do_shortcode("[srp_profile lat='{$listing->lat}' lng='{$listing->lng}' address='{$listing->address}' city='{$listing->city}' state='{$listing->state}' zip_code='{$listing->zip}']");
	
else: ?>
<?php do_action('seo_rets_unfound_page', 'sr-srp',$params);?>
<?php endif; ?>
