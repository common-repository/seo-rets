<?php
wp_enqueue_script('sr_seorets-min');
wp_print_scripts(array('sr_seorets-min'));
?>
<div></div>
<script type="text/javascript">

    (function () {

        var chunk = function (array, n) {
            var retval = [];
            for (var i = 0, len = array.length; i < len; i += n) {
                retval.push(array.slice(i, i + n));
            }
            return retval;
        };
        var m = (function () {
            var s = document.getElementsByTagName('script');
            return s[s.length - 1];
        })();
        seorets.options = {blogurl: "<?php echo get_bloginfo('url')?>"};
        jQuery(function () {
            jQuery('.progress').css('width', jQuery('.sr-narrow').width());
            jQuery('.progress').css('height', jQuery('.sr-narrow').height());
            jQuery(window).resize(function () {
                jQuery('.progress').css('width', jQuery('.sr-narrow').width());
                jQuery('.progress').css('height', jQuery('.sr-narrow').height());
            });
            seorets.startForm(jQuery(m).nextAll('.sr-formsection:first'), function (root) {
                var features_list = [];

                jQuery(".ul__features").on("change", ".sr-formelement", function () {
                    jQuery('.progress').css('width', jQuery('.sr-narrow').width());
                    jQuery('.progress').css('height', jQuery('.sr-narrow').height());
                    jQuery('.progress').show();
                    if (jQuery(this).is(':checked')) {
                        features_list.push(jQuery(this).val());
                    } else {
                        var tem = features_list.indexOf(jQuery(this).val());
                        if (tem > -1) {
                            features_list.splice(tem, 1);
                        }
                    }
                    jQuery.ajax({
                        url: '<?php bloginfo('url') ?>/sr-ajax?action=get-listings-features',
                        type: 'get',
                        data: {
                            conditions: encodeURIComponent(Base64.encode(JSON.stringify(seorets.getFormRequest(root))))
                        },
                        success: function (response) {
                            if (response) {
//                                console.log(response['listings_count']);
                                if (response['listings_count'] > 1) {
                                    jQuery('.count_listings').html('We found ' + response['listings_count'] + ' listings');
                                } else {
                                    jQuery('.count_listings').html('We found ' + response['listings_count'] + ' listing');

                                }
                                var arr = Object.keys(response['features']).map(function (key) {
                                    return response['features'][key]
                                });
//                                console.log(arr.length);
                                var chunkA;
//                                console.log(chunk(arr, arr.length / 2));
                                chunkA = chunk(arr, arr.length / 2);
//                                console.log(chunkA.length);
                                var t = 0;
                                for (t; t <= chunkA.length - 1; t++) {
                                    jQuery(".chunc-" + t).html(' ');
                                    var i = 0;
                                    for (i; i <= chunkA[t].length - 1; i++) {
                                        if (features_list.indexOf(chunkA[t][i]) > -1) {
                                            jQuery("<li><input checked id='sr_features-" + chunkA[t][i].replace(/\s+/g, '') + "' type='checkbox' class='sr-formelement' srfield='features' sroperator='=' value='" + chunkA[t][i] + "'><label for='sr_features-" + chunkA[t][i].replace(/\s+/g, '') + "'>" + chunkA[t][i] + "</label></li>").appendTo(".chunc-" + t);
                                        } else {
                                            jQuery("<li><input id='sr_features-" + chunkA[t][i].replace(/\s+/g, '') + "' type='checkbox' class='sr-formelement' srfield='features' sroperator='=' value='" + chunkA[t][i] + "'><label for='sr_features-" + chunkA[t][i].replace(/\s+/g, '') + "'>" + chunkA[t][i] + "</label></li>").appendTo(".chunc-" + t);
                                        }
                                    }
                                }

                            }
                            jQuery('.progress').hide();

                        }
                    });
                });

            });
        });
    })();</script>
<style>
    .progress {
        position: absolute;
        display: none;
        text-align: center;
        width: 0;
        height: 3px;
        background: #fff;
        opacity: 0.7;
        z-index: 777;
        transition: width .3s;
    }

    #ajax-loader2 {
        position: absolute;
        left: 50%;
        top: 50%;
        margin-top: -24px;
        margin-left: -24px;
    }
