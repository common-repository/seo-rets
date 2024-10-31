<?php
global $seo_rets_plugin;
$sr = $seo_rets_plugin;
$params = json_decode(base64_decode($_GET['params']), true);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

    <title>SEO RETS Map</title>


    <?php
    wp_enqueue_style('sr_method_map', $this->css_resources_dir . 'methods/map.css');
    wp_print_styles(array('sr_method_map'));

    wp_enqueue_script('sr_method_google-map', $this->js_resources_dir . 'google-map.js', array('jquery'));
    wp_enqueue_script('sr_method_map', $this->js_resources_dir . '/methods/map.js');
    wp_print_scripts(array('sr_method_google-map'));
    wp_print_scripts(array('sr_method_map'));
    ?>


    <!--	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>-->
</head>

<body onload="initialize()">
<?php
if (!$sr->api_key) return '<p class="sr-error">You must activate the SEO RETS plugin before using shortcodes.</p>';
if (!$sr->is_type_valid($params['type'])) return '<p class="sr-error">Shortcode parameter "type" not set or invalid.</p>';

$type = $params['type'];
$us_bounds = isset($params['bounds']) ? explode(",", $params['bounds']) : NULL;
$us_bounds = (($us_bounds == null) || (count($us_bounds) < 4)) ? NULL : $us_bounds;
$limit = isset($params['limit']) ? ((intval($params['limit']) == 0) ? 10 : intval($params['limit'])) : 10;
$only = isset($params['onlymylistings']) && strtolower($params['onlymylistings']) != "no";
$order = isset($params['order']) ? explode(":", $params['order']) : NULL;
if (count($order) != 2) {
    $order = NULL;
} else {
    $order = array(array(
        'field' => $order[0],
        'order' => $order[1]
    ));
}


$params = $sr->filter_params($params, $type);

$conditions = $sr->build_conditions($params);

if (!is_array($conditions)) {
    $conditions = array();
}

/*$conditions[] = array(
	'field' => 'lat',
	'operator' => 'exists',
	'value' => true
);*/

$prioritization = get_option('sr_prioritization');
$prioritization = ($prioritization === false) ? array() : $prioritization;


$query = $this->prioritize(array(
    'type' => $type,
    //'fields' => array('lat', 'lng', 'address', 'price', 'bedrooms', 'baths_full', 'seo_url', 'zip', 'state', 'city', 'mls_id'),
    'order' => $order,
    'query' => array(
        'boolopr' => 'AND',
        'conditions' => $conditions
    )
), $prioritization);

if ($only && count($prioritization) > 0) {
    array_pop($query);
}

$request = $this->api_request("get_listings", array(
    'query' => $query,
    'fields' => array('lat', 'lng', 'address', 'price', 'bedrooms', 'baths_full', 'seo_url', 'zip', 'state', 'city', 'mls_id'),
    'limit' => array(
        'range' => $limit,
        'offset' => 0
    )
));

$newresults = array();
$geocode = array();

if (count($request->result) > 0) {
    foreach ($request->result as $result) {

        $result->url = $sr->listing_to_url($result, $type);
        if (isset($result->lat) && $result->lat && isset($result->lng) && $result->lng) {
            $newresults[] = $result;
        } else {
            $geocode[] = $result;
        }
    }
}
?>
<div id="map_canvas" style="width:100%; height:100%"></div>


<script type="text/javascript">
    var listings = <?php echo json_encode($newresults)?>;
    var geocode = <?php echo json_encode($geocode)?>;
    var map;
    var markers = [];
    var bounds;

    var us_bounds = <?php echo json_encode($us_bounds) ?>;
    var blogURL = "<?php echo get_bloginfo('url')?>";
    var serverName = "<?php echo $sr->feed->server_name?>";

    initialize();

</script>
</body>

</html>
<?php //exit; ?>
