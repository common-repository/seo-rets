<!-- fotorama.css & fotorama.js. -->
<link href="http://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.css" rel="stylesheet"> <!-- 3 KB -->
<script src="http://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.js"></script> <!-- 16 KB -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<?php
wp_enqueue_script('sr_method_google-map', $this->js_resources_dir . 'google-map.js');
wp_print_scripts(array('sr_method_google-map'));
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
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

            // The idle event is a debounced event, so we can query & listen without
            // throwing too many requests at the server.
        });
        jQuery("#mapTab").on('shown.bs.tab', function () {
            google.maps.event.trigger(map, 'resize');

            var geocoder = new google.maps.Geocoder();
            var marker = new google.maps.Marker({
                map: map,
                position: new google.maps.LatLng(<?php echo (isset($l->lat) && is_float($l->lat)) ? $l->lat : 0?>, <?php echo (isset($l->lng) && is_float($l->lng)) ? $l->lng : 0?>)
            });

            google.maps.event.addListener(marker, 'click', function () {
                document.location = "http://maps.google.com/maps?saddr=<?php echo urlencode($l->address . " " . $l->city . ", " . $l->state)?>";
            });

            geocoder.geocode({
                'address': "<?php echo htmlentities($l->address)?>, <?php echo htmlentities($l->city)?>, <?php echo htmlentities($l->state)?> <?php echo htmlentities($l->zip)?>"
            }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    map.setCenter(results[0].geometry.location);
                    marker.setPosition(results[0].geometry.location);
                }
            });


            /* Trigger map resize event */
        });


    });
