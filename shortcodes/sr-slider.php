<?php
$sr = $seo_rets_plugin;

if ( !defined("DONOTCACHEPAGE") ) define("DONOTCACHEPAGE", true);
if ( !$sr->api_key ) return '<p class="sr-error">You must activate the SEO RETS plugin.</p>';
if ( !$sr->is_type_valid($params['type']) ) return '<p class="sr-error">Shortcode parameter "type" not set or invalid.</p>';

$type = $params['type'];
unset($params['type']);

$perpage = isset($params['perpage']) ? ((intval($params['perpage']) == 0 ) ? 10 : intval($params['perpage'])) : 10;
$only    = isset($params['onlymylistings']) && strtolower($params['onlymylistings']) != "no";
$order   = isset($params['order']) ? explode(":", $params['order']) : array();

if ( count($order) != 2 ) {
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


$qcc = $query;

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

$result = $this->api_request("get_listings", array(
	'query' => $query,
	'limit' => array(
		'range'  => $perpage
	)
));

wp_enqueue_style('sr_shortcodes_slider',$this->css_resources_dir.'shortcodes/slider.css');
wp_print_styles(array('sr_shortcodes_slider'));
?>



<script type="text/javascript">
function addCommas(nStr) {
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

function url_segment(seg) {
	return seg.toLowerCase().replace(/ /g, "-");
}


jQuery(function($) {
	
	var ls = <?php echo json_encode($result) ?>;
	var current_listing = 0;

	var preload = function() {
		for ( var i in ls.result ) (new Image()).src = "http://img.seorets.com/<?php echo $seo_rets_plugin->feed->server_name?>/" + ls.result[i].seo_url + "-" + ls.result[i].mls_id + "-1.jpg";
	};

	var change_slide = function() {
		var listing = ls.result[current_listing];
		
		$("#sr-slide-image-back").attr("src", "http://img.seorets.com/<?php echo $seo_rets_plugin->feed->server_name?>/" + listing.seo_url + "-" + listing.mls_id + "-1.jpg");

		$("#sr-slide-image").fadeOut("slow", function() {
			$("#sr-slide-info-city").html(listing.city + ", " + listing.state);
			$("#sr-slide-info-bb").html("Bedrooms: " + listing.bedrooms + " &bull; Bathrooms: " + listing.baths);
			$("#sr-slide-info-price").html("$" + addCommas(listing.price) + ".00");
			$("#sr-slide-info-description").html(listing.remarks.substr(0, 110) + "...");
			$("#sr-slide-info-readmore").attr("href", "/" + listing.seo_url + "/" + url_segment(listing.city) + "/" + url_segment(listing.state) + "/" + listing.zip + "/" + listing.mls_id + "/res");
			$(this).attr("src", "http://img.seorets.com/<?php echo $seo_rets_plugin->feed->server_name?>/" + listing.seo_url + "-" + listing.mls_id + "-1.jpg").fadeIn("slow");
		});
		
		current_listing++;
		if ( current_listing == ls.result.length ) current_listing = 0;

	};
	var slide_image = $("#sr-slide-image");
	preload();
	var interval_id = setInterval(change_slide, 8000);
	change_slide();
});
</script>


<div id="sr-slider">
	<img src="<?php bloginfo('template_url') ?>/images/sample-slide.png" id="sr-slide-image" />
	<img src="<?php bloginfo('template_url') ?>/images/sample-slide.png" id="sr-slide-image-back" />

	<div id="sr-slide-info">
		<a href="#" class="button" id="sr-slide-info-readmore">Read More &gt;&gt;</a>
		<h2 id="sr-slide-info-city"></h2>
		<p id="sr-slide-info-bb"></p>
		<h3 id="sr-slide-info-price"></h3>
		<!--<p id="sr-slide-info-description"></p>-->
		
	</div>
</div>