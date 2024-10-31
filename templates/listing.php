<?php
$l = $listing;
$l->city2 = preg_replace('/\s/', '+', $l->city);
$l->subdivision2 = preg_replace('/\s/', '+', $l->subdivision);

wp_enqueue_style('sr_templates_listing', $this->css_resources_dir . 'templates/listing.css');
wp_print_styles(array('sr_templates_listing'));
?>

    <script type="text/javascript">
        jQuery(document).ready(function () {
            jQuery('.sr-listing-pg').find('div.addToCompare').each(function () {
                var it = jQuery(this);
                it.html('<div class="cssload-squares"><span></span><span></span><span></span><span></span><span></span></div>');
                jQuery.ajax({
                    url: '<?php bloginfo('url') ?>/sr-ajax?action=getCompareListID',
                    type: 'post',
                    data: {
                        type: it.data('type'),
                        mls: it.data('mls')
                    },
                    success: function (response) {
//                    console.log(response);

                        if (response['current'] == "Yes") {
                            var k;
                            if (response['current'] === false) {
                                k = 0;
                            } else {
                                k = response['current'];
                            }
                            it.html('<span class="startCompare">Compare</span> | <i data-rmid="' + k + '"  class="removeFromCompare">Remove</i>');
                            it.removeClass('addToCompare');
                        } else {
                            it.html('Add to Compare');

                        }
                    }
                });
            });
            jQuery('.sr-listing-pg').on('click', 'i.removeFromCompare', function () {
                var it = jQuery(this).parent();
                var check = new Array;
                check.push(jQuery(this).data('rmid'));
                it.html('<div class="cssload-squares"><span></span><span></span><span></span><span></span><span></span></div>');

                jQuery.ajax({
                    url: '<?php bloginfo('url') ?>/sr-ajax?action=removeCompareListings',
                    type: 'post',
                    data: {
                        type: 'remove',
                        mls: check
                    },
                    success: function (response) {
                        console.log(response);
                        if (response) {
                            it.addClass('addToCompare');
                            it.html('Add to Compare');
                        }

                    }
                });
            });
            jQuery('.sr-listing-pg').on('click', 'span.startCompare', function () {
                window.open('<?php echo get_home_url(); ?>/sr-compare/', 'mypopuptitle', 'width=600,height=950');
            });
            jQuery('.addToCompare').click(function () {
                var it = jQuery(this);
                if (it.hasClass('addToCompare')) {
                    it.html('<div class="cssload-squares"><span></span><span></span><span></span><span></span><span></span></div>');
                    jQuery.ajax({
                        url: '<?php bloginfo('url') ?>/sr-ajax?action=addToCompare',
                        type: 'post',
                        data: {
                            type: it.data('type'),
                            mls: it.data('mls')
                        },
                        success: function (response) {
                            console.log(response);

//                        if (response['count'] > 1) {
                            var k;
                            if (response['current'] === false) {
                                k = 0;
                            } else {
                                k = response['current'];
                            }
                            it.html('<span class="startCompare">Compare</span> | <i data-rmid="' + k + '"  class="removeFromCompare">Remove</i>');
                            it.removeClass('addToCompare');
//                        } else {
//                            var j = response['current'] === true ? 1 : 0;
//                            it.html('<i data-rmid="' + j + '" class="removeFromCompare">Remove</i>');
//                            it.addClass('removeCompare');
//                        }
                        }
                    });
                }
            });
            jQuery('.zoom-gallery').magnificPopup({
                delegate: 'a',
                type: 'image',
                closeOnContentClick: false,
                closeBtnInside: false,
                mainClass: 'mfp-with-zoom mfp-img-mobile',
                image: {
                    verticalFit: true,
                    titleSrc: function (item) {
                        return item.el.attr('title') + ' &middot; <a class="image-source-link" href="' + item.el.attr('data-source') + '" target="_blank">image source</a>';
                    }
                },
                gallery: {
                    enabled: true
                },
                zoom: {
                    enabled: true,
                    duration: 300, // don't foget to change the duration also in CSS
                    opener: function (element) {
                        return element.find('img');
                    }
                }

            });
        });
        var sr_plugin_dir = '<?php echo $sr->plugin_dir?>';

        var sr_popup2;

        function close_popup2() {
            jQuery("#sr-popup-form2").fadeOut("slow", function () {
                sr_popup2.fadeOut("slow");
            });
        }
        ;


        jQuery(function ($) {

            $('#main-photo-a').click(function () {
                $('.sr-listing-photos .sr-thumbs a:eq(0)').click();
            });

            $(".sr-listing-photos .sr-thumbs a").mouseover(function () {
                var sender = $(this);
                $(".sr-listing-photo-details-main").attr('src', $(sender.children()[0]).attr('src'));
                $('#main-photo-a').unbind("click").click(function () {
                    sender.click();
                });
            });
            $("#sr-alert").click(function () {
//                var mfp = jQuery.magnificPopup.instance;
                var htm = '<div id="sr-popup2" class="zoom-anim-dialog"><iframe style="width: 100%;height:300px;border:0;" id="popup-iframe" border="0" scrolling="no" src="<?php echo get_bloginfo('url')?>/sr-alert?mls_id=<?php echo urlencode($l->mls_id)?>&type=<?php echo urlencode($wp_query->query['sr_type'])?>&address=<?php echo urlencode($l->address)?>&city=<?php echo urlencode($l->city)?>&state=<?php echo urlencode($l->state)?>&zip=<?php echo urlencode($l->zip)?>"></iframe></div>';
                jQuery.magnificPopup.open({
                    items: {
                        src: '<?php echo get_bloginfo('url') . '/sr-alert?mls_id=' . urlencode($l->mls_id) . '&type=' . urlencode($wp_query->query['sr_type']) . '&address=' . urlencode($l->address) . '&city=' . urlencode($l->city) . '&state=' . urlencode($l->state) . '&zip=' . urlencode($l->zip) ?>',
                        type: 'ajax'
                    },
                    fixedContentPos: true,
                    fixedBgPos: true,

                    overflowY: 'auto',

                    closeBtnInside: true,
                    preloader: false,

                    midClick: true,
                    closeOnBgClick: false,
                    removalDelay: 300,
                    mainClass: 'my-mfp-zoom-in'
                });
            });
//            $("#sr-alert").click(function() {
//                $("#sr-popup2").remove();
//
//                var htm = '<div id="sr-popup2" style="display:none;"> <style>#sr-popup-form2 { background-color: #f0f4f5; transition: 0.5s; } @media only screen and (min-width: 630px) { #sr-popup-form2 { height: 360px; width: 565px; max-height: 95%; } iframe#popup-iframe { height: 340px; } } @media only screen and (max-width: 630px) { #sr-popup-form2 { min-width: 200px; padding-right: 20px; max-height: 595px; width: 95%; height: 95%; } iframe#popup-iframe { max-height: 570px; } }</style> <div id="sr-popup-form2" style="display:none;"><a href="javascript: void(0);"><img src="<?php //echo $sr->plugin_dir ?>//resources/images/close.png" id="sr-popup-close2"/></a> <iframe id="popup-iframe" style="border:0;" border="0" scrolling="no" src="<?php //bloginfo('url'); ?>///sr-alert?mls_id=<?php //echo urlencode($l->mls_id) ?>//&type=<?php //echo urlencode($wp_query->query['sr_type']) ?>//&address=<?php //echo urlencode($l->address) ?>//&city=<?php //echo urlencode($l->city) ?>//&state=<?php //echo urlencode($l->state) ?>//&zip=<?php //echo urlencode($l->zip) ?>//"></iframe> </div> </div>';
//
//                $("body").append(htm);
//
//                sr_popup2 = $("#sr-popup2");
//
//                sr_popup2.fadeIn("slow", function() {
//                    $("#sr-popup-form2").fadeIn("slow");
//                });
//
//                $("#sr-popup-close2").click(close_popup2);
//            });
        });

    </script>

