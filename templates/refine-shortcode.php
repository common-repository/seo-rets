<?php
$act = $this->boot_refine_sc;
wp_enqueue_script('sr_method_google-map', $this->js_resources_dir . 'google-map.js');
wp_print_scripts(array('sr_method_google-map'));
?>

<script type="text/javascript">

    <?php
    $short_conditions = $sr->convert_to_search_conditions($qcc);
    $shortcode_conditions = json_encode($short_conditions['c']);
    ?>

    function str_to_float(str) {
        return parseFloat(str.replace(/[^0-9.]/g, ""));
    }

    if (typeof window.Base64 === "undefined") window.Base64 = {
// private property
        _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

// public method for encoding
        encode: function (input) {
            var output = "";
            var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
            var i = 0;

            input = Base64._utf8_encode(input);

            while (i < input.length) {

                chr1 = input.charCodeAt(i++);
                chr2 = input.charCodeAt(i++);
                chr3 = input.charCodeAt(i++);

                enc1 = chr1 >> 2;
                enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                enc4 = chr3 & 63;

                if (isNaN(chr2)) {
                    enc3 = enc4 = 64;
                } else if (isNaN(chr3)) {
                    enc4 = 64;
                }

                output = output +
                    this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
                    this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

            }

            return output;
        },

// public method for decoding
        decode: function (input) {
            var output = "";
            var chr1, chr2, chr3;
            var enc1, enc2, enc3, enc4;
            var i = 0;

            input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

            while (i < input.length) {

                enc1 = this._keyStr.indexOf(input.charAt(i++));
                enc2 = this._keyStr.indexOf(input.charAt(i++));
                enc3 = this._keyStr.indexOf(input.charAt(i++));
                enc4 = this._keyStr.indexOf(input.charAt(i++));

                chr1 = (enc1 << 2) | (enc2 >> 4);
                chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                chr3 = ((enc3 & 3) << 6) | enc4;

                output = output + String.fromCharCode(chr1);

                if (enc3 != 64) {
                    output = output + String.fromCharCode(chr2);
                }
                if (enc4 != 64) {
                    output = output + String.fromCharCode(chr3);
                }

            }

            output = Base64._utf8_decode(output);

            return output;

        },

// private method for UTF-8 encoding
        _utf8_encode: function (string) {
            string = string.replace(/\r\n/g, "\n");
            var utftext = "";

            for (var n = 0; n < string.length; n++) {

                var c = string.charCodeAt(n);

                if (c < 128) {
                    utftext += String.fromCharCode(c);
                }
                else if ((c > 127) && (c < 2048)) {
                    utftext += String.fromCharCode((c >> 6) | 192);
                    utftext += String.fromCharCode((c & 63) | 128);
                }
                else {
                    utftext += String.fromCharCode((c >> 12) | 224);
                    utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                    utftext += String.fromCharCode((c & 63) | 128);
                }

            }

            return utftext;
        },

// private method for UTF-8 decoding
        _utf8_decode: function (utftext) {
            var string = "";
            var i = 0;
            var c = c1 = c2 = 0;

            while (i < utftext.length) {

                c = utftext.charCodeAt(i);

                if (c < 128) {
                    string += String.fromCharCode(c);
                    i++;
                }
                else if ((c > 191) && (c < 224)) {
                    c2 = utftext.charCodeAt(i + 1);
                    string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                    i += 2;
                }
                else {
                    c2 = utftext.charCodeAt(i + 1);
                    c3 = utftext.charCodeAt(i + 2);
                    string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                    i += 3;
                }

            }

            return string;
        }

    };
    var conditions = <?php echo $shortcode_conditions?>;
    jQuery(function ($) {
        $("#sr-refinebtn").click(function () {

            if ($("#bedrooms").val() != "") conditions.push({
                "f": "bedrooms",
                "o": ">=",
                "v": parseInt($("#bedrooms").val())
            });

            if ($("#baths").val() != "") conditions.push({
                "f": "baths",
                "o": ">=",
                "v": parseInt($("#baths").val())
            });


            if ($("#price-low").val() != "") conditions.push({
                "f": "price",
                "o": ">=",
                "v": str_to_float($("#price-low").val())
            });

            if ($("#price-high").val() != "") conditions.push({
                "f": "price",
                "o": "<=",
                "v": str_to_float($("#price-high").val())
            });


            var request = {
                "q": {
                    "b": 1,
                    "c": conditions
                },
                "t": "<?php echo $type?>",
                "o": [{
                    "f": jQuery('select#sr-price-sort option:selected').attr('srfield'),
                    "o": jQuery('select#sr-price-sort option:selected').attr('srdirection')
                }],
                "p": 10,
                "g": 1
            };


            document.location = "<?php echo get_bloginfo('url')?>/sr-search?" + encodeURIComponent(Base64.encode(JSON.stringify(request)));
        });
    });


