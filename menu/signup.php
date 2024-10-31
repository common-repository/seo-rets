<?php
wp_enqueue_style('sr-crm');
wp_enqueue_script('sr_tinyMC');
$sr = $seo_rets_plugin;
$plugin_title = $sr->admin_title;
$plugin_id = $sr->admin_id;
global $current_user;
//echo "<pre>";
//    print_r(get_option("sr_users"));
//echo "</pre>";
//update_option('srRewDB', 'fsssrst');

if ($_GET['stat'] == 'actual') {
    $sql_getTodayList = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-m-d') . " 00:00:00' AND `time` <='" . date('Y-m-d') . " 23:59:59' AND mtype='View'";
    $resTodayList = $sr->wpdbSelectResults($sql_getTodayList);
    $resTodayList = json_decode(json_encode($resTodayList), True);
    $arr = array();
    foreach ($resTodayList as $key => $tday) {
        if (isset($arr[$tday['stat_id']])) {
            $arr[$tday['stat_id']] = $arr[$tday['stat_id']] + 1;
        } else {
            $arr[$tday['stat_id']] = 1;
        }
    }
    $AllPopularListings = $arr;
    arsort($AllPopularListings);
    $tenPopularListings = array_chunk($AllPopularListings, 15, true);
    $mergequeries = array();
    foreach ($tenPopularListings[0] as $key => $pop) {
        $sql_getTodayMostPopular_listing = "SELECT * FROM $sr->wpdb_sr_stat_mls WHERE id=" . $key;
        $res_MostPopular_today_listing = $sr->wpdbSelectResults($sql_getTodayMostPopular_listing);
        $getListApiQuery = $res_MostPopular_today_listing[0];
        $mergequeries[] = array(
            'type' => $getListApiQuery->mtype,
            'query' => array(
                'boolopr' => 'AND',
                'conditions' => array(
                    array(
                        'field' => 'mls_id',
                        'operator' => '=',
                        'value' => $getListApiQuery->mls
                    )
                )
            ),
            'limit' => array(
                'range' => 1,
                'offset' => 0

            )
        );
    }
    $request = array(
        'query' => $mergequeries

    );
    $getPopList = $sr->api_request('get_listings', $request);
    $mostPopularListings = $arr;
    arsort($mostPopularListings);
    reset($mostPopularListings);
    $first_key = key($mostPopularListings);
    $sql_getTodayMostPopular_listing = "SELECT * FROM $sr->wpdb_sr_stat_mls WHERE id=" . $first_key;
    $res_MostPopular_today_listing = $sr->wpdbSelectResults($sql_getTodayMostPopular_listing);
    $getListApiQuery = $res_MostPopular_today_listing[0];
    $query = array(
        'type' => $getListApiQuery->mtype,
        'query' => array(
            'boolopr' => 'AND',
            'conditions' => array(
                array(
                    'field' => 'mls_id',
                    'operator' => '=',
                    'value' => $getListApiQuery->mls
                )
            )
        ),
        'limit' => array(
            'range' => 1,
            'offset' => 0

        )
    );
    $l = $sr->api_request('get_listings', $query);
    for ($timeStart = 0; $timeStart <= 24; $timeStart++) {
        $timeEnd = $timeStart + 1;
        $sql_getTodayStat_v = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-m-d') . " " . $timeStart . ":00:00' AND `time` <='" . date('Y-m-d') . " " . $timeEnd . ":00:00' AND mtype='View'";
        $resTodayStat_v = $sr->wpdbSelectResultsCount($sql_getTodayStat_v);

        $sql_getYesterdayStat_v = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-m-d', strtotime("-1 days")) . " " . $timeStart . ":00:00' AND `time` <='" . date('Y-m-d', strtotime("-1 days")) . " " . $timeEnd . ":00:00' AND mtype='View'";
        $resYesterdayStat_v = $sr->wpdbSelectResultsCount($sql_getYesterdayStat_v);
        $resTodayStat[$timeStart] = array(
            'today' => $resTodayStat_v,
            'yesterday' => $resYesterdayStat_v
        );
    }

    for ($timeStart = 0; $timeStart <= 24; $timeStart++) {
        $timeEnd = $timeStart + 1;
        $sql_getTodayStat_p = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-m-d') . " " . $timeStart . ":00:00' AND `time` <='" . date('Y-m-d') . " " . $timeEnd . ":00:00' AND mtype='Print'";
        $resTodayStat_p = $sr->wpdbSelectResultsCount($sql_getTodayStat_p);

        $sql_getYesterdayStat_p = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-m-d', strtotime("-1 days")) . " " . $timeStart . ":00:00' AND `time` <='" . date('Y-m-d', strtotime("-1 days")) . " " . $timeEnd . ":00:00' AND mtype='Print'";
        $resYesterdayStat_p = $sr->wpdbSelectResultsCount($sql_getYesterdayStat_p);
        $resTodayStatPrint[$timeStart] = array(
            'today' => $resTodayStat_p,
            'yesterday' => $resYesterdayStat_p
        );
    }
    for ($timeStart = 0; $timeStart <= 24; $timeStart++) {
        $timeEnd = $timeStart + 1;
        $sql_getTodayStat_e = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-m-d') . " " . $timeStart . ":00:00' AND `time` <='" . date('Y-m-d') . " " . $timeEnd . ":00:00' AND mtype='Email'";
        $resTodayStat_e = $sr->wpdbSelectResultsCount($sql_getTodayStat_e);

        $sql_getYesterdayStat_e = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-m-d', strtotime("-1 days")) . " " . $timeStart . ":00:00' AND `time` <='" . date('Y-m-d', strtotime("-1 days")) . " " . $timeEnd . ":00:00' AND mtype='Email'";
        $resYesterdayStat_e = $sr->wpdbSelectResultsCount($sql_getYesterdayStat_e);
        $resTodayStatEmail[$timeStart] = array(
            'today' => $resTodayStat_e,
            'yesterday' => $resYesterdayStat_e
        );
    }
    for ($timeStart = 0; $timeStart <= 24; $timeStart++) {
        $timeEnd = $timeStart + 1;
        $sql_getTodayStat_f = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-m-d') . " " . $timeStart . ":00:00' AND `time` <='" . date('Y-m-d') . " " . $timeEnd . ":00:00' AND mtype='Fav'";
        $resTodayStat_f = $sr->wpdbSelectResultsCount($sql_getTodayStat_f);

        $sql_getYesterdayStat_f = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-m-d', strtotime("-1 days")) . " " . $timeStart . ":00:00' AND `time` <='" . date('Y-m-d', strtotime("-1 days")) . " " . $timeEnd . ":00:00' AND mtype='Fav'";
        $resYesterdayStat_f = $sr->wpdbSelectResultsCount($sql_getYesterdayStat_f);
        $resTodayStatFav[$timeStart] = array(
            'today' => $resTodayStat_f,
            'yesterday' => $resYesterdayStat_f
        );
    }
} else if ($_GET['stat'] == 'week') {
    $sql_getTodayList = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-m-d', strtotime("-7 days")) . " 00:00:00' AND `time` <='" . date('Y-m-d') . " 23:59:59' AND mtype='View'";
    $resTodayList = $sr->wpdbSelectResults($sql_getTodayList);
    $resTodayList = json_decode(json_encode($resTodayList), True);
    $arr = array();
    foreach ($resTodayList as $key => $tday) {
        if (isset($arr[$tday['stat_id']])) {
            $arr[$tday['stat_id']] = $arr[$tday['stat_id']] + 1;
        } else {
            $arr[$tday['stat_id']] = 1;
        }
    }
    $AllPopularListings = $arr;
    arsort($AllPopularListings);
    $tenPopularListings = array_chunk($AllPopularListings, 15, true);
    $mergequeries = array();
    foreach ($tenPopularListings[0] as $key => $pop) {
        $sql_getTodayMostPopular_listing = "SELECT * FROM $sr->wpdb_sr_stat_mls WHERE id=" . $key;
        $res_MostPopular_today_listing = $sr->wpdbSelectResults($sql_getTodayMostPopular_listing);
        $getListApiQuery = $res_MostPopular_today_listing[0];
        $mergequeries[] = array(
            'type' => $getListApiQuery->mtype,
            'query' => array(
                'boolopr' => 'AND',
                'conditions' => array(
                    array(
                        'field' => 'mls_id',
                        'operator' => '=',
                        'value' => $getListApiQuery->mls
                    )
                )
            ),
            'limit' => array(
                'range' => 1,
                'offset' => 0

            )
        );
    }
    $request = array(
        'query' => $mergequeries

    );
    $getPopList = $sr->api_request('get_listings', $request);
    $mostPopularListings = $arr;
    arsort($mostPopularListings);
    reset($mostPopularListings);
    $first_key = key($mostPopularListings);
    $sql_getTodayMostPopular_listing = "SELECT * FROM $sr->wpdb_sr_stat_mls WHERE id=" . $first_key;
    $res_MostPopular_today_listing = $sr->wpdbSelectResults($sql_getTodayMostPopular_listing);
    $getListApiQuery = $res_MostPopular_today_listing[0];
    $query = array(
        'type' => $getListApiQuery->mtype,
        'query' => array(
            'boolopr' => 'AND',
            'conditions' => array(
                array(
                    'field' => 'mls_id',
                    'operator' => '=',
                    'value' => $getListApiQuery->mls
                )
            )
        ),
        'limit' => array(
            'range' => 1,
            'offset' => 0

        )
    );

    $l = $sr->api_request('get_listings', $query);
    for ($timeStart = 6; $timeStart >= 0; $timeStart--) {
        $timeEnd = $timeStart + 7;
        $sql_getTodayStat_v = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-m-d', strtotime("-" . $timeStart . " days")) . " 00:00:00' AND `time` <='" . date('Y-m-d', strtotime("-" . $timeStart . " days")) . " 23:59:59' AND mtype='View'";
        $resTodayStat_v = $sr->wpdbSelectResultsCount($sql_getTodayStat_v);

        $sql_getYesterdayStat_v = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-m-d', strtotime("-" . $timeEnd . " days")) . " 00:00:00' AND `time` <='" . date('Y-m-d', strtotime("-" . $timeEnd . " days")) . " 23:59:59' AND mtype='View'";
        $resYesterdayStat_v = $sr->wpdbSelectResultsCount($sql_getYesterdayStat_v);
        $resTodayStat[$timeStart] = array(
            'today' => $resTodayStat_v,
            'yesterday' => $resYesterdayStat_v
        );
    }

    for ($timeStart = 6; $timeStart >= 0; $timeStart--) {
        $timeEnd = $timeStart + 7;
        $sql_getTodayStat_p = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-m-d', strtotime("-" . $timeStart . " days")) . " 00:00:00' AND `time` <='" . date('Y-m-d', strtotime("-" . $timeStart . " days")) . " 23:59:59' AND mtype='Print'";
        $resTodayStat_p = $sr->wpdbSelectResultsCount($sql_getTodayStat_p);

        $sql_getYesterdayStat_p = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-m-d', strtotime("-" . $timeEnd . " days")) . " 00:00:00' AND `time` <='" . date('Y-m-d', strtotime("-" . $timeEnd . " days")) . " 23:59:59' AND mtype='Print'";
        $resYesterdayStat_p = $sr->wpdbSelectResultsCount($sql_getYesterdayStat_p);
        $resTodayStatPrint[$timeStart] = array(
            'today' => $resTodayStat_p,
            'yesterday' => $resYesterdayStat_p
        );
    }
    for ($timeStart = 6; $timeStart >= 0; $timeStart--) {
        $timeEnd = $timeStart + 7;
        $sql_getTodayStat_e = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-m-d', strtotime("-" . $timeStart . " days")) . " 00:00:00' AND `time` <='" . date('Y-m-d', strtotime("-" . $timeStart . " days")) . " 23:59:59' AND mtype='Email'";
        $resTodayStat_e = $sr->wpdbSelectResultsCount($sql_getTodayStat_e);

        $sql_getYesterdayStat_e = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-m-d', strtotime("-" . $timeEnd . " days")) . " 00:00:00' AND `time` <='" . date('Y-m-d', strtotime("-" . $timeEnd . " days")) . " 23:59:59' AND mtype='Email'";
        $resYesterdayStat_e = $sr->wpdbSelectResultsCount($sql_getYesterdayStat_e);
        $resTodayStatEmail[$timeStart] = array(
            'today' => $resTodayStat_e,
            'yesterday' => $resYesterdayStat_e
        );
    }
    for ($timeStart = 6; $timeStart >= 0; $timeStart--) {
        $timeEnd = $timeStart + 7;
        $sql_getTodayStat_f = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-m-d', strtotime("-" . $timeStart . " days")) . " 00:00:00' AND `time` <='" . date('Y-m-d', strtotime("-" . $timeStart . " days")) . " 23:59:59' AND mtype='Fav'";
        $resTodayStat_f = $sr->wpdbSelectResultsCount($sql_getTodayStat_f);

        $sql_getYesterdayStat_f = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-m-d', strtotime("-" . $timeEnd . " days")) . " 00:00:00' AND `time` <='" . date('Y-m-d', strtotime("-" . $timeEnd . " days")) . " 23:59:59' AND mtype='Fav'";
        $resYesterdayStat_f = $sr->wpdbSelectResultsCount($sql_getYesterdayStat_f);
        $resTodayStatFav[$timeStart] = array(
            'today' => $resTodayStat_f,
            'yesterday' => $resYesterdayStat_f
        );
    }
} else if ($_GET['stat'] == 'month') {
    $sql_getTodayList = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-m-d', strtotime('first day of this month')) . " 00:00:00' AND `time` <='" . date('Y-m-d', strtotime('last day of this month')) . " 23:59:59' AND mtype='View'";
    $resTodayList = $sr->wpdbSelectResults($sql_getTodayList);
    $resTodayList = json_decode(json_encode($resTodayList), True);
    $arr = array();
    foreach ($resTodayList as $key => $tday) {
        if (isset($arr[$tday['stat_id']])) {
            $arr[$tday['stat_id']] = $arr[$tday['stat_id']] + 1;
        } else {
            $arr[$tday['stat_id']] = 1;
        }
    }
    $AllPopularListings = $arr;
    arsort($AllPopularListings);
    $tenPopularListings = array_chunk($AllPopularListings, 15, true);
    $mergequeries = array();
    foreach ($tenPopularListings[0] as $key => $pop) {
        $sql_getTodayMostPopular_listing = "SELECT * FROM $sr->wpdb_sr_stat_mls WHERE id=" . $key;
        $res_MostPopular_today_listing = $sr->wpdbSelectResults($sql_getTodayMostPopular_listing);
        $getListApiQuery = $res_MostPopular_today_listing[0];
        $mergequeries[] = array(
            'type' => $getListApiQuery->mtype,
            'query' => array(
                'boolopr' => 'AND',
                'conditions' => array(
                    array(
                        'field' => 'mls_id',
                        'operator' => '=',
                        'value' => $getListApiQuery->mls
                    )
                )
            ),
            'limit' => array(
                'range' => 1,
                'offset' => 0

            )
        );
    }
    $request = array(
        'query' => $mergequeries

    );
    $getPopList = $sr->api_request('get_listings', $request);
    $mostPopularListings = $arr;
    arsort($mostPopularListings);
    reset($mostPopularListings);
    $first_key = key($mostPopularListings);
    $sql_getTodayMostPopular_listing = "SELECT * FROM $sr->wpdb_sr_stat_mls WHERE id=" . $first_key;
    $res_MostPopular_today_listing = $sr->wpdbSelectResults($sql_getTodayMostPopular_listing);
    $getListApiQuery = $res_MostPopular_today_listing[0];
    $query = array(
        'type' => $getListApiQuery->mtype,
        'query' => array(
            'boolopr' => 'AND',
            'conditions' => array(
                array(
                    'field' => 'mls_id',
                    'operator' => '=',
                    'value' => $getListApiQuery->mls
                )
            )
        ),
        'limit' => array(
            'range' => 1,
            'offset' => 0

        )
    );

    $l = $sr->api_request('get_listings', $query);


    for ($timeStart = 0; $timeStart <= date('t') - 1; $timeStart++) {
        $date = date_create(date('Y-m-d', strtotime('first day of this month')));
        $dateLast = date_create(date('Y-m-d', strtotime('first day of last month')));
        date_modify($date, '+' . $timeStart . ' days');
        date_modify($dateLast, '+' . $timeStart . ' days');
        $sql_getTodayStat_v = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date_format($date, 'Y-m-d') . " 00:00:00' AND `time` <='" . date_format($date, 'Y-m-d') . " 23:59:59' AND mtype='View'";
        $resTodayStat_v = $sr->wpdbSelectResultsCount($sql_getTodayStat_v);

        $sql_getYesterdayStat_v = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date_format($dateLast, 'Y-m-d') . " 00:00:00' AND `time` <='" . date_format($dateLast, 'Y-m-d') . " 23:59:59' AND mtype='View'";
        $resYesterdayStat_v = $sr->wpdbSelectResultsCount($sql_getYesterdayStat_v);
        $resTodayStat[$timeStart] = array(
            'today' => $resTodayStat_v,
            'yesterday' => $resYesterdayStat_v
        );
    }

    for ($timeStart = 0; $timeStart <= date('t') - 1; $timeStart++) {
        $date = date_create(date('Y-m-d', strtotime('first day of this month')));
        $dateLast = date_create(date('Y-m-d', strtotime('first day of last month')));
        date_modify($date, '+' . $timeStart . ' days');
        date_modify($dateLast, '+' . $timeStart . ' days');
        $sql_getTodayStat_p = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE  `time` >='" . date_format($date, 'Y-m-d') . " 00:00:00' AND `time` <='" . date_format($date, 'Y-m-d') . " 23:59:59'  AND mtype='Print'";
        $resTodayStat_p = $sr->wpdbSelectResultsCount($sql_getTodayStat_p);

        $sql_getYesterdayStat_p = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date_format($dateLast, 'Y-m-d') . " 00:00:00' AND `time` <='" . date_format($dateLast, 'Y-m-d') . " 23:59:59' AND mtype='Print'";
        $resYesterdayStat_p = $sr->wpdbSelectResultsCount($sql_getYesterdayStat_p);
        $resTodayStatPrint[$timeStart] = array(
            'today' => $resTodayStat_p,
            'yesterday' => $resYesterdayStat_p
        );
    }
    for ($timeStart = 0; $timeStart <= date('t') - 1; $timeStart++) {
        $date = date_create(date('Y-m-d', strtotime('first day of this month')));
        $dateLast = date_create(date('Y-m-d', strtotime('first day of last month')));
        date_modify($date, '+' . $timeStart . ' days');
        date_modify($dateLast, '+' . $timeStart . ' days');
        $sql_getTodayStat_e = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date_format($date, 'Y-m-d') . " 00:00:00' AND `time` <='" . date_format($date, 'Y-m-d') . " 23:59:59' AND mtype='Email'";
        $resTodayStat_e = $sr->wpdbSelectResultsCount($sql_getTodayStat_e);

        $sql_getYesterdayStat_e = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date_format($dateLast, 'Y-m-d') . " 00:00:00' AND `time` <='" . date_format($dateLast, 'Y-m-d') . " 23:59:59' AND mtype='Email'";
        $resYesterdayStat_e = $sr->wpdbSelectResultsCount($sql_getYesterdayStat_e);
        $resTodayStatEmail[$timeStart] = array(
            'today' => $resTodayStat_e,
            'yesterday' => $resYesterdayStat_e
        );
    }
    for ($timeStart = 0; $timeStart <= date('t') - 1; $timeStart++) {
        $date = date_create(date('Y-m-d', strtotime('first day of this month')));
        $dateLast = date_create(date('Y-m-d', strtotime('first day of last month')));
        date_modify($date, '+' . $timeStart . ' days');
        date_modify($dateLast, '+' . $timeStart . ' days');
        $sql_getTodayStat_f = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date_format($date, 'Y-m-d') . " 00:00:00' AND `time` <='" . date_format($date, 'Y-m-d') . " 23:59:59' AND mtype='Fav'";
        $resTodayStat_f = $sr->wpdbSelectResults($sql_getTodayStat_f);

        $sql_getYesterdayStat_f = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date_format($dateLast, 'Y-m-d') . " 00:00:00' AND `time` <='" . date_format($dateLast, 'Y-m-d') . " 23:59:59' AND mtype='Fav'";
        $resYesterdayStat_f = $sr->wpdbSelectResults($sql_getYesterdayStat_f);
        $resTodayStatFav[$timeStart] = array(
            'today' => count($resTodayStat_f),
            'yesterday' => count($resYesterdayStat_f)
        );
    }
} else if ($_GET['stat'] == 'year') {
    $sql_getTodayList = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y') . "-01-01 00:00:00' AND `time` <='" . date('Y') . "12-31 23:59:59' AND mtype='View'";
    $resTodayList = $sr->wpdbSelectResults($sql_getTodayList);
    $resTodayList = json_decode(json_encode($resTodayList), True);
    $arr = array();
    foreach ($resTodayList as $key => $tday) {
        if (isset($arr[$tday['stat_id']])) {
            $arr[$tday['stat_id']] = $arr[$tday['stat_id']] + 1;
        } else {
            $arr[$tday['stat_id']] = 1;
        }
    }

    $AllPopularListings = $arr;
    arsort($AllPopularListings);
    $tenPopularListings = array_chunk($AllPopularListings, 15, true);

    $mergequeries = array();
    foreach ($tenPopularListings[0] as $key => $pop) {
        $sql_getTodayMostPopular_listing = "SELECT * FROM $sr->wpdb_sr_stat_mls WHERE id=" . $key;
        $res_MostPopular_today_listing = $sr->wpdbSelectResults($sql_getTodayMostPopular_listing);
        $getListApiQuery = $res_MostPopular_today_listing[0];
        $mergequeries[] = array(
            'type' => $getListApiQuery->mtype,
            'query' => array(
                'boolopr' => 'AND',
                'conditions' => array(
                    array(
                        'field' => 'mls_id',
                        'operator' => '=',
                        'value' => $getListApiQuery->mls
                    )
                )
            ),
            'limit' => array(
                'range' => 1,
                'offset' => 0

            )
        );
    }
    $request = array(
        'query' => $mergequeries

    );
    $getPopList = $sr->api_request('get_listings', $request);


    $mostPopularListings = $arr;
    arsort($mostPopularListings);
    reset($mostPopularListings);
    $first_key = key($mostPopularListings);
    $sql_getTodayMostPopular_listing = "SELECT * FROM $sr->wpdb_sr_stat_mls WHERE id=" . $first_key;
    $res_MostPopular_today_listing = $sr->wpdbSelectResults($sql_getTodayMostPopular_listing);
    $getListApiQuery = $res_MostPopular_today_listing[0];
    $query = array(
        'type' => $getListApiQuery->mtype,
        'query' => array(
            'boolopr' => 'AND',
            'conditions' => array(
                array(
                    'field' => 'mls_id',
                    'operator' => '=',
                    'value' => $getListApiQuery->mls
                )
            )
        ),
        'limit' => array(
            'range' => 1,
            'offset' => 0

        )
    );

    $l = $sr->api_request('get_listings', $query);


    for ($timeStart = 1; $timeStart <= 12; $timeStart++) {
        $sql_getTodayStat_v = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-' . $timeStart . '-01') . " 00:00:00' AND `time` <='" . date('Y-' . $timeStart . '-' . cal_days_in_month(CAL_GREGORIAN, $timeStart, date('Y'))) . " 23:59:59' AND mtype='View'";
        $resTodayStat_v = $sr->wpdbSelectResultsCount($sql_getTodayStat_v);

        $sql_getYesterdayStat_v = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-' . $timeStart . '-01', strtotime('last year')) . " 00:00:00' AND `time` <='" . date('Y-' . $timeStart . '-' . cal_days_in_month(CAL_GREGORIAN, $timeStart, date('Y')), strtotime('last year')) . " 23:59:59' AND mtype='View'";
        $resYesterdayStat_v = $sr->wpdbSelectResultsCount($sql_getYesterdayStat_v);
        $resTodayStat[$timeStart] = array(
            'today' => $resTodayStat_v,
            'yesterday' => $resYesterdayStat_v
        );
    }

    for ($timeStart = 1; $timeStart <= 12; $timeStart++) {
        $sql_getTodayStat_p = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-' . $timeStart . '-01') . " 00:00:00' AND `time` <='" . date('Y-' . $timeStart . '-' . cal_days_in_month(CAL_GREGORIAN, $timeStart, date('Y'))) . " 23:59:59' AND mtype='Print'";
        $resTodayStat_p = $sr->wpdbSelectResultsCount($sql_getTodayStat_p);

        $sql_getYesterdayStat_p = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-' . $timeStart . '-01', strtotime('last year')) . " 00:00:00' AND `time` <='" . date('Y-' . $timeStart . '-' . cal_days_in_month(CAL_GREGORIAN, $timeStart, date('Y')), strtotime('last year')) . " 23:59:59' AND mtype='Print'";
        $resYesterdayStat_p = $sr->wpdbSelectResultsCount($sql_getYesterdayStat_p);
        $resTodayStatPrint[$timeStart] = array(
            'today' => $resTodayStat_p,
            'yesterday' => $resYesterdayStat_p
        );
    }
    for ($timeStart = 1; $timeStart <= 12; $timeStart++) {
        $sql_getTodayStat_e = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-' . $timeStart . '-01') . " 00:00:00' AND `time` <='" . date('Y-' . $timeStart . '-' . cal_days_in_month(CAL_GREGORIAN, $timeStart, date('Y'))) . " 23:59:59' AND mtype='Email'";
        $resTodayStat_e = $sr->wpdbSelectResultsCount($sql_getTodayStat_e);

        $sql_getYesterdayStat_e = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-' . $timeStart . '-01', strtotime('last year')) . " 00:00:00' AND `time` <='" . date('Y-' . $timeStart . '-' . cal_days_in_month(CAL_GREGORIAN, $timeStart, date('Y')), strtotime('last year')) . " 23:59:59' AND mtype='Email'";
        $resYesterdayStat_e = $sr->wpdbSelectResultsCount($sql_getYesterdayStat_e);
        $resTodayStatEmail[$timeStart] = array(
            'today' => $resTodayStat_e,
            'yesterday' => $resYesterdayStat_e
        );
    }
    for ($timeStart = 1; $timeStart <= 12; $timeStart++) {
        $sql_getTodayStat_f = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-' . $timeStart . '-01') . " 00:00:00' AND `time` <='" . date('Y-' . $timeStart . '-' . cal_days_in_month(CAL_GREGORIAN, $timeStart, date('Y'))) . " 23:59:59' AND mtype='Fav'";
        $resTodayStat_f = $sr->wpdbSelectResultsCount($sql_getTodayStat_f);

        $sql_getYesterdayStat_f = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE `time` >='" . date('Y-' . $timeStart . '-01', strtotime('last year')) . " 00:00:00' AND `time` <='" . date('Y-' . $timeStart . '-' . cal_days_in_month(CAL_GREGORIAN, $timeStart, date('Y')), strtotime('last year')) . " 23:59:59' AND mtype='Fav'";
        $resYesterdayStat_f = $sr->wpdbSelectResultsCount($sql_getYesterdayStat_f);
        $resTodayStatFav[$timeStart] = array(
            'today' => $resTodayStat_f,
            'yesterday' => $resYesterdayStat_f
        );
    }
}

