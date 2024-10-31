<?php
ini_set('memory_limit', '100M');
$sr = $seo_rets_plugin;

wp_enqueue_style('sr_shortcodes_market',$this->css_resources_dir.'shortcodes/market.css');
wp_print_styles(array('sr_shortcodes_market'));

?>

<?php
if (!defined("DONOTCACHEPAGE")) {//support for WP Super Cache
    define("DONOTCACHEPAGE",true);
}


if ( !$sr->api_key ) return '<p class="sr-error">You must activate the SEO RETS plugin.</p>';

//$request = $this->api_request("get_statistic_info");
//
//$board_general=array();
//$board_general['maxPrice']=0;
//$board_general['minPrice']=999999999999;
//$board_general['maxSqft']=0;
//$board_general['minSqft']=999999999999;
//$board_general['maxBuiltYear']=0;
//$board_general['minBuiltYear']=999999999999;
//$factor=array();
//foreach ($request->result as $coll){
//    if ($coll->general[0]->total>0){
//        $board_general['total']+=$coll->general[0]->total;
//    }
//    if ($coll->general[0]->averageBuiltYear>0){
//        $board_general['averageBuiltYear']+=$coll->general[0]->averageBuiltYear;
//    }
//    else{
//        $factor['builtYear']++;
//    }
//    if ($coll->general[0]->averageSqft>0){
//        $board_general['averageSqft']+=$coll->general[0]->averageSqft;
//    }
//    else{
//        $factor['sqft']++;
//    }
//    if ($coll->general[0]->averagePrice>0){
//        $board_general['averagePrice']+=$coll->general[0]->averagePrice;
//    }
//    else{
//        $factor['price']++;
//    }
//
//    foreach($coll->YOYAveragePrice as $year){
//        $board_general['yoy'][$year->date_modified]['total']+=$year->count;
//        $board_general['yoy'][$year->date_modified]['totalPrice']+=$year->totalPrice;
//    }
//
//
//    if ($coll->general[0]->maxPrice > 0 && $coll->general[0]->maxPrice > $board_general['maxPrice']) {
//        $board_general['maxPrice'] = $coll->general[0]->maxPrice;
//    }
//    if ($coll->general[0]->maxSqft > 0 && $coll->general[0]->maxSqft > $board_general['maxSqft']) {
//        $board_general['maxSqft'] = $coll->general[0]->maxSqft;
//    }
//    if ($coll->general[0]->maxBuiltYear > 0 && $coll->general[0]->maxBuiltYear > $board_general['maxBuiltYear'] && $coll->general[0]->maxBuiltYear != 9999) {
//        $board_general['maxBuiltYear'] = $coll->general[0]->maxBuiltYear;
//    }
//    if ($coll->general[0]->minBuiltYear > 0 && $coll->general[0]->minBuiltYear < $board_general['minBuiltYear']) {
//        $board_general['minBuiltYear'] = $coll->general[0]->minBuiltYear;
//    }
//    if ($coll->general[0]->minPrice > 0 && $coll->general[0]->minPrice < $board_general['minPrice']) {
//        $board_general['minPrice'] = $coll->general[0]->minPrice;
//    }
//    if ($coll->general[0]->minSqft > 0 && $coll->general[0]->minSqft < $board_general['minSqft']) {
//        $board_general['minSqft'] = $coll->general[0]->minSqft;
//    }
//
//}
//$board_general['averageBuiltYear']=round($board_general['averageBuiltYear']/($request->count-$factor['builtYear']));
//$board_general['averageSqft']=round($board_general['averageSqft']/($request->count-$factor['sqft']));
//$board_general['averagePrice']=round($board_general['averagePrice']/($request->count-$factor['price']));


$cond['key'] = array('city'=> 1);
$res = $this->api_request("get_grouped_statistic_info_for_board", $cond);
$res->result=(array)$res->result;
ksort($res->result);

$cond['key'] = array('subdivision'=> 1);
$subdivisions = $this->api_request("get_grouped_statistic_info_for_board", $cond);
$subdivisions->result=(array)$subdivisions->result;
ksort($subdivisions->result);

$cond['key'] = array('proj_name'=> 1);
$condosKey='proj_name';
$condos = $this->api_request("get_grouped_statistic_info_for_board", $cond);
if (empty($condos->count)){
    $cond['key'] = array('subdivision'=> 1);
    $condos = $this->api_request("get_grouped_statistic_info_for_board", $cond);
    $condosKey='subdivision';
}
$condos->result=(array)$condos->result;
ksort($condos->result);





include($sr->server_plugin_dir . "/templates/market.php");

