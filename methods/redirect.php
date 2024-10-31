<?php



// Use the $_GET variables sr_mls, and sr_type to make an api request to get extra data needed for the redirect
// You can use $this->api_request
// Construct new url from that data and send a header redirecting them


$request = $this->api_request("get_listings", array(
	'type' => $wp_query->query['sr_type'],
	'query' => array(
		'boolopr' => 'AND', 
		'conditions' => array(
			array (
				'field' => 'mls_id',
				'operator' => '=',
				'value' => $wp_query->query['sr_mls']
			)
		)
	),
	'fields' => array('address', 'city', 'state', 'zip'),
	'limit' => array('range' => 1)
));

if (isset($request->result[0])) {
	$request->result[0]->mls_id = $wp_query->query['sr_mls'];
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: " . $this->listing_to_url($request->result[0], $wp_query->query['sr_type']));
}

exit; // Keep wordpress from rendering page