</style>
<div class="sr-narrow sr-content sr-formsection" sroperator="AND" srtype="<?php echo $type ?>">
    <div class="progress">
        <img id="ajax-loader2" src="<?php echo $this->plugin_dir ?>resources/images/ajax2.gif"/>
    </div>
    <h4 class="margin-top-10">To further narrow your search, please select the features that interest you by checking
        the checkboxes in the
        lists below.<br/>
        To apply your selected features to your search criteria, click the "Apply Features" button below.</h4>

    <input type="submit" class="submit sr-submit margin-top-10" name="submit" id="searchsubmit" value="Apply Features"/>
    <div class="count_listings margin-top-10"></div>
    <?php
    if (isset($order) && is_array($order)) {


        foreach ($order as $ord) {
            ?>
            <input type="text" style="display: none" class="sr-order" srfield="<?= $ord['field']; ?>"
                   srdirection="<?= $ord['order'] ?>">
            <?php

        }
    }

    foreach ($qcc['conditions'] as $key => $cod) {
        if (!empty($qcc['conditions'][$key]['conditions'])) {
            ?>
            <select style="display: none" class="sr-formelement"
                    srfield="<?= $qcc['conditions'][$key]['conditions'][0]['field']; ?>" sroperator="LIKE"
                    srloose="1"
                    multiple="" name="" id="">
                <?php
                foreach ($qcc['conditions'][$key]['conditions'] as $icod) {
                    ?>
                    <option selected value="<?= $icod['value']; ?>"><?= $icod['value'] ?></option>
                    <?php
                }
                ?>
            </select>

            <?php
        } else {
            ?>
            <input style="display: none" type="text" srloose="1" class="sr-formelement" srfield="<?= $cod['field']; ?>"
                   sroperator="<?= $cod['operator']; ?>"
                   value="<?= $cod['value']; ?>">
            <?php
        }
        ?>
    <?php } ?>
    <?php
    $pp = count($features_list) / 2;
    $newar = array_chunk($features_list, ceil($pp));
    ?>
    <div class="row">
        <div class="col-md-12 margin-top-20 features_block">
            <div class="row">
                <!--                --><?php //if (isset($features_list) && is_array($features_list) && count($features_list) > 0) {
                //                    $pp = count($features_list) / 4;
                //                    $newar = array_chunk($features_list, ceil($pp));
                //
                //                } ?>
                <div class="col-md-6 col-sm-6 col-xs-12 ">
                    <ul class="ul__features chunc-0">
                        <?php
                        foreach ($newar[0] as $feature) :?>
                            <?php
                            $featureid = str_replace(' ', '', $feature);
                            ?>
                            <li>
                                <input id="sr_features-<?php echo $featureid; ?>" type="checkbox"
                                       class="sr-formelement"
                                       srfield="features"
                                       sroperator="=" value="<?php echo ucwords($feature) ?>">
                                <label
                                    for="sr_features-<?php echo $featureid; ?>"><?php echo ucwords($feature) ?></label>
                            </li>
                            <?php
                        endforeach;
                        ?>
                    </ul>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <ul class="ul__features chunc-1">
                        <?php
                        foreach ($newar[1] as $feature) :?>
                            <?php
                            $featureid = str_replace(' ', '', $feature);
                            ?>
                            <li>
                                <input id="sr_features-<?php echo $featureid; ?>" type="checkbox"
                                       class="sr-formelement"
                                       srfield="features"
                                       sroperator="=" value="<?php echo ucwords($feature) ?>">
                                <label
                                    for="sr_features-<?php echo $featureid; ?>"><?php echo ucwords($feature) ?></label>
                            </li>
                            <?php
                        endforeach;
                        ?>
                    </ul>
                </div>
                <!--                <div class="col-md-6 col-sm-6 col-xs-12">-->
                <!--                    <ul class="ul__features chunc-2">-->
                <!--                        --><?php
                //                        foreach ($newar[2] as $feature) :?>
                <!--                            --><?php
                //                            $featureid = str_replace(' ', '', $feature);
                //                            ?>
                <!--                            <li>-->
                <!--                                <input id="sr_features--->
                <?php //echo $featureid; ?><!--" type="checkbox"-->
                <!--                                       class="sr-formelement"-->
                <!--                                       srfield="features"-->
                <!--                                       sroperator="=" value="-->
                <?php //echo ucwords($feature) ?><!--">-->
                <!--                                <label-->
                <!--                                    for="sr_features---><?php //echo $featureid; ?><!--">-->
                <?php //echo ucwords($feature) ?><!--</label>-->
                <!--                            </li>-->
                <!--                            --><?php
                //                        endforeach;
                //                        ?>
                <!--                    </ul>-->
                <!--                </div>-->
                <!--                <div class="col-md-3 col-sm-6 col-xs-12">-->
                <!--                    <ul class="ul__features chunc-3">-->
                <!--                        --><?php
                //                        foreach ($newar[3] as $feature) :?>
                <!--                            --><?php
                //                            $featureid = str_replace(' ', '', $feature);
                //                            ?>
                <!--                            <li>-->
                <!--                                <input id="sr_features--->
                <?php //echo $featureid; ?><!--" type="checkbox"-->
                <!--                                       class="sr-formelement"-->
                <!--                                       srfield="features"-->
                <!--                                       sroperator="=" value="-->
                <?php //echo ucwords($feature) ?><!--">-->
                <!--                                <label-->
                <!--                                    for="sr_features---><?php //echo $featureid; ?><!--">-->
                <?php //echo ucwords($feature) ?><!--</label>-->
                <!--                            </li>-->
                <!--                            --><?php
                //                        endforeach;
                //                        ?>
                <!--                    </ul>-->
                <!--                </div>-->

            </div>
        </div>
    </div>
    <input type="submit" class="submit sr-submit" name="submit" id="searchsubmit" value="Apply Features"/>
</div>