?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<?php
if ($_GET['stat'] == 'actual') {

    ?>
    <script>
        google.charts.load('current', {packages: ['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawAnnotations);

        function drawAnnotations() {
            var data = new google.visualization.DataTable();
            data.addColumn('timeofday', 'Time of Day');
            data.addColumn('number', 'Today activity');
//        data.addColumn({type: 'string', role: 'annotation'});
            data.addColumn('number', 'Yesterday activity');
//        data.addColumn({type: 'string', role: 'annotation'});

            data.addRows([
                <?php
                foreach ($resTodayStat as $key => $count) {
                    echo "[{v: [" . $key . ", 30, 0], f: '" . $key . " o\'clock'},   " . $count['today'] . ", " . $count['yesterday'] . "],";
                }
                ?>
            ]);

            var dataPrint = new google.visualization.DataTable();
            dataPrint.addColumn('timeofday', 'Time of Day');
            dataPrint.addColumn('number', 'Today activity');
//        data.addColumn({type: 'string', role: 'annotation'});
            dataPrint.addColumn('number', 'Yesterday activity');
//        data.addColumn({type: 'string', role: 'annotation'});

            dataPrint.addRows([
                <?php
                foreach ($resTodayStatPrint as $key => $count) {
                    echo "[{v: [" . $key . ", 30, 0], f: '" . $key . " o\'clock'},   " . $count['today'] . ", " . $count['yesterday'] . "],";
                }
                ?>
            ]);

            var dataEmail = new google.visualization.DataTable();
            dataEmail.addColumn('timeofday', 'Time of Day');
            dataEmail.addColumn('number', 'Today activity');
//        data.addColumn({type: 'string', role: 'annotation'});
            dataEmail.addColumn('number', 'Yesterday activity');
//        data.addColumn({type: 'string', role: 'annotation'});

            dataEmail.addRows([
                <?php
                foreach ($resTodayStatEmail as $key => $count) {
                    echo "[{v: [" . $key . ", 30, 0], f: '" . $key . " o\'clock'},   " . $count['today'] . ", " . $count['yesterday'] . "],";
                }
                ?>
            ]);
            var dataFav = new google.visualization.DataTable();
            dataFav.addColumn('timeofday', 'Time of Day');
            dataFav.addColumn('number', 'Today activity');
//        data.addColumn({type: 'string', role: 'annotation'});
            dataFav.addColumn('number', 'Yesterday activity');
//        data.addColumn({type: 'string', role: 'annotation'});

            dataFav.addRows([
                <?php
                foreach ($resTodayStatFav as $key => $count) {
                    echo "[{v: [" . $key . ", 30, 0], f: '" . $key . " o\'clock'},   " . $count['today'] . ", " . $count['yesterday'] . "],";
                }
                ?>
            ]);

            var options = {

                title: 'Two days statistic by viewed listings',
                annotations: {
                    alwaysOutside: true,
                    textStyle: {
                        fontSize: 14,
                        color: '#000',
                        auraColor: 'none'
                    }
                },
                hAxis: {
                    title: 'Time of Day',
                    format: 'h:mm a',
                    viewWindow: {
                        min: [0, 0, 0],
                        max: [23, 30, 0]
                    }
                },
                vAxis: {
                    title: 'Viewed'
                }
            };
            var optionsPrint = {

                title: 'Two days statistic by printed listings',
                annotations: {
                    alwaysOutside: true,
                    textStyle: {
                        fontSize: 14,
                        color: '#000',
                        auraColor: 'none'
                    }
                },
                hAxis: {
                    title: 'Time of Day',
                    format: 'h:mm a',
                    viewWindow: {
                        min: [0, 0, 0],
                        max: [23, 30, 0]
                    }
                },
                vAxis: {
                    title: 'Printed'
                }
            };
            var optionsEmail = {

                title: 'Two days statistic by subscribe listings',
                annotations: {
                    alwaysOutside: true,
                    textStyle: {
                        fontSize: 14,
                        color: '#000',
                        auraColor: 'none'
                    }
                },
                hAxis: {
                    title: 'Time of Day',
                    format: 'h:mm a',
                    viewWindow: {
                        min: [0, 0, 0],
                        max: [23, 30, 0]
                    }
                },
                vAxis: {
                    title: 'Subscribed'
                }
            };
            var optionsFav = {

                title: 'Two days statistic by Favorite listings',
                annotations: {
                    alwaysOutside: true,
                    textStyle: {
                        fontSize: 14,
                        color: '#000',
                        auraColor: 'none'
                    }
                },
                hAxis: {
                    title: 'Time of Day',
                    format: 'h:mm a',
                    viewWindow: {
                        min: [0, 0, 0],
                        max: [23, 30, 0]
                    }
                },
                vAxis: {
                    title: 'Favorite'
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
            chart.draw(data, options);

            var chartPrint = new google.visualization.ColumnChart(document.getElementById('chart_div_print'));
            chartPrint.draw(dataPrint, optionsPrint);

            var chartEmail = new google.visualization.ColumnChart(document.getElementById('chart_div_email'));
            chartEmail.draw(dataEmail, optionsEmail);

            var chartFav = new google.visualization.ColumnChart(document.getElementById('chart_div_fav'));
            chartFav.draw(dataFav, optionsFav);
        }
    </script>
    <?php
} else if ($_GET['stat'] == 'week') {
    ?>
    <script>
        google.charts.load('current', {'packages': ['bar']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Days', 'Current Week', 'Last Week'],
                <?php
                foreach ($resTodayStat as $key => $count) {
                    $timeEnd = $key + 7;
                    echo "['" . date('m-d', strtotime("-" . $key . " days")) . "-" . date('d', strtotime("-" . $timeEnd . " days")) . "', " . $count['today'] . ", " . $count['yesterday'] . " ],";
                }
                ?>

            ]);
            var dataPrint = google.visualization.arrayToDataTable([
                ['Days', 'Current Week', 'Last Week'],
                <?php
                foreach ($resTodayStatPrint as $key => $count) {
                    $timeEnd = $key + 7;
                    echo "['" . date('m-d', strtotime("-" . $key . " days")) . "-" . date('d', strtotime("-" . $timeEnd . " days")) . "', " . $count['today'] . ", " . $count['yesterday'] . " ],";
                }
                ?>

            ]);
            var dataEmail = google.visualization.arrayToDataTable([
                ['Days', 'Current Week', 'Last Week'],
                <?php
                foreach ($resTodayStatEmail as $key => $count) {
                    $timeEnd = $key + 7;
                    echo "['" . date('m-d', strtotime("-" . $key . " days")) . "-" . date('d', strtotime("-" . $timeEnd . " days")) . "', " . $count['today'] . ", " . $count['yesterday'] . " ],";
                }
                ?>

            ]);
            var dataFav = google.visualization.arrayToDataTable([
                ['Days', 'Current Week', 'Last Week'],
                <?php
                foreach ($resTodayStatFav as $key => $count) {
                    $timeEnd = $key + 7;
                    echo "['" . date('m-d', strtotime("-" . $key . " days")) . "-" . date('d', strtotime("-" . $timeEnd . " days")) . "', " . $count['today'] . ", " . $count['yesterday'] . " ],";
                }
                ?>

            ]);

            var options = {
                chart: {
                    title: '7 day viewed activity',
                    subtitle: 'Last week activity'
                },

                bars: 'vertical',
                vAxis: {format: 'decimal'},
                height: 300,
                colors: ['#1b9e77', '#d95f02', '#7570b3']
            };

            var optionsPrint = {
                chart: {
                    title: '7 day printed activity',
                    subtitle: 'Last week activity'
                },

                bars: 'vertical',
                vAxis: {format: 'decimal'},
                height: 300,
                colors: ['#1b9e77', '#d95f02', '#7570b3']
            };
            var optionsEmailed = {
                chart: {
                    title: '7 day printed activity',
                    subtitle: 'Last week activity'
                },

                bars: 'vertical',
                vAxis: {format: 'decimal'},
                height: 300,
                colors: ['#1b9e77', '#d95f02', '#7570b3']
            };
            var optionsFav = {
                chart: {
                    title: '7 day favorite activity',
                    subtitle: 'Last week activity'
                },

                bars: 'vertical',
                vAxis: {format: 'decimal'},
                height: 300,
                colors: ['#1b9e77', '#d95f02', '#7570b3']
            };

            var chart = new google.charts.Bar(document.getElementById('chart_div'));
            chart.draw(data, google.charts.Bar.convertOptions(options));

            var chartPrint = new google.charts.Bar(document.getElementById('chart_div_print'));
            chartPrint.draw(dataPrint, google.charts.Bar.convertOptions(optionsPrint));

            var chartEmail = new google.charts.Bar(document.getElementById('chart_div_email'));
            chartEmail.draw(dataEmail, google.charts.Bar.convertOptions(optionsEmailed));

            var chartFav = new google.charts.Bar(document.getElementById('chart_div_fav'));
            chartFav.draw(dataFav, google.charts.Bar.convertOptions(optionsFav));

        }
    </script>
    <?php
} else if ($_GET['stat'] == 'month') {

    ?>
    <script>
        google.charts.load('current', {'packages': ['corechart']});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Days', 'Current month', 'Last Month'],
                <?php
                foreach ($resTodayStat as $key => $count) {
                    $date = date_create(date('Y-m-d', strtotime('first day of this month')));
                    date_modify($date, '+' . $key . ' days');
                    echo "['" . date_format($date, 'd') . "', " . $count['today'] . ", " . $count['yesterday'] . " ],";
                }
                ?>
            ]);
            var dataEmail = google.visualization.arrayToDataTable([
                ['Days', 'Current month', 'Last Month'],
                <?php
                foreach ($resTodayStatEmail as $key => $count) {
                    $date = date_create(date('Y-m-d', strtotime('first day of this month')));
                    date_modify($date, '+' . $key . ' days');
                    echo "['" . date_format($date, 'd') . "', " . $count['today'] . ", " . $count['yesterday'] . " ],";
                }
                ?>
            ]);
            var dataPrint = google.visualization.arrayToDataTable([
                ['Days', 'Current month', 'Last Month'],
                <?php
                foreach ($resTodayStatPrint as $key => $count) {
                    $date = date_create(date('Y-m-d', strtotime('first day of this month')));
                    date_modify($date, '+' . $key . ' days');
                    echo "['" . date_format($date, 'd') . "', " . $count['today'] . ", " . $count['yesterday'] . " ],";
                }
                ?>
            ]);
            var dataFav = google.visualization.arrayToDataTable([
                ['Days', 'Current month', 'Last Month'],
                <?php
                foreach ($resTodayStatFav as $key => $count) {
                    $date = date_create(date('Y-m-d', strtotime('first day of this month')));
                    date_modify($date, '+' . $key . ' days');
                    echo "['" . date_format($date, 'd') . "', " . $count['today'] . ", " . $count['yesterday'] . " ],";
                }
                ?>
            ]);

            var options = {
                title: 'Month Views Activity',
                hAxis: {title: 'Days', titleTextStyle: {color: '#333'}},
                vAxis: {minValue: 0, format: 'decimal'}
            };
            var optionsEmail = {
                title: 'Month Subscribe Activity',
                hAxis: {title: 'Days', titleTextStyle: {color: '#333'}},
                vAxis: {minValue: 0, format: 'decimal'}
            };
            var optionsPrint = {
                title: 'Month Printed Activity',
                hAxis: {title: 'Days', titleTextStyle: {color: '#333'}},
                vAxis: {minValue: 0, format: 'decimal'}
            };
            var optionsFav = {
                title: 'Month Favorite Activity',
                hAxis: {title: 'Days', titleTextStyle: {color: '#333'}},
                vAxis: {minValue: 0, format: 'decimal'}
            };

            var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
            chart.draw(data, options);
            var chartEmail = new google.visualization.AreaChart(document.getElementById('chart_div_email'));
            chartEmail.draw(dataEmail, optionsEmail);
            var chartPrint = new google.visualization.AreaChart(document.getElementById('chart_div_print'));
            chartPrint.draw(dataPrint, optionsPrint);
            var chartFav = new google.visualization.AreaChart(document.getElementById('chart_div_fav'));
            chartFav.draw(dataFav, optionsFav);
        }
    </script>
    <?php
} else if ($_GET['stat'] == 'year') {
    ?>
    <script>
        google.charts.load('current', {'packages': ['bar']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Month', 'Current Year', 'Last Year'],
                <?php
                foreach ($resTodayStat as $key => $count) {
                    echo "['" . date('Y-' . $key) . "', " . $count['today'] . ", " . $count['yesterday'] . " ],";
                }
                ?>

            ]);
            var dataPrint = google.visualization.arrayToDataTable([
                ['Month', 'Current Year', 'Last Year'],
                <?php
                foreach ($resTodayStatPrint as $key => $count) {
                    echo "['" . date('Y-' . $key) . "', " . $count['today'] . ", " . $count['yesterday'] . " ],";
                }
                ?>

            ]);
            var dataEmail = google.visualization.arrayToDataTable([
                ['Month', 'Current Year', 'Last Year'],
                <?php
                foreach ($resTodayStatEmail as $key => $count) {
                    $timeEnd = $key + 7;
                    echo "['" . date('Y-' . $key) . "', " . $count['today'] . ", " . $count['yesterday'] . " ],";
                }
                ?>

            ]);
            var dataFav = google.visualization.arrayToDataTable([
                ['Month', 'Current Year', 'Last Year'],
                <?php
                foreach ($resTodayStatFav as $key => $count) {
                    $timeEnd = $key + 7;
                    echo "['" . date('Y-' . $key) . "', " . $count['today'] . ", " . $count['yesterday'] . " ],";
                }
                ?>

            ]);

            var options = {
                chart: {
                    title: 'Year viewed activity',
//                    subtitle: 'Last week activity'
                },

                bars: 'vertical',
                vAxis: {format: 'decimal'},
                height: 300,
                colors: ['#1b9e77', '#d95f02', '#7570b3']
            };

            var optionsPrint = {
                chart: {
                    title: 'Year printed activity',
//                    subtitle: 'Last week activity'
                },

                bars: 'vertical',
                vAxis: {format: 'decimal'},
                height: 300,
                colors: ['#1b9e77', '#d95f02', '#7570b3']
            };
            var optionsEmailed = {
                chart: {
                    title: 'Year printed activity',
//                    subtitle: 'Last week activity'
                },

                bars: 'vertical',
                vAxis: {format: 'decimal'},
                height: 300,
                colors: ['#1b9e77', '#d95f02', '#7570b3']
            };
            var optionsFav = {
                chart: {
                    title: 'Year favorite activity',
//                    subtitle: 'Last week activity'
                },

                bars: 'vertical',
                vAxis: {format: 'decimal'},
                height: 300,
                colors: ['#1b9e77', '#d95f02', '#7570b3']
            };

            var chart = new google.charts.Bar(document.getElementById('chart_div'));
            chart.draw(data, google.charts.Bar.convertOptions(options));

            var chartPrint = new google.charts.Bar(document.getElementById('chart_div_print'));
            chartPrint.draw(dataPrint, google.charts.Bar.convertOptions(optionsPrint));

            var chartEmail = new google.charts.Bar(document.getElementById('chart_div_email'));
            chartEmail.draw(dataEmail, google.charts.Bar.convertOptions(optionsEmailed));

            var chartFav = new google.charts.Bar(document.getElementById('chart_div_fav'));
            chartFav.draw(dataFav, google.charts.Bar.convertOptions(optionsFav));

        }
    </script>
    <?php
}
?>
<script>

    jQuery(document).ready(function () {
        jQuery('.searchMLSStat').keypress(function (e) {
            if (e.which == 13) {
                window.location = "/wp-admin/admin.php?page=<?php echo $sr->admin_id; ?>-crm&mlssearch=" + jQuery('.searchMLSStat').val();
            }
        });
        jQuery(function () {
            jQuery("#activityTab").tabs();
        });
    });
