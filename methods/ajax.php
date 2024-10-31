<?php
error_reporting(0);
header("Content-Type: application/json");
session_start();
global $seo_rets_plugin;
$sr = $seo_rets_plugin;

$_POST = stripslashes_deep($_POST);

function require_auth()
{
    if (!current_user_can('activate_plugins')) {
        echo json_encode(array(
            'error' => 1,
            'mes' => 'Not authorized.'
        ));
        exit;
    }
}

$response = array(
    'error' => 1,
    'mes' => 'Action not found.'
);
switch ($_GET['action']) {
    case "getType" :
        if (isset($_POST['type'])) {
            foreach ($sr->metadata as $key => $val) {
                if ($sr->is_type_hidden($key)) {
                    continue;
                }
                $typeSet[] = $val;
            }
            $response = $typeSet;
        }
        break;
    case "addToCompare" :
        if (isset($_POST['type'])) {
            function recursive_array_search($needle, $haystack)
            {
                $ar = array();
                foreach ($haystack as $key => $value) {
                    $current_key = $key;
                    if (preg_match("/$needle/i", $value) OR (is_array($value) && recursive_array_search($needle, $value) !== false)) {
                        $ar[] = $current_key;
                    }
                }
                if (!empty($ar)) {
                    return true;
                }
                return false;
            }

            function recursive_array_search2($needle, $haystack)
            {
//                $ar = array();
                foreach ($haystack as $key => $value) {
                    $current_key = $key;
                    if (preg_match("/$needle/i", $value) OR (is_array($value) && recursive_array_search($needle, $value) !== false)) {
                        $ar = $current_key;
                    }
                }
                if (!empty($ar)) {
                    return $ar;
                }
                return false;
            }

            if (!recursive_array_search($_POST['mls'], $_SESSION['compare'])) {
                $_SESSION['compare'][] = array(
                    'mls' => $_POST['mls'],
                    'type' => $_POST['type']
                );
                $response = array(
                    'count' => count($_SESSION['compare']),
                    'current' => recursive_array_search2($_POST['mls'], $_SESSION['compare'])
                );
            } else {
                $response = array(
                    'error' => 0,
                    'current' => recursive_array_search2($_POST['mls'], $_SESSION['compare'])
                );
            }
        }
        break;
    case "getCompareListings" :
        $response = $_SESSION['compare'];
        break;
    case "getCompareListID" :
        if (isset($_POST['type'])) {
            function recursive_array_search($needle, $haystack)
            {
                $ar = array();
                foreach ($haystack as $key => $value) {
                    $current_key = $key;
                    if (preg_match("/$needle/i", $value) OR (is_array($value) && recursive_array_search($needle, $value) !== false)) {
                        $ar[] = $current_key;
                    }
                }
                if (!empty($ar)) {
                    return true;
                }
                return false;
            }

            function recursive_array_search2($needle, $haystack)
            {
//                $ar = array();
                foreach ($haystack as $key => $value) {
                    $current_key = $key;
                    if (preg_match("/$needle/i", $value) OR (is_array($value) && recursive_array_search($needle, $value) !== false)) {
                        $ar = $current_key;
                    }
                }
                if (!empty($ar)) {
                    return $ar;
                }
                return false;
            }

            if (!recursive_array_search($_POST['mls'], $_SESSION['compare'])) {
                $response = array(
                    'count' => 0,
                    'current' => 'No'
                );
            } else {
                $response = array(
                    'error' => 0,
                    'current' => 'Yes'
                );
            }
        }
        break;
    case "removeCompareListings" :
//        if (isset($_POST['type'])) {
//        unset($_SESSION['compare']);
        foreach ($_POST['mls'] as $m) {
            unset($_SESSION['compare'][$m]);
        }
        $response = $_SESSION['compare'];
//        }
        break;
    case "fb-appid" :
        require_auth();
        if ($_POST['appid']) {
            update_option('fbid', $_POST['appid']);
            $response = array(
                'error' => 0,
                'mes' => 'App Id save.'
            );
        } else {
            update_option('fbid', false);
            $response = array(
                'error' => 0,
                'mes' => 'App Id save.'
            );
        }
        break;
    case "google-appid" :
        require_auth();
        if ($_POST['appid']) {
            update_option('googleid', $_POST['appid']);
            $response = array(
                'error' => 0,
                'mes' => 'App Id save.'
            );
        } else {
            update_option('googleid', false);
            $response = array(
                'error' => 0,
                'mes' => 'App Id save.'
            );
        }
        break;
    case "google-users" :
        $users = get_option('sr_users');
        $newUser = array();
        $newUser = array(
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'google_id' => $_POST['uid'],
            'full_name' => $_POST['fullname']
        );
        function recursive_array_search($needle, $haystack)
        {
            foreach ($haystack as $key => $value) {
                $current_key = $key;
                if ($needle === $value OR (is_array($value) && recursive_array_search($needle, $value) !== false)) {
                    return $current_key;
                }
            }
            return false;
        }

        $key = recursive_array_search($_POST['email'], $users);
        if ($key !== false) {
            date_default_timezone_set("US/Central");
            $sr->set_session_data('user_index', $key);
            $users[$key]['last_login'] = date("Y-m-d H:i:s");
            update_option('sr_users', $users);

            $response = array(
                'error' => 0,
                'mes' => 'Action found.'
            );
        } else {
            $users[] = $newUser;
            update_option('sr_users', $users);
            $response = array(
                'error' => 0,
                'mes' => 'User Add.'
            );
            $key = recursive_array_search($_POST['email'], $users);
            $sr->set_session_data('user_index', $key);
        }
        break;
    case "fb-users" :
        $users = get_option('sr_users');
        $newUser = array();
        $newUser = array(
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'fb_id' => $_POST['uid'],
            'full_name' => $_POST['fullname']
        );
        function recursive_array_search($needle, $haystack)
        {
            foreach ($haystack as $key => $value) {
                $current_key = $key;
                if ($needle === $value OR (is_array($value) && recursive_array_search($needle, $value) !== false)) {
                    return $current_key;
                }
            }
            return false;
        }

        $key = recursive_array_search($_POST['email'], $users);
        if ($key !== false) {
            date_default_timezone_set("US/Central");
            $sr->set_session_data('user_index', $key);
            $users[$key]['last_login'] = date("Y-m-d H:i:s");
            update_option('sr_users', $users);

            $response = array(
                'error' => 0,
                'mes' => 'Action found.'
            );
        } else {
            $users[] = $newUser;
            update_option('sr_users', $users);
            $response = array(
                'error' => 0,
                'mes' => 'User Add.'
            );
            $key = recursive_array_search($_POST['email'], $users);
            $sr->set_session_data('user_index', $key);
        }
        break;
    case "user-logout" :

        require_auth();
        if ($_POST['test']) {
            $sr->set_session_data('user_index', false);
            $response = array(
                'error' => 0,
                'mes' => 'Logout.'
            );
        } else {
            $response = array(
                'error' => 0,
                'mes' => 'Logout now work.'
            );
        }
        break;
    case "get-fav" :
        $server_name = $sr->feed->server_name;
        $photo_dir = 'http://img.seorets.com/' . $server_name;
        require_auth();
        $users = get_option('sr_users');
        $sql_getFav = "SELECT * FROM $sr->wpdb_sr_favorites WHERE user_id=" . $_POST['userID'];
        $favorites = $sr->wpdbSelectResults($sql_getFav);
        if ($favorites) {
            $i = 1;
            foreach ($favorites as $fav) {
                $output = '';
                $mergequeries = array();
                $mergequeries[] = array(
                    'type' => $fav->mtype,
                    'query' => array(
                        'boolopr' => 'AND',
                        'conditions' => array(
                            array(
                                'field' => 'mls_id',
                                'operator' => '=',
                                'value' => $fav->mls
                            )
                        )
                    ),
                    'limit' => array(
                        'range' => 1,
                        'offset' => 0

                    )
                );
                $request = array(
                    'query' => $mergequeries

                );
                $l = $sr->api_request('get_listings', $request);
                if (!isset($l->result[0]->address) || $l->result[0]->address == "") {
                    $l->result[0]->address = "N/A";
                }
                if (!isset($l->result[0]->city) || $l->result[0]->city == "") {
                    $l->result[0]->city = "N/A";
                } else {
                    $l->result[0]->city2 = preg_replace('/\s/', '+', $l->result[0]->city);
                }

                if (!isset($l->result[0]->subdivision) || $l->result[0]->subdivision == "") {
                    $l->result[0]->subdivision = "N/A";
                } else {
                    $l->result[0]->subdivision2 = preg_replace('/\s/', '+', $l->result[0]->subdivision);
                }

                if (isset($l->result[0]->system_type)) {
                    $url = $sr->listing_to_url($l->result[0], $l->result[0]->system_type);
                } else {
                    $url = $sr->listing_to_url($l->result[0], $fav->mtype);
                }
                if ($l->result[0]->mls_id) {
                    $output .= '<tr>';
                    $output .= '<td>' . $i++ . '</td>';
                    $output .= '<td><a target="_blank" href="' . get_bloginfo('url') . $url . '">';
                    $output .= '<img src="' . $photo_dir . '/' . $l->result[0]->seo_url . '-' . $l->result[0]->mls_id . '-1.jpg" class="sr-listing-photoss"';
                    $output .= 'alt="' . htmlentities($l->result[0]->address) . ',' . htmlentities($l->result[0]->city) . ',' . htmlentities($l->result[0]->state) . ' ' . htmlentities($l->result[0]->zip) . '- 1"';
                    $output .= 'title="' . htmlentities($l->result[0]->address) . ',' . htmlentities($l->result[0]->city) . ',' . htmlentities($l->result[0]->state) . ' ' . htmlentities($l->result[0]->zip) . '- 1"/>';
                    $output .= '</a></td>';
                    $output .= '<td><a target="_blank" href="' . get_bloginfo('url') . $url . '">' . htmlentities($l->result[0]->address) . '</a></td>';
                    $output .= '<td>' . $l->result[0]->mls_id . '</td>';
                    $output .= '<td>' . number_format($l->result[0]->price, 0) . '</td>';
                    $output .= '<div class="sr-listing-descr"><div class="info"><span>User view this listing <b>' . $fav->views . '</b> time</span><br/>';
                    $output .= '<td>' . $fav->last_views . '</td>';
                    $output .= '<td>' . $fav->add_times . '</td>';
                    $output .= '</tr>';
                    $lis[] = $output;
                }
            }
            $response = $lis;
        }

        break;
    case "get-email-alerts" :
        require_auth();
        $users = get_option('sr_users');
        if (isset($_POST['userID'])) {
//            $sub = [];
            foreach ($users[$_POST['userID']]['subscrib'] as $subscreiber) {
                $output = '';
                $output .= '<div class="row margin-top-10">';
                $output .= '<div class="col-3-left">';
                $output .= '<span> ' . $subscreiber['email'] . ' (' . $subscreiber['name'] . ') </span></div>';
                $output .= '<div class="col-3-left">';
                $output .= '<span> Type: ' . $subscreiber['type'] . '</span></div>';
                $output .= '<div class="col-3-left">';
                $output .= '<span> Field: ' . $subscreiber['conditions'][0]['field'] . '</span><br />';
                $output .= '<span> Value: ' . $subscreiber['conditions'][0]['value'] . '</span><br />';
                $output .= '</div>';
                $output .= '</div>';
                $sub[] = $output;
            }
            $response = $sub;
        }
        break;
    case "get-last-view":
        require_auth();
        $users = get_option('sr_users');

        if (isset($_POST['userID'])) {
            $val = $users[$_POST['userID']]['other'];
            $val = array_reverse($val);
//            $resp = [];
            for ($i = 0; $i <= 5; $i++) {
                $mergequeries = array();
                $mergequeries[] = array(
                    'type' => $val[$i]['type'],
                    'query' => array(
                        'boolopr' => 'AND',
                        'conditions' => array(
                            array(
                                'field' => 'mls_id',
                                'operator' => '=',
                                'value' => $val[$i]['mls']
                            )
                        )
                    ),
                    'limit' => array(
                        'range' => 1,
                        'offset' => 0

                    )
                );
                $request = array(
                    'query' => $mergequeries

                );

                $l = $sr->api_request('get_listings', $request);
                if ($l->result[0]->mls_id) {
                    if (isset($l->result[0]->system_type)) {
                        $url = $sr->listing_to_url($l->result[0], $l->result[0]->system_type);
                    } else {
                        $url = $sr->listing_to_url($l->result[0], $val[$i]['type']);
                    }
                    $resp[] = '<p class="sr-listing-title">
                        <a target="_blank" href="' . get_bloginfo('url') . $url . '">' . htmlentities($l->result[0]->address) . '</a></p>';

                }
            }


        } else {
            $response = array(
                'error' => 1,
                'mes' => 'Something going wrong.'
            );
        }
        $response = $resp;
        break;
    case "order-save":
        require_auth();
        if (isset($_POST['order'])) {
            update_option("sr_listingsOrder", $_POST['order']);
        } else {
            update_option("sr_listingsOrder", 'none');
        }
        $response = array(
            'error' => 0,
            'mes' => 'Sort option updated.'
        );
        break;
    case "deleteShortcode":
        $shortcode = get_option('sr-shortcode');
        if (isset($_POST['shortcode'])) {
//            delete_option('sr-shortcode');
            $shortcode = array_flip($shortcode);
            unset ($shortcode[base64_encode($_POST['shortcode'])]);
            $shortcode = array_flip($shortcode);
            update_option('sr-shortcode', $shortcode);
            $response = array(
                'error' => 0,
                'mes' => 'Deleted.'
            );
        }
        break;
    case "getAllShortcode":
        $shortcode = get_option('sr-shortcode');
        foreach ($shortcode as $sh) {
            $short[] = base64_decode($sh);
        }
        if (isset($_POST['type'])) {
//            $response = $shortcode;
            $response = array_reduce($short, function ($a, $b) {
                static $stored = array();

                $hash = md5(serialize($b));

                if (!in_array($hash, $stored)) {
                    $stored[] = $hash;
                    $a[] = $b;
                }

                return $a;
            }, array());
        }
        break;
    case "saveShortcode":
        $shortcode = get_option('sr-shortcode');
        if (isset($_POST['shortcode'])) {
            $response = $_POST['shortcode'];
            $shortcode[] = base64_encode($_POST['shortcode']);
            update_option('sr-shortcode', $shortcode);
            $response = 'Shortcode is saved now';
        }
        break;
    case "getFields":
        if (isset($_POST['type'])) {
            $response = $_POST['type'];
            $fields = $sr->metadata->$_POST['type']->fields;
//            $filedSet = array();
            foreach ($sr->metadata->$_POST['type']->fields as $key => $field) {
                if ($sr->metadata->$_POST['type']->fields->$key->values != "") {
                    $filedSet[] = $key;
                }
            }
            sort($filedSet);
            reset($filedSet);
            $response = $filedSet;
        }
        break;
    case "getFieldsValue":
        if (isset($_POST['type']) && isset($_POST['fields'])) {
            $response = $_POST['type'];
            $fields = $sr->metadata->$_POST['type']->fields->$_POST['fields']->values;
//            $filedSet = array();
            foreach ($sr->metadata->$_POST['type']->fields->$_POST['fields']->values as $field) {
                $filedSet[] = $field;
            }
            sort($filedSet);
            reset($filedSet);
            $response = $filedSet;
        }
        break;
    case "getOnPolygon" :
        $conditions = array();

        if (isset($_POST['fil']) && $_POST['fil'] != "") {
            foreach ($_POST['fil'] as $field) {
                foreach ($field as $key => $f) {
                    $conditions[] = array(
                        'field' => $key,
                        'operator' => 'LIKE',
                        'loose' => true,
                        'value' => $f
                    );
                }
            }
        }

        if (isset($_POST['city']) && $_POST['city'] != "") {
            $conditions[] = array(
                'field' => 'city',
                'operator' => 'LIKE',
                'loose' => true,
                'value' => $_POST['city']
            );
        }

        if (isset($_POST['zip']) && $_POST['zip'] != "") {
            $conditions[] = array(
                'field' => 'zip',
                'operator' => 'LIKE',
                'loose' => true,
                'value' => $_POST['zip']
            );
        }

        if (isset($_POST['waterview']) && $_POST['waterview'] != "") {
            if (is_array($_POST['waterview'])) {
                foreach ($_POST['waterview'] as $value) {
                    $conditions[] = array(
                        'field' => 'waterview',
                        'operator' => 'LIKE',
                        'loose' => true,
                        'value' => $value
                    );
                }
            } else {
                $conditions[] = array(
                    'field' => 'waterview',
                    'operator' => 'LIKE',
                    'loose' => true,
                    'value' => $_POST['waterview']
                );
            }

        }

        if (isset($_POST['waterfront']) && $_POST['waterfront'] != "") {
            if (is_array($_POST['waterfront'])) {
                foreach ($_POST['waterfront'] as $value) {
                    $conditions[] = array(
                        'field' => 'waterfront',
                        'operator' => 'LIKE',
                        'loose' => true,
                        'value' => $value
                    );
                }
            } else {
                $conditions[] = array(
                    'field' => 'waterfront',
                    'operator' => 'LIKE',
                    'loose' => true,
                    'value' => $_POST['waterfront']
                );
            }

        }

        if (isset($_POST['price-low']) && $_POST['price-low'] != "") {
            $conditions[] = array(
                'field' => 'price',
                'operator' => '>=',
                'value' => $_POST['price-low']
            );
        }

        if (isset($_POST['price-high']) && $_POST['price-high'] != "") {
            $conditions[] = array(
                'field' => 'price',
                'operator' => '<=',
                'value' => $_POST['price-high']
            );
        }

        if (isset($_POST['bedrooms-low']) && $_POST['bedrooms-low'] != "") {
            $conditions[] = array(
                'field' => 'bedrooms',
                'operator' => '>=',
                'value' => $_POST['bedrooms-low']
            );
        }

        if (isset($_POST['bedrooms-high']) && $_POST['bedrooms-high'] != "") {
            $conditions[] = array(
                'field' => 'bedrooms',
                'operator' => '<=',
                'value' => $_POST['bedrooms-high']
            );
        }

        if (isset($_POST['baths-low']) && $_POST['baths-low'] != "") {
            $conditions[] = array(
                'field' => 'baths',
                'operator' => '>=',
                'value' => $_POST['baths-low']
            );
        }

        if (isset($_POST['baths-high']) && $_POST['baths-high'] != "") {
            $conditions[] = array(
                'field' => 'baths',
                'operator' => '<=',
                'value' => $_POST['baths-high']
            );
        }
        if (isset($_POST['subdivision']) && $_POST['subdivision'] != "") {
            $conditions[] = array(
                'field' => 'subdivision',
                'operator' => 'LIKE',
                'loose' => true,
                'value' => $_POST['subdivision']
            );
        }
        if (isset($_POST['proj_name']) && $_POST['proj_name'] != "") {
            $conditions[] = array(
                'field' => 'proj_name',
                'operator' => 'LIKE',
                'loose' => true,
                'value' => $_POST['proj_name']
            );
        }
        if (isset($_POST['area']) && $_POST['area'] != "") {
            $conditions[] = array(
                'field' => 'area',
                'operator' => 'LIKE',
                'loose' => true,
                'value' => $_POST['area']
            );
        }


        $order = isset($_POST['order']) ? explode(":", $_POST['order']) : array();

        if (count($order) != 2) {
            $order = array(
                array(
                    'field' => 'price',
                    'order' => 'DESC'
                )
            );
        } else {
            $save = $order;
            $order = array(
                array(
                    'field' => $save[0],
                    'order' => $save[1]
                )
            );
        }

        $prioritization = get_option('sr_prioritization');
        $prioritization = ($prioritization === false) ? array() : $prioritization;
        $only = isset($_POST['onlymylistings']) && strtolower($_POST['onlymylistings']) != "no";

        $perpage = isset($_POST['limit']) ? ((int)$_POST['limit'] - 1) : 25;
        if (isset($_POST['polygon']) && $_POST['polygon'] != "") {
            $conditions[] = array(
                'field' => 'pos',
                'operator' => 'geo',
                'value' => $_POST['polygon']
            );
        }

        $query = $this->prioritize(array(
            'type' => $_POST['type'],
            'query' => array(
                'boolopr' => 'AND',
//                'polygon' => $_POST['polygon'],
                'conditions' => $conditions
            ),
            'order' => $order
        ), $prioritization);


        if ($only && count($prioritization) > 0) {
            array_pop($query);
        }

        $response = $sr->api_request('get_listings', array(
            'query' => $query,
            'type' => $_POST['type'],
            'limit' => array(
                'range' => 100
            )
        ));
        foreach ($response->result as $index => $listing) {
            $response->result[$index]->url = $sr->listing_to_url($listing, $_POST['type']);
        }
        break;
    case "getOnType" :
        if (isset($_POST['type'])) {
            $response = $_POST['type'];

            $cities = $sr->metadata->$_POST['type']->fields->city->values;
            sort($cities);
            $response = $cities;
        }
        if (isset($_POST['areas'])) {
            $response = $_POST['areas'];

            $areas = $sr->metadata->$_POST['areas']->fields->area->values;
            sort($areas);
            $response = $areas;
        }
        if (isset($_POST['subd'])) {
            $response = $_POST['subd'];

            $subdivision = $sr->metadata->$_POST['subd']->fields->subdivision->values;
            sort($subdivision);
            $response = $subdivision;
        }
        if (isset($_POST['subt'])) {
            $response = $_POST['subt'];

            $sub_types = $sr->metadata->$_POST['subt']->fields->sub_type->values;
            sort($sub_types);
            $response = $sub_types;
        }
        break;
    case "get-listings-geocoord":
        $get_vars = json_decode(base64_decode(urldecode($_GET['conditions'])));

        if (is_array($get_vars->q->c)) {
            $get_vars->p = isset($get_vars->p) ? intval($get_vars->p) : 10; // Default to 10 per page if request doesn't specify
            $get_vars->g = isset($get_vars->g) ? intval($get_vars->g) : 1;

            // Start recursive function to build a request to be sent to the api for search
            $conditions = $sr->convert_to_api_conditions($get_vars->q);

            $prioritization = get_option('sr_prioritization');
            $prioritization = ($prioritization === false) ? array() : $prioritization;


            $query = array(
                "type" => $get_vars->t,
                "query" => $conditions,
            );

            if (isset($get_vars->o) && is_array($get_vars->o)) {
                $query["order"] = array();

                foreach ($get_vars->o as $order) {
                    $query["order"][] = array(
                        "field" => $order->f,
                        "order" => $order->o == 0 ? "DESC" : "ASC"
                    );
                }
            }

            $newquery = $sr->prioritize($query, $prioritization);

            $resp = $sr->api_request("get_listings", array(
                'query' => $newquery,
                'limit' => array(
                    'range' => $get_vars->p,
                    'offset' => ($get_vars->g - 1) * $get_vars->p
                )
            ));
            $response = $resp->result;
            foreach ($response as $index => $listing) {
                $response[$index]->url = $sr->listing_to_url($listing, $get_vars->t);
            }
//            $response['listings_count'] = $resp->count;

        } else {
            $response = array(
                'error' => 1,
                'mes' => 'Error: Invalid Request'
            );
        }

        break;
    case
    "get-listings-features":
        $get_vars = json_decode(base64_decode(urldecode($_GET['conditions'])));

        if (is_array($get_vars->q->c)) {
            $get_vars->p = isset($get_vars->p) ? intval($get_vars->p) : 10; // Default to 10 per page if request doesn't specify
            $get_vars->g = isset($get_vars->g) ? intval($get_vars->g) : 1;

            // Start recursive function to build a request to be sent to the api for search
            $conditions = $sr->convert_to_api_conditions($get_vars->q);

            $prioritization = get_option('sr_prioritization');
            $prioritization = ($prioritization === false) ? array() : $prioritization;


            $query = array(
                "type" => $get_vars->t,
                "query" => $conditions,
            );

            if (isset($get_vars->o) && is_array($get_vars->o)) {
                $query["order"] = array();

                foreach ($get_vars->o as $order) {
                    $query["order"][] = array(
                        "field" => $order->f,
                        "order" => $order->o == 0 ? "DESC" : "ASC"
                    );
                }
            }

            $newquery = $sr->prioritize($query, $prioritization);
            $onco = $this->api_request("get_listings", array(
                'query' => $newquery,
                'limit' => array(
                    'range' => $get_vars->p,
                    'offset' => 0
                ),
                'fields' => array(
                    'onlycount' => 1
                )

            ));
            $resp = $sr->api_request("get_listings", array(
                'query' => $newquery,
                'limit' => array(
                    'range' => $onco->count,
                    'offset' => 0
                )
            ));
            $listings = $resp->result;
            foreach ($listings as $key => $l) {
                foreach ($l->features as $f) {
                    if (!($f == "Yes" || $f == "No" || $f == "None")) {
                        $features_list[$f] = $f;
                    }
                }
            }
            asort($features_list);
            $response['features'] = $features_list;
            $response['listings_count'] = $resp->count;

        } else {
            $response = array(
                'error' => 1,
                'mes' => 'Error: Invalid Request'
            );
        }

        break;
    case "get-listings-predictive":
        $get_vars = json_decode(base64_decode($_GET['conditions']));

        if (is_array($get_vars->q->c)) {
            $get_vars->p = isset($get_vars->p) ? intval($get_vars->p) : 10; // Default to 10 per page if request doesn't specify
            $get_vars->g = isset($get_vars->g) ? intval($get_vars->g) : 1;

            // Start recursive function to build a request to be sent to the api for search
            $conditions = $sr->convert_to_api_conditions($get_vars->q);
            $conditions['boolopr'] = 'OR';
            
            foreach ( $conditions as $key=>$condition) {
                if (isset($conditions[$key]['loose'])) {
                    $conditions[$key]['loose'] = true;
                }
            }

            $prioritization = array();

            $query = array(
                "type" => $get_vars->t,
                "query" => $conditions,
            );

            if (isset($get_vars->o) && is_array($get_vars->o)) {
                $query["order"] = array();

                foreach ($get_vars->o as $order) {
                    $query["order"][] = array(
                        "field" => $order->f,
                        "order" => $order->o == 0 ? "DESC" : "ASC"
                    );
                }
            }

            $newquery = $sr->prioritize($query, $prioritization);

            $listings = $sr->api_request("get_listings", array(
                'query' => $newquery,
                'fields' => array("mls_id", "address", "city", "state", "zip", "coll_name"),
                'limit' => array(
                    'range' => $get_vars->p,
                    'offset' => 0
                )
            ));

            $base = get_bloginfo('url');
            foreach ($listings->result as $index => $listing) {
                $listings->result[$index]->url = $base. $sr->listing_to_url($listing, $listing->coll_name);
            }
            $response = array(
                'error' => 0,
                'mes' => $listings,
                // 'key'=> base64_encode($conditions['conditions'][0]['value'])
                'key'=> $conditions['conditions'][0]['value']
            );

        } else {
            $response = array(
                'error' => 1,
                'mes' => 'Error: Invalid Request'
            );
        }

        break;
    case "get-listings-amount":
        $get_vars = json_decode(base64_decode($_GET['conditions']));

        if (is_array($get_vars->q->c)) {
            $get_vars->p = isset($get_vars->p) ? intval($get_vars->p) : 10; // Default to 10 per page if request doesn't specify
            $get_vars->g = isset($get_vars->g) ? intval($get_vars->g) : 1;

            // Start recursive function to build a request to be sent to the api for search
            $conditions = $sr->convert_to_api_conditions($get_vars->q);

            $prioritization = get_option('sr_prioritization');
            $prioritization = ($prioritization === false) ? array() : $prioritization;


            $query = array(
                "type" => $get_vars->t,
                "query" => $conditions,
            );

            if (isset($get_vars->o) && is_array($get_vars->o)) {
                $query["order"] = array();

                foreach ($get_vars->o as $order) {
                    $query["order"][] = array(
                        "field" => $order->f,
                        "order" => $order->o == 0 ? "DESC" : "ASC"
                    );
                }
            }

            $newquery = $sr->prioritize($query, $prioritization);

            $listings = $sr->api_request("get_listings", array(
                'query' => $newquery,
                'limit' => array(
                    'range' => $get_vars->p,
                    'offset' => ($get_vars->g - 1) * $get_vars->p
                ),
                'fields' => array(
                    'onlycount' => 1
                )

            ));
            $response = array(
                'error' => 0,
                'mes' => $listings
            );


        } else {
            $response = array(
                'error' => 1,
                'mes' => 'Error: Invalid Request'
            );
        }

        break;
    case "get-possible-values":
        unset($_GET['action']);
        if (!$sr->api_key) {
            $response = array(
                'error' => 1,
                'mes' => 'You must activate the SEO RETS plugin.'
            );
        }
        if (!$sr->is_type_valid($_REQUEST['type'])) {
            $response = array(
                'error' => 1,
                'mes' => 'Parameter "type" not set or invalid.'
            );
        }
        if (!array_key_exists('object', $_REQUEST)) {
            $response = array(
                'error' => 1,
                'mes' => 'Parameter "object" not set or invalid.'
            );
        }
        $type = $_GET['type'];
        $object = $_GET['object'];
        unset($_GET['type']);
        unset($_GET['object']);

        foreach ($_GET as $key => $value) {
            if (empty($value)) {
                unset($_GET[$key]);
            }
        }
        $cond = array(
            'type' => $type,
            'object' => $object,
            'conditions' => $_GET
        );

        $request = $this->api_request("get_list", $cond);
        sort($request->result);

        if (!empty($request)) {
            $response = array(
                'error' => 0,
                'count' => $request->count,
                'mes' => $request->result
            );
        }
        break;
    case "listEF_get":
        require_auth();

        include($sr->server_plugin_dir . '/includes/listExtraDataModel.php');

        $modelList = new SRModelListExtraFields();

        $result = $modelList->get();
        $response = array(
            'error' => 0,
            'mes' => 'Plugin Text Updated'
        );

        break;
    case "edit-plugintext":
        require_auth();

        update_option('sr-plugintext', array(
            'login' => $_POST['login'],
            'signup' => $_POST['signup'],
            'forgot' => $_POST['forgot']
        ));

        $response = array(
            'error' => 0,
            'mes' => 'Plugin Text Updated'
        );

        break;
    case "submit-sitemap":
        require_auth();

        $response = array(
            'error' => 0,
            'mes' => 'Sitemap Submitted'
        );

        $meta = get_option('sr_metadata');
        $total = 0;

        foreach ($meta as $key => $m) {

            $request = $sr->api_request('get_listings', array(
                'type' => $key,
                'fields' => array('doesntexist' => 1),
                'count' => true,
                'limit' => array(
                    'range' => 1
                )
            ));

            $total += $request->count;

        }

        $num_of_files = ceil($total / 50000);

        for ($n = 0; $n < $num_of_files; $n++) {
            $sr->http_request('http://www.google.com/webmasters/tools/ping?sitemap=' . urlencode(get_bloginfo('url') . '/sr-sitemap.xml?n=' . ($n + 1)));
            //echo $sr->http_request('http://submissions.ask.com/ping?sitemap=' . urlencode(get_bloginfo('url') . '/sr-sitemap.xml?n=' . ($n + 1)));
            $sr->http_request('http://www.bing.com/webmaster/ping.aspx?siteMap=' . urlencode(get_bloginfo('url') . '/sr-sitemap.xml?n=' . ($n + 1)));
        }

        /*
          http://www.google.com/webmasters/tools/ping?sitemap=http://www.emergencysoft.com/sitemap.xml
          http://submissions.ask.com/ping?sitemap=http://www.emergencysoft.com/sitemap.xml
          http://www.bing.com/webmaster/ping.aspx?siteMap=http://www.emergencysoft.com/sitemap.xml
          */

        break;
    case "edit-css":
        require_auth();
        if (isset($_POST['css'])) {
            update_option('sr_css', $_POST['css']);

            $response = array(
                'error' => 0,
                'mes' => 'CSS Updated',
                'css' => (get_option('sr_css') ? get_option('sr_css') : $this->include_return("resources/defaults/template-styles.css"))
            );
        } else {

            $response = array(
                'error' => 0,
                'mes' => 'CSS Updated'
            );
        }
        break;
    case "edit-seo":
        require_auth();
        update_option('sr_seodata', array(
            'title' => $_POST['title'],
            'keywords' => $_POST['keywords'],
            'description' => $_POST['description']
        ));
        $response = array(
            'error' => 0,
            'mes' => 'SEO Data Updated'
        );
        break;
    case "edit-seo-list":
        require_auth();
        delete_option('sr_seodata_list');
        update_option('sr_seodata_list', array(
            'title' => $_POST['title'],
            'keywords' => $_POST['keywords'],
            'description' => $_POST['description'],
            'introduction-p' => $_POST['seo_introd_p']
        ));
        $response = array(
            'error' => 0,
            'mes' => 'SEO Data Updated'
        );
        break;
    case "edit-extrapage-info":
        require_auth();
        update_option('sr_templates_community', trim(base64_decode($_POST['temp_community'])));
        update_option('sr_templates_overview', trim(base64_decode($_POST['temp_overview'])));
        update_option('sr_templates_features', trim(base64_decode($_POST['temp_features'])));
        update_option('sr_templates_map', trim(base64_decode($_POST['temp_map'])));
        update_option('sr_templates_video', trim(base64_decode($_POST['temp_video'])));

        $response = array(
            'error' => 0,
            'mes' => 'Data Updated'
        );
        break;
    case "edit-popup":
        require_auth();
        update_option('sr_popup', array(
            'status' => $_POST['status'],
            'title' => $_POST['title'],
            'num' => $_POST['num'],
            'sub' => $_POST['sub'],
            'btn' => $_POST['btn'],
            'css' => $_POST['css'],
            'force' => $_POST['force'],
            'success' => $_POST['success'],
            'error' => $_POST['error'],
            'email' => $_POST['email'],
            'customHtml' => base64_decode($_POST['customHtml']),
            'showCustom' => $_POST['showCustom'],
            'showType' => $_POST['showType']

        ));

        $response = array(
            'error' => 0,
            'mes' => 'Settings Saved'
        );
        break;
    case "edit-templates":
        require_auth();
        $_POST['details'] = base64_decode($_POST['details']);
        $_POST['result'] = base64_decode($_POST['result']);
        $_POST['css'] = base64_decode($_POST['css']);
        $templatesList = get_option('sr_templates_list');

        foreach ($templatesList as $key => $template) {
            if ($template['id'] == $_POST['id']) {
                $templatesListId = $key;
                break;
            }
        }
        $templatesList[$templatesListId]['templates'] = array(
            'details' => $_POST['details'],
            'result' => $_POST['result'],
            'css' => $_POST['css']
        );
        if ($templatesList[$templatesListId]['default'] == 1) {
            update_option('sr_templates', array(
                'details' => $_POST['details'],
                'result' => $_POST['result'],
                'css' => $_POST['css']
            ));
        }
        update_option('sr_templates_list', $templatesList);
        $extraFields = get_option('sr_templates_extra');
        $extraFields['show_related_properties'] = $_POST['relatedproperties'];
        $extraFields['rp_zipcode'] = $_POST['rpzipcode'];
        $extraFields['rp_bedrooms'] = $_POST['rpbedrooms'];

        update_option('sr_templates_extra', $extraFields);
        $response = array(
            'error' => 0,
            'mes' => 'Templates Saved'
        );

        break;
    case "create-templates":
        require_auth();
        $templatesList = get_option('sr_templates_list');
        $last = end($templatesList);
        $id = $last['id'] + 1;
        $newOption = array(
            'id' => $id,
            'name' => $_POST['name'],
            'default' => 0,
            'templates' => array(
                'details' => $_POST['details'],
                'result' => $_POST['result'],
                'css' => $_POST['css']
            )
        );
        $templatesList[] = $newOption;
        update_option('sr_templates_list', $templatesList);
        $response = array(
            'error' => 0,
            'mes' => 'Template Created',
            'id' => $id,
            'name' => $_POST['name'],
        );
        break;
    case "delete-templates":
        require_auth();
        $templatesList = get_option('sr_templates_list');

        if (count($templatesList) > 1) {
            foreach ($templatesList as $key => $template) {
                if ($template['id'] == $_POST['id']) {
                    $setDefault = $template['default'] == 1 ? 'true' : 'false';
                    unset($templatesList[$key]);
                    break;
                }
            }
            if ($setDefault == 'true') {
                $srTemplates = reset($templatesList);
                $key = key(reset($templatesList));
                $templatesList[$key]['default'] = 1;
                $newDefaultID = $srTemplates['id'];
                $srTemplates['default'] = 1;
                update_option('sr_templates', $srTemplates['templates']);
            }
            update_option('sr_templates_list', $templatesList);
            $response = array(
                'error' => 0,
                'mes' => 'Template Deleted',
                'deletedDefault' => $setDefault,
                'newDefaultID' => $newDefaultID
            );
        } else {
            $response = array(
                'error' => 1,
                'mes' => 'You can\'t delete last templates',
            );
        }
        break;
    case "set-default-templates":
        require_auth();
        $templatesList = get_option('sr_templates_list');
        foreach ($templatesList as $key => $template) {
            if ($template['id'] == $_POST['id']) {
                $templatesListId = $key;
            }
            $templatesList[$key]['default'] = 0;
        }
        $templatesList[$templatesListId]['default'] = 1;
        update_option('sr_templates_list', $templatesList);

        update_option('sr_templates', $templatesList[$templatesListId]['templates']);
        $response = array(
            'error' => 0,
            'mes' => 'Template Set As Default'
        );
        break;
    case "get-data-templates":
        require_auth();
        $templatesList = get_option('sr_templates_list');
        foreach ($templatesList as $key => $template) {
            if ($template['id'] == $_POST['id']) {
                $templatesListId = $key;
                break;
            }
        }
        $response = $templatesList[$templatesListId];
        break;
    case "lookup-agent":
        require_auth();
        foreach ($sr->metadata as $key => $value) {
            $qs[] = array(
                'type' => $key,
                'fields' => array("mls_id", "agent_id", "office_id"),
                'query' => array(
                    'boolopr' => 'OR',
                    'conditions' => array(
                        array(
                            'field' => 'mls_id',
                            'operator' => '=',
                            'value' => $_POST['id']
                        )
                    )
                )
            );
        }

        $request = $sr->api_request('get_listings', array(
            'query' => $qs,
            'limit' => array(
                "range" => 1
            )
        ));

        if (count($request->result) > 0) {
            $listing = $request->result[0];
            $response = array(
                'error' => 0,
                'mes' => "Agent ID: {$listing->agent_id} Office ID: {$listing->office_id}"
            );
        } else {
            $response = array(
                'error' => 1,
                'mes' => 'MLS ID not found.'
            );
        }
        break;
    case "reset-templates":
        require_auth();
        $id = -1;
        $id2 = -1;
        $id3 = -2;
        $templatesList = get_option('sr_templates_list');
        foreach ($templatesList as $key => $template) {
            if ($template['name'] == 'seo rets responsive template') {
                $id = $key;
            }
            if ($template['name'] == 'seo rets responsive template2') {
                $id2 = $key;
            }
            if ($template['name'] == 'seo rets responsive template3') {
                $id3 = $key;
            }

        }

        $isset = 1;
        $isset2 = 1;
        $isset3 = 1;
        if ($id < 0) {
            $key++;
            $id = $key;
            $templatesList[$id]['name'] = 'seo rets responsive template';
            $templatesList[$id]['id'] = $templatesList[$key - 1]['id'] + 1;
            $templatesList[$id]['default'] = 0;
            $isset = 0;
        }
        if ($id2 < 0) {
            $id2 = $key + 1;
            $templatesList[$id2]['name'] = 'seo rets responsive template2';
            $templatesList[$id2]['id'] = $templatesList[$key]['id'] + 1;
            $templatesList[$id2]['default'] = 0;
            $isset2 = 0;
        }
        if ($id3 < 0) {
            $id3 = $key + 1;
            $templatesList[$id3]['name'] = 'seo rets responsive template3';
            $templatesList[$id3]['id'] = $templatesList[$key]['id'] + 1;
            $templatesList[$id3]['default'] = 0;
            $isset3 = 0;
        }

        $responsiveResult = file_get_contents($sr->server_plugin_dir . "/resources/defaults/template-responsive-result.php");
        $responsiveDetails = file_get_contents($sr->server_plugin_dir . "/resources/defaults/template-responsive-details.php");
        $responsiveCssJs = file_get_contents($sr->server_plugin_dir . "/resources/defaults/template-responsive-css-js.php");

        $templatesList[$id]['templates']['details'] = $responsiveDetails;
        $templatesList[$id]['templates']['result'] = $responsiveResult;
        $templatesList[$id]['templates']['css'] = $responsiveCssJs;


        $responsiveResult2 = file_get_contents($sr->server_plugin_dir . "/resources/defaults/template-responsive-result2.php");
        $responsiveDetails2 = file_get_contents($sr->server_plugin_dir . "/resources/defaults/template-responsive-details2.php");
        $responsiveCssJs2 = file_get_contents($sr->server_plugin_dir . "/resources/defaults/template-responsive-css-js2.php");
        $responsiveCssJs2 = preg_replace('/%WP_PLUGIN_URL%/', WP_PLUGIN_URL, $responsiveCssJs2);

        $templatesList[$id2]['templates']['details'] = $responsiveDetails2;
        $templatesList[$id2]['templates']['result'] = $responsiveResult2;
        $templatesList[$id2]['templates']['css'] = $responsiveCssJs2;


        $responsiveResult3 = file_get_contents($sr->server_plugin_dir . "/resources/defaults/template-responsive-result3.php");
        $responsiveDetails3 = file_get_contents($sr->server_plugin_dir . "/resources/defaults/template-responsive-details3.php");
        $responsiveCssJs3 = file_get_contents($sr->server_plugin_dir . "/resources/defaults/template-responsive-css-js3.php");
        $responsiveCssJs3 = preg_replace('/%WP_PLUGIN_URL%/', WP_PLUGIN_URL, $responsiveCssJs3);

        $templatesList[$id3]['templates']['details'] = $responsiveDetails3;
        $templatesList[$id3]['templates']['result'] = $responsiveResult3;
        $templatesList[$id3]['templates']['css'] = $responsiveCssJs3;


        update_option('sr_templates_list', $templatesList);

        if ($templatesList[$id]['default'] == 1) {
            $currentTemplate = get_option('sr_templates');
            $currentTemplate['details'] = $responsiveDetails;
            $currentTemplate['result'] = $responsiveResult;
            $currentTemplate['css'] = $responsiveCssJs;
            update_option('sr_templates', $currentTemplate);
        }
        if ($templatesList[$id2]['default'] == 1) {
            $currentTemplate = get_option('sr_templates');
            $currentTemplate['details'] = $responsiveDetails2;
            $currentTemplate['result'] = $responsiveResult2;
            $currentTemplate['css'] = $responsiveCssJs2;
            update_option('sr_templates', $currentTemplate);
        }
        if ($templatesList[$id3]['default'] == 1) {
            $currentTemplate = get_option('sr_templates');
            $currentTemplate['details'] = $responsiveDetails3;
            $currentTemplate['result'] = $responsiveResult3;
            $currentTemplate['css'] = $responsiveCssJs3;
            update_option('sr_templates', $currentTemplate);
        }

        $response = array(
            'error' => 0,
            'reload' => 'true',
            'mes' => 'Templates Reset'
        );

        break;
    case "no-popup":
        $this->set_session_data('registered', true);
        $response = array(
            'error' => 0,
            'mes' => 'I know, I hate popups too. (they made me do it)'
        );
        break;
    case "can-i-close-popup":
        $response = array(
            'error' => 0,
            'mes' => $this->get_session_data('registered')
        );
        break;
    case "edit-template":
        require_auth();


        if (isset($_POST['type']) && isset($_POST['allvalue']) && isset($_POST['everyvalues'])) {
            update_option('sr_template', array(
                "type" => $_POST['type'],
                "all-value" => $_POST['allvalue'],
                "every-values" => $_POST['everyvalues']
            ));

            $response = array(
                'error' => 0,
                'mes' => 'Template Updated'
            );
        } else {
            $response = array(
                'error' => 1,
                'mes' => 'Error is settings'
            );
        }

        break;
    case "add-id":
        require_auth();

        if ($_POST['field'] && $_POST['id']) {
            $prioritization = get_option('sr_prioritization');
            if ($prioritization === false) {
                $prioritization = array();
            }

            foreach ($prioritization as $p) {
                if ($p['id'] == $_POST['id']) {
                    echo json_encode(array(
                        'error' => 1,
                        'mes' => 'ID already exists'
                    ));
                    exit;
                }
            }

            $field = $_POST['field'];

            foreach ($sr->metadata as $key => $value) {
                $qs[] = array(
                    'type' => $key,
                    'fields' => array("mls_id", "agent_id", "office_id"),
                    'query' => array(
                        'boolopr' => 'OR',
                        'conditions' => array(
                            array(
                                'field' => $field,
                                'operator' => '=',
                                'value' => $_POST['id']
                            )
                        )
                    )
                );
            }

            $request = $sr->api_request('get_listings', array(
                'query' => $qs,
                'limit' => array(
                    'range' => 1
                )
            ));


            if (count($request->result) > 0) {
                $prioritization[] = array(
                    'id' => $_POST['id'],
                    'field' => $field
                );
                update_option('sr_prioritization', $prioritization);

                $response = array(
                    'error' => 0,
                    'mes' => 'ID Added'
                );
            } else {
                $response = array(
                    'error' => 1,
                    'mes' => 'Agent or office ID not found.'
                );
            }
        } else {
            $response = array(
                'error' => 1,
                'mes' => 'Please enter an agent or office ID.'
            );
        }
        break;
    case "delete-id":
        require_auth();

        $prioritization = get_option('sr_prioritization');
        $prioritization = ($prioritization === false) ? array() : $prioritization;

        foreach ($prioritization as $n => $p) {
            if ($p['id'] == $_POST['id']) {
                unset($prioritization[$n]);
            }
        }

        update_option('sr_prioritization', $prioritization);

        $response = array(
            'error' => 0,
            'mes' => 'ID deleted.'
        );

        break;
    case "geocode":
        $geocode = json_decode($_POST['geocode']);
        $response = array(
            'error' => 0,
            'geocode' => array()
        );

        foreach ($geocode as $property) {
            $geo = json_decode($seo_rets_plugin->http_request("http://dev.virtualearth.net/REST/v1/Locations?query=" . urlencode($property->address) . "&maxResults=1&key=Ai0dLc6pJ17mWx-ADXpSzaptSV7PN5gyPzT1j5qmmgcHOfsk3DMJlssGv8Y61Tdt"));

            $coord = $geo->resourceSets[0]->resources[0]->point->coordinates;

            array_push($response['geocode'], array(
                'index' => $property->index,
                'latitude' => $coord[0],
                'longitude' => $coord[1]
            ));
        }

        break;
    case "mailchimp-key":
        require_auth();

        if (isset($_POST['key'])) {
            require_once($seo_rets_plugin->server_plugin_dir . "/includes/MCAPI.class.php");

            $mailchimp = new MCAPI($_POST['key'], true);
            $result = $mailchimp->ping();

            if ($result == "Everything's Chimpy!") {

                update_option('sr-mailchimptoken', $_POST['key']);
                $temp = $mailchimp->lists();
                $data = $temp['data'];
                $lists = array();

                foreach ($data as $list) {
                    $lists[] = (object)array("id" => $list['id'], "name" => $list['name']);
                }

                $response = array(
                    'error' => 0,
                    'mes' => 'Key added',
                    'lists' => $lists
                );
            } elseif ($mailchimp->errorCode == 104) {

                $response = array(
                    'error' => 1,
                    'mes' => current(explode(':', $mailchimp->errorMessage, 2))
                );
            } else {
                if ($mailchimp->errorMessage) {
                    $response = array(
                        'error' => 1,
                        'mes' => $mailchimp->errorMessage
                    );
                } elseif ($result) {
                    $response = array(
                        'error' => 1,
                        'mes' => $result
                    );
                } else {
                    $response = array(
                        'error' => 1,
                        'mes' => 'Unspecified error'
                    );
                }
            }
        } else {
            update_option('sr-mailchimptoken', false);
            $response = array(
                'error' => 0,
                'mes' => 'API key deleted.'
            );
        }

        break;
    case "mailchimp-list":
        require_auth();
        if (isset($_POST['id']) && $_POST['id'] != '') {
            update_option("sr-mailchimplist", $_POST['id']);
        } else {
            update_option("sr-mailchimplist", false);
        }
        $response = array(
            'error' => 0,
            'mes' => 'List selected.'
        );
        break;
    case "leadcapture-send":
        include $this->server_plugin_dir . '/includes/secureimage/securimage.php';
        $securimage = new Securimage();

        if ($securimage->check($_POST['captcha_code']) == false) {
            $response = array(
                'error' => 1,
                'mes' => 'Captcha is incorrect.'
            );
        } elseif (empty($_POST['email'])) {
            $response = array(
                'error' => 1,
                'mes' => 'Please type your email'
            );

        } else {
            if ($_POST['interest'] == 'Both') {
                $_POST['interest'] = 'Buying and Selling';
            }
            $emailBody = "Name: " . $_POST['name'] . "\r\n" .
                "Email: " . $_POST['email'] . "\r\n" .
                "Phone: " . $_POST['phone'] . "\r\n" .
                "Price range: " . $_POST['low_price'] . " - " . $_POST['high_price'] . "\r\n" .
                "Time Frame: " . $_POST['time_frame'] . "\r\n" .
                "Prequalified: " . $_POST['prequalified'] . "\r\n" .
                "Interest: " . $_POST['interest'] . "\r\n";
            $to = get_option('admin_email');
            $subject = "Lead Capture " . get_site_url();
            $res = SEO_RETS_Plugin::sendEmail($to, $subject, $emailBody);
            if ($res) {
                $response = array(
                    'error' => 0,
                    'mes' => 'Email was successfully sent'
                );
            } else {
                $response = array(
                    'error' => 1,
                    'mes' => 'Occurred some errors while sending email, please try again later'
                );
            }

        }

        break;
    case "leadcapture-save":
        require_auth();
        if (isset($_POST['emails']) && $_POST['emails'] != '') {
            update_option("sr_leadcapture", $_POST['emails']);
        } else {
            update_option("sr_leadcapture", false);
        }
        $response = array(
            'error' => 0,
            'mes' => 'Email list updated.'
        );
        break;
    case "using_boot-save":
        require_auth();
        if (isset($_POST['check'])) {
            update_option("sr_boot", $_POST['check']);
        } else {
            update_option("sr_boot", false);
        }
        $response = array(
            'error' => 0,
            'mes' => 'Bootstrap option updated.'
        );
        break;
    case "fast_query-save":
        require_auth();
        if (isset($_POST['check'])) {
            update_option("sr_fq", $_POST['check']);
        } else {
            update_option("sr_fq", false);
        }
        $response = array(
            'error' => 0,
            'mes' => 'Fast Query option updated.'
        );
        break;
    case "using_crm-save":
        require_auth();
        if (isset($_POST['check'])) {
            update_option("sr_crm", $_POST['check']);
        } else {
            update_option("sr_crm", false);
        }
        $response = array(
            'error' => 0,
            'mes' => 'CRM option updated.'
        );
        break;
    case "show_features-save":
        require_auth();
        if (isset($_POST['check'])) {
            update_option("sr_show_features", $_POST['check']);
        } else {
            update_option("sr_show_features", false);
        }
        $response = array(
            'error' => 0,
            'mes' => 'Search option updated.'
        );
        break;
    case "open_in_new_window-save":
        require_auth();
        if (isset($_POST['check'])) {
            update_option("sr_open_in_new_window", $_POST['check']);
        } else {
            update_option("sr_open_in_new_window", false);
        }
        $response = array(
            'error' => 0,
            'mes' => 'Refine option updated.'
        );
        break;
    case "bootstrap_refine-save":
        require_auth();
        if (isset($_POST['check'])) {
            update_option("bootstrap_refine", $_POST['check']);
        } else {
            update_option("bootstrap_refine", false);
        }
        $response = array(
            'error' => 0,
            'mes' => 'Search option updated.'
        );
        break;
    case "bootstrap_refine_sc-save":
        require_auth();
        if (isset($_POST['check'])) {
            update_option("bootstrap_refine_sc", $_POST['check']);
        } else {
            update_option("bootstrap_refine_sc", false);
        }
        $response = array(
            'error' => 0,
            'mes' => 'Shortcode option updated.'
        );
        break;
    case "use_custom_pagi-save":
        require_auth();
        if (isset($_POST['check'])) {
            update_option("sr_use_custom_pagi", $_POST['check']);
        } else {
            update_option("sr_use_custom_pagi", false);
        }
        $response = array(
            'error' => 0,
            'mes' => 'Pagination option Active.'
        );
        break;
    case "using_ace-save":
        require_auth();
        if (isset($_POST['check'])) {
            update_option("sr_aceEditor", $_POST['check']);
        } else {
            update_option("sr_aceEditor", false);
        }
        $response = array(
            'error' => 0,
            'mes' => 'Ace Editor option updated.'
        );
        break;
    case "emailmethod-save":
        require_auth();
        if (isset($_POST['emailmethod']) && $_POST['emailmethod'] != '') {
            update_option("sr_emailmethod", $_POST['emailmethod']);
        } else {
            update_option("sr_emailmethod", false);
        }
        $response = array(
            'error' => 0,
            'mes' => 'Email method updated.'
        );
        break;
    case "customform-save":
        require_auth();
        if (isset($_POST['html']) && $_POST['html'] != '') {
            update_option("sr_customform", base64_decode($_POST['html']));
        } else {
            update_option("sr_customform", false);
        }
        $response = array(
            'error' => 0,
            'mes' => 'Custom Form updated.'
        );
        break;
    case "unfoundpage-save":
        require_auth();
        if (isset($_POST['html']) && $_POST['html'] != '') {
            update_option("sr_unfoundpage", $_POST['html']);
        } else {
            update_option("sr_unfoundpage", false);
        }
        $response = array(
            'error' => 0,
            'mes' => 'Unfound page updated.'
        );
        break;
    case "get-count":
        if (isset($_POST['q']) && is_string($_POST['q']) && ($request = json_decode($_POST['q'])) !== null) {
            if (is_array($request->q->c)) {
                // Start recursive function to build a request to be sent to the api for search
                $conditions = $this->convert_to_api_conditions($request->q);
                /*$query = array(
                        "type" => $request->t,
                        "query" => $conditions,
                    );*/

                $apiresponse = $this->api_request("get_listings", array(
                    'query' => $conditions,
                    'type' => $request->t,
                    'count' => true,
                    'fields' => array('doesntexist' => 1),
                    'limit' => array(
                        'range' => 1,
                    )
                ));

                $response = array("error" => 0, "count" => $apiresponse->count);
                break;
            }
            $response['mes'] = "c is not an array";
            break;
        }
        $response['mes'] = "Error in request format";
        break;
    case "map-search":
        $conditions = array();

        if (isset($_POST['fil']) && $_POST['fil'] != "") {
            foreach ($_POST['fil'] as $field) {
                foreach ($field as $key => $f) {
                    $conditions[] = array(
                        'field' => $key,
                        'operator' => 'LIKE',
                        'loose' => true,
                        'value' => $f
                    );
                }
            }
        }


        if (isset($_POST['city']) && $_POST['city'] != "") {
            $conditions[] = array(
                'field' => 'city',
                'operator' => 'LIKE',
                'loose' => true,
                'value' => $_POST['city']
            );
        }

        if (isset($_POST['zip']) && $_POST['zip'] != "") {
            $conditions[] = array(
                'field' => 'zip',
                'operator' => 'LIKE',
                'loose' => true,
                'value' => $_POST['zip']
            );
        }

        if (isset($_POST['waterview']) && $_POST['waterview'] != "") {
            if (is_array($_POST['waterview'])) {
                foreach ($_POST['waterview'] as $value) {
                    $conditions[] = array(
                        'field' => 'waterview',
                        'operator' => 'LIKE',
                        'loose' => true,
                        'value' => $value
                    );
                }
            } else {
                $conditions[] = array(
                    'field' => 'waterview',
                    'operator' => 'LIKE',
                    'loose' => true,
                    'value' => $_POST['waterview']
                );
            }

        }

        if (isset($_POST['waterfront']) && $_POST['waterfront'] != "") {
            if (is_array($_POST['waterfront'])) {
                foreach ($_POST['waterfront'] as $value) {
                    $conditions[] = array(
                        'field' => 'waterfront',
                        'operator' => 'LIKE',
                        'loose' => true,
                        'value' => $value
                    );
                }
            } else {
                $conditions[] = array(
                    'field' => 'waterfront',
                    'operator' => 'LIKE',
                    'loose' => true,
                    'value' => $_POST['waterfront']
                );
            }

        }

        if (isset($_POST['price-low']) && $_POST['price-low'] != "") {
            $conditions[] = array(
                'field' => 'price',
                'operator' => '>=',
                'value' => $_POST['price-low']
            );
        }

        if (isset($_POST['price-high']) && $_POST['price-high'] != "") {
            $conditions[] = array(
                'field' => 'price',
                'operator' => '<=',
                'value' => $_POST['price-high']
            );
        }

        if (isset($_POST['bedrooms-low']) && $_POST['bedrooms-low'] != "") {
            $conditions[] = array(
                'field' => 'bedrooms',
                'operator' => '>=',
                'value' => $_POST['bedrooms-low']
            );
        }

        if (isset($_POST['bedrooms-high']) && $_POST['bedrooms-high'] != "") {
            $conditions[] = array(
                'field' => 'bedrooms',
                'operator' => '<=',
                'value' => $_POST['bedrooms-high']
            );
        }

        if (isset($_POST['baths-low']) && $_POST['baths-low'] != "") {
            $conditions[] = array(
                'field' => 'baths',
                'operator' => '>=',
                'value' => $_POST['baths-low']
            );
        }

        if (isset($_POST['baths-high']) && $_POST['baths-high'] != "") {
            $conditions[] = array(
                'field' => 'baths',
                'operator' => '<=',
                'value' => $_POST['baths-high']
            );
        }

        if (isset($_POST['ne-lat'])) {
            $conditions[] = array(
                'field' => 'lat',
                'operator' => '<',
                'value' => (float)$_POST['ne-lat']
            );
        }

        if (isset($_POST['ne-lng'])) {
            $conditions[] = array(
                'field' => 'lng',
                'operator' => '<',
                'value' => (float)$_POST['ne-lng']
            );
        }

        if (isset($_POST['sw-lat'])) {
            $conditions[] = array(
                'field' => 'lat',
                'operator' => '>',
                'value' => (float)$_POST['sw-lat']
            );
        }

        if (isset($_POST['sw-lng'])) {
            $conditions[] = array(
                'field' => 'lng',
                'operator' => '>',
                'value' => (float)$_POST['sw-lng']
            );
        }

        if (isset($_POST['subdivision']) && $_POST['subdivision'] != "") {
            $conditions[] = array(
                'field' => 'subdivision',
                'operator' => 'LIKE',
                'loose' => true,
                'value' => $_POST['subdivision']
            );
        }
        if (isset($_POST['proj_name']) && $_POST['proj_name'] != "") {
            $conditions[] = array(
                'field' => 'proj_name',
                'operator' => 'LIKE',
                'loose' => true,
                'value' => $_POST['proj_name']
            );
        }
        if (isset($_POST['area']) && $_POST['area'] != "") {
            $conditions[] = array(
                'field' => 'area',
                'operator' => 'LIKE',
                'loose' => true,
                'value' => $_POST['area']
            );
        }


        $order = isset($_POST['order']) ? explode(":", $_POST['order']) : array();

        if (count($order) != 2) {
            $order = array(
                array(
                    'field' => 'price',
                    'order' => 'DESC'
                )
            );
        } else {
            $save = $order;
            $order = array(
                array(
                    'field' => $save[0],
                    'order' => $save[1]
                )
            );
        }

        $prioritization = get_option('sr_prioritization');
        $prioritization = ($prioritization === false) ? array() : $prioritization;
        $only = isset($_POST['onlymylistings']) && strtolower($_POST['onlymylistings']) != "no";

        $perpage = isset($_POST['limit']) ? ((int)$_POST['limit'] - 1) : 25;


        $query = $this->prioritize(array(
            'type' => $_POST['type'],
            'query' => array(
                'boolopr' => 'AND',
                'conditions' => $conditions
            ),
            'order' => $order
        ), $prioritization);


        if ($only && count($prioritization) > 0) {
            array_pop($query);
        }

        /*
          $response = $sr->api_request('get_listings', $query);
          */

//        $response = $query;
        $response = $sr->api_request('get_listings', array(
            'query' => $query,
            'limit' => array(
                'range' => $perpage
            )
        ));
        if (empty($response)) {
            $query = $this->prioritize(array(
                'type' => $_POST['type'],
                'query' => array(
                    'boolopr' => 'AND',
                    'conditions' => $conditions
                ),
                'order' => null
            ), $prioritization);
            $response = $sr->api_request('get_listings', array(
                'query' => $query,
                'limit' => array(
                    'range' => $perpage
                )
            ));
        }

        foreach ($response->result as $index => $listing) {
            $response->result[$index]->url = $sr->listing_to_url($listing, $_POST['type']);
        }
        break;
}

echo json_encode($response);

exit;
?>



