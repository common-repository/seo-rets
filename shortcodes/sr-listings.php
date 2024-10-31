<?php
$sr = $seo_rets_plugin;

if (!defined("DONOTCACHEPAGE")) {//support for WP Super Cache
    define("DONOTCACHEPAGE", true);
}

if (!$sr->api_key) return '<p class="sr-error">You must activate the SEO RETS plugin.</p>';
if (!$sr->is_type_valid($params['type'])) return '<p class="sr-error">Shortcode parameter "type" not set or invalid.</p>';

$type = $params['type'];
unset($params['type']);

$perpage = isset($params['perpage']) ? ((intval($params['perpage']) == 0) ? 10 : intval($params['perpage'])) : 10;
$only = isset($params['onlymylistings']) && strtolower($params['onlymylistings']) != "no";
$refine = isset($params['refine']) && strtolower($params['refine']) != "no";
$page = ($wp_query->query_vars['page'] == 0) ? 1 : $wp_query->query_vars['page'];
$order = isset($params['order']) ? explode(":", $params['order']) : array();
$widgetize = isset($params['widgetize']) && strtolower($params['widgetize']) != "no";

if (count($order) != 2) {
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

if (!is_array($conditions)) {
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
    'type' => $type,
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
    'limit' => array(
        'range' => $perpage,
        'offset' => (($page - 1) * $perpage)
    )
));
$count = $request->count;

$parsed_url = parse_url($_SERVER['REQUEST_URI']);
if (is_front_page()) {
    $pagination_html = $sr->paginate($page, get_bloginfo('url') . '/page/', $perpage, count($request->result), $count);
} else {
    if ($wp_query->query_vars['page'] != 0) {
        $split_url = explode("/", $parsed_url['path']);

        $countSplitUrl = count($split_url);
        if (empty($split_url[$countSplitUrl - 1]) && is_numeric($split_url[$countSplitUrl - 2])) {
            array_pop($split_url);
        }

        array_pop($split_url);
        $parsed_url['path'] = implode("/", $split_url);

        $pagination_html = $sr->paginate($page, $parsed_url['path'] . '/', $perpage, count($request->result), $count);
    } else {
        $pagination_html = $sr->paginate($page, $parsed_url['path'] . '/', $perpage, count($request->result), $count);
    }
}

if (count($request->result) == 0 || $no_paginate) {
    $pagination_html = '';
}

if (count($request->result) > 0):
    if ($refine) include($sr->server_plugin_dir . "/templates/refine-shortcode.php");
    ?>
    <div>
        <?php echo $pagination_html ?>
    </div>
    <?php
    $listings = $request->result;
    include($sr->server_plugin_dir . "/templates/results.php");
    ?>
    <div>
        <?php echo $pagination_html ?>
    </div>
<?php elseif (!$silent): ?>
    <?php do_action('seo_rets_unfound_page', 'sr-listings', $params); ?>
<?php endif; ?>