</script>
<div class="wrap">
    <div id="icon-tools" class="icon32"></div>
    <h2><?php echo $plugin_title ?> :: CRM<sup>
            <small><i>(beta)</i></small>
        </sup>
    </h2>

    <div class="header-nav">
        <ul>
            <li><a href="/wp-admin/admin.php?page=<?php echo $plugin_id; ?>-crm">Dashboard</a></li>
            <li><a <?php if ($_GET['user'] || $_GET['profile']) echo 'class="active"'; ?>
                    href="/wp-admin/admin.php?page=<?php echo $plugin_id; ?>-crm&user=show">Users</a></li>
            <li><a <?php if ($_GET['stat']) echo 'class="active"'; ?>
                    href="/wp-admin/admin.php?page=<?php echo $plugin_id; ?>-crm&stat=actual">Stat</a></li>
        </ul>
    </div>
    <?php
    if ($_GET['user']) {
        $sql = "SELECT * FROM $sr->wpdb_sr_users";
        $res = $sr->wpdbSelectResults($sql);

        $page = !empty($_GET['pages']) ? (int)$_GET['pages'] : 1;
        $total = count($res); //total items in array
        $limit = 10; //per page
        $totalPages = ceil($total / $limit); //calculate total pages
        $page = max($page, 1); //get 1 page when $_GET['page'] <= 0
        $page = min($page, $totalPages); //get last page when $_GET['page'] > $totalPages
        $offset = ($page - 1) * $limit;
        if ($offset < 0) $offset = 0;

        $res = array_slice($res, $offset, $limit, true);
        $link = 'admin.php?page=' . $sr->admin_id . '-crm&user=show&pages=%d';
        $pagerContainer = '<ul>';
        if ($totalPages != 0) {
            for ($i = 1; $i <= $totalPages; $i++) {

                if ($page == $i) {
                    $pagerContainer .= sprintf('<li><a class="activePage" href="' . $link . '"><span>' . $i . '</span></a></li>', $i);
                } else {
                    $pagerContainer .= sprintf('<li><a class="pagiA" href="' . $link . '"><span>' . $i . '</span></a></li>', $i);
                }
            }
        }
        $pagerContainer .= '</ul>';


        ?>
        <div class="datagrid">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Favorites</th>
                    <th>Saved Search</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td colspan="6">
                        <div id="paging">
                            <?php echo $pagerContainer; ?>
                        </div>
                </tr>
                </tfoot>
                <tbody>
                <?php
                foreach ($res as $user) {
                    ?>
                    <tr>
                        <td>
                            <?php echo $user->id; ?>
                        </td>
                        <td>
                            <a href=<?php echo 'admin.php?page=' . $sr->admin_id . '-crm&profile=' . $user->id; ?>><?php echo $user->u_name ?></a>
                        </td>
                        <td><?php echo $user->u_email ?></td>
                        <td><?php echo $user->u_phone ?></td>
                        <td><?php
                            $sql_getCount = "SELECT * FROM $sr->wpdb_sr_favorites WHERE user_id = " . $user->id;
                            $results = $sr->wpdbSelectResults($sql_getCount);
                            echo count($results);
                            ?>
                        </td>
                        <td><?php
                            $sql_getCount = "SELECT * FROM $sr->wpdb_sr_savesearch WHERE user_id = " . $user->id;
                            $results = $sr->wpdbSelectResults($sql_getCount);
                            echo count($results);
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>

    <?php


    } elseif ($_GET['stat']) {
    ?>
        <div class="header_sub_nav">
            <ul>
                <li><a href=<?php echo 'admin.php?page=' . $sr->admin_id . '-crm&stat=actual'; ?>>Actual</a>
                </li>
                <li><a href=<?php echo 'admin.php?page=' . $sr->admin_id . '-crm&stat=week'; ?>>7 Days</a></li>
                <li><a href=<?php echo 'admin.php?page=' . $sr->admin_id . '-crm&stat=month'; ?>>Month</a></li>
                <li><a href=<?php echo 'admin.php?page=' . $sr->admin_id . '-crm&stat=year'; ?>>Year</a></li>
                <li><input type="text" id="searchMLSStat" class="searchMLSStat"
                           placeholder="Put MLS number and press Enter"/></li>
            </ul>
        </div>
        <div class="stat">
            <div class="col-7-left">
                <div class="row">
                    <div id="chart_div"></div>
                </div>
                <div class="row margin-top-10">
                    <div id="chart_div_print"></div>
                </div>
                <div class="row margin-top-10">
                    <div id="chart_div_email"></div>
                </div>
                <div class="row margin-top-10">
                    <div id="chart_div_fav"></div>
                </div>
            </div>
            <div class="col-4-left">
                <div class="datagrid box-shadow">
                    <table>
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>MLS</th>
                            <th>Address</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $ia = 1;
                        $firstOut = false;
                        foreach ($getPopList->result as $popularL) {
                            if (isset($popularL->system_type)) {
                                $url = $sr->listing_to_url($popularL, $popularL->system_type);
                            } else {
                                $url = $sr->listing_to_url($popularL, $popularL->mtype);
                            }
                            ?>
                            <tr>
                                <td>
                                    <?php echo $ia++; ?>
                                </td>
                                <td>
                                    <a href="/wp-admin/admin.php?page=<?php echo $plugin_id; ?>-crm&mlssearch=<?php echo $popularL->mls_id; ?>"><?php echo $popularL->mls_id; ?>
                                        <i class="fa fa-line-chart" aria-hidden="true"></i></a>
                                </td>
                                <td>
                                    <?php echo '<a target="_blank" href="' . get_bloginfo('url') . $url . $popularL->coll_name . '">' . $popularL->address . '</a>'; ?>
                                </td>

                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php

    } elseif ($_GET['mlssearch']){
    $sql_getMLSStatID = "SELECT * FROM $sr->wpdb_sr_stat_mls WHERE mls=" . $_GET['mlssearch'];
    $res_getMLSstatID = $sr->wpdbSelectResults($sql_getMLSStatID);
    $sql_getMLSStat = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE stat_id=" . $res_getMLSstatID[0]->id . " ORDER BY time ASC";
    $res_mlsStat = $sr->wpdbSelectResults($sql_getMLSStat);
    $dd = date_create('2016-05-14 00:12:45');
    //    echo date_format($dd, 'Y-m-d');
    $resList = json_decode(json_encode($res_mlsStat), True);
    $query = array(
        'type' => $res_getMLSstatID[0]->mtype,
        'query' => array(
            'boolopr' => 'AND',
            'conditions' => array(
                array(
                    'field' => 'mls_id',
                    'operator' => '=',
                    'value' => $res_getMLSstatID[0]->mls
                )
            )
        ),
        'limit' => array(
            'range' => 1,
            'offset' => 0

        )
    );
    $getListInfo = $sr->api_request('get_listings', $query);
    foreach ($resList as $list) {
        $dd = date_create($list['time']);
        $sortByType[$list['mtype']][date_format($dd, 'Y-m-d')][] = $list;
    }
    ?>
        <!--                <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>-->
        <script>
            google.charts.load('current', {packages: ['corechart', 'line']});
            google.charts.setOnLoadCallback(drawBasic);

            function drawBasic() {

                var data_mla_viewed = new google.visualization.DataTable();
                data_mla_viewed.addColumn('string', 'X');
                data_mla_viewed.addColumn('number', 'Count');

                data_mla_viewed.addRows([
                    <?php
                    foreach ($sortByType['View'] as $key => $cc) {
                        echo '["' . $key . '", ' . count($sortByType['View'][$key]) . '],';
                    }
                    ?>
                ]);

                var data_mla_em = new google.visualization.DataTable();
                data_mla_em.addColumn('string', 'X');
                data_mla_em.addColumn('number', 'Count');

                data_mla_em.addRows([
                    <?php
                    foreach ($sortByType['Email'] as $key => $cc) {
                        echo '["' . $key . '", ' . count($sortByType['Email'][$key]) . '],';
                    }
                    ?>
                ]);


                var data_mla_printed = new google.visualization.DataTable();
                data_mla_printed.addColumn('string', 'X');
                data_mla_printed.addColumn('number', 'Count');

                data_mla_printed.addRows([
                    <?php
                    foreach ($sortByType['Print'] as $key => $cc) {
                        echo '["' . $key . '", ' . count($sortByType['Print'][$key]) . '],';
                    }
                    ?>
                ]);

                var data_mla_fav = new google.visualization.DataTable();
                data_mla_fav.addColumn('string', 'X');
                data_mla_fav.addColumn('number', 'Count');

                data_mla_fav.addRows([
                    <?php
                    foreach ($sortByType['Fav'] as $key => $cc) {
                        echo '["' . $key . '", ' . count($sortByType['Fav'][$key]) . '],';
                    }
                    ?>
                ]);

                var options_mls_viewed = {
                    title: 'Viewed activity',
                    hAxis: {
                        title: 'Time'
                    },
                    vAxis: {
                        title: 'Popularity'
                    }
                };
                var options_mls_printed = {
                    title: 'Printed activity',
                    hAxis: {
                        title: 'Time'
                    },
                    vAxis: {
                        title: 'Popularity'
                    }
                };
                var options_mls_em = {
                    title: 'Emailed activity',
                    hAxis: {
                        title: 'Time'
                    },
                    vAxis: {
                        title: 'Popularity'
                    }
                };
                var options_mls_fav = {
                    title: 'Favorited activity',
                    hAxis: {
                        title: 'Time'
                    },
                    vAxis: {
                        title: 'Popularity'
                    }
                };

                var chartMLS_p = new google.visualization.LineChart(document.getElementById('chart_div_pp'));
                chartMLS_p.draw(data_mla_printed, options_mls_printed);

                var chartMLS_v = new google.visualization.LineChart(document.getElementById('chart_div_dd'));
                chartMLS_v.draw(data_mla_viewed, options_mls_viewed);

                var chartMLS_e = new google.visualization.LineChart(document.getElementById('chart_div_em'));
                chartMLS_e.draw(data_mla_em, options_mls_em);

                var chartMLS_fav = new google.visualization.LineChart(document.getElementById('chart_div_fav'));
                chartMLS_fav.draw(data_mla_fav, options_mls_fav);
            }
            jQuery(document).ready(function () {
                jQuery('.searchMLSStat').keypress(function (e) {
                    if (e.which == 13) {
                        window.location = "/wp-admin/admin.php?page=<?php echo $sr->admin_id; ?>-crm&mlssearch=" + jQuery('.searchMLSStat').val();
                    }
                });
            });
        </script>
        <div class="header_sub_nav">
            <ul>
                <li><a href=<?php echo 'admin.php?page=' . $sr->admin_id . '-crm&stat=actual'; ?>>Actual</a>
                </li>
                <li><a href=<?php echo 'admin.php?page=' . $sr->admin_id . '-crm&stat=week'; ?>>7 Days</a></li>
                <li><a href=<?php echo 'admin.php?page=' . $sr->admin_id . '-crm&stat=month'; ?>>Month</a></li>
                <li><a href=<?php echo 'admin.php?page=' . $sr->admin_id . '-crm&stat=year'; ?>>Year</a></li>
                <li><input type="text" id="searchMLSStat" class="searchMLSStat"
                           placeholder="Put MLS number and press Enter"/></li>
            </ul>
        </div>
        <div class="stat row">
            <div class="col-7-left">
                <div class="byListings">
                    <h2> Stat info by listing MLS#: <?php echo $_GET['mlssearch']; ?></h2>
                    <div class="row">
                        <div id="chart_div_dd"></div>
                    </div>
                    <div class="row margin-top-10">
                        <div id="chart_div_pp"></div>
                    </div>
                    <div class="row margin-top-10">
                        <div id="chart_div_em"></div>
                    </div>
                    <div class="row margin-top-10">
                        <div id="chart_div_fav"></div>
                    </div>

                </div>
            </div>
            <div class="col-4-left">
                <div class="byListings">
                    <h2>Listing Info</h2>
                    <div class="datagrid">
                        <table>
                            <tbody>
                            <?php
                            if ($getListInfo->result[0]->mls_id) {
                                $server_name = $sr->feed->server_name;
                                $photo_dir = 'http://img.seorets.com/' . $server_name;
                                if (isset($getListInfo->result[0]->system_type)) {
                                    $url = $sr->listing_to_url($getListInfo->result[0], $getListInfo->result[0]->system_type);
                                } else {
                                    $url = $sr->listing_to_url($getListInfo->result[0], $getListInfo->result[0]->coll_name);
                                }
                                $output .= '<tr><td colspan="2"><a target="_blank" href="' . get_bloginfo('url') . $url . '">';
                                $output .= '<img src="' . $photo_dir . '/' . $getListInfo->result[0]->seo_url . '-' . $getListInfo->result[0]->mls_id . '-1.jpg" class="sr-listing-info"';
                                $output .= 'alt="' . htmlentities($getListInfo->result[0]->address) . ',' . htmlentities($getListInfo->result[0]->city) . ',' . htmlentities($getListInfo->result[0]->state) . ' ' . htmlentities($getListInfo->result[0]->zip) . '- 1"';
                                $output .= 'title="' . htmlentities($getListInfo->result[0]->address) . ',' . htmlentities($getListInfo->result[0]->city) . ',' . htmlentities($getListInfo->result[0]->state) . ' ' . htmlentities($getListInfo->result[0]->zip) . '- 1"/>';
                                $output .= '</a></td></tr>';
                                $output .= '<tr><td>Address:</td><td><a target="_blank" href="' . get_bloginfo('url') . $url . '">' . htmlentities($getListInfo->result[0]->address) . '</a></td></tr>';
                                $output .= '<tr><td>MLS #:</td><td>' . htmlentities($getListInfo->result[0]->mls_id) . '</td></tr>';
                                $output .= '<tr><td>City:</td><td>' . htmlentities($getListInfo->result[0]->city) . '</td></tr>';
                                $output .= '<tr><td>Price:</td><td>' . htmlentities($getListInfo->result[0]->price) . '</td></tr>';
                                $output .= '<tr><td>sqft:</td><td>' . htmlentities($getListInfo->result[0]->sqft) . '</td></tr>';
                                $output .= '<tr><td>Status:</td><td>' . htmlentities($getListInfo->result[0]->status) . '</td></tr>';
                                $output .= '<tr><td>Year Built:</td><td>' . htmlentities($getListInfo->result[0]->year_built) . '</td></tr>';
                                echo $output;
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    <?php
    }
    elseif ($_GET['profile'])  {
    date_default_timezone_set("US/Central");
    if ($_POST['notes-save']) {
    $noteInfo = [
        'id' => '',
        'note' => empty($_POST['notes']) ? ' ' : $_POST['notes'],
        'time' => date("Y-m-d H:i:s"),
        'user_id' => $_POST['userID']
    ];
    $sr->wpdbInsertRow('$sr->wpdb_sr_user_note', $noteInfo);
    ?>
        <script>
            location.reload();
        </script>
    <?
    }
    if ($_GET['deletenote']) {
        $deleteNote = [
            'id' => $_GET['deletenote']
        ];
        $sr->wpdbDeleteRow('$sr->wpdb_sr_user_note', $deleteNote);
        $url = 'admin.php?page=' . $sr->admin_id . '-crm&profile=' . $_GET['profile'];
        echo '<script>window.location = "' . $url . '";</script>';
    }


    $sql_getUser = "SELECT * FROM $sr->wpdb_sr_users WHERE id=" . $_GET['profile'];
    $res = $sr->wpdbSelectResults($sql_getUser);
    $user = $res[0];
    ?>
        <div class="header">
            <!--            <div class="searchBox"><i class="fa fa-search" aria-hidden="true"></i></div>-->
            <div class="backToList"><a href="admin.php?page=<?php echo $sr->admin_id; ?>-crm&user=show"><i
                        class="fa fa-arrow-left" aria-hidden="true"></i>Back to users</a></div>
        </div>
        <div class="userBox">
            <div class="row">
                <div class="col-4-left">
                    <div class="contactBox">
                        <h1>Contacts</h1>
                        <ul>
                            <li><i class="fa fa-phone" aria-hidden="true"></i> <?php echo $user->u_phone ?></li>
                            <li><i class="fa fa-envelope" aria-hidden="true"></i> <?php echo $user->u_email ?></li>
                        </ul>
                    </div>
                </div>
                <div class="col-7-left">
                    <div class="infoBox">
                        <div class="nameInfo">
                            <h1><?php echo $user->full_name; ?>(<?php echo $user->u_name; ?>)</h1>
                            <span>Sign Up: <?php echo $user->sign_up; ?></span>
                        </div>
                        <div id="activityTab">
                            <div class="header_nav_a">
                                <ul>
                                    <li><a href="#tabs-note">Notes</a></li>
                                    <li><a href="#tabs-fav">Favorites</a></li>
                                    <li><a href="#tabs-save">Saved Search</a></li>
                                </ul>
                            </div>
                            <div id="tabs-note">
                                <script src="//tinymce.cachefly.net/4.2/tinymce.min.js"></script>
                                <script>
                                    jQuery(document).ready(function () {
                                        jQuery.ajax({
                                            url: '<?php bloginfo('url') ?>/sr-ajax?action=get-last-view',
                                            type: 'post',
                                            data: {
                                                userID: <?php echo $user->id; ?>
                                            },
                                            success: function (response) {
//                                                console.log(response);
                                                jQuery('#recentlyViewed').html(response);
                                            }
                                        });
                                        jQuery.ajax({
                                            url: '<?php bloginfo('url') ?>/sr-ajax?action=get-fav',
                                            type: 'post',
                                            data: {
                                                userID: <?php echo $user->id; ?>

                                            },
                                            success: function (response) {
//                                                console.log(response);
                                                jQuery('.spiner').hide();
                                                jQuery('#tabListngs').html(response);
                                            }
                                        });
                                        jQuery.ajax({
                                            url: '<?php bloginfo('url') ?>/sr-ajax?action=get-email-alerts',
                                            type: 'post',
                                            data: {
                                                userID: <?php echo $user->id; ?>

                                            },
                                            success: function (response) {
//                                                console.log(response);
                                                jQuery('#tabs-4').html(response);
                                            }
                                        });
                                    });
                                </script>
                                <script>
                                    tinymce.init({
                                        selector: 'textarea',
                                        toolbar: [
                                            "undo redo  styleselect  bold italic  link image  alignleft  aligncenter  alignright  numlist "
                                        ],
                                        menubar: false,
                                        statusbar: false
                                    });</script>
                                <form action="" method="post">
                                    <input type="text" hidden name="userID" value="<?= $user->id; ?>">

                                    <textarea name="notes" id="notes"></textarea>
                                    <input type="submit" style="float: right; margin-top: 10px" name="notes-save"
                                           id="notes-save"
                                           class="button-primary" value="Add this note"/>
                                </form>
                                <div style="clear: both"></div>

                                <?php
                                $sql_getNotes = "SELECT * FROM $sr->wpdb_sr_user_note WHERE user_id=" . $user->id;
                                $res_user_notes = $sr->wpdbSelectResults($sql_getNotes);
                                foreach ($res_user_notes as $key => $note) {
                                    ?>
                                    <blockquote>
                                        <p>
                                            <?php echo $note->note; ?>
                                        </p>
                                        <footer><?php echo $note->time; ?> | <a
                                                href="admin.php?page=<?php echo $sr->admin_id; ?>-crm&profile=<?= $user->id; ?>&deletenote=<?= $note->id ?>">Delete</a>
                                        </footer>
                                    </blockquote>
                                    <?
                                }
                                ?>
                            </div>
                            <div id="tabs-fav">
                                <div class="spiner">
                                    <i class="fa fa-refresh fa-spin fa-3x fa-fw" aria-hidden="true"></i>
                                    <span class="sr-only">Refreshing...</span>
                                </div>
                                <div class="datagrid">
                                    <table>
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Photo</th>
                                            <th>Address</th>
                                            <th>MLS</th>
                                            <th>Price</th>
                                            <th>Last View</th>
                                            <th>Add Time</th>
                                        </tr>
                                        </thead>
                                        <!--                                        <tfoot>-->
                                        <!--                                        <tr>-->
                                        <!--                                            <td colspan="7">-->
                                        <!--                                                <div id="paging">-->
                                        <!--                                                    <ul>-->
                                        <!--                                                        <li><a href="#"><span>Previous</span></a></li>-->
                                        <!--                                                        <li><a href="#" class="active"><span>1</span></a></li>-->
                                        <!--                                                        <li><a href="#"><span>2</span></a></li>-->
                                        <!--                                                        <li><a href="#"><span>3</span></a></li>-->
                                        <!--                                                        <li><a href="#"><span>4</span></a></li>-->
                                        <!--                                                        <li><a href="#"><span>5</span></a></li>-->
                                        <!--                                                        <li><a href="#"><span>Next</span></a></li>-->
                                        <!--                                                    </ul>-->
                                        <!--                                                </div>-->
                                        <!--                                        </tr>-->
                                        <!--                                        </tfoot>-->
                                        <tbody id="tabListngs">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="tabs-save">
                                <div class="datagrid">
                                    <table>
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Fields</th>
                                            <th>Add Times</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <!--                                        <tfoot>-->
                                        <!--                                        <tr>-->
                                        <!--                                            <td colspan="4">-->
                                        <!--                                                <div id="paging">-->
                                        <!--                                                    <ul>-->
                                        <!--                                                        <li><a href="#"><span>Previous</span></a></li>-->
                                        <!--                                                        <li><a href="#" class="active"><span>1</span></a></li>-->
                                        <!--                                                        <li><a href="#"><span>2</span></a></li>-->
                                        <!--                                                        <li><a href="#"><span>3</span></a></li>-->
                                        <!--                                                        <li><a href="#"><span>4</span></a></li>-->
                                        <!--                                                        <li><a href="#"><span>5</span></a></li>-->
                                        <!--                                                        <li><a href="#"><span>Next</span></a></li>-->
                                        <!--                                                    </ul>-->
                                        <!--                                                </div>-->
                                        <!--                                        </tr>-->
                                        <!--                                        </tfoot>-->
                                        <tbody id="tabListngs">
                                        <?php
                                        $sql_getSaveSearch = "SELECT * FROM $sr->wpdb_sr_savesearch WHERE user_id=" . $user->id;
                                        $saveSearch = $sr->wpdbSelectResults($sql_getSaveSearch);
                                        $i = 1;
                                        foreach ($saveSearch as $search) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo $i++; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $link = json_decode(base64_decode($search->base64link), true);
                                                    foreach ($link['q']['c'] as $c) {
                                                        if ($c['c']) {
                                                            foreach ($c['c'] as $l) {
                                                                ?>
                                                                <div class="row">
                                                                    <div class="col-2-left">
                                                                        <?
                                                                        $minMax = str_replace('=', '', $l['o']);
                                                                        if ($minMax != "" && $minMax == ">") {
                                                                            echo 'Min ' . $l['f'];
                                                                        } elseif ($minMax != "" && $minMax == "<") {
                                                                            echo 'Max ' . $l['f'];
                                                                        } else {
                                                                            echo $l['f'];
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                    <div class="col-2-left">
                                                                        <?= $l['v']; ?>
                                                                    </div>
                                                                </div>
                                                                <?
                                                            }
                                                        }
                                                        ?>
                                                        <div class="row">
                                                            <div class="col-2-left">
                                                                <?
                                                                $minMax2 = str_replace('=', '', $c['o']);
                                                                if ($minMax2 != "" && $minMax2 == ">") {
                                                                    echo 'Min ' . $c['f'];
                                                                } elseif ($minMax2 != "" && $minMax2 == "<") {
                                                                    echo 'Max ' . $c['f'];
                                                                } else {
                                                                    echo $c['f'];
                                                                }
                                                                ?>
                                                            </div>
                                                            <div class="col-2-left">
                                                                <?= $c['v']; ?>
                                                            </div>
                                                        </div>
                                                        <?
                                                    } ?>
                                                </td>
                                                <td>
                                                    <?php echo $search->add_times; ?>
                                                </td>
                                                <td>
                                                    <a target="_blank" href="/sr-search/?<?= $search->base64link; ?>">Repeat
                                                        Search</a>
                                                </td>

                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    <?php } else {
    $sql_getAllUsers = "SELECT * FROM $sr->wpdb_sr_users ORDER BY sign_up DESC";
    $res_getAllUsers = $sr->wpdbSelectResults($sql_getAllUsers);

    $sql_getAllViewedListings = "SELECT * FROM $sr->wpdb_sr_stat_mls";
    $res_getAllViewedListings = $sr->wpdbSelectResultsCount($sql_getAllViewedListings);

    $sql_getCountView = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE mtype='View'";
    $res_countView = $sr->wpdbSelectResultsCount($sql_getCountView);

    $sql_getCountPrint = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE mtype='Print'";
    $res_countPrint = $sr->wpdbSelectResultsCount($sql_getCountPrint);

    $sql_getCountEmail = "SELECT * FROM $sr->wpdb_sr_stat_option WHERE mtype='Email'";
    $res_countEmail = $sr->wpdbSelectResultsCount($sql_getCountEmail);

    $sql_getCountFav = "SELECT * FROM $sr->wpdb_sr_favorites";
    $res_countFav = $sr->wpdbSelectResultsCount($sql_getCountFav);

    $sql_getLastActivity = "SELECT * FROM $sr->wpdb_sr_stat_option ORDER BY time DESC";
    $res_getLastActivity = $sr->wpdbSelectResults($sql_getLastActivity);


    if (count($res_getLastActivity) >= 10) {
        $ccActivity = 10;
    } else {
        $ccActivity = count($res_getLastActivity) - 1;
    }
    for ($tt = 0; $tt <= $ccActivity; $tt++) {
        $sql_getCurrentActiveListing = "SELECT * FROM $sr->wpdb_sr_stat_mls WHERE id=" . $res_getLastActivity[$tt]->stat_id;
        $res_getCurrentUserActivity = $sr->wpdbSelectResults($sql_getCurrentActiveListing);
        $x = $res_getLastActivity[$tt]->mtype;
        switch ($x) {
            case 'Fav':
                $actv = 'Add to Favorite';
                break;
            case 'Email':
                $actv = 'Add to Subscribe';
                break;
            case 'Print':
                $actv = 'Printed info';
                break;
            case 'View':
                $actv = 'Was Viewed';
                break;

        }
        $activity_array[] = array(
            'mls' => $res_getCurrentUserActivity[0]->mls,
            'type' => $res_getCurrentUserActivity[0]->mtype,
            'active_type' => $actv,
            'time' => $res_getLastActivity[$tt]->time
        );
    }
    //    echo "<pre>";
    //    print_r($activity_array);
    //    echo "</pre>";

    ?>
        <div class="row">
            <div class="col-1-left">
                <div class="bg-blue">
                    <h1><?php echo count($res_getAllUsers); ?></h1>
                    <span>Users</span>
                </div>
            </div>
            <div class="col-1-left">
                <div class="bg-viol">
                    <h1><?php echo $res_getAllViewedListings; ?></h1>
                    <span>Total Viewed Listings</span>
                </div>
            </div>
            <div class="col-1-left">
                <div class="bg-white">
                    <h1><?php echo $res_countView; ?></h1>
                    <span>Count Viewed Listings</span>
                </div>
            </div>
            <div class="col-1-left">
                <div class="bg-white">
                    <h1><?php echo $res_countPrint; ?></h1>
                    <span>Count Printed Listings</span>
                </div>
            </div>
            <div class="col-1-left">
                <div class="bg-white">
                    <h1><?php echo $res_countEmail; ?></h1>
                    <span>Count Subscribes Listings</span>
                </div>
            </div>
            <div class="col-1-left">
                <div class="bg-white">
                    <h1><?php echo $res_countFav; ?></h1>
                    <span>Count Favorite Listings</span>
                </div>
            </div>
        </div>
        <div class="row margin-top-25">
            <div class="col-4-left">
                <div class="datagrid box-shadow">
                    <h2>Last Activity</h2>
                    <table>
                        <table>
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>MLS</th>
                                <th>Activity Type</th>
                                <th>Time</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $ii = 0;
                            foreach ($activity_array as $act) {
                                echo '<tr><td>' . $ii++ . '</td><td><a href="admin.php?page=' . $plugin_id . '-crm&mlssearch=' . $act['mls'] . '">' . $act['mls'] . '<i class="fa fa-line-chart" aria-hidden="true"></i></a></td><td>' . $act['active_type'] . '</td><td>' . $act['time'] . '</td></tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                </div>
            </div>
            <div class="col-4-left">
                <div class="datagrid box-shadow">
                    <h2>Last Register Users</h2>

                    <table>
                        <table>
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Registration Time</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (count($res_getAllUsers) >= 11) {
                                $ccUsers = 11;
                            } else {
                                $ccUsers = count($res_getAllUsers);
                            }
                            for ($ii = 0; $ii <= $ccUsers - 1; $ii++) {
                                echo '<tr><td>' . $ii . '</td><td><a href="admin.php?page=' . $plugin_id . '-crm&profile=' . $res_getAllUsers[$ii]->id . '">' . $res_getAllUsers[$ii]->u_name . '</a></td><td>' . $res_getAllUsers[$ii]->sign_up . '</td></tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                </div>


            </div>
        </div>

        <?php

    }
    ?>

</div>