</script>
<?php if ($act == 'true') { ?>
    <script type="text/javascript">
        var zoom_to;
        var conditions2 = <?php echo $shortcode_conditions?>;

        jQuery(document).ready(function () {
            var priceSort_A = jQuery('#sr-sort');
            priceSort_A.change(function () {
                var dataField = jQuery(this).find(':selected').data('field');
                var dataType = jQuery(this).find(':selected').data('dir');
                var request2 = {
                    "q": {
                        "b": 1,
                        "c": conditions2
                    },
                    "t": "<?php echo $type?>",
                    "o": [{
                        "f": dataField,
                        "o": dataType
                    }],
                    "p": 10,
                    "g": 1
                };
                document.location = "<?php echo get_bloginfo('url')?>/sr-search?" + encodeURIComponent(Base64.encode(JSON.stringify(request2)));


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
            var request3 = {
                "q": {
                    "b": 1,
                    "c": conditions2
                },
                "t": "<?php echo $type?>",
                "p": 10,
                "g": 1
            };

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
                        conditions: Base64.encode(JSON.stringify(request3))
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
    <?php if ($act == "true") { ?>
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

            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="RefineYourSearch">
                    <div class="row margin-top-10 margin-bottom-10">
                        <div class="col-md-4 col-sm-6">
                            <label for="sr-sort">Sort:</label>
                            <select id="sr-sort" name="sorting" class="sr-sort form-control">
                                <option value="main_order">Sort By</option>
                                <option data-type="1" data-dir="ASK" data-field="price" value="price-ASK">Sort by
                                    price: low to
                                    high
                                </option>
                                <option data-type="0" data-dir="DESK" data-field="price" value="price-DESK">Sort by
                                    price: high to
                                    low
                                </option>
                                <option data-type="1" data-dir="ASK" data-field="year_built" value="year_built-ASK">
                                    Sort by Year
                                    Built:
                                    low
                                    to
                                    high
                                </option>
                                <option data-type="0" data-dir="DESK" data-field="year_built"
                                        value="year_built-DESK">Sort by Year
                                    Built:
                                    high to
                                    low
                                </option>
                                <option data-type="1" data-dir="ASK" data-field="bedrooms" value="bedrooms-ASK">Sort
                                    by Bedrooms:
                                    low
                                    to
                                    high
                                </option>
                                <option data-type="0" data-dir="DESK" data-field="bedrooms" value="bedrooms-DESK">
                                    Sort by Bedrooms:
                                    high
                                    to low
                                </option>
                                <option data-type="1" data-dir="ASK" data-field="baths_full" value="baths_full-ASK">
                                    Sort by Baths:
                                    low
                                    to
                                    high
                                </option>
                                <option data-type="0" data-dir="DESK" data-field="baths_full"
                                        value="baths_full-DESK">Sort by Baths:
                                    high
                                    to low
                                </option>
                                <option data-type="1" data-dir="ASK" data-field="sqft" value="sqft-ASK">Sort by
                                    sqft: low to high
                                </option>
                                <option data-type="0" data-dir="DESK" data-field="sqft" value="sqft-DESK">Sort by
                                    sqft: high to low
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane " id="NarrowYourSearch">
                    <?php
                    $get_vars = $this->parse_url_to_vars();
                    //                        if ($get_vars != NULL) {
                    // We can say that the only required variable to be set is conditions in new request format, so we'll assume that's what this request is

                    //                            if (is_array($shortcode_conditions)) {
                    //                                $get_vars->p = isset($get_vars->p) ? intval($get_vars->p) : 10; // Default to 10 per page if request doesn't specify
                    //                                $get_vars->g = isset($get_vars->g) ? intval($get_vars->g) : 1;
                    //                                $conditions = $this->convert_to_api_conditions($get_vars->q);
                    $prioritization = get_option('sr_prioritization');
                    $prioritization = ($prioritization === false) ? array() : $prioritization;

                    $query = array(
                        "type" => $type,
                        "query" => $qcc,
                    );
                    //                        echo "<pre>";
                    //                            print_r($qcc);
                    //                        echo "</pre>";
                    if (isset($order) && is_array($order)) {
                        $query["order"] = array();

                        foreach ($order as $ord) {
                            $query["order"][] = array(
                                "field" => $ord['field'],
                                "order" => $ord['order']
                            );
                        }
                    }
                    $newquery = $this->prioritize($query, $prioritization);

                    $listings = $this->api_request("get_listings", array(
                        'query' => $newquery,
                        'limit' => array(
                            'range' => $perpage,
                            'offset' => (($page - 1) * $perpage)
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
                    $listings = $response->result;
                    foreach ($listings as $key => $l) {
                        foreach ($l->features as $f) {
                            if (!($f == "Yes" || $f == "No" || $f == "None")) {
                                $features_list[$f] = $f;
                            }
                        }
                    }
                    asort($features_list);

                    include_once('narrow-shortcode.php');

                    //                        $currentPage->post_content .= $listing_html;

                    //
                    //                            }
                    //                        }
                    ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="ModifySearch">
                    <!--                    <div class="row">-->
                    <!--                        <div class="col-md-12">-->
                    <!--                            <span>Refine Your Search</span>-->
                    <!--                        </div>-->
                    <!--                    </div>-->
                    <div class="row margin-top-10">
                        <div class="col-md-4 col-sm-4">
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <label for="bedrooms">Bedrooms:</label>
                                    <select class="form-control" id="bedrooms">
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
                                    <label for="">Baths:</label>
                                    <select class="form-control" id="baths">
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
                                    <label for="">Price:</label>
                                    <input class="form-control" type="text" size="9" placeholder="Min"
                                           id="price-low">
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <label for="">&nbsp;</label>
                                    <input class="form-control" type="text" size="9" placeholder="Max"
                                           id="price-high">
                                </div>

                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="row">
                                <div class="col-md-6 col-sm-6">

                                    <label for="sr-price-sort">Price sort:</label>

                                    <select id="sr-price-sort" class="sr-order form-control">
                                        <option srfield="price" srdirection="0" value="price:DESC">High to Low
                                        </option>
                                        <option srfield="price" srdirection="1" value="price:ASC">Low to High
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <label for="">&nbsp;</label>
                                    <input type="submit" class="form-control" id="sr-refinebtn" value="Refine">
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
                    </div>

                </div>
            </div>

        </div>
    <?php } else { ?>
        <div class="row">
            <div class="col-md-12">
                <span>Refine Your Search</span>
            </div>
        </div>
        <div class="row margin-top-10">
            <div class="col-md-4 col-sm-4">
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <label for="bedrooms">Bedrooms:</label>
                        <select class="form-control" id="bedrooms">
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
                        <label for="">Baths:</label>
                        <select class="form-control" id="baths">
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
                        <label for="">Price:</label>
                        <input class="form-control" type="text" size="9" placeholder="Min" id="price-low">
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <label for="">&nbsp;</label>
                        <input class="form-control" type="text" size="9" placeholder="Max" id="price-high">
                    </div>

                </div>
            </div>
            <div class="col-md-4 col-sm-4">
                <div class="row">
                    <div class="col-md-6 col-sm-6">

                        <label for="sr-price-sort">Price sort:</label>

                        <select id="sr-price-sort" class="sr-order form-control">
                            <option srfield="price" srdirection="0" value="price:DESC">High to Low</option>
                            <option srfield="price" srdirection="1" value="price:ASC">Low to High</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <label for="">&nbsp;</label>
                        <input type="submit" class="form-control" id="sr-refinebtn" value="Refine">
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
