<?php
if (count($listings) == 0 && !$silent) {
    do_action('seo_rets_unfound_page', 'results', $conditions);
    return;
}

?>

<div class="sr-listings sr-content">
    <?php
    $templates = get_option('sr_templates');
    $server_name = $this->feed->server_name;
    $match = array();

    if (is_array($type)) $type = $type[0];

    if (preg_match("/^([a-zA-Z]+)\\.([a-zA-Z]+)$/", $type, $match)) {
        $server_name = $match[1];
    }
    $photo_dir = "http://img.seorets.com/" . $server_name;
    if (isset($templates['css'])) {
        ?>

        <?php echo $templates['css']; ?>
        <?php
    } else {
        include($sr->resp_css);
    }

    foreach ($lis as $l):
        $type = $l->type;
        if (!isset($l->address) || $l->address == "") {
            $l->address = "N/A";
        }
        if (!isset($l->city) || $l->city == "") {
            $l->city = "N/A";
        } else {
            $l->city2 = preg_replace('/\s/', '+', $l->city);
        }

        if (!isset($l->subdivision) || $l->subdivision == "") {
            $l->ubdivision = "N/A";
        } else {
            $l->subdivision2 = preg_replace('/\s/', '+', $l->subdivision);
        }

        if (isset($l->system_type)) {
            $url = $sr->listing_to_url($l, $l->system_type);
        } else {
            $url = $sr->listing_to_url($l, $type);
        }


        if (isset($widgetize) && $widgetize) : ?>
            <div class="srm-listing-sidebar">
                <div style="width: 50%;float:left;" id="srm-listing-sidebar-left">
                    <?php if ($l->photos > 0): ?>
                        <a href="<?php echo get_bloginfo('url') ?><?php echo $url ?>"><img
                                src="http://img.seorets.com/<?php echo $sr->feed->server_name ?>/<?php echo $l->seo_url ?>-<?php echo $l->mls_id ?>-1.jpg"
                                width="65" height="65"/></a>
                    <?php else: ?>
                        <div class="srm-photo-none-small">No<br/>Photo</div>
                    <?php endif; ?>
                </div>
                <div style="width: 50%;float:right;" id="srm-listing-sidebar-right">
                    <a href="<?php echo get_bloginfo('url') ?><?php echo $url ?>"><strong><?php echo ucwords(strtolower($l->address)) ?></strong></a>

                    <p class="srm-sidebar-price">Price: $<?php echo number_format($l->price) ?></p>

                    <p class="srm-sidebar-beds">Beds: <?php echo $l->bedrooms ?></p>
                </div>
                <div style="clear:both;"></div>
            </div>
            <?php
        elseif (isset($templates['result'])): {
            eval('?>' . $templates['result']);
        } else :
            include($this->results_template);
        endif;

    endforeach;
    ?>
    <div class="clear"></div>
</div>
