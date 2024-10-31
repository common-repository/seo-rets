<?
wp_enqueue_script('sr_seorets-min');
wp_print_scripts(array('sr_seorets-min'));
wp_enqueue_script('sr_method_google-map', $this->js_resources_dir . 'google-map.js');
wp_print_scripts(array('sr_method_google-map'));
$act = $this->boot_refine;
$url = $_SERVER['REQUEST_URI'];
$url2 = $_SERVER['QUERY_STRING'];
$link = str_replace('/sr-search', '/sr-search-fav', $url);
$link = str_replace('?', '?add=', $link);

$link2 = '/sr-search-fav?add=' . $url2;
date_default_timezone_set("US/Central");

$linkNarrow = str_replace('/sr-search', '/sr-narrow-search', $url);
$linkNarrow2 = '/sr-narrow-search?' . $url;


function searchURLinStatistic($array, $key, $value)
{
    $results = array();

    if (is_array($array)) {
        if (isset($array[$key]) && $array[$key] == $value) {
            $results[] = $array;
        }

        foreach ($array as $index => $subarray) {
            $subarray['index'] = $index;
            $results = array_merge($results, searchURLinStatistic($subarray, $key, $value));
        }
    }

    return $results;
}


$searchViewStatistic = get_option('sr_searchViewStatistic');
$searchView = searchURLinStatistic($searchViewStatistic, 'link', $url);
if (isset($searchView) && !empty($searchView)) {
    $searchViewStatistic[$searchView[0]['index']]['count']++;
    $searchViewStatistic[$searchView[0]['index']]['last_view'] = date("Y-m-d H:i:s");
    update_option('sr_searchViewStatistic', $searchViewStatistic);
} else {
    $searchViewA = array(
        'link' => $url,
        'count' => 1,
        'last_view' => date("Y-m-d H:i:s"),
    );
    $searchViewStatistic[] = $searchViewA;
    update_option('sr_searchViewStatistic', $searchViewStatistic);
}
?>
<div></div>

<script type="text/javascript">
    jQuery(function () {
        var form = jQuery("#ModifySearch");
        var refinebtn = jQuery("#sr-refinebtn");
        var bedsfield = jQuery("#sr-bedsfield");
        var bathsfield = jQuery("#sr-bathsfield");
        var pricefieldl = jQuery("#sr-pricefieldl");
        var pricefieldh = jQuery("#sr-pricefieldh");
        var priceSort = jQuery("#sr-price-sort");

        var request = <?php echo json_encode($query)?>;
        if (request.o) {
            if (request.o[0]['f'] == 'price') {
                if (request.o[0]['o'] == 0) {
                    priceSort.val('price:DESC');
                } else {
                    priceSort.val('price:ASC');
                }
            }
        }
        console.log(request);
        var query = request.q;
        form.attr("srtype", request.t);
        if (request.o) {
            if (request.o[0]['f'] == 'price') {
                if (request.o[0]['o'] == 0) {
                    priceSort.val('price:DESC');
                } else {
                    priceSort.val('price:ASC');
                }
            }
        }

        if (query.b !== 1) {
            query = {b: 1, c: [query]};
        }

        for (var i = 0; i < query.c.length; i++) {
            var cond = query.c[i];

            if (sr_parse_condition(cond)) {
                query.c.splice(i, 1);
                i--;
            }
        }

        function format_money(amount, decimals, decimal_sep, thousands_sep) {
            var n = amount,
                c = isNaN(decimals) ? 2 : Math.abs(decimals),
                d = decimal_sep || '.',
                t = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                sign = (n < 0) ? '-' : '',
                i = parseInt(n = Math.abs(n).toFixed(c)) + '',
                j = ((j = i.length) > 3) ? j % 3 : 0;
            return sign + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');
        }

        function sr_parse_condition(condition) {
            if (condition.f == "bedrooms" && (condition.o == ">=" || condition.o == ">")) {
                if (condition.o == ">") {
                    condition.v++;
                }
                bedsfield.find("option[value=" + condition.v + "]").attr("selected", "selected");
            } else if (condition.f == "baths" && (condition.o == ">=" || condition.o == ">")) {
                if (condition.o == ">") {
                    condition.v++;
                }
                bathsfield.find("option[value=" + condition.v + "]").attr("selected", "selected");
            } else if (condition.f == "price") {
                if (condition.o == ">" || condition.o == ">=") {
                    pricefieldl.val("$" + format_money(condition.v, 0));
                } else if (condition.o == "<" || condition.o == "<=") {
                    pricefieldh.val("$" + format_money(condition.v, 0));
                }
            }
            else {
                return false;
            }
            return true;
        }
        refinebtn.click(function () {
            var newrequest = seorets.getFormRequest(form);
            request.q = newrequest.q;
            request.o = [newrequest.o[0]];
            request.q.c = request.q.c.concat(query.c);
            request.g = 1;
            window.location = "?" + encodeURIComponent(Base64.encode(JSON.stringify(request)));
        });

    });
