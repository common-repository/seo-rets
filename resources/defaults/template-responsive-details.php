<div class="sr-listing-pg">
    <div class="sr-listing-photos">

        <a class="sr-listing-det-buttons" href="<?php echo get_bloginfo('url')?>/sr-favorites?add=<?php echo $l->mls_id?>,<?php echo $type?>"><img src="<?php echo $sr->plugin_dir?>resources/images/save-to-favorites.png" /></a>
        <a class="sr-listing-det-buttons" href="javascript:void(0);" id="sr-alert"><img src="<?php echo $sr->plugin_dir?>resources/images/email-alerts.png" /></a>
        <br />
        <div class="zoom-gallery">
            <a title="<?= $l->subdivision ?> Real Estate - <?= htmlentities($l->address) ?>, <?= htmlentities($l->city) ?>, <?= htmlentities($l->state) ?> - <?= $n ?>" data-source="<?= $photo_dir ?>/<?= $l->seo_url ?>-<?= $l->mls_id ?>-1.jpg" href="<?= $photo_dir ?>/<?= $l->seo_url ?>-<?= $l->mls_id ?>-1.jpg" id="main-photo-a" style="padding-top:8px;"><img
                    src="<?= $photo_dir ?>/<?= $l->seo_url ?>-<?= $l->mls_id ?>-1.jpg"
                    class="img-responsive sr-listing-photo sr-listing-photo-details-main"
                    alt="<?= $l->subdivision ?><?php if ($type == "cnd"): ?> Condos<?php elseif ($type == "lnds"): ?> Lots<?php else: ?> Homes<?php endif; ?> For Sale - <?= htmlentities($l->address) ?>, <?= htmlentities($l->city) ?>, <?= htmlentities($l->state) ?>  - 1"
                    title="<?= $l->subdivision ?><?php if ($type == "cnd"): ?> Condos<?php elseif ($type == "lnds"): ?> Lots<?php else: ?> Homes<?php endif; ?> For Sale - <?= htmlentities($l->address) ?>, <?= htmlentities($l->city) ?>, <?= htmlentities($l->state) ?>  - 1"/></a>
            <?php
            $n = 1;
            while ($n++ < $l->photos): ?>
                <a title="<?= $l->subdivision ?> Real Estate - <?= htmlentities($l->address) ?>, <?= htmlentities($l->city) ?>, <?= htmlentities($l->state) ?> - <?= $n ?>" data-source="<?= $photo_dir ?>/<?= $l->seo_url ?>-<?= $l->mls_id ?>-<?= $n ?>.jpg" href="<?= $photo_dir ?>/<?= $l->seo_url ?>-<?= $l->mls_id ?>-<?= $n ?>.jpg"><img
                        src="<?= $photo_dir ?>/<?= $l->seo_url ?>-<?= $l->mls_id ?>-<?= $n ?>.jpg"
                        class="sr-listing-photo sr-listing-photo-details"
                        alt="<?= $l->subdivision ?> Real Estate - <?= htmlentities($l->address) ?>, <?= htmlentities($l->city) ?>, <?= htmlentities($l->state) ?> - <?= $n ?>"
                        title="<?= $l->subdivision ?> Real Estate - <?= htmlentities($l->address) ?>, <?= htmlentities($l->city) ?>, <?= htmlentities($l->state) ?> - <?= $n ?>"/></a>
            <?php endwhile; ?>
        </div>
        <div style="clear:both;"></div>
    </div>


    <div class="basic_info">
        <?php if (isset($l->spn_pre)) echo $l->spn_pre;
        ?>
        <table>
            <tbody>
            <tr>
                <td><a href="<?php echo $extraData['backLink'];?>"><img class="backButton" src="<?= $sr->plugin_dir?>/resources/images/back-button.png"></a></td>
            </tr>
            <tr>
                <td class="listing-td1"><strong><?php if ( $type == "rens" ): ?>Rent<?php else: ?>List Price<?php endif; ?>: </strong></td><td class="listing-td2"><strong>$<?php echo number_format($l->price, 2)?></strong></td></strong>
            </tr>
            <tr>
                <td >Bedrooms:</td><td class="listing-td2"><?php echo $l->bedrooms?></td>
            </tr>
            <tr>
                <td>Full Baths:</td><td><?php echo  isset($l->baths_full) ? $l->baths_full : 'N/A' ?></td>
            </tr>
            <tr>
                <td>Half Baths:</td><td><?php echo  isset($l->baths_half) ? $l->baths_half : 'N/A' ?></td>
            </tr>
            <tr>
                <td>Total Square Feet:</td><td><?php echo  isset($l->sqft) ? $l->sqft : 'N/A' ?></td>
            </tr>
            <tr>
                <td>City State & Zip:</td><td><a href="<?php echo $extraData['siteUrl'];?>/sr-cities/<?php echo $l->city2.'/'.$type;?>"><?php echo $l->city?></a>, <?php echo $l->state?> <?php echo $l->zip?></td>
            </tr>
            <tr>
                <td>County:</td><td><?php echo $l->county?></td>
            </tr>
            <tr>
                <td>Elementary School:</td><td><?php echo  isset($l->elem_school) ? $l->elem_school : 'N/A' ?></td>
            </tr>
            <tr>
                <td>Middle School:</td><td><?php echo  isset($l->middle_school) ? $l->middle_school : 'N/A' ?></td>
            </tr>
            <tr>
                <td>High School:</td><td><?php echo  isset($l->high_school) ? $l->high_school : 'N/A' ?></td>
            </tr>
            <tr>
                <td>MLS ID:</td><td><?php echo $l->mls_id?></td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="description">
        <strong>Description of Property at <?php echo $l->address?></strong>: <br> <?php echo $l->remarks?>
    </div>

    <div class="additional_info">
        <strong>Amenities, Community, & Other Features of <?php echo $l->address?>:</strong><br>

        <ul>
            <li>Subdivision: <a href="<?php echo $extraData['siteUrl'];?>/sr-communities/<?php echo $l->subdivision2.'/'.$type;?>"><?php echo $l->subdivision?></a></li>
            <li>Home Style: <?php echo $l->style?></li>
            <li>Year Built: <?php echo $l->year_built?></li>
            <li>Appliances: <?php echo $l->appliances?></li>
            <li>Outdoor & Yard Description: <?php echo isset($l->outdoor_desc) ? $l->outdoor_desc : $l->exterior_desc;?></li>
            <li>Lot Dimensions: <?php echo $l->lot_dimensions?></li>
        </ul>

        <?php if(isset($l->features) && is_array($l->features) && count($l->features) > 0) {
            echo "<p>Lot features</p><ul>";
            foreach ($l->features as $feature) :?>

                <li><?php echo ucwords($feature)?></li>
            <?php
            endforeach;
            echo "</ul>";
        }?>
        <p>This Listing was provided courtesy of <?php echo $l->office_name?></p>
        <p><a href="<?php echo get_bloginfo('url')?>/sr-pdf?mls=<?php echo $l->mls_id?>&type=<?php echo $type?>&address=<?php echo $l->seo_url?>">Download listing PDF</a> | <a href="<?php echo get_bloginfo('url')?>/sr-favorites?add=<?php echo $l->mls_id?>,<?php echo $type?>">Save to favorites</a></p>
    </div>
    <div style="clear:both"></div>
    <?php
    wp_enqueue_script('sr_method_google-map',$this->js_resources_dir.'google-map.js');
    wp_print_scripts(array('sr_method_google-map'));
    ?>
    <script type="text/javascript">

        jQuery(function($) {
            var geocoder = new google.maps.Geocoder();

            var map = new google.maps.Map(document.getElementById("map_canvas"), {
                center: new google.maps.LatLng(<?php echo (isset($l->lat) && is_float($l->lat))? $l->lat : 0?>, <?php echo (isset($l->lng) && is_float($l->lng)) ? $l->lng : 0?>),
                zoom: 14,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            var marker = new google.maps.Marker({
                map: map,
                disableDefaultUI: true,
                position: new google.maps.LatLng(<?php echo (isset($l->lat) && is_float($l->lat))? $l->lat : 0?>, <?php echo (isset($l->lng) && is_float($l->lng)) ? $l->lng : 0?>)
            });

            google.maps.event.addListener(marker, 'click', function() {
                document.location = "http://maps.google.com/maps?saddr=<?php echo urlencode($l->address ." " . $l->city . ", " . $l->state)?>";
            });

            geocoder.geocode({
                'address': "<?php echo htmlentities($l->address)?>, <?php echo htmlentities($l->city)?>, <?php echo htmlentities($l->state)?> <?php echo htmlentities($l->zip)?>"
            }, function(results, status) {
                if ( status == google.maps.GeocoderStatus.OK ) {
                    map.setCenter(results[0].geometry.location);
                    marker.setPosition(results[0].geometry.location);
                }
            });
        });

    </script>
    <div id="map_canvas" style="width:auto; height: <?php echo $map_height?>px;"></div>
    <div style="clear:both"></div>

</div>