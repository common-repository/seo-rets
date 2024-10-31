<?php


class SEO_Rets_Search_Widget extends WP_Widget
{

    function SEO_Rets_Search_Widget()
    {
        global $seo_rets_plugin;

        $this->sr = $seo_rets_plugin;

        $widget_ops = array('classname' => 'sr-search-widget', 'description' => 'Displays a SEO RETS search form in your sidebar.');
        $control_ops = array('width' => 300, 'height' => 350, 'id_base' => 'seo-rets-search-widget');
        $this->WP_Widget('seo-rets-search-widget', 'SEO Rets Search Form', $widget_ops, $control_ops);
    }

    function widget($args, $instance)
    {
        echo $args['before_widget'];
        if (isset($instance['city_list']) && is_array($instance['city_list']) && count($instance['city_list']) > 0) {
            $cities = $instance['city_list'];
        } else {
            $cities = $this->sr->metadata->res->fields->city->values;
        }
        sort($cities);
        if (isset($instance['feature_list']) && is_array($instance['feature_list']) && count($instance['feature_list']) > 0) {
            $feature_list = $instance['feature_list'];
        } else {
            $feature_list = "";
        }

        sort($feature_list);

        ?>
        <h3 class="widget-title"><?php echo isset($instance['title']) ? $instance['title'] : "" ?></h3>
        <?php wp_enqueue_script('sr_seorets-min');
        wp_print_scripts(array('sr_seorets-min'));
        ?>
        <style>
            .flat-slider.ui-corner-all,
            .flat-slider .ui-corner-all {
                border-radius: 0;
            }

            .flat-slider.ui-slider {
                border: 0;
                background: #cccccc;
                border-radius: 2px;
            }

            .flat-slider.ui-slider-horizontal {
                height: 3px;
            }

            .flat-slider.ui-slider-vertical {
                height: 15em;
                width: 3px;
            }

            .flat-slider .ui-slider-handle {
                width: 13px;
                height: 13px;
                background: #2aacff;
                border-radius: 50%;
                border: none;
                cursor: pointer;
            }

            .flat-slider.ui-slider-horizontal .ui-slider-handle {
                top: 50%;
                margin-top: -6.5px;
            }

            .flat-slider.ui-slider-vertical .ui-slider-handle {
                left: 50%;
                margin-left: -6.5px;
            }

            .flat-slider .ui-slider-handle:hover {
                opacity: .8;
            }

            .flat-slider .ui-slider-range {
                border: 0;
                border-radius: 2;
                background: #3d4448;
            }

            .flat-slider.ui-slider-horizontal .ui-slider-range {
                top: 0;
                height: 3px;
            }

            .flat-slider.ui-slider-vertical .ui-slider-range {
                left: 0;
                width: 3px;
            }

            .sr_input {
                height: 30px !important;
                width: 100%;
                border: 1px solid #efefef;
                border-radius: 0;
                -webkit-border-radius: 0;
                -moz-border-radius: 0;
                padding: 0 15px;
                background-color: #fff;
                text-transform: capitalize;
            }

            .sr_select {
                height: 30px !important;
                width: 100%;
                border-radius: 0;
                -webkit-border-radius: 0;
                -moz-border-radius: 0;
                padding: 0 15px;
                text-transform: capitalize;
            }

            #min_amount {
                float: left;
            }

