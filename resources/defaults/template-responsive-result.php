<div class="sr-listing">
    <div class="sr-listing-image">

        <a href="<?php echo get_bloginfo('url')?><?php echo $url?>"><img src="<?php echo $photo_dir?>/<?php echo $l->seo_url?>-<?php echo $l->mls_id?>-1.jpg" class="sr-listing-photo" alt="<?php echo htmlentities($l->address)?>, <?php echo htmlentities($l->city)?>, <?php echo htmlentities($l->state)?> <?php echo htmlentities($l->zip)?> - 1" title="<?php echo htmlentities($l->address)?>, <?php echo htmlentities($l->city)?>, <?php echo htmlentities($l->state)?> <?php echo htmlentities($l->zip)?> - 1"  /></a>
    </div>

    <div class="sr-listing-descr">
        <p class="sr-listing-title">
            <a href="<?php echo get_bloginfo('url')?><?php echo $url?>"><?php echo htmlentities($l->address)?></a>
        </p>
        <p>
            <strong><?php if ( $type == "rens" ): ?>Rent<?php else: ?>List Price<?php endif; ?>:</strong> $<?php echo number_format($l->price, 2)?>
        </p>
        <p>
            <strong>Bedrooms:</strong> <?php echo htmlentities($l->bedrooms)?>
        </p>
        <p>
            <strong>Full Baths:</strong> <?php echo htmlentities($l->baths_full)?>
        </p>
        <p class="bigScreen">
            <strong>City:</strong> <a href="<?php echo $extraData['siteUrl'];?>/sr-cities/<?php echo $l->city2.'/'.$type;?>"><?php echo htmlentities($l->city)?></a>
        </p>
        <p class="smallScreen">
            <strong>Address:</strong> <?php echo htmlentities($l->address).', '. htmlentities($l->city).', '. htmlentities($l->state).', '.htmlentities($l->zip)?>
        </p>
        <p class="bigScreen">
            <strong>State:</strong> <?php echo htmlentities($l->state)?>
        </p>
        <p class="bigScreen">
            <strong>Zip:</strong> <?php echo htmlentities($l->zip)?>
        </p>
        <p>
            <strong>MLS ID:</strong> <?php echo htmlentities($l->mls_id)?>
        </p>
        <p>
            Courtesy of <?php echo htmlentities($l->office_name)?>
        </p>

    </div>
    <div class="sr-listing-clear"></div>
</div>