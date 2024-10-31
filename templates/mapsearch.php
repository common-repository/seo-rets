<div class="sr-content">
    <style>
        .sr-word-wrap {
            word-wrap: break-word;
        }
    </style>
    <?php
    $fields = isset($params['fields']) ? explode(";", $params['fields']) : NULL;
    foreach ($fields as $field) {
        $f = explode(":", $field);
        $field_A[$f[0]] = explode(",", $f[1]);
    }
    wp_enqueue_script('sr_method_google-map', $this->js_resources_dir . 'google-map.js', array('jquery'));
    wp_print_scripts(array('sr_method_google-map'));

    wp_enqueue_style('sr_splitSearch', $this->css_resources_dir . 'splitsearch.css');
    wp_print_styles(array('sr_splitSearch'));
    ?>
    <script type="text/javascript">

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


        var zoom_to;
        var rightPolygon = null;
        var drawingManager = null;
        var crtOverlays = [];


        jQuery(function ($) {
            function clearOverlays() {
//                console.log('Clear Overlays');
//                drawingManager.setMap(null);
                for (var i = 0; i < crtOverlays.length; i++) {
                    crtOverlays[i].setMap(null);
                }
                crtOverlays = [];

            }


            function clearPolygon(controlDiv, map) {

                // Set CSS for the control border.
                var controlUI = document.createElement('div');
                controlUI.style.backgroundColor = '#fff';
                controlUI.style.border = '2px solid #fff';
                controlUI.style.borderRadius = '3px 0 0 3px';
                controlUI.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
                controlUI.style.cursor = 'pointer';
                controlUI.style.marginBottom = '22px';
                controlUI.style.textAlign = 'center';
                controlUI.title = 'Click to clear the map';
                controlDiv.appendChild(controlUI);

                // Set CSS for the control interior.
                var controlText = document.createElement('div');
                controlText.style.color = 'rgb(25,25,25)';
                controlText.style.fontFamily = 'Roboto,Arial,sans-serif';
                controlText.style.fontSize = '16px';
                controlText.style.lineHeight = '38px';
                controlText.style.paddingLeft = '5px';
                controlText.style.paddingRight = '5px';
                controlText.innerHTML = 'Clear';
                controlUI.appendChild(controlText);

                // Setup the click event listeners: simply set the map to Chicago.
                controlUI.addEventListener('click', function () {
//                    console.log("click to clear");
                    deleteAllShape();
                });

            }

            var poly = null;
            var polysearch = false;
            var updating_geo = false;
//            console.log(polysearch);
            function get_form_data_polygon(param) {
                var form_data = {
                    "limit": 100,
                    "polygon": param
                };
                $("#search-form select").each(function () {
                    if ($(this).val() != "") form_data[this.name] = $(this).val();
                });
                return form_data;
            };

            function searchInPolygon(polygon) {
                drawingManager.setDrawingMode(null);
                clearOverlays();
                var vertices = polygon.getPath();
                var param = "";
                for (var i = 0; i < vertices.length; i++) {
                    param += vertices.getAt(i).lat() + "," + vertices.getAt(i).lng();
                    if (i < vertices.length - 1) {
                        param += "|";
                    }
                }
                $("#ajax-loader, #ajax-loader2").show();

                jQuery.ajax({
                    url: '<?php bloginfo('url') ?>/sr-ajax?action=getOnPolygon',
                    type: 'post',
                    data: get_form_data_polygon(param),
                    success: function (response) {
                        $("#ajax-loader, #ajax-loader2").hide();
//                        console.log(response);
                        map_listings_pol(response.result);
                    }
                });

            }

            /***************/
            function map_listings_pol(listings) {

                var add_listings_to_map = function () {
                    for (var n = 0; n < markers.length; n++) {
                        markers[n].setMap(null);
                    }
                    markers = [];
                    bounds = new google.maps.LatLngBounds();


                    $("#listings").html("");
                    for (var n = 0; n < listings.length; n++) {

                        var listing = listings[n];

                        $("#listings").html($("#listings").html() + '<div class="sr-content" style="margin-top: 10px;"><div class="listing row" style="margin-left: 0px;margin-right:0px" onclick="zoom_to(' + n + ')"> <div class="col-md-4 col-sm-4"><a href="<?php bloginfo('url') ?>' + listing.url + '"> <img class="img-responsive" src="' + "http://img.seorets.com/<?php echo $seo_rets_plugin->feed->server_name ?>/" + listing.seo_url + "-" + listing.mls_id + "-1.jpg" + '"> </a></div> <div class="col-md-8 col-sm-8"> <div class="row"> <div class="col-md-12 col-sm-12"><a href="<?php bloginfo('url') ?>' + listing.url + '">' + listing.address + '</a></div> </div> <div class="row"> <div class="col-md-12"> $' + addCommas(listing.price) + ' - ' + listing.city + ', ' + listing.state + '</div> </div> ' + ((typeof listing.proj_name != 'undefined' && typeof listing.unit_number != 'undefined') ? ' <div class="row"> <div class="col-md-8">' + listing.proj_name + '</div> <div class="col-md-4">' + listing.unit_number + '</div> </div> ' : '') + ' <div class="row"> <div class="col-md-8 col-sm-8">Beds:</div> <div class="col-md-4 col-sm-4">' + listing.bedrooms + '</div> </div> <div class="row"> <div class="col-md-8 col-sm-8">Baths:</div> <div class="col-md-4 col-sm-4">' + listing.baths + '</div> </div> ' + ((typeof listing.waterview != 'undefined') ? ' <div class="row"> <div class="col-md-12">Waterview:</div></div><div class="row"><div class="col-md-12">' + listing.waterview + '</div></div>' : '') + '</div></div></div>');


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

                        var clicked_index = n;

                        google.maps.event.addListener(markers[n], 'click', (function (x) {
                            return function () {
                                updating_geo = true;
                                $(".listing").css("background-color", "#FFF");
                                close_infos();
                                infos[x].open(map, markers[x]);
                                var listings_el = $("#listings");
                                var listing_el = $(".listing:eq(" + x + ")");
                                listings_el.animate({
                                    scrollTop: (listings_el.scrollTop() + listing_el.position().top) - ((listings_el.height() / 2) - (listing_el.height() / 2))
                                }, 1000, function () {
                                    listing_el.css("background-color", "#EEE");
                                    setTimeout(function () {
                                        updating_geo = false;
                                    }, 1000);
                                });
                            };
                        })(n));


                        bounds.extend(position);
                    }

                    if (!inbounds) map.fitBounds(bounds);
                    inbounds = false;


                };

                var needs_geocoding = [];


                for (var n = 0; n < listings.length; n++) {
                    if (((typeof listings[n].lat) == "undefined") || isNaN(listings[n].lat) || isNaN(listings[n].lng) || listings[n].lat == 0 || listings[n].lng == 0) {
                        needs_geocoding.push({
                            index: n,
                            address: listings[n].address + " " + listings[n].city + ", " + listings[n].state
                        });
                    }
                }

                if (needs_geocoding.length > 0) {
                    $.ajax({
                        url: '<?php bloginfo('url') ?>/sr-ajax?action=geocode',
                        type: 'post',
                        data: {
                            geocode: JSON.stringify(needs_geocoding)
                        },
                        success: function (response) {

                            if (response !== null) {
                                for (var n = 0; n < response.geocode.length; n++) {
                                    listings[response.geocode[n].index].lat = response.geocode[n].latitude;
                                    listings[response.geocode[n].index].lng = response.geocode[n].longitude;
                                }
                            } else {

                                for (var n = 0; n < needs_geocoding.length; n++) {
                                    delete listings[needs_geocoding[n].index];
                                }

                                listings = Object.keys(listings).map(function (v) {
                                    return listings[v];
                                });
                            }

                            add_listings_to_map();
                            updating = true;

                        }
                    });
                } else {
                    add_listings_to_map();
                    updating = true;

                }
            };
            /***************/
            jQuery.ajax({
                url: '<?php bloginfo('url') ?>/sr-ajax?action=getOnType',
                type: 'post',
                data: {
                    subd: jQuery('#property-type').val()
                },
                success: function (response) {
                    if (response != null) {
                        jQuery("#subd-none").removeClass("disp-none");
                        jQuery("#subdivision").html(' ');
                        i = 0;
                        for (i; i <= response.length - 1; i++) {
                            if (i == 0) {
                                jQuery("<option value='' selected='selected'>Any</option>").appendTo("#subdivision");
                            }
//                                console.log(response[i]);
                            jQuery("<option>" + response[i] + "</option>").appendTo("#subdivision");
                        }
                    } else {
                        jQuery("#subd-none").addClass("disp-none");
                    }
                }
            });
            jQuery('#proj_name').change(function () {

                if (jQuery('#property-type').val() != 'cnd' && jQuery('#proj_name').val() != '') {
                    jQuery('#property-type').val('cnd');
                }
            });
            jQuery('#property-type').change(function () {
                if (jQuery('#property-type').val() != '') {
//                    console.log(jQuery('#property-type').val());

                    jQuery.ajax({
                        url: '<?php bloginfo('url') ?>/sr-ajax?action=getOnType',
                        type: 'post',
                        data: {
                            type: jQuery('#property-type').val()
                        },
                        success: function (response) {
//                            console.log(response);
                            jQuery("#cities").html(' ');
                            i = 0;
                            for (i; i <= response.length - 1; i++) {
                                if (i == 0) {
                                    jQuery("<option value='' selected='selected'>Any</option>").appendTo("#cities");
                                }
//                                console.log(response[i]);
                                jQuery("<option>" + response[i] + "</option>").appendTo("#cities");
                            }
                        }
                    });
                    jQuery.ajax({
                        url: '<?php bloginfo('url') ?>/sr-ajax?action=getOnType',
                        type: 'post',
                        data: {
                            areas: jQuery('#property-type').val()
                        },
                        success: function (response) {
//                            console.log(response);
                            jQuery("#areas").html(' ');
                            i = 0;
                            for (i; i <= response.length - 1; i++) {
                                if (i == 0) {
                                    jQuery("<option value='' selected='selected'>Any</option>").appendTo("#areas");
                                }
//                                console.log(response[i]);
                                jQuery("<option>" + response[i] + "</option>").appendTo("#areas");
                            }
                        }
                    });
                    jQuery.ajax({
                        url: '<?php bloginfo('url') ?>/sr-ajax?action=getOnType',
                        type: 'post',
                        data: {
                            subd: jQuery('#property-type').val()
                        },
                        success: function (response) {
                            if (response != null) {
                                jQuery("#subd-none").removeClass("disp-none");
                                jQuery("#subdivision").html(' ');
                                i = 0;
                                for (i; i <= response.length - 1; i++) {
                                    if (i == 0) {
                                        jQuery("<option value='' selected='selected'>Any</option>").appendTo("#subdivision");
                                    }
//                                console.log(response[i]);
                                    jQuery("<option>" + response[i] + "</option>").appendTo("#subdivision");
                                }
                            } else {
                                jQuery("#subd-none").addClass("disp-none");
                            }
                        }
                    });
                }
            });

            var map = new google.maps.Map(document.getElementById('map-canvas'), {
                zoom: 4,
                center: new google.maps.LatLng(30.375393, -86.358401),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            var centerControlDiv = document.createElement('div');
            var clearPolygon = new clearPolygon(centerControlDiv, map);

            centerControlDiv.index = 1;
//            map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(centerControlDiv);

            drawingManager = new google.maps.drawing.DrawingManager({
                drawingControl: true,
                drawingControlOptions: {
                    drawingModes: [google.maps.drawing.OverlayType.POLYGON],
                    position: google.maps.ControlPosition.TOP_CENTER
                },
                markerOptions: {
                    draggable: true
                },
                polygonOptions: {
                    strokeWeight: 2,
                    fillOpacity: 0.45,
                    fillColor: "#FF0000",
                    strokeColor: "#FF0000",
                    editable: true
                }
            });

            drawingManager.setMap(map);
            var all_overlays = [];
            var selectedShape;
            var colors = ['#1E90FF', '#FF1493', '#32CD32', '#FF8C00', '#4B0082'];
            var selectedColor;
            var colorButtons = {};

            function clearSelection() {
                if (selectedShape) {
                    selectedShape.setEditable(false);
                    selectedShape = null;
                }
            }

            function setSelection(shape) {
                clearSelection();
                selectedShape = shape;
                shape.setEditable(true);
//                selectColor(shape.get('fillColor') || shape.get('strokeColor'));
            }

            function deleteSelectedShape() {
                if (selectedShape) {
                    selectedShape.setMap(null);
                }
            }

            function deleteAllShape() {
//                console.log('deleteAllShape |362');
                for (var i = 0; i < all_overlays.length; i++) {
                    all_overlays[i].overlay.setMap(null);
                }
                all_overlays = [];
                update_map();
                map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].clear();

            }

            google.maps.event.addListener(drawingManager, 'overlaycomplete', function (e) {
                all_overlays.push(e);
                if (e.type != google.maps.drawing.OverlayType.MARKER) {
//                    console.log(e);
                    var newShape = e.overlay;
                    newShape.type = e.type;
                    setSelection(newShape);
//                    alert('as');
                }
            });
            google.maps.event.addListener(drawingManager, 'polygoncomplete', function (polygon) {
                map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(centerControlDiv);
                polysearch = true;
//                console.log(polygon);
                poly = polygon;
                google.maps.event.addListener(polygon.getPaths().getAt(0), 'set_at', function () {
                    searchInPolygon(polygon);
                });
                google.maps.event.addListener(polygon.getPaths().getAt(0), 'insert_at', function () {
                    searchInPolygon(polygon);
                });
                google.maps.event.addListener(polygon.getPaths().getAt(0), 'remove_at', function () {
                    searchInPolygon(polygon);
                });


                if (rightPolygon != null) {
                    rightPolygon.setMap(null);
                }
                rightPolygon = polygon;
//                update_map();
                searchInPolygon(polygon);

            });
            var inbounds = false;
            var updating = false;
            var bounds = new google.maps.LatLngBounds();
            var markers = [];
            var infos = [];

            var close_infos = function () {
                for (var n = 0; n < infos.length; n++) {
                    infos[n].close();
                }
            };

            zoom_to = function (index) {
                updating = true;
                $(".listing").css("background-color", "#FFF");
                close_infos();
                infos[index].open(map, markers[index]);
                var listings_el = $("#listings");
                var listing_el = $(".listing:eq(" + index + ")");
                listings_el.animate({
                    scrollTop: (listings_el.scrollTop() + listing_el.position().top) - ((listings_el.height() / 2) - (listing_el.height() / 2))
                }, 1000, function () {
                    listing_el.css("background-color", "#EEE");
                    setTimeout(function () {
                        updating = false;
                    }, 1000);
                });
            };

            var update_map = function () {

//                console.log('upd map |347');
                    var get_form_data = function () {

                        var b = map.getBounds();

                        var form_data = {
                            "limit": 100
                        };

                        var truncate_coord = function (coord, percision) {
                            percision = typeof percision !== 'undefined' ? percision : 6;

                            var length = coord.toString().split(".")[0].length;

                            return coord.toPrecision(length + percision);
                        };

                        if (typeof b != 'undefined' && inbounds) {
                            form_data['ne-lat'] = Math.ceil(b.getNorthEast().lat() * 1000000) / 1000000;
                            form_data['ne-lng'] = Math.ceil(b.getNorthEast().lng() * 1000000) / 1000000;
                            form_data['sw-lat'] = Math.floor(b.getSouthWest().lat() * 1000000) / 1000000;
                            form_data['sw-lng'] = Math.floor(b.getSouthWest().lng() * 1000000) / 1000000;
                        }

                        $("#search-form select").each(function () {
                            if ($(this).val() != "") form_data[this.name] = $(this).val();
                        });

                        return form_data;
                    };

                    var map_listings = function (listings) {

                        var add_listings_to_map = function () {
                            for (var p = 0; p < markers.length; p++) {
//                            console.log(markers);
//                                    console.log("#499" + markers.length);
                                markers[p].setMap(null);
                            }
                            markers = [];
                            bounds = new google.maps.LatLngBounds();


                            $("#listings").html("");
//                        console.log(listings);
                            for (var n = 0; n < listings.length; n++) {
                                if (!listings[n]) {
//                                n++;
//                                        listings.length--;
                                    markers[n] = new google.maps.Marker({
                                        position: position,
                                        map: map,
                                        title: listing.address,
                                        icon: "<?php bloginfo('url') ?>/wp-content/plugins/seo-rets/resources/images/marker.png"
                                    });
                                    markers[n].setVisible(false);
//                                        console.log('next #508');
                                } else {

                                    var listing = listings[n];

                                    $("#listings").html($("#listings").html() + '<div class="sr-content" style="margin-top: 10px;"><div class="listing row" style="margin-left: 0px;margin-right:0px" onclick="zoom_to(' + n + ')"> <div class="col-md-4 col-sm-4"><a href="<?php bloginfo('url') ?>' + listing.url + '"> <img class="img-responsive" src="' + "http://img.seorets.com/<?php echo $seo_rets_plugin->feed->server_name ?>/" + listing.seo_url + "-" + listing.mls_id + "-1.jpg" + '"> </a></div> <div class="col-md-8 col-sm-8"> <div class="row"> <div class="col-md-12 col-sm-12"><a href="<?php bloginfo('url') ?>' + listing.url + '">' + listing.address + '</a></div> </div> <div class="row"> <div class="col-md-12"> $' + addCommas(listing.price) + ' - ' + listing.city + ', ' + listing.state + '</div> </div> ' + ((typeof listing.proj_name != 'undefined' && typeof listing.unit_number != 'undefined') ? ' <div class="row"> <div class="col-md-8">' + listing.proj_name + '</div> <div class="col-md-4">' + listing.unit_number + '</div> </div> ' : '') + ' <div class="row"> <div class="col-md-8 col-sm-8">Beds:</div> <div class="col-md-4 col-sm-4">' + listing.bedrooms + '</div> </div> <div class="row"> <div class="col-md-8 col-sm-8">Baths:</div> <div class="col-md-4 col-sm-4">' + listing.baths + '</div> </div> ' + ((typeof listing.waterview != 'undefined') ? ' <div class="row"> <div class="col-md-12">Waterview:</div></div><div class="row"><div class="col-md-12">' + listing.waterview + '</div></div>' : '') + '</div></div></div>');


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
                                            $(".listing").css("background-color", "#FFF");
                                            close_infos();
                                            infos[x].open(map, markers[x]);
                                            var listings_el = $("#listings");
                                            var listing_el = $(".listing:eq(" + x + ")");
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
                            $("#ajax-loader, #ajax-loader2").toggle();
                            setTimeout(function () {
                                updating = false;
                            }, 1000);

                        };

                        var needs_geocoding = [];


                        for (var n = 0; n < listings.length; n++) {
                            if (((typeof listings[n].lat) == "undefined") || listings[n].lat == " " || listings[n].lng == " " || isNaN(listings[n].lat) || isNaN(listings[n].lng) || listings[n].lat == 0 || listings[n].lng == 0) {
//                                    console.log('need geocode #565');
                                needs_geocoding.push({
                                    index: n,
                                    address: listings[n].address + " " + listings[n].city + " " + listings[n].state + " " + listings[n].zip
                                });
                            }
                        }
//                            console.log(needs_geocoding.length);
//                            console.log(needs_geocoding);
//                            console.log(listings.length);
//                            console.log(listings);

                        if (needs_geocoding.length > 0) {
                            var geocoder = new google.maps.Geocoder();
                            $.ajax({
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
//                                                    console.log(needs_geocoding[l].index);
//                                                    console.log(listings.length);
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
//                                            console.log(needs_geocoding.length);
//                                            console.log(listings.length);
//                                            console.log(listings);

                                        add_listings_to_map();
                                    }
                                }
                            )
                            ;
                        }
                        else {
                            add_listings_to_map();
                        }
                    }

                    updating = true;
                    $("#ajax-loader, #ajax-loader2").toggle();
                    $.ajax({
                        url: "<?php bloginfo('url') ?>/sr-ajax?action=map-search",
                        type: "post",
                        data: get_form_data(),
                        success: function (data) {
                            map_listings(data.result);
                        }
                    });
                }
                ;
            //            $("#search-area select").change(update_map);
            $("#search-area select").change(function () {
                if (!polysearch) {
//                    console.log('CHANGE | 502');
                    update_map();
                } else {
//                    console.log('CHANGE Else | 505');
                    searchInPolygon(poly);
                }
            });
            google.maps.event.addListener(map, 'idle', function () {
                if (!updating) {
                    //alert(map.getBounds());
//                    console.log('Listaner |513');
                    inbounds = true;
                    update_map();
                }
            });

            update_map();


        })
        ;


    </script>
    <?php
    wp_enqueue_style('sr_templates_splitsearch', $this->css_resources_dir . 'templates/splitsearch.css');
    wp_print_styles(array('sr_templates_splitsearch'));
    ?>
    <div id="search-area" class="table-responsive respstyle">
        <div id="search-form">
            <?php
            if ($fields != NULL) {
                $i = 0;
                foreach ($field_A as $key => $values) {
                    ?>
                    <select style="display: none" class="sr-formelement" name="fil[<?= $i; ?>][<?= $key; ?>]" id="">
                        <?php
                        foreach ($values as $sval) {
                            ?>
                            <option selected value="<?= $sval; ?>"><?= $sval ?></option>
                            <?
                        }
                        ?>
                    </select>
                    <?
                    $i++;
                }
            }
            ?>
            <div class="row">
                <div class="col-md-4 col-sm-4 ">
                    <label for="property-type">Type:</label>
                    <select id="property-type" class="form-control" name="type">
                        <?php
                        $n = 0;
                        foreach ($sr->metadata as $key => $val) {
                            if ($sr->is_type_hidden($key)) {
                                continue;
                            }
                            if (($key == 'res') || ($key == 'cre')) {
                                echo "<option selected  value='$key' /> " . (isset($val->pretty_name) ? $val->pretty_name : $key) . "</option>";
                            } else {
                                echo "<option  value='$key' /> " . (isset($val->pretty_name) ? $val->pretty_name : $key) . "</option>";
                            }


                        }
                        ?>
                    </select>

                </div>
                <div class="col-md-4 col-sm-4">
                    <div class="row">
                        <!--                        <div class="col-md-4 col-sm-4">Bedrooms:</div>-->
                        <div class="col-md-6 col-sm-6">
                            <label for="">Bedrooms:</label>
                            <select class="form-control" name="bedrooms-low">
                                <option value="">Any</option>
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                                <option>6</option>
                                <option>7</option>
                                <option>8</option>
                                <option>9</option>
                                <option>10</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <label for="">&nbsp;</label>
                            <select class="form-control" name="bedrooms-high">
                                <option value="">Any</option>
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                                <option>6</option>
                                <option>7</option>
                                <option>8</option>
                                <option>9</option>
                                <option>10</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4">
                    <div class="row">
                        <!--                        <div class="col-md-4 col-sm-4">Baths:</div>-->
                        <div class="col-md-6 col-sm-6">
                            <label for="">Baths:</label>
                            <select class="form-control" name="baths-low">
                                <option value="">Any</option>
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                                <option>6</option>
                                <option>7</option>
                                <option>8</option>
                                <option>9</option>
                                <option>10</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <label for="">&nbsp;</label>

                            <select class="form-control" name="baths-high">
                                <option value="">Any</option>
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                                <option>6</option>
                                <option>7</option>
                                <option>8</option>
                                <option>9</option>
                                <option>10</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row" style="margin-top: 15px">
                <div class="col-md-4 col-sm-4">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Min price:</label>
                            <select class="form-control" name="price-low">
                                <option value="">Any</option>
                                <option value="100000">$100,000</option>
                                <option value="125000">$125,000</option>
                                <option value="150000">$150,000</option>
                                <option value="175000">$175,000</option>
                                <option value="200000">$200,000</option>
                                <option value="225000">$225,000</option>
                                <option value="250000">$250,000</option>
                                <option value="275000">$275,000</option>
                                <option value="300000">$300,000</option>
                                <option value="325000">$325,000</option>
                                <option value="350000">$350,000</option>
                                <option value="375000">$375,000</option>
                                <option value="400000">$400,000</option>
                                <option value="425000">$425,000</option>
                                <option value="450000">$450,000</option>
                                <option value="475000">$475,000</option>
                                <option value="500000">$500,000</option>
                                <option value="600000">$600,000</option>
                                <option value="700000">$700,000</option>
                                <option value="800000">$800,000</option>
                                <option value="900000">$900,000</option>
                                <option value="1000000">$1,000,000</option>
                                <option value="1500000">$1,500,000</option>
                                <option value="2000000">$2,000,000</option>
                                <option value="2500000">$2,500,000</option>
                                <option value="3000000">$3,000,000</option>
                                <option value="3500000">$3,500,000</option>
                                <option value="4000000">$4,000,000</option>
                                <option value="4500000">$4,500,000</option>
                                <option value="5000000">$5,000,000</option>
                            </select>
                        </div>
                        <!--                        <div class="col-md-4 col-sm-4"> City:</div>-->

                    </div>
                </div>
                <div class="col-md-4 col-sm-4">


                    <label for="">Max price:</label>
                    <select class="form-control" name="price-high">
                        <option value="">Any</option>
                        <option value="100000">$100,000</option>
                        <option value="125000">$125,000</option>
                        <option value="150000">$150,000</option>
                        <option value="175000">$175,000</option>
                        <option value="200000">$200,000</option>
                        <option value="225000">$225,000</option>
                        <option value="250000">$250,000</option>
                        <option value="275000">$275,000</option>
                        <option value="300000">$300,000</option>
                        <option value="325000">$325,000</option>
                        <option value="350000">$350,000</option>
                        <option value="375000">$375,000</option>
                        <option value="400000">$400,000</option>
                        <option value="425000">$425,000</option>
                        <option value="450000">$450,000</option>
                        <option value="475000">$475,000</option>
                        <option value="500000">$500,000</option>
                        <option value="600000">$600,000</option>
                        <option value="700000">$700,000</option>
                        <option value="800000">$800,000</option>
                        <option value="900000">$900,000</option>
                        <option value="1000000">$1,000,000</option>
                        <option value="1500000">$1,500,000</option>
                        <option value="2000000">$2,000,000</option>
                        <option value="2500000">$2,500,000</option>
                        <option value="3000000">$3,000,000</option>
                        <option value="3500000">$3,500,000</option>
                        <option value="4000000">$4,000,000</option>
                        <option value="4500000">$4,500,000</option>
                        <option value="5000000">$5,000,000</option>
                    </select>


                    <!--                        <div class="col-md-4 col-sm-4">Waterfront:</div>-->

                </div>
                <!--            </div>-->
                <div class="col-md-4 col-sm-4">
                    <div class="row">
                        <!--                        <div class="col-md-4 col-sm-4">Waterview:</div>-->
                        <div class="col-md-12">
                            <label for="">City:</label>
                            <select id="cities" class="form-control" name="city">
                                <option value="" selected="selected">Any</option>
                                <?php
                                $cities = $sr->metadata->res->fields->city->values;
                                sort($cities);
                                foreach ($cities as $city) {
                                    echo "<option>" . htmlentities($city) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top: 15px">
                <div class="col-md-4 col-sm-4">
                    <div class="row">
                        <!--                        <div class="col-md-4 col-sm-4"> Area:</div>-->
                        <div class="col-md-12">
                            <label for="">Area:</label>
                            <select class="form-control" id="areas" name="area">
                                <option value="">Any</option>
                                <?php
                                $areas = $sr->metadata->res->fields->area->values;
                                sort($areas);
                                foreach ($areas as $area) {
                                    echo "<option value='$area'>" . htmlentities($area) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="subd-none" class="col-md-4 col-sm-4 disp-none">
                    <div class="row">
                        <!--                        <div class="col-md-4 col-sm-4"> Area:</div>-->
                        <div class="col-md-12">
                            <label for="">Subdivision:</label>
                            <select class="form-control" id="subdivision" name="subdivision">
                                <option value="">Any</option>
                                <?php
                                $subdivision = $sr->metadata->res->fields->subdivision->values;
                                sort($subdivision);
                                foreach ($subdivision as $subd) {
                                    echo "<option value='$subd'>" . htmlentities($subd) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4">
                    <div class="row">
                        <!--                        <div class="col-md-4 col-sm-4">Order:</div>-->
                        <div class="col-md-12">
                            <label for="">Order:</label>
                            <select class="form-control" name="order">
                                <option value="price:DESC">Price High to Low</option>
                                <option value="price:ASC">Price Low to High</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <div class="col-md-12">
            <div id="map-container">
                <div id="map-canvas"></div>
                <div id="ajax-loader" style="display:none;">Retrieving Most Recent MLS Data<br/><br/><img
                        src="<?php echo $this->plugin_dir ?>resources/images/ajax.gif"/></div>
            </div>
        </div>
        <?php if ($_SERVER['REQUEST_URI'] == '/sr-mapsearch/') { ?>
            <div id="listings-container" class="col-md-12">
                <div id="listings"></div>
                <img id="ajax-loader2" src="
        <?php echo $this->plugin_dir ?>resources/images/ajax2.gif"
                     style="display:none;"/>
            </div>
        <?php } ?>
        <div style="clear:both;"></div>
        <!--        <p>Powered By <a href="http://seorets.com/" target="_blank">SEO RETS</a></p>-->

    </div>

</div>