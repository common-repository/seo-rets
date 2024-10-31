<?php

global $seo_rets_plugin;
$sr = $seo_rets_plugin;

ini_set('memory_limit', '1024M');
$sql_getListings = "SELECT * FROM $sr->wpdb_sr_stat_mls WHERE mls=" . $_GET['mls'];
$res_getListings = $sr->wpdbSelectResults($sql_getListings);

$ar = $res_getListings[0];
date_default_timezone_set("US/Central");
$statOptAdd = array(
    'id' => '',
    'stat_id' => $ar->id,
    'mtype' => 'Print',
    'time' => date("Y-m-d H:i:s")
);
$sr->wpdbInsertRow($sr->wpdb_sr_stat_option, $statOptAdd);


$request = $sr->api_request('get_pdf', array(
    'url' => home_url(),
    'type' => $_GET['type'],
    'query' => array(
        'boolopr' => 'AND',
        'conditions' => array(array(
            'field' => 'mls_id',
            'operator' => '=',
            'value' => $_GET['mls']
        ))
    )
));
header("Content-Type: application/pdf");

if (!isset($_GET['view'])) header('Content-Disposition: attachment; filename="' . $sr->pretty_url($_GET['address']) . '.pdf"');

echo base64_decode($request->pdf);

exit;
