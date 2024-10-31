<div class="sr-listing">

    <div class="sr-listing-header">

        <a href="<?php echo get_bloginfo('url')?><?php echo $url?>"><img src="<?php echo $photo_dir?>/<?php echo $l->seo_url?>-<?php echo $l->mls_id?>-1.jpg" class="sr-listing-photo" alt="<?php echo htmlentities($l->address)?>, <?php echo htmlentities($l->city)?>, <?php echo htmlentities($l->state)?> <?php echo htmlentities($l->zip)?> - 1" title="<?php echo htmlentities($l->address)?>, <?php echo htmlentities($l->city)?>, <?php echo htmlentities($l->state)?> <?php echo htmlentities($l->zip)?> - 1"  /></a>
    </div>
    <div class="sr-listing-body">
        <h3 class="sr-listing-title">
            <a href="<?php echo get_bloginfo('url')?><?php echo $url?>"><?php echo htmlentities($l->address)?></a>
        </h3>
        <div class="srl-region">
            <?php echo htmlentities($l->city)?>, <?php echo htmlentities($l->state)?> <?php echo htmlentities($l->zip)?>
        </div>
        <div class="srl-price">
            $<?php echo number_format($l->price, 2)?>
        </div>
        <div class="srl-subdivision">
            Subdivision: <?php echo $l->subdivision;?>
        </div>
        <hr class="srl-hr">
        <ul class="details">
            <li class="bath"><?php echo htmlentities($l->baths)?></li>
            <li class="bed"><?php echo htmlentities($l->bedrooms)?></li>
            <li class="area"><?php echo htmlentities($l->sqft)?> ft<sup>2</sup></li>
        </ul>
        <div class="srl-mls_id">
            MLS ID: <?php echo htmlentities($l->mls_id)?>
        </div>

    </div>
</div>
<a href="<?php echo get_bloginfo('url')?><?php echo $url?>"/></a>