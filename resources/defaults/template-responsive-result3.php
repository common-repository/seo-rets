<div class="sr-content margin-top-10">
    <div class="sr-listing">
        <div class="row">
            <div class="col-md-5 col-sm-6">
                <a href="<?php echo get_bloginfo('url') ?><?php echo $url ?>"><img
                        src="<?php echo $photo_dir ?>/<?php echo $l->seo_url ?>-<?php echo $l->mls_id ?>-1.jpg"
                        class="sr-listing-photo img-responsive"
                        alt="<?php echo htmlentities($l->address) ?>, <?php echo htmlentities($l->city) ?>, <?php echo htmlentities($l->state) ?> <?php echo htmlentities($l->zip) ?> - 1"
                        title="<?php echo htmlentities($l->address) ?>, <?php echo htmlentities($l->city) ?>, <?php echo htmlentities($l->state) ?> <?php echo htmlentities($l->zip) ?> - 1"/></a>

            </div>
            <div class="col-md-7 col-sm-6">
                <div class="row  sr-listing-title">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <a href="<?php echo get_bloginfo('url') ?><?php echo $url ?>"><?php echo htmlentities($l->address) ?></a>,
                                <a
                                    href="<?php echo $extraData['siteUrl']; ?>/sr-cities/<?php echo $l->city2 . '/' . $type; ?>"><?php echo htmlentities($l->city) ?></a>
                                <i><?php echo htmlentities($l->state) . ', ' . htmlentities($l->zip) ?></i>
                            </div>
                        </div>
                        <div class="row sr-listing-title-price margin-top-5">
                            <div class="col-md-12">
                                $<?php echo number_format($l->price, 2) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row margin-top-10 sr-info-section">
                    <div class="col-md-6 col-sm-6">
                        <div class="row">
                            <div class="col-md-8 col-sm-8">
                                Bedrooms:
                            </div>
                            <div class="col-md-4 col-sm-4 sr-float-right">
                                <?php echo htmlentities($l->bedrooms) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="row">
                            <div class="col-md-8 col-sm-8">
                                Full Baths:
                            </div>
                            <div class="col-md-4 col-sm-4 sr-float-right">
                                <?php echo htmlentities($l->baths_full) ?>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="row margin-top-10 sr-info-section">
                    <div class="col-md-6 col-sm-6">
                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                Lot size:
                            </div>
                            <div class="col-md-6 col-sm-6 sr-float-right">
                                <?php echo isset($l->sqft) ? $l->sqft : 'N/A' ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                MLS ID:
                            </div>
                            <div class="col-md-6 col-sm-6 sr-float-right">
                                <?php echo htmlentities($l->mls_id) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row margin-top-10 sr-info-section">

                    <div class="col-md-4 col-sm-4">
                        Courtesy of
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <?php echo htmlentities($l->office_name) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