            #max_amount {
                float: right;
            }

            .feature_list label {
                display: inline;
                margin: 0;
                padding: 0;
            }

            .feature_list input {
                margin: 0;
                padding: 0;
            }
        </style>

        <script type="text/javascript">
            (function () {
                var m = (function () {
                    var s = document.getElementsByTagName('script');
                    return s[s.length - 1];
                })();
                jQuery(function () {
                    seorets.options = {blogurl: "<?php echo get_bloginfo('url')?>"};
                    seorets.startForm(jQuery(m).nextUntil('.sr-formsection + *', '.sr-formsection'), function (root) {
                        var propType = root.find('.sr-class');
                        propType.change(function () {
                            var me = jQuery(this);
                            var val = me.attr("srtype");
                            if (me.is("select")) {
                                var option = me.find("option:selected").attr("srtype");
                                if (option !== undefined) {
                                    val = option;
                                }
                            }
                            root.attr("srtype", val);
                        });
                        propType.change();
                    });
                });
            })();
        </script>
        <div class="sr-formsection sr-content" sroperator="AND" srtype="<?php echo $instance['type'] ?>">
            <?php
            if ($instance['use_type_f'] == "checked" || !isset($instance['use_type_f'])) {
                ?>
                <div class="row margin-top-10">
                    <div class="col-md-12">
                        <label for="sr_type">PROPERTY TYPE</label>
                        <select class="sr-class sr_select" srtype="<?php echo $instance['type'] ?>" name="ptype">
                            <option value="" selected="selected">Property Type</option>
                            <?php
                            foreach ($this->sr->metadata as $key => $val) {
                                echo "\t\t\t\t\t\t\t\t<option srtype='$key'>" . (isset($val->pretty_name) ? $val->pretty_name : $key) . "</option>";

                            }
                            ?>
                        </select>
                    </div>
                </div>
                <?php
            }
            if ($instance['use_city_f'] == "checked" || !isset($instance['use_city_f'])) {
                ?>
                <div class="row margin-top-10">
                    <div class="col-md-12 col-sm-12">
                        <label for="sr_city">CITY:</label>
                        <select id="sr_city" class="sr-formelement sr_select" srfield="city" sroperator="=">
                            <option value=''>All</option>
                            <?php
                            foreach ($cities as $city) {
                                echo "\t\t\t\t\t\t\t<option>{$city}</option>\n";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            <?php }
            if ($instance['use_bathbeds_f'] == "checked" || !isset($instance['use_bathbeds_f'])) {
                ?>
                <div class="row margin-top-10">
                    <div class="col-md-6 col-sm-6">
                        <label for="sr_bath">BATHS:</label>
                        <select id="sr_bath" class="sr-formelement sr_select" srtype="numeric" srfield="baths"
                                sroperator=">=">
                            <option value=''>All</option>
                            <option value='1'>1+</option>
                            <option value='2'>2+</option>
                            <option value='3'>3+</option>
                            <option value='4'>4+</option>
                            <option value='5'>5+</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <label for="sr_beds">BEDS:</label>
                        <select id="sr_beds" class="sr-formelement sr_select" srtype="numeric" srfield="bedrooms"
                                sroperator=">=">
                            <option value=''>All</option>
                            <option value='1'>1+</option>
                            <option value='2'>2+</option>
                            <option value='3'>3+</option>
                            <option value='4'>4+</option>
                            <option value='5'>5+</option>
                        </select>
                    </div>
                </div>
            <?php }
            if ($instance['use_location_f'] == "checked" || !isset($instance['use_location_f'])) {
                ?>
                <div class="row margin-top-10">
                    <div class="col-md-12 col-sm-12">
                        <label for="sr_address">LOCATION:</label>
                        <input type="text" id="sr_address" placeholder="State, Zip, Address, Subdivision"
                               class="sr_input">
                    </div>
                    <div id="hidden_elements" class="sr-formsection" sroperator="OR">
                        <input type="hidden" class="sr-formelement hidden_field" srfield="state" sroperator="LIKE">
                        <input type="hidden" class="sr-formelement hidden_field" srfield="zip" sroperator="=">
                        <input type="hidden" class="sr-formelement hidden_field" srfield="subdivision" sroperator="LIKE"
                               srloose="yes">
                        <input type="hidden" class="sr-formelement hidden_field" srfield="address" sroperator="LIKE"
                               srloose="yes">
                    </div>
                </div>
            <?php }
            if ($instance['use_price_f'] == "checked" || !isset($instance['use_price_f'])) {
                ?>
                <div class="row margin-top-10">
                    <div class="col-md-6">
                        <label for="min_price">PRICE:</label>
                        <input type="text" id="min_price" placeholder="Min:" class="sr-formelement sr_input"
                               srtype="numeric"
                               srfield="price"
                               sroperator=">=" value="<?php echo $instance['defaults_minprice'] ?>"/>
                    </div>
                    <div class="col-md-6">
                        <label for="max_price">&nbsp;</label>
                        <input type="text" id="max_price" placeholder="Max:" class="sr-formelement sr_input"
                               srtype="numeric"
                               srfield="price"
                               sroperator="<=" value="<?php echo $instance['defaults_maxprice'] ?>"/>
                    </div>

                </div>
            <?php } ?>
            <?php
            if ($instance['use_mls_f'] == "checked" || !isset($instance['use_mls_f'])) {
                ?>
                <div class="row margin-top-10">
                    <div class="col-md-12 col-sm-12">
                        <label for="sr_mls">MLS #:</label>
                        <input type="text" id="sr_mls" placeholder="MLS #:" class="sr-formelement sr_input"
                               srfield="mls_id" sroperator="="
                               value="<?php echo $instance['defaults_mls'] ?>"
                               onchange="this.value=jQuery.trim(this.value);"/>

                    </div>
                </div>
            <?php }
            if ($instance['use_features'] == "checked" || !isset($instance['use_features'])) { ?>
                <div class="row">
                    <div class="col-md-12 col-sm-12 feature_list">
                        <ul>
                            <?php
                            foreach ($feature_list as $key => $feature) :?>

                                <li><input class="sr-formelement" value="<?php echo ucwords($feature) ?>"
                                           srfield="features" sroperator="LIKE" srloose="" type="checkbox"
                                           id="<?php echo $key; ?>"> <label
                                        for="<?php echo $key ?>"> <?php echo ucwords($feature) ?></label></li>
                                <?php
                            endforeach;
                            ?>
                        </ul>
                    </div>
                </div>
            <?php } ?>
            <div class="row margin-top-10">
                <div class="col-md-4 col-sm-4">
                    <label for=""></label>
                </div>
                <div class="col-md-8 col-sm-8">
                    <input type="submit" class="sr-submit" value="Search"/>

                </div>
            </div>
            <?php if (isset($instance['sorting']) && !empty($instance['sorting'])): ?>
                <input type="hidden" class="sr-order" srfield="price" srdirection="<?php echo $instance['sorting'] ?>"/>
            <?php endif; ?>
            <input type="hidden" class="sr-limit" value="10">
        </div>
        <?php
        echo $args['after_widget'];
    }

    function update($new_instance, $old_instance)
    {
        return $new_instance;
    }

    function form($instance)
    {
        $cities = $this->sr->metadata->res->fields->city->values;

        $features = array();
        foreach (get_object_vars($this->sr->metadata) as $object) {
            $fields = $object->fields;
            if (isset($fields->features->values) && is_array($fields->features->values)) {
                $features = array_merge($features, $fields->features->values);
            }
        }
        $features = array_unique($features);
        sort($features);
        ?>
        <h3 style="margin-top:0;">Settings</h3>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
            <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>"
                   value="<?php echo isset($instance['title']) ? $instance['title'] : "" ?>" style="width:95%;"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('type'); ?>">Listing Type:</label>

            <select id="<?php echo $this->get_field_id('type'); ?>"
                    name="<?php echo $this->get_field_name('type'); ?>"><?php

                foreach ($this->sr->metadata as $type) {
                    $type = $type->system_name;
                    if ($this->sr->is_type_hidden($type)) {
                        continue;
                    }
                    if ($type == $instance['type']) {
                        echo "<option value='$type' selected>$type</option>";
                    } else {
                        echo "<option value='$type'>$type</option>";
                    }
                }

                ?></select>
        </p>
        <h3>Cities</h3>
        <p>Select which cities you would like to display in your quicksearch dropdown. Use control + click to select
            multiple cities.</p>
        <p>
            <select style="width: 100%;height:150px;" multiple="multiple"
                    name="<?php echo $this->get_field_name('city_list'); ?>[]">
                <?php sort($cities); ?>
                <?php foreach ($cities as $city): ?>
                    <?php if ($instance['city_list'] && in_array($city, $instance['city_list'])): ?>
                        <option selected><?php echo $city ?></option>
                    <?php else: ?>
                        <option><?php echo $city ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </p>
        <h3>Features</h3>
        <input type="hidden" name="<?php echo $this->get_field_name('use_features') ?>" value=""/>
        <p>
            <input type="checkbox" value="checked"
                   name="<?php echo $this->get_field_name('use_features') ?>"<?php echo (isset($instance['use_features']) && $instance['use_features'] == "checked") ? " checked" : "" ?> />
            Use features tags for layout
        </p>
        <p>Select which Features you would like to display in your quicksearch dropdown. Use control + click to select
            multiple Features.</p>
        <p>
            <select style="width: 100%;height:150px;" multiple="multiple"
                    name="<?php echo $this->get_field_name('feature_list'); ?>[]">
                <?php foreach ($features as $feature): ?>
                    <?php if ($instance['feature_list'] && in_array($feature, $instance['feature_list'])): ?>
                        <option selected><?php echo $feature ?></option>
                    <?php else: ?>
                        <option><?php echo $feature ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </p>
        <h3>Defaults</h3>
        <p>
        <table>

            <tr>
                <td>Min Beds:</td>
                <td><input type="text" name="<?php echo $this->get_field_name('defaults_minbeds'); ?>"
                           value="<?php echo isset($instance['defaults_minbeds']) ? $instance['defaults_minbeds'] : "" ?>"/>
                </td>
            </tr>
            <tr>
                <td>Min Baths:</td>
                <td><input type="text" name="<?php echo $this->get_field_name('defaults_minbaths'); ?>"
                           value="<?php echo isset($instance['defaults_minbaths']) ? $instance['defaults_minbaths'] : "" ?>"/>
                </td>
            </tr>
            <tr>
                <td>Min Price:</td>
                <td><input type="text" name="<?php echo $this->get_field_name('defaults_minprice'); ?>"
                           value="<?php echo isset($instance['defaults_minprice']) ? $instance['defaults_minprice'] : "" ?>"/>
                </td>
            </tr>
            <tr>
                <td>Max Price:</td>
                <td><input type="text" name="<?php echo $this->get_field_name('defaults_maxprice'); ?>"
                           value="<?php echo isset($instance['defaults_maxprice']) ? $instance['defaults_maxprice'] : "" ?>"/>
                </td>
            </tr>
            <tr>
                <td>MLS #:</td>
                <td><input type="text" name="<?php echo $this->get_field_name('defaults_mls'); ?>"
                           value="<?php echo isset($instance['defaults_mls']) ? $instance['defaults_mls'] : "" ?>"/>
                </td>
            </tr>
        </table>
        </p>
        <?php
        if (!isset($instance['use_type_f'])) {
            $instance['use_type_f'] = "checked";
        }
        if (!isset($instance['use_city_f'])) {
            $instance['use_city_f'] = "checked";
        }
        if (!isset($instance['use_bathbeds_f'])) {
            $instance['use_bathbeds_f'] = "checked";
        }
        if (!isset($instance['use_location_f'])) {
            $instance['use_location_f'] = "checked";
        }
        if (!isset($instance['use_price_f'])) {
            $instance['use_price_f'] = "checked";
        }
        if (!isset($instance['use_mls_f'])) {
            $instance['use_mls_f'] = "checked";
        }
        ?>
        <h3>Defaults Fields</h3>
        <input type="hidden" name="<?php echo $this->get_field_name('use_type_f') ?>" value=""/>
        <p>
            <input type="checkbox" value="checked"
                   name="<?php echo $this->get_field_name('use_type_f') ?>"<?php echo (isset($instance['use_type_f']) && $instance['use_type_f'] == "checked") ? " checked" : "" ?> />
            Use Property Types
        </p>
        <input type="hidden" name="<?php echo $this->get_field_name('use_city_f') ?>" value=""/>
        <p>
            <input type="checkbox" value="checked"
                   name="<?php echo $this->get_field_name('use_city_f') ?>"<?php echo (isset($instance['use_city_f']) && $instance['use_city_f'] == "checked") ? " checked" : "" ?> />
            Use City
        </p>

        <input type="hidden" name="<?php echo $this->get_field_name('use_bathbeds_f') ?>" value=""/>
        <p>
            <input type="checkbox" value="checked"
                   name="<?php echo $this->get_field_name('use_bathbeds_f') ?>"<?php echo (isset($instance['use_bathbeds_f']) && $instance['use_bathbeds_f'] == "checked") ? " checked" : "" ?> />
            Use Bath and Beds
        </p>
        <input type="hidden" name="<?php echo $this->get_field_name('use_location_f') ?>" value=""/>
        <p>
            <input type="checkbox" value="checked"
                   name="<?php echo $this->get_field_name('use_location_f') ?>"<?php echo (isset($instance['use_location_f']) && $instance['use_location_f'] == "checked") ? " checked" : "" ?> />
            Use Location
        </p>

        <input type="hidden" name="<?php echo $this->get_field_name('use_price_f') ?>" value=""/>
        <p>
            <input type="checkbox" value="checked"
                   name="<?php echo $this->get_field_name('use_price_f') ?>"<?php echo (isset($instance['use_price_f']) && $instance['use_price_f'] == "checked") ? " checked" : "" ?> />
            Use Price Range
        </p>

        <input type="hidden" name="<?php echo $this->get_field_name('use_mls_f') ?>" value=""/>
        <p>
            <input type="checkbox" value="checked"
                   name="<?php echo $this->get_field_name('use_mls_f') ?>"<?php echo (isset($instance['use_mls_f']) && $instance['use_mls_f'] == "checked") ? " checked" : "" ?> />
            Use MLS
        </p>


        <h3>Result Sorting</h3>
        <p>
            <select name="<?php echo $this->get_field_name('sorting'); ?>">
                <option <?php echo ($instance['sorting'] == "") ? "selected=\"selected\" " : "" ?>value="">None</option>
                <option <?php echo ($instance['sorting'] == "DESC") ? "selected=\"selected\" " : "" ?>value="DESC">Price
                    highest to lowest
                </option>
                <option <?php echo ($instance['sorting'] == "ASC") ? "selected=\"selected\" " : "" ?>value="ASC">Price
                    lowest to highest
                </option>
            </select>
        </p>
        <?php
    }
}