<?php
$templates = get_option('sr_templates');
$extraData['backLink'] = array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER'] : '';
$extraData['siteUrl'] = get_site_url();

if (isset($tmp)) {

    switch ($tmp) {
        case "community":
            $template = get_option('sr_templates_community');
            eval('?>' . $template);
            break;
        case "overview":
            $template = get_option('sr_templates_overview');
            eval('?>' . $template);
            break;
        case "features":
            $template = get_option('sr_templates_features');
            eval('?>' . $template);
            break;
        case "map":
            $template = get_option('sr_templates_map');
            eval('?>' . $template);
            break;
        case "video":
            $template = get_option('sr_templates_video');
            eval('?>' . $template);
            break;
    }
} else {
    update_post_meta($currentPage->ID, '_wp_page_template', get_theme_root() . '/era-test/page_wide.php');
    add_post_meta($currentPage->ID, '_date', $l->date_modified);

    if (isset($templates['details'])) {
//        function searchMLSinFAV($array, $key, $value)
//        {
//            $results = array();
//
//            if (is_array($array)) {
//                if (isset($array[$key]) && $array[$key] == $value) {
//                    $results[] = $array;
//                }
//
//                foreach ($array as $index => $subarray) {
//                    $subarray['index'] = $index;
//                    $results = array_merge($results, searchMLSinFAV($subarray, $key, $value));
//                }
//            }
//
//            return $results;
//        }

        date_default_timezone_set("US/Central");

        $index = $this->get_session_data('user_index');
        $users = get_option('sr_users');

        $sql_getListings = "SELECT * FROM wp_sr_stat_mls WHERE mls=" . $l->mls_id;
        $res_getListings = $sr->wpdbSelectResults($sql_getListings);
        if (empty($res_getListings)) {
            $stat = array(
                'id' => '',
                'mls' => $l->mls_id,
                'mtype' => $type
            );
            $lastId = $sr->wpdbInsertRow('wp_sr_stat_mls', $stat);
        }
        $ar = $res_getListings[0];
        $statOptAdd = array(
            'id' => '',
            'stat_id' => $ar->id,
            'mtype' => 'View',
            'time' => date("Y-m-d H:i:s")
        );
        $sr->wpdbInsertRow('wp_sr_stat_option', $statOptAdd);
//        if ($index !== false) {
//            $users[$index]['favorites'];
//            $fav = searchMLSinFAV($users[$index]['favorites'], 'mls', $l->mls_id);
//            if (!empty($fav)) {
//                $indexFav = $fav[0]['index'];
//                $users[$index]['favorites'][$indexFav]['views'] = $users[$index]['favorites'][$indexFav]['views'] + 1;
//                $users[$index]['favorites'][$indexFav]['last_views'] = date("Y-m-d H:i:s");
//                update_option('sr_users', $users);
//            } else {
//                $oth = searchMLSinFAV($users[$index]['other'], 'mls', $l->mls_id);
//                if (!empty($oth)) {
//                    $users[$index]['other'][$oth[0]['index']]['views'] = $users[$index]['other'][$oth[0]['index']]['views'] + 1;
//                    $users[$index]['other'][$oth[0]['index']]['last_views'] = date("Y-m-d H:i:s");
//                    update_option('sr_users', $users);
//                } else {
//                    $other = array(
//                        'mls' => $l->mls_id,
//                        'views' => 1,
//                        'last_views' => date("Y-m-d H:i:s"),
//                        'type' => $type
//                    );
//                    $users[$index]['other'][] = $other;
////
//                    update_option('sr_users', $users);
//
//                }
//
//
//            }
//
//        }
//        function searchMLSinStatistic($array, $key, $value)
//        {
//            $results = array();
//
//            if (is_array($array)) {
//                if (isset($array[$key]) && $array[$key] == $value) {
//                    $results[] = $array;
//                }
//
//                foreach ($array as $index => $subarray) {
//                    $subarray['index'] = $index;
//                    $results = array_merge($results, searchMLSinStatistic($subarray, $key, $value));
//                }
//            }
//
//            return $results;
//        }
//
//        $listingsFullStatistic = get_option('sr_listingsFullStatistick');
//        $lfs = searchMLSinStatistic($listingsFullStatistic, 'mls', $l->mls_id);
//        if (isset($lfs) && !empty($lfs)) {
//            $listingsFullStatistic[$lfs[0]['index']]['views'][0]['count']++;
//            $listingsFullStatistic[$lfs[0]['index']]['views'][0]['last_view'] = date("Y-m-d H:i:s");
//            update_option('sr_listingsFullStatistick', $listingsFullStatistic);
//        } else {
//            $lV = array(
//                'mls' => $l->mls_id,
//                'views' => array([
//                    'count' => 1,
//                    'last_view' => date("Y-m-d H:i:s")
//                ]),
//                'type' => $type
//            );
//            $listingsFullStatistic[] = $lV;
//            update_option('sr_listingsFullStatistick', $listingsFullStatistic);
//        }

//        $listingsViewStatistic = get_option('sr_listingsViewStatistic');
//        $listingsView = searchMLSinStatistic($listingsViewStatistic, 'mls', $l->mls_id);
//        if (isset($listingsView) && !empty($listingsView)) {
//            $listingsViewStatistic[$listingsView[0]['index']]['count']++;
//            $listingsViewStatistic[$listingsView[0]['index']]['last_view'] = date("Y-m-d H:i:s");
//            update_option('sr_listingsViewStatistic', $listingsViewStatistic);
//        } else {
//            $lV = array(
//                'mls' => $l->mls_id,
//                'count' => 1,
//                'last_view' => date("Y-m-d H:i:s"),
//                'type' => $type
//            );
//            $listingsViewStatistic[] = $lV;
//            update_option('sr_listingsViewStatistic', $listingsViewStatistic);
//        }

        if (isset($templates['css'])) {
            ?>

            <?php echo $templates['css']; ?>

            <?php
        } else {
            include($sr->resp_css);
        }

        eval('?>' . $templates['details']);
        //echo $templates['details'];
    } else {
        include($this->details_template);
    }
    if ($extraFieldsTemplate['show_related_properties'] == 'true') {
        echo '<h2 class="entry-title">Related Listings</h2>';
        $listings = $addRequest->result;
        include($sr->server_plugin_dir . "/templates/results.php");
    }
}