</script>
<div class="sr-listing-pg sr-content">
    <div id="detail-tab" class="row">
        <!-- Nav tabs -->


        <ul class="menu-tabs" role="tablist">


            <li role="presentation" class="active"><a href="#tab-gellary" aria-controls="tab-gellary" role="tab"
                                                      data-toggle="tab" aria-expanded="true"><i
                        class="fa fa-image"></i><span class="title"><span>Gallery</span></span></a></li>

            <li role="presentation" class=""><a href="#tab-map" id="mapTab" aria-controls="tab-map" role="tab"
                                                data-toggle="tab"
                                                aria-expanded="false"><i class="fa fa-map-o"></i> <span
                        class="title"><span>Google map</span></span></a>
            </li>


            <li>
                <a href="<?php echo get_bloginfo('url') ?>/sr-pdf?mls=<?php echo $l->mls_id ?>&type=<?php echo $type ?>&address=<?php echo $l->seo_url ?>"
                   target="_blank"><i class="fa fa-file-pdf-o"></i><span class="title"><span>Download PDF</span></span></a>
            </li>
            <li>
                <a class="sr-listing-det-buttons" href="javascript:void(0);" id="sr-alert" target="_blank"
                   title="Email"><i
                        class="fa fa-envelope-o"></i><span
                        class="title"><span>Email</span></span></a>
            </li>
            <li>
                <a href="<?php echo get_bloginfo('url') ?>/sr-favorites?add=<?php echo $l->mls_id ?>,<?php echo $type ?>"
                   target="_blank"><i class="fa fa-star-o"></i><span class="title"><span>Save to Favorites</span></span></a>
            </li>

        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="tab-gellary">
                <div class="fotorama" data-width="100%" data-nav="thumbs">
                    <?php
                    $n = 0;
                    while ($n++ < $l->photos): ?>
                        <a title="<?= $l->subdivision ?> Real Estate - <?= htmlentities($l->address) ?>, <?= htmlentities($l->city) ?>, <?= htmlentities($l->state) ?> - <?= $n ?>"
                           data-source="<?= $photo_dir ?>/<?= $l->seo_url ?>-<?= $l->mls_id ?>-<?= $n ?>.jpg"
                           href="<?= $photo_dir ?>/<?= $l->seo_url ?>-<?= $l->mls_id ?>-<?= $n ?>.jpg"><img
                                src="<?= $photo_dir ?>/<?= $l->seo_url ?>-<?= $l->mls_id ?>-<?= $n ?>.jpg"
                                class="sr-listing-photo sr-listing-photo-details"
                                alt="<?= $l->subdivision ?> Real Estate - <?= htmlentities($l->address) ?>, <?= htmlentities($l->city) ?>, <?= htmlentities($l->state) ?> - <?= $n ?>"
                                title="<?= $l->subdivision ?> Real Estate - <?= htmlentities($l->address) ?>, <?= htmlentities($l->city) ?>, <?= htmlentities($l->state) ?> - <?= $n ?>"/></a>
                    <?php endwhile; ?>
                </div>

            </div>
            <div role="tabpanel" class="tab-pane" id="tab-map">
                <div id="map_canvas" style="width:auto; height: <?php echo $map_height ?>px;"></div>
            </div>
        </div>
    </div>
    <div class="row top-information-block">
        <div class="col-md-3 col-sm-3">
            <span>
                House Size: <span><?php echo isset($l->sqft) ? $l->sqft . ' square' : 'N/A' ?></span>
            </span>
        </div>
        <div class="col-md-3 col-sm-3 second-block">
            Bedrooms: <span><?php echo $l->bedrooms ?></span>
        </div>
        <div class="col-md-3 col-sm-3">
            Baths: <span><?php echo isset($l->baths_full) ? $l->baths_full : 'N/A' ?></span>
        </div>
        <div class="col-md-3 col-sm-3 price-block">
            <span>
                <?php if ($type == "rens"): ?>Rent <?php else: ?>For Sale <?php endif; ?>
                : <strong>$<?php echo number_format($l->price) ?></strong>
            </span>
        </div>
    </div>
    <div class="row more-information-block margin-top-20">
        <div class="col-md-5 col-sm-5 col-xs-12">
            <h2>HOME DETAILS</h2>
            <hr>
            <div class="row">
                <div class="col-md-6 col-sm-6"><span>City State & Zip:</span></div>
                <div class="col-md-6 col-sm-6"><a
                        href="<?php echo $extraData['siteUrl']; ?>/sr-cities/<?php echo $l->city2 . '/' . $type; ?>"><?php echo $l->city ?></a>, <?php echo $l->state ?> <?php echo $l->zip ?>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-6 col-sm-6"><span>County:</span></div>
                <div class="col-md-6 col-sm-6"><?php echo $l->county ?></div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-6 col-sm-6"><span>Elementary School:</span></div>
                <div class="col-md-6 col-sm-6"><?php echo isset($l->elem_school) ? $l->elem_school : 'N/A' ?></div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-6 col-sm-6"><span>Middle School:</span></div>
                <div class="col-md-6 col-sm-6"><?php echo isset($l->middle_school) ? $l->middle_school : 'N/A' ?></div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-6 col-sm-6"><span>High School:</span></div>
                <div class="col-md-6 col-sm-6"><?php echo isset($l->high_school) ? $l->high_school : 'N/A' ?></div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-6 col-sm-6"><span>MLS ID:</span></div>
                <div class="col-md-6 col-sm-6"><?php echo $l->mls_id ?></div>
            </div>
        </div>
        <div class="col-md-7 col-sm-7 col-xs-12">
            <h2>DESCRIPTION</h2>
            <hr>
            <?php echo $l->remarks ?>
        </div>


    </div>
    <div class="row margin-top-20">
        <div class="col-md-12 margin-top-20 features_block">
            <h2>FEATURES & AMENITIES</h2>
            <hr>
            <div class="row">
                <?php if (isset($l->features) && is_array($l->features) && count($l->features) > 0) {
                    $pp = count($l->features) / 4;
                    $newar = array_chunk($l->features, ceil($pp));

                } ?>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <ul>
                        <?php
                        foreach ($newar[0] as $feature) :?>

                            <li><i class="fa fa-check-square-o"></i> <?php echo ucwords($feature) ?></li>
                            <?php
                        endforeach;
                        ?>
                    </ul>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <ul>
                        <?php
                        foreach ($newar[1] as $feature) :?>

                            <li><i class="fa fa-check-square-o"></i> <?php echo ucwords($feature) ?></li>
                            <?php
                        endforeach;
                        ?>
                    </ul>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <ul>
                        <?php
                        foreach ($newar[2] as $feature) :?>

                            <li><i class="fa fa-check-square-o"></i> <?php echo ucwords($feature) ?></li>
                            <?php
                        endforeach;
                        ?>
                    </ul>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <ul>
                        <?php
                        foreach ($newar[3] as $feature) :?>

                            <li><i class="fa fa-check-square-o"></i> <?php echo ucwords($feature) ?></li>
                            <?php
                        endforeach;
                        ?>
                    </ul>
                </div>
            </div>
        </div>

    </div>
    <div class="row margin-top-20">
        <div class="col-md-12 margin-top-20">
            <h2>ADDITIONAL DETAILS</h2>
            <hr>
            <ul>
                <li>Subdivision: <a
                        href="<?php echo $extraData['siteUrl']; ?>/sr-communities/<?php echo $l->subdivision2 . '/' . $type; ?>"><?php echo $l->subdivision ?></a>
                </li>
                <li>Home Style: <?php echo $l->style ?></li>
                <li>Year Built: <?php echo $l->year_built ?></li>
                <li>Appliances: <?php echo $l->appliances ?></li>
                <li>Outdoor & Yard
                    Description: <?php echo isset($l->outdoor_desc) ? $l->outdoor_desc : $l->exterior_desc; ?></li>
                <li>Lot Dimensions: <?php echo $l->lot_dimensions ?></li>
            </ul>
        </div>
    </div>
</div>