</script>

<?php if ($act == 'true') { ?>
    <script type="text/javascript">
        var zoom_to;

        jQuery(document).ready(function () {
            var request_A = <?php echo json_encode($query)?>;
            var priceSort_A = jQuery('#sr-sort');
            if (request_A.o) {
                var field = request_A.o[0]['f'];
                if (request_A.o[0]['o'] == 1) {
                    var fieldVal = 'ASK';
                } else {
                    var fieldVal = 'DESK';
                }
                var stringSort = field + '-' + fieldVal;
                console.log(stringSort);
                priceSort_A.val(stringSort);
            }
            priceSort_A.change(function () {
                var dataField = jQuery(this).find(':selected').data('field');
                var dataType = jQuery(this).find(':selected').data('type');
                if (request_A.o) {
                    request_A.o[0]['f'] = dataField;
                    request_A.o[0]['o'] = parseInt(dataType);
                } else {
                    var newReq =
                    {
                        f: dataField,
                        o: parseInt(dataType)
                    };

                    request_A.o = [newReq];
                }
                window.location = "?" + encodeURIComponent(Base64.encode(JSON.stringify(request_A)));

            });
            jQuery("#NarrowYourSearchLink, #maptab").on('shown.bs.tab', function () {
                jQuery('.srm-pages').hide();
                jQuery('.sr-listings').hide();
            });
            jQuery("#NarrowYourSearchLink, #maptab").on('hidden.bs.tab', function () {
                jQuery('.srm-pages').show();
                jQuery('.sr-listings').show();
            });

            var map;
            var infowindow;
            var service;
            var pyrmont;
            jQuery(function ($) {
                map = new google.maps.Map(document.getElementById("map_canvas"), {
                    center: new google.maps.LatLng(<?php echo (isset($l->lat) && is_float($l->lat)) ? $l->lat : 0?>, <?php echo (isset($l->lng) && is_float($l->lng)) ? $l->lng : 0?>),
                    zoom: 15,
                    disableDefaultUI: true,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });
            });
            <?php
            //            $link_geo = str_replace('/fig8/sr-search/?', '', $_SERVER['REQUEST_URI']);
            $link_geo = $_SERVER['QUERY_STRING'];

            ?>

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

            var markers = [];
            var infos = [];
            var inbounds = false;
            var updating = false;
            var bounds = new google.maps.LatLngBounds();
            var close_infos = function () {
                for (var n = 0; n < infos.length; n++) {
                    infos[n].close();
                }
            };
            var map_listings = function (listings) {
                console.log(listings.length);
                var add_listings_to_map = function () {
                    for (var p = 0; p < markers.length; p++) {
                        markers[p].setMap(null);
                    }
                    markers = [];
                    bounds = new google.maps.LatLngBounds();

                    jQuery("#listings").html("");

                    for (var n = 0; n < listings.length; n++) {
                        if (!listings[n]) {
                            markers[n] = new google.maps.Marker({
                                position: position,
                                map: map,
                                title: 'ops',
                                icon: "<?php bloginfo('url') ?>/wp-content/plugins/seo-rets/resources/images/marker.png"
                            });
                            markers[n].setVisible(false);
                        } else {

                            var listing = listings[n];
                            jQuery("#listings").html(jQuery("#listings").html() + '<div class="sr-content" style="margin-top: 10px;"><div class="listing row" style="margin-left: 0px;margin-right:0px" onclick="zoom_to(' + n + ')"> <div class="col-md-4 col-sm-4"><a href="<?php bloginfo('url') ?>' + listing.url + '"> <img class="img-responsive" src="' + "http://img.seorets.com/<?php echo $seo_rets_plugin->feed->server_name ?>/" + listing.seo_url + "-" + listing.mls_id + "-1.jpg" + '"> </a></div> <div class="col-md-8 col-sm-8"> <div class="row"> <div class="col-md-12 col-sm-12"><a href="<?php bloginfo('url') ?>' + listing.url + '">' + listing.address + '</a></div> </div> <div class="row"> <div class="col-md-12"> $' + addCommas(listing.price) + ' - ' + listing.city + ', ' + listing.state + '</div> </div> ' + ((typeof listing.proj_name != 'undefined' && typeof listing.unit_number != 'undefined') ? ' <div class="row"> <div class="col-md-8">' + listing.proj_name + '</div> <div class="col-md-4">' + listing.unit_number + '</div> </div> ' : '') + ' <div class="row"> <div class="col-md-8 col-sm-8">Beds:</div> <div class="col-md-4 col-sm-4">' + listing.bedrooms + '</div> </div> <div class="row"> <div class="col-md-8 col-sm-8">Baths:</div> <div class="col-md-4 col-sm-4">' + listing.baths + '</div> </div> ' + ((typeof listing.waterview != 'undefined') ? ' <div class="row"> <div class="col-md-12">Waterview:</div></div><div class="row"><div class="col-md-12">' + listing.waterview + '</div></div>' : '') + '</div></div></div>');

                            var position = new google.maps.LatLng(listing.lat, listing.lng);

                            infos[n] = new google.maps.InfoWindow({
                                content: '<table><tr><td><a target="_parent" href="<?php bloginfo('url') ?>' + listing.url + '"><img style="width:130px;height:86px;" src="http://img.seorets.com/<?php echo $seo_rets_plugin->feed->server_name?>/' + listing.seo_url + '-' + listing.mls_id + '-1.jpg" /' + '></a></td><td valign="top" style="padding-left:5px;"><strong><a target="_parent" href="<?php bloginfo('url') ?>' + listing.url + '">' + listing.address + '</a></strong><br /' + '>Price: $' + addCommas(listing.price) + '<br /' + '>Bedrooms: ' + listing.bedrooms + '<br /' + '>Baths: ' + listing.baths_full + '</td></tr></table>'
                            });

                            markers[n] = new google.maps.Marker({
                                position: position,
                                map: map,
                                title: listing.address,
                                icon: "<?php bloginfo('url') ?>/wp-content/plugins/seo-rets/resources/images/marker.png"
                            });
//                                        console.log(markers.length + "#531");
                            var clicked_index = n;
                            google.maps.event.addListener(markers[n], 'click', (function (x) {

                                return function () {
                                    updating = true;
                                    jQuery(".listing").css("background-color", "#FFF");
                                    close_infos();
                                    infos[x].open(map, markers[x]);
                                    var listings_el = jQuery("#listings");
                                    var listing_el = jQuery(".listing:eq(" + x + ")");
                                    listings_el.animate({
                                        scrollTop: (listings_el.scrollTop() + listing_el.position().top) - ((listings_el.height() / 2) - (listing_el.height() / 2))
                                    }, 1000, function () {
                                        listing_el.css("background-color", "#EEE");
                                        setTimeout(function () {
                                            updating = false;
                                        }, 1000);
                                    });
                                };
                            })(n));


                            bounds.extend(position);
                        }
                    }

                    if (!inbounds) map.fitBounds(bounds);
                    inbounds = false;
                    jQuery("#ajax-loader, #ajax-loader2").toggle();
                    setTimeout(function () {
                        updating = false;
                    }, 1000);

                };

                var needs_geocoding = [];


                for (var n = 0; n < listings.length; n++) {
                    if (((typeof listings[n].lat) == "undefined") || listings[n].lat == " " || listings[n].lng == " " || isNaN(listings[n].lat) || isNaN(listings[n].lng) || listings[n].lat == 0 || listings[n].lng == 0) {
                        needs_geocoding.push({
                            index: n,
                            address: listings[n].address + " " + listings[n].city + " " + listings[n].state + " " + listings[n].zip
                        });
                    }
                }

                if (needs_geocoding.length > 0) {
                    var geocoder = new google.maps.Geocoder();
                    jQuery.ajax({
                        url: '<?php bloginfo('url') ?>/sr-ajax?action=geocode',
                        type: 'post',
                        data: {
                            geocode: JSON.stringify(needs_geocoding)
                        },
                        success: function (response) {
//                                        console.log(response);
                            if (response !== null) {
                                var l = 0;
                                for (l; l < response.geocode.length; l++) {
                                    if (response.geocode[l].latitude != null || response.geocode[l].longitude != null) {
                                        listings[response.geocode[l].index].lat = response.geocode[l].latitude;
                                        listings[response.geocode[l].index].lng = response.geocode[l].longitude;
                                    } else {

                                        delete listings[needs_geocoding[l].index];
                                    }
                                }
                            } else {
                                for (var n = 0;
                                     n < needs_geocoding.length;
                                     n++
                                ) {
                                    delete listings[needs_geocoding[n].index];
                                }

                                listings = Object.keys(listings).map(function (v) {
                                    return listings[v];
                                });
                            }

                            add_listings_to_map();
                        }
                    });
                }
                else {
                    add_listings_to_map();
                }
            };
            zoom_to = function (index) {
                updating = true;
                jQuery(".listing").css("background-color", "#FFF");
                close_infos();
                infos[index].open(map, markers[index]);
                var listings_el = jQuery("#listings");
                var listing_el = jQuery(".listing:eq(" + index + ")");
                listings_el.animate({
                    scrollTop: (listings_el.scrollTop() + listing_el.position().top) - ((listings_el.height() / 2) - (listing_el.height() / 2))
                }, 1000, function () {
                    listing_el.css("background-color", "#EEE");
                    setTimeout(function () {
                        updating = false;
                    }, 1000);
                });
            };
            jQuery("#maptab").on('shown.bs.tab', function () {
                jQuery("#ajax-loader, #ajax-loader2").toggle();

                jQuery.ajax({
                    url: '<?php bloginfo('url') ?>/sr-ajax?action=get-listings-geocoord',
                    type: 'get',
                    data: {
                        conditions: '<?php echo $link_geo; ?>'
                    },
                    success: function (response) {
                        if (response) {
                            console.log(response);
                            map_listings(response);
                        }
                    }
                });
                google.maps.event.trigger(map, 'resize');


                /* Trigger map resize event */
            });
        });
    </script>
<?php } ?>
<div class="sr-content" id="refinesearch">
    <?php
    if ($act == "true") { ?>
        <div>

            <!-- Nav tabs -->
            <ul class="nav-ref nav-ref-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#RefineYourSearch" aria-controls="RefineYourSearch"
                                                          role="tab" data-toggle="tab">Your Search Results</a></li>

                <li role="presentation"><a href="#NarrowYourSearch" id="NarrowYourSearchLink"
                                           aria-controls="NarrowYourSearch" role="tab"
                                           data-toggle="tab">Narrow Your
                        Search</a>
                </li>
                <li role="presentation"><a href="#ModifySearch" id="ModifySearchLink"
                                           aria-controls="ModifySearch" role="tab"
                                           data-toggle="tab">Modify your Search</a>
                </li>
                <li role="presentation"><a href="#mapresults" id="maptab" aria-controls="mapresults" role="tab"
                                           data-toggle="tab">Map Results</a></li>
                <li><a target="_blank" href="<?php echo get_bloginfo('url') ?>/<?php echo $link2 ?>">Save Your
                        Search</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="RefineYourSearch">
                    <div class="row margin-top-10 margin-bottom-10">
                        <div class="col-md-4 col-sm-6">
                            <label for="sr-sort">Sort:</label>
                            <select id="sr-sort" name="sorting" class="sr-sort form-control">
                                <option value="main_order">Sort By</option>
                                <option data-type="1" data-field="price" value="price-ASK">Sort by price: low to
                                    high
                                </option>
                                <option data-type="0" data-field="price" value="price-DESK">Sort by price: high to
                                    low
                                </option>
                                <option data-type="1" data-field="year_built" value="year_built-ASK">Sort by Year Built:
                                    low
                                    to
                                    high
                                </option>
                                <option data-type="0" data-field="year_built" value="year_built-DESK">Sort by Year
                                    Built:
                                    high to
                                    low
                                </option>
                                <option data-type="1" data-field="bedrooms" value="bedrooms-ASK">Sort by Bedrooms: low
                                    to
                                    high
                                </option>
                                <option data-type="0" data-field="bedrooms" value="bedrooms-DESK">Sort by Bedrooms: high
                                    to low
                                </option>
                                <option data-type="1" data-field="baths_full" value="baths_full-ASK">Sort by Baths: low
                                    to
                                    high
                                </option>
                                <option data-type="0" data-field="baths_full" value="baths_full-DESK">Sort by Baths:
                                    high
                                    to low
                                </option>
                                <option data-type="1" data-field="sqft" value="sqft-ASK">Sort by sqft: low to high
                                </option>
                                <option data-type="0" data-field="sqft" value="sqft-DESK">Sort by sqft: high to low
                                </option>
                            </select>
                        </div>
                        <!--                        <div class="col-md-6 col-sm-6">-->
                        <!--                            <label for="">&nbsp;</label>-->
                        <!--                            <input type="submit" class="form-control" id="sr-refinebtn" value="Refine"/>-->
                        <!--                        </div>-->
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="NarrowYourSearch">
                    <?php
                    $get_vars = $this->parse_url_to_vars();
                    if ($get_vars != NULL) { // We can say that the only required variable to be set is conditions in new request format, so we'll assume that's what this request is

                        if (is_array($get_vars->q->c)) {
                            $get_vars->p = isset($get_vars->p) ? intval($get_vars->p) : 10; // Default to 10 per page if request doesn't specify
                            $get_vars->g = isset($get_vars->g) ? intval($get_vars->g) : 1;
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

                            $listings = $this->api_request("get_listings", array(
                                'query' => $newquery,
                                'limit' => array(
                                    'range' => $get_vars->p,
                                    'offset' => ($get_vars->g - 1) * $get_vars->p
                                ),
                                'fields' => array(
                                    'onlycount' => 1
                                )

                            ));
                            $response = $this->api_request("get_listings", array(
                                'query' => $newquery,
                                'limit' => array(
                                    'range' => $listings->count,
                                    'offset' => 0
                                ), 'fields' => array(
                                    'features' => 1
                                )
                            ));
//                            echo "<pre>";
//                            print_r($response);
//                            echo "</pre>";
                            $listings = $response->result;
                            foreach ($listings as $key => $l) {
                                foreach ($l->features as $f) {
                                    if (!($f == "Yes" || $f == "No" || $f == "None")) {
                                        $features_list[$f] = $f;
                                    }
                                }
                            }
                            asort($features_list);

                            include_once('narrow.php');

//                        $currentPage->post_content .= $listing_html;


                        }
                    }
                    ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="ModifySearch">
                    <div class="row margin-top-10 margin-bottom-10">
                        <div class="col-md-4 col-sm-4">
                            <div class="row">
                                <?php

                                if ($query['type'] == 'lnds') {
                                    ?>
                                    <div class="col-md-6 col-sm-6">
                                        <label for="sr-acreagefieldl">Acreage:</label>
                                        <input type="text" id="sr-acreagefieldl" name="sd94jrew" placeholder="Min"
                                               class="sr-formelement form-control"
                                               srfield="acreage" srtype="numeric" sroperator=">=" size="9"/>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <label for="sr-acreagefieldh">&nbsp;</label>
                                        <input type="text" id="sr-acreagefieldh" name="mkx73sdf" placeholder="Max"
                                               class="sr-formelement form-control"
                                               srfield="acreage" srtype="numeric" sroperator="<=" size="9"/>
                                    </div>
                                <?php } else {
                                    ?>
                                    <div class="col-md-6 col-sm-6">
                                        <label for="sr-bedsfield">Bedrooms:</label>
                                        <select class="sr-formelement form-control" id="sr-bedsfield" name="fk30c"
                                                srfield="bedrooms"
                                                srtype="numeric" sroperator=">=">
                                            <option value="">Any</option>
                                            <option value="1">1+</option>
                                            <option value="2">2+</option>
                                            <option value="3">3+</option>
                                            <option value="4">4+</option>
                                            <option value="5">5+</option>
                                            <option value="6">6+</option>
                                            <option value="7">7+</option>
                                            <option value="8">8+</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <label for="sr-bathsfield">Baths:</label>
                                        <select class="sr-formelement form-control" id="sr-bathsfield" name="3jc7q"
                                                srfield="baths"
                                                srtype="numeric" sroperator=">=">
                                            <option value="">Any</option>
                                            <option value="1">1+</option>
                                            <option value="2">2+</option>
                                            <option value="3">3+</option>
                                            <option value="4">4+</option>
                                            <option value="5">5+</option>
                                            <option value="6">6+</option>
                                            <option value="7">7+</option>
                                            <option value="8">8+</option>
                                        </select>
                                    </div>

                                    <?php

                                } ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <label for="sr-pricefieldl">Price:</label>
                                    <input type="text" id="sr-pricefieldl" name="sd94j" placeholder="Min"
                                           class="sr-formelement form-control"
                                           srfield="price" srtype="numeric" sroperator=">=" size="9"/>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <label for="">&nbsp;</label>
                                    <input type="text" id="sr-pricefieldh" name="mkx73" placeholder="Max"
                                           class="sr-formelement form-control"
                                           srfield="price" srtype="numeric" sroperator="<=" size="9"/>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <label for="sr-price-sort">Price sort:</label>

                                    <select id="sr-price-sort" class="sr-order form-control">
                                        <option srfield="price" srdirection="DESC" value="price:DESC">High to Low
                                        </option>
                                        <option srfield="price" srdirection="ASC" value="price:ASC">Low to High</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <label for="">&nbsp;</label>
                                    <input type="submit" class="form-control" id="sr-refinebtn" value="Modify"/>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div role="tabpanel" class="tab-pane" id="mapresults">

                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div id="map-container" class="margin-top-15">
                                <div id="map_canvas" style="width:auto; height: 400px;"></div>

                            </div>
                            <img id="ajax-loader2" style="display:none;"
                                 src="<?php echo $this->plugin_dir ?>resources/images/ajax.gif"/>
                        </div>
                        <!--                        <div class="col-md-4 col-sm-4">-->
                        <!--                            <div style="height: 415px; overflow: auto" id="listings"></div>-->
                        <!---->
                        <!--                        </div>-->
                    </div>

                </div>
            </div>

        </div>
    <?php } else { ?>
        <div class="row">
            <div class="col-md-3 col-sm-3">
                <span>Refine Your Search</span>
            </div>
            <div class="col-md-3 col-sm-3">
<!--            <span id="narrow-search-link" class="narrow-search-link"><a href="--><?php //echo $linkNarrow2 ?><!--">Narrow Your-->
<!--                    Search</a></span>-->
            </div>
            <div style="text-align: right" class="col-md-6 col-sm-6">

            <span id="save-search-link" class="save-search-link"><a href="<?php echo $link2 ?>">Save Your
                    Search</a></span>
            </div>
        </div>
        <div id="ModifySearch" class="row margin-top-10">
            <div class="col-md-4 col-sm-4">
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <label for="sr-bedsfield">Bedrooms:</label>
                        <select class="sr-formelement form-control" id="sr-bedsfield" name="fk30c"
                                srfield="bedrooms"
                                srtype="numeric" sroperator=">=">
                            <option value="">Any</option>
                            <option value="1">1+</option>
                            <option value="2">2+</option>
                            <option value="3">3+</option>
                            <option value="4">4+</option>
                            <option value="5">5+</option>
                            <option value="6">6+</option>
                            <option value="7">7+</option>
                            <option value="8">8+</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <label for="sr-bathsfield">Baths:</label>
                        <select class="sr-formelement form-control" id="sr-bathsfield" name="3jc7q"
                                srfield="baths"
                                srtype="numeric" sroperator=">=">
                            <option value="">Any</option>
                            <option value="1">1+</option>
                            <option value="2">2+</option>
                            <option value="3">3+</option>
                            <option value="4">4+</option>
                            <option value="5">5+</option>
                            <option value="6">6+</option>
                            <option value="7">7+</option>
                            <option value="8">8+</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4">
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <label for="sr-pricefieldl">Price:</label>
                        <input type="text" id="sr-pricefieldl" name="sd94j" placeholder="Min"
                               class="sr-formelement form-control"
                               srfield="price" srtype="numeric" sroperator=">=" size="9"/>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <label for="">&nbsp;</label>
                        <input type="text" id="sr-pricefieldh" name="mkx73" placeholder="Max"
                               class="sr-formelement form-control"
                               srfield="price" srtype="numeric" sroperator="<=" size="9"/>
                    </div>

                </div>
            </div>
            <div class="col-md-4 col-sm-4">
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <label for="sr-price-sort">Price sort:</label>

                        <select id="sr-price-sort" class="sr-order form-control">
                            <option srfield="price" srdirection="DESC" value="price:DESC">High to Low</option>
                            <option srfield="price" srdirection="ASC" value="price:ASC">Low to High</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <label for="">&nbsp;</label>
                        <input type="submit" class="form-control" id="sr-refinebtn" value="Refine"/>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>