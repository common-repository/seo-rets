<?php
$sr = $this;
if (!$sr->api_key) {
    $currentPage->post_title = 'Search Results';
    $currentPage->post_content = 'You must activate the SEO RETS plugin.';
} else {

    $page_name = "Search";
    if ($this->template_settings['type'] == "all") {
        wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['all-value'])), "post_meta");
    } else {
        wp_cache_set($post_id, array("_wp_page_template" => array($this->template_settings['every-values'][$page_name])), "post_meta");
    }


    if (isset($_POST['conditions'])) {
        $conditions = array();

        foreach ($_POST['conditions'] as $condition) {
            if (isset($condition['value']) && $condition['value'] != "") {
                if (is_array($condition['value'])) {
                    $newcondition['boolopr'] = "OR";
                    $newcondition['conditions'] = array();
                    foreach ($condition['value'] as $value) {
                        if ($value == "") {
                            continue;
                        }

                        $split = explode(",", $value);

                        if (count($split) > 1 && $condition['operator'] == "LIKE") {
                            foreach ($split as $v) {
                                $subcondition = array('field' => $condition['field'], 'operator' => $condition['operator'], 'value' => $v);
                                if (isset($condition['loose'])) {
                                    $subcondition['loose'] = $condition['loose'];
                                }
                                $newcondition['conditions'][] = $subcondition;
                            }
                        } else {
                            $subcondition = array('field' => $condition['field'], 'operator' => $condition['operator'], 'value' => $value);
                            if (isset($condition['loose'])) {
                                $subcondition['loose'] = $condition['loose'];
                            }
                            $newcondition['conditions'][] = $subcondition;
                        }
                    }
                    if (count($newcondition['conditions']) > 0) {
                        $conditions[] = $newcondition;
                    }
                } else {
                    $conditions[] = $condition;
                }
            }
        }


        $_POST['perpage'] = isset($_POST['perpage']) ? $_POST['perpage'] : 10;

        $new_request = array(
            'q' => $sr->convert_to_search_conditions(array(
                'boolopr' => '1',
                'conditions' => $conditions
            )),
            't' => $_POST['type'],
            'p' => (int)$_POST['perpage']
        );

        if (isset($_POST['order_wp_sux'])) {

            $new_request['o'] = array();

            foreach (array_values($_POST['order_wp_sux']) as $a_order) $new_request['o'][] = array('f' => $a_order['field'], 'o' => (strtolower($a_order['order']) == "desc") ? 0 : 1);

        }

        $new_request['q']['b'] = 1;

        $new_request_json = json_encode($new_request);


    } else {

        $url=$_SERVER['REQUEST_URI'];
        $typesList=array('res','cnd','cms','cre', 'frm','lnds');
        $typesString= implode('|', $typesList);
        preg_match('/\/(sr-.+?)\//',$url, $matches);
        $splitedUrl[1]=$matches[1];
        preg_match('/\/sr-.+?\/+(.+)\/('.$typesString.')/',$url, $matches);

        $splitedUrl[2]=$matches[1];
        $splitedUrl[3]=$matches[2];

        preg_match('/\/sr-.+?\/+(.+)\/('.$typesString.')\/([0-9]*?)\//',$url, $matches);
        if (empty($matches[3])){
            preg_match('/\/sr-.+?\/+(.+)\/('.$typesString.')\/([0-9]*?)$/',$url, $matches);
        }
        $splitedUrl[4]=$matches[3];

        $new_request['p'] = 10;
        $new_request['p']=apply_filters('seo-rets-auto-generated-pages-listings-perpage', $new_request['p']);
        if (!empty($splitedUrl[4])){
            $new_request['g'] = $splitedUrl[4];
        }

        $splitedUrl[2]=preg_replace('/\+/',' ',$splitedUrl[2]);
        $splitedUrl[2]=preg_replace("/\\'/","\'",$splitedUrl[2]);


        if (($splitedUrl[1] == "sr-cities")) {
            $new_request['t'] = $splitedUrl[3];
            $new_request['q'] = array(

                'c' => array(
                    array(
                        'f' => 'city',
                        'o' => 'LIKE',
                        'l' => 1,
                        'v' => $splitedUrl[2],
                    )
                ),
                'b'=>1

            );
        } elseif (($splitedUrl[1] == "sr-communities")) {
            $new_request['t'] = $splitedUrl[3];
            $new_request['q'] = array(

                'c' => array(
                    array(
                        'f' => 'subdivision',
                        'o' => 'LIKE',
                        'l' => 1,
                        'v' => $splitedUrl[2],
                    )
                ),
                'b'=>1

            );
        } elseif ($splitedUrl[1] == 'sr-condos') {
            $new_request['t'] = $splitedUrl[3];
            $new_request['q'] = array(

                'c' => array(
                    array(
                        'f' => 'proj_name',
                        'o' => 'LIKE',
                        'l' => 1,
                        'v' => $splitedUrl[2],
                    )
                ),
                'b'=>1

            );
        }

        $new_request_json = json_encode($new_request);
    }

    if (empty($new_request_json)) {
        $currentPage->post_content = 'It looks like there is a problem with your permalink structure. Please set to "/%postname%".';
        $currentPage->post_title = 'Error';
    }
    // Figure out if this is a legacy form processor request or the new version

    $get_vars = json_decode($new_request_json);
    $currentPage->post_title =  $get_vars->q->c[0]->v;

    $currentPage->post_content = '';


    if ($get_vars != NULL) { // We can say that the only required variable to be set is conditions in new request format, so we'll assume that's what this request is

        $type=$get_vars->t;
        $object=$get_vars->q->c[0]->f;
        $objectValue=$get_vars->q->c[0]->v;

        $list_seo_data['type']=$this->to_pretty_type($type);
        $list_seo_data['object']=$object;
        $list_seo_data['object_value']=$objectValue;

        if (is_array($get_vars->q->c)) {
            $get_vars->p = isset($get_vars->p) ? intval($get_vars->p) : 10; // Default to 10 per page if request doesn't specify
            $get_vars->g = isset($get_vars->g) ? intval($get_vars->g) : 1;

            // Start recursive function to build a request to be sent to the api for search
            $conditions = $this->convert_to_api_conditions($get_vars->q);

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

            $newquery = $this->prioritize($query, $prioritization);


            $response = $this->api_request("get_listings", array(
                'query' => $newquery,
                'limit' => array(
                    'range' => $get_vars->p,
                    'offset' => ($get_vars->g - 1) * $get_vars->p
                ),
                'statistic_info'=>array(
                    'type'=>$type,
                    'object'=>$object,
                    'value'=>$objectValue
                )
            ));

            $list_seo_data['numberofresults']=convert_number_to_words($response->count);
            $list_seo_data['numberofresults_digits']=$response->count;
            $list_seo_data['calculatedaverageprice']='\$'.number_format($response->statistic_info->averagePrice);
            $list_seo_data['calculatedlowprice']='\$'.number_format($response->statistic_info->minPrice);
            $list_seo_data['calculatedhighprice']='\$'.number_format($response->statistic_info->maxPrice);
            $list_seo_data['calculatedaveragesqft']=number_format($response->statistic_info->averageSqft);
            $list_seo_data['calculatedhighsqft']=number_format($response->statistic_info->maxSqft);
            $list_seo_data['calculatedlowsqft']=number_format($response->statistic_info->minSqft);

            if (empty($response->result)){
                $get_vars->g =1;
            }

            $listings = $response->result;
            //}


            $type = $get_vars->t;




            $seodata     = get_option('sr_seodata_list');
            $title                     = $seodata['title'];
            $currentPage->post_title=parse_seo_data_list($title, $list_seo_data);

            add_action('wp_head', array($this,'print_meta_info_list'), 0);

            $seodata['description']=parse_seo_data_list($seodata['description'],$list_seo_data);
            $seodata['keywords']=parse_seo_data_list($seodata['keywords'],$list_seo_data);
            $this-> meta_data= '<!-- Start SEO RETS Meta Data -->
<meta name="description" content="' . htmlentities($seodata['description']) . '" />
<meta name="keywords" content="' . htmlentities($seodata['keywords']) . '" />
<!-- End SEO RETS Meta Data -->' . "\n";


            $introductionParagraph='<p class="introductionParagraph">'.parse_seo_data_list($seodata['introduction-p'], $list_seo_data).'</p>';

            $listing_html = $this->include_return('templates/results.php', get_defined_vars());
            $pagination_html = $this->pagination_html_customsearch($get_vars, $get_vars->g, ceil($response->count / $get_vars->p), $response->count);


            $currentPage->post_content .= $introductionParagraph.$pagination_html . $listing_html . $pagination_html;

        } else {
            $currentPage->post_content .= 'Error: Invalid Request';
        }

    } else {
        if (isset($_POST['conditions'])) {
            $conditions = array();

            foreach ($_POST['conditions'] as $condition) {
                if (isset($condition['value']) && $condition['value'] != "") {
                    if (is_array($condition['value'])) {
                        $newcondition['boolopr'] = "OR";
                        $newcondition['conditions'] = array();
                        foreach ($condition['value'] as $value) {
                            if ($value == "") {
                                continue;
                            }

                            $split = explode(",", $value);

                            if (count($split) > 1 && $condition['operator'] == "LIKE") {
                                foreach ($split as $v) {
                                    $subcondition = array('field' => $condition['field'], 'operator' => $condition['operator'], 'value' => $v);
                                    if (isset($condition['loose'])) {
                                        $subcondition['loose'] = $condition['loose'];
                                    }
                                    $newcondition['conditions'][] = $subcondition;
                                }
                            } else {
                                $subcondition = array('field' => $condition['field'], 'operator' => $condition['operator'], 'value' => $value);
                                if (isset($condition['loose'])) {
                                    $subcondition['loose'] = $condition['loose'];
                                }
                                $newcondition['conditions'][] = $subcondition;
                            }
                        }
                        if (count($newcondition['conditions']) > 0) {
                            $conditions[] = $newcondition;
                        }
                    } else {
                        $conditions[] = $condition;
                    }
                }
            }


            $_POST['perpage'] = isset($_POST['perpage']) ? $_POST['perpage'] : 10;

            $new_request = array(
                'q' => $sr->convert_to_search_conditions(array(
                    'boolopr' => '1',
                    'conditions' => $conditions
                )),
                't' => $_POST['type'],
                'p' => (int)$_POST['perpage']
            );

            if (isset($_POST['order_wp_sux'])) {

                $new_request['o'] = array();

                foreach (array_values($_POST['order_wp_sux']) as $a_order) $new_request['o'][] = array('f' => $a_order['field'], 'o' => (strtolower($a_order['order']) == "desc") ? 0 : 1);

            }

            $new_request['q']['b'] = 1;

            $new_request_json = json_encode($new_request);

            header("Location: " . home_url() . "/sr-customsearch?" . urlencode(base64_encode($new_request_json)));
            exit;
        } else {
            $currentPage->post_content = 'It looks like there is a problem with your permalink structure. Please set to "/%postname%".';
            $currentPage->post_title = 'Error';
        }

    }
}
function parse_seo_data_list($string, $seo_data){
    foreach ($seo_data as $key=>$value){
        $string=preg_replace('/%'.$key.'%/',$value,$string);
    }
    return $string;
}

function convert_number_to_words($number) {

    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}