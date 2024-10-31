<?php
$sr = $seo_rets_plugin;

if (!$sr->api_key) return '<p class="sr-error">You must activate the SEO RETS plugin before using shortcodes.</p>';

$type = isset($params['type']) ? $params['type'] : "advanced";
$collection = isset($params['coll']) ? $params['coll'] : "";
$order = isset($params['order']) ? explode(":", $params['order']) : NULL;
$fields = isset($params['fields']) ? explode(";", $params['fields']) : NULL;
foreach ($fields as $field) {
    $f = explode(":", $field);
    $field_A[$f[0]] = explode(",", $f[1]);
}
if ($type == "simple"):?>
    <form action="<?php echo get_bloginfo('url') ?>/sr-search" method="get">
        <input type="hidden" name="perpage" value="10">
        <input type="hidden" name="type" value="Homes">
        <table style="width:100%;">
            <tr>
                <td>City:</td>
                <td>
                    <input type="hidden" name="conditions[0][field]" value="city"/>
                    <input type="hidden" name="conditions[0][operator]" value="LIKE"/>
                    <input type="hidden" name="conditions[0][loose]" value="1"/>
                    <select name="conditions[0][value]">
                        <option value="">All</option>
                        <?php
                        $cities = $sr->metadata->res->fields->city->values;
                        sort($cities);
                        foreach ($cities as $city) {
                            echo "<option>{$city}</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <input type="hidden" name="conditions[1][field]" value="bedrooms"/>
                <input type="hidden" name="conditions[1][operator]" value=">="/>
                <td>Min Beds:</td>
                <td><input type="text" name="conditions[1][value]"/></td>
            </tr>
            <tr>
                <input type="hidden" name="conditions[2][field]" value="baths_full"/>
                <input type="hidden" name="conditions[2][operator]" value=">="/>
                <td>Min Baths:</td>
                <td><input type="text" name="conditions[2][value]"/></td>
            </tr>
            <tr>
                <input type="hidden" name="conditions[3][field]" value="price"/>
                <input type="hidden" name="conditions[3][operator]" value=">="/>
                <td>Min Price:</td>
                <td><input type="text" name="conditions[3][value]"/></td>
            </tr>
            <tr>
                <input type="hidden" name="conditions[4][field]" value="price"/>
                <input type="hidden" name="conditions[4][operator]" value="<="/>
                <td>Max Price:</td>
                <td><input type="text" name="conditions[4][value]"/></td>
            </tr>
            <tr>
                <input type="hidden" name="conditions[5][field]" value="mls_id"/>
                <input type="hidden" name="conditions[5][operator]" value="="/>
                <td>MLS #:</td>
                <td><input type="text" name="conditions[5][value]" onchange="this.value=jQuery.trim(this.value);"/></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" value="Search"/></td>
            </tr>
        </table>
        <?php if (isset($order[0]) && isset($order[1])): ?>
            <input type="hidden" name="order_wp_sux[0][field]" value="<?php echo $order[0] ?>"/>
            <input type="hidden" name="order_wp_sux[0][order]" value="<?php echo $order[1] ?>"/>
        <?php endif; ?>
    </form>
<?php elseif ($type == "custom"):
    $search = array(
        chr(145),
        chr(146),
        chr(147),
        chr(148),
        chr(151)
    );

    $replace = array(
        "'",
        "'",
        '"',
        '"',
        '-'
    );
    echo iconv("UTF-8", "ISO-8859-1//TRANSLIT", str_replace($search, $replace, html_entity_decode($content, ENT_COMPAT, 'UTF-8')));
elseif ($type == "customform"):
    echo do_shortcode(get_option("sr_customform"));
elseif ($type == "script"):
    wp_enqueue_script('sr_seorets-min');
    wp_print_scripts(array('sr_seorets-min'));
    ?>
    <script type="text/javascript">seorets.options = {blogurl: "<?php echo get_bloginfo('url')?>"};</script>
    <div></div>
<?php elseif ($type == "basicstyle"):
    wp_enqueue_script('sr_seorets-min');
    wp_print_scripts(array('sr_seorets-min'));
    ?>
    <script type="text/javascript">
        (function () {
            var m = (function () {
                var s = document.getElementsByTagName('script');
                return s[s.length - 1];
            })();
            jQuery(function () {
                seorets.options = {blogurl: "<?php echo get_bloginfo('url')?>"};
                seorets.startForm(jQuery(m).nextAll('.sr-formsection:first'), function (root) {
                    root.find('.sr-class').change(function () {
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
                    root.find('.sr-class:checked').change();

                    var allElements = root.find("input, select");
                    root.find(".sr-reset").click(function () {
                        allElements.filter(':text').each(function () {
                            this.value = this.defaultValue;
                        });
                        allElements.filter(':radio, :checkbox').each(function () {
                            this.checked = this.defaultChecked;
                            this.selected = this.defaultSelected;
                        });
                        allElements.filter("select").each(function () {
                            jQuery(this).find('option').each(function () {
                                this.selected = this.defaultSelected;
                            });
                        });
                        root.find('.sr-class:checked').change();
                        root.find('.sr-select-field:checked').change();
                    });

                    var select = root.find('.sr-select-replacement');
                    var hidden = root.find('.hidden');

                    root.find(".sr-select-field").each(function () {
                        var me = jQuery(this);
                        me.data("selectBox", hidden.find("[srfield=" + me.val() + "]").detach());
                    });

                    hidden.remove();//we won't need this anymore
                    var currentSelect = jQuery();
                    root.find('.sr-select-field').change(function () {
                        currentSelect.detach();
                        currentSelect = jQuery(this).data("selectBox").appendTo(select);
                    });
                    root.find('.sr-select-field:checked').change();
                });
            });
        })();</script>
    <div class="sr-formsection" sroperator="AND" srtype="res">
        <?php
        if ($fields != NULL) {
            foreach ($field_A as $key => $values) {
                ?>
                <select style="display: none" class="sr-formelement" srfield="<?= $key; ?>" sroperator="LIKE"
                        multiple="" name="" id="">
                    <?php
                    foreach ($values as $sval) {
                        ?>
                        <option selected value="<?= $sval; ?>"><?= $sval ?></option>
                        <?
                    }
                    ?>
                </select>
                <?
            }
        }
        ?>
        <select class="sr-class" srtype="res">
            <?php
            $n = 0;
            foreach ($sr->metadata as $key => $val) {
                if ($sr->is_type_hidden($key)) {
                    continue;
                }
                if ($n == 0) {
                    $n++;
                    echo "\t\t<option srtype=\"{$key}\" selected=\"selected\">" . (isset($val->pretty_name) ? $val->pretty_name : $key) . "</option>";
                } else {
                    echo "\t\t<option srtype=\"{$key}\">" . (isset($val->pretty_name) ? $val->pretty_name : $key) . "</option>";
                }
            }
            ?>
        </select>
	<span>
		City: <input type="radio" name="class" class="sr-select-field" value="city" checked="checked">
		Zip:	<input type="radio" name="class" class="sr-select-field" value="zip">
		County: <input type="radio" name="class" class="sr-select-field" value="county">
	</span>

        <div>
            <div class="float-left sr-select-replacement">
            </div>
            <div class="float-right">
                <div class="float-left">
                    Low Price: <input type="text" name="s4h23" class="sr-formelement" srfield="price" srtype="numeric"
                                      sroperator="&gt;="/><br/>
                    Bedrooms:
                    <select class="sr-formelement" srfield="bedrooms" sroperator="&gt;=" srtype="numeric">
                        <option value="">All</option>
                        <option value="0" sroperator="=">None (Studio)</option>
                        <option value="1">1+</option>
                        <option value="2">2+</option>
                        <option value="3">3+</option>
                        <option value="4">4+</option>
                        <option value="5">5+</option>
                        <option value="6">6+</option>
                        <option value="7">7+</option>
                        <option value="8">8+</option>
                    </select><br/>
                    Min SQFT: <input type="text" name="r834k" class="sr-formelement" srfield="sqft" srtype="numeric"
                                     sroperator="&gt;="/>
                </div>
                <div class="float-right">
                    High Price: <input type="text" name="hdw9s" class="sr-formelement" srfield="price" srtype="numeric"
                                       sroperator="&lt;="/><br/>
                    Bathrooms:
                    <select class="sr-formelement" srfield="baths" sroperator="&gt;=" srtype="numeric">
                        <option value="">All</option>
                        <option value="1">1+</option>
                        <option value="2">2+</option>
                        <option value="3">3+</option>
                        <option value="4">4+</option>
                        <option value="5">5+</option>
                        <option value="6">6+</option>
                        <option value="7">7+</option>
                        <option value="8">8+</option>
                    </select><br/>
                    Sort By:
                    <select class="sr-order" srfield="price">
                        <option>None</option>
                        <option selected="selected" srdirection="DESC">Most Expensive to Least Expensive</option>
                        <option srdirection="ASC">Least Expensive to Most Expensive</option>
                    </select>
                </div>
            </div>
        </div>
        Per Page:
        <select class="sr-limit">
            <option>5</option>
            <option selected="selected">10</option>
            <option>20</option>
            <option>25</option>
            <option>50</option>
        </select>
        <button class="sr-reset">Reset</button>
        <button class="sr-submit">Search</button>
        <div class="hidden">
            <select class="sr-formelement" srfield="city" sroperator="=" multiple="multiple">
                <option value="">All</option>
                <?php
                $cities = $sr->metadata->res->fields->city->values;
                sort($cities);
                foreach ($cities as $city) {
                    echo "\t\t\t<option>{$city}</option>";
                }
                ?>
            </select>
            <select class="sr-formelement" srfield="zip" sroperator="=" multiple="multiple">
                <option value="">All</option>
                <?php
                $zips = $sr->metadata->res->fields->zip->values;
                sort($zips);
                foreach ($zips as $zip) {
                    echo "\t\t\t<option>{$zip}</option>";
                }
                ?>
            </select>
            <select class="sr-formelement" srfield="county" sroperator="=" multiple="multiple">
                <option value="">All</option>
                <?php
                $counties = $sr->metadata->res->fields->county->values;
                sort($counties);
                foreach ($counties as $county) {
                    echo "<option>{$county}</option>";
                }
                ?>
            </select>
        </div>
    </div>
<?php elseif ($collection != "" && $type == "advanced"):
    wp_enqueue_script('sr_seorets-min');
    wp_print_scripts(array('sr_seorets-min'));
    ?>
    <div></div>
    <script type="text/javascript">
        (function () {
            var m = (function () {
                var s = document.getElementsByTagName('script');
                return s[s.length - 1];
            })();
            seorets.options = {blogurl: "<?php echo get_bloginfo('url')?>"};
            jQuery(function () {
                seorets.startForm(jQuery(m).nextAll('.sr-formsection:first'), function (root) {
                    root.find('.sr-class').change(function () {
                        root.attr("srtype", jQuery(this).attr("srtype"));
                        jQuery.ajax({
                            url: '<?php bloginfo('url') ?>/sr-ajax?action=getOnType',
                            type: 'post',
                            data: {
                                type: jQuery(this).attr("srtype")
                            },
                            success: function (response) {
                                if (response) {
                                    jQuery("#cities").html(' ');
                                    i = 0;
                                    for (i; i <= response.length - 1; i++) {
                                        if (i == 0) {
                                            jQuery("<option value='' selected='selected'>Any</option>").appendTo("#cities");
                                        }

                                        jQuery("<option>" + response[i] + "</option>").appendTo("#cities");
                                    }
                                }
                            }
                        });
                        jQuery.ajax({
                            url: '<?php bloginfo('url') ?>/sr-ajax?action=getOnType',
                            type: 'post',
                            data: {
                                areas: jQuery(this).attr("srtype")
                            },
                            success: function (response) {
                                if (response) {
                                    jQuery("#area").html(' ');
                                    i = 0;
                                    for (i; i <= response.length - 1; i++) {
                                        if (i == 0) {
                                            jQuery("<option value='' selected='selected'>Any</option>").appendTo("#area");
                                        }

                                        jQuery("<option>" + response[i] + "</option>").appendTo("#area");
                                    }
                                }
                            }
                        });
                        jQuery.ajax({
                            url: '<?php bloginfo('url') ?>/sr-ajax?action=getOnType',
                            type: 'post',
                            data: {
                                subd: jQuery(this).attr("srtype")
                            },
                            success: function (response) {
                                jQuery("#subdivision").html(' ');
                                i = 0;
                                if (response != null) {
                                    jQuery("#subd-none").removeClass("disp-none");

                                    for (i; i <= response.length - 1; i++) {
                                        if (i == 0) {
                                            jQuery("<option value='' selected='selected'>Any</option>").appendTo("#subdivision");
                                        }

                                        jQuery("<option>" + response[i] + "</option>").appendTo("#subdivision");
                                    }
                                } else {
                                    jQuery("#subd-none").addClass("disp-none");
                                }
                            }
                        });
                        jQuery.ajax({
                            url: '<?php bloginfo('url') ?>/sr-ajax?action=getOnType',
                            type: 'post',
                            data: {
                                subt: jQuery(this).attr("srtype")
                            },
                            success: function (response) {
                                if (response) {
                                    jQuery("#sub_type").html(' ');
                                    i = 0;
                                    for (i; i <= response.length - 1; i++) {
                                        if (response != null) {
                                            jQuery("#sb_type").removeClass("disp-none");
                                            if (i == 0) {
                                                jQuery("<option value='' selected='selected'>Any</option>").appendTo("#sub_type");
                                            }

                                            jQuery("<option>" + response[i] + "</option>").appendTo("#sub_type");
                                        } else {
                                            jQuery("#sb_type").addClass("disp-none");
                                        }

                                    }
                                }
                            }
                        });

                    });
                    root.find('.sr-class:checked').change();
                });
            });
        })();</script>
    <style>
        .disp-none {
            display: none;
        }
    </style>
    <div class="sr-formsection sr-content" srtype="res" sroperator="AND">
        <?php
        if ($fields != NULL) {
            foreach ($field_A as $key => $values) {
                ?>
                <select style="display: none" class="sr-formelement" srfield="<?= $key; ?>" sroperator="LIKE"
                        multiple="" name="" id="">
                    <?php
                    foreach ($values as $sval) {
                        ?>
                        <option selected value="<?= $sval; ?>"><?= $sval ?></option>
                        <?
                    }
                    ?>
                </select>
                <?
            }
        }
        ?>
        <div class="row">
            <div class="col-md-3 col-sm-3">
                <label for="">
                    Location &amp; Home Type
                </label>
            </div>
            <div class="col-md-9 col-sm-9 left-border-line">
                <div id="sb_type" class="row disp-none margin-top-15">
                    <div class="col-md-4 col-sm-4">
                        <label for="">Type:</label>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <select id="sub_type" class="sr-formelement form-control" srfield="sub_type" sroperator="="
                                multiple="">
                            <option value="">All</option>
                            <?php
                            $sub_types = $sr->metadata->res->fields->sub_type->values;
                            sort($sub_types);
                            foreach ($sub_types as $sub_type) {
                                echo "\t\t\t\t\t\t\t\t<option>" . htmlentities($sub_type) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div style="display: none" class="row">
                    <div class="col-md-4 col-sm-4">
                        <label for="">Type:</label>
                    </div>
                    <div class="col-md-8 col-sm-8">

                        <?php
                        $n = 0;
                        foreach ($sr->metadata as $key => $val) {
                            if ($sr->is_type_hidden($key)) {
                                continue;
                            }
                            if ($key == $collection || $val->pretty_name == $collection) {
                                echo "\t\t\t\t\t\t\t<input type=\"radio\" name=\"class\" class=\"sr-class\" srtype=\"{$key}\" checked=\"checked\" /> " . (isset($val->pretty_name) ? $val->pretty_name : $key) . "<br />";
                            } else {
                                echo "\t\t\t\t\t\t\t<input type=\"radio\" name=\"class\" class=\"sr-class\" srtype=\"{$key}\" /> " . (isset($val->pretty_name) ? $val->pretty_name : $key) . "<br />";
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="row margin-top-15">
                    <div class="col-md-4 col-sm-4">
                        <label for="">Search Within City:</label>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <select id="cities" class="sr-formelement form-control" srfield="city" sroperator="="
                                multiple="">
                            <option value="">All</option>
                            <?php
                            $cities = $sr->metadata->res->fields->city->values;
                            sort($cities);
                            foreach ($cities as $city) {
                                echo "\t\t\t\t\t\t\t\t<option>" . htmlentities($city) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row margin-top-15">
                    <div class="col-md-4 col-sm-4">
                        <label for="">Search Within Area:</label>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <select id="area" class="sr-formelement form-control" srfield="area" sroperator="="
                                multiple="">
                            <option value="">All</option>
                            <?php
                            $areas = $sr->metadata->res->fields->area->values;
                            sort($areas);
                            foreach ($areas as $area) {
                                echo "\t\t\t\t\t\t\t\t<option>" . htmlentities($area) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row margin-top-15">
                    <div class="col-md-4 col-sm-4"><label for="">Address:</label></div>
                    <div class="col-md-8 col-sm-8">
                        <input type="text" name="dsg84" class="sr-formelement form-control" srfield="address"
                               sroperator="LIKE" srloose=""/>
                    </div>
                </div>
                <div id="subd-none" class="row margin-top-15 disp-none">
                    <div class="col-md-4 col-sm-4"><label for="">Subdivision:</label></div>
                    <div class="col-md-8 col-sm-8">
                        <select id="subdivision" class="sr-formelement form-control" srfield="subdivision"
                                sroperator="="
                                multiple="">
                            <option value="">All</option>
                            <?php
                            $sundivisions = $sr->metadata->res->fields->subdivision->values;
                            sort($sundivisions);
                            foreach ($sundivisions as $sundivision) {
                                echo "\t\t\t\t\t\t\t\t<option>" . htmlentities($sundivision) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row margin-top-15">
                    <div class="col-md-4 col-sm-4"><label for="">MLS#:</label></div>
                    <div class="col-md-8 col-sm-8">
                        <input type="text" name="2k30f" class="sr-formelement form-control" srfield="mls_id"
                               sroperator="LIKE"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row margin-top-40">
            <div class="col-md-3 col-sm-3">
                <label for="">Price & Size</label>
            </div>
            <div class="col-md-9 col-sm-9 left-border-line">
                <div class="row">
                    <div class="col-md-4 col-sm-4">
                        <label for="">
                            Price:
                        </label>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <div class="row">
                            <!--                            <div class="col-md-2 col-sm-2">-->
                            <!--                                <label for="">Min:</label>-->
                            <!--                            </div>-->
                            <div class="col-md-66 col-sm-6">
                                <input type="text" name="f89d5" class="sr-formelement form-control" placeholder="Min:"
                                       srfield="price"
                                       sroperator="&gt;=" srtype="numeric"/>
                            </div>
                            <!--                            <div class="col-md-2 col-sm-2">-->
                            <!--                                <label for="">Max:</label>-->
                            <!--                            </div>-->
                            <div class="col-md-6 col-sm-6">
                                <input type="text" name="pa3jv" class="sr-formelement form-control" placeholder="Max:"
                                       srfield="price"
                                       sroperator="&lt;=" srtype="numeric"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row margin-top-15">
                    <div class="col-md-4 col-sm-4"><label for="">Bedrooms:</label></div>
                    <div class="col-md-8 col-sm-8">
                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                <input type="text" name="d844k" class="sr-formelement form-control" placeholder="Min:"
                                       srfield="bedrooms"
                                       sroperator="&gt;=" srtype="numeric"/>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <input type="text" name="fd8j7" class="sr-formelement form-control" placeholder="Max:"
                                       srfield="bedrooms"
                                       sroperator="&lt;=" srtype="numeric"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row margin-top-15">
                    <div class="col-md-4 col-sm-4">
                        <label for="">Bathrooms:</label>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                <input type="text" name="df69d" class="sr-formelement form-control" placeholder="Min:"
                                       srfield="baths"
                                       sroperator="&gt;=" srtype="numeric"/>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <input type="text" name="hg75n" class="sr-formelement form-control" placeholder="Max:"
                                       srfield="baths"
                                       sroperator="&lt;=" srtype="numeric"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row margin-top-15">
                    <div class="col-md-4 col-sm-4">
                        <label for="">Sqft:</label>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                <input type="text" name="8vfm3" class="sr-formelement form-control" placeholder="Min:"
                                       srfield="sqft"
                                       sroperator="&gt;=" srtype="numeric"/>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <input type="text" name="pkf63" class="sr-formelement form-control" placeholder="Max:"
                                       srfield="sqft"
                                       sroperator="&lt;=" srtype="numeric"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row margin-top-40">
            <div class="col-md-3 col-sm-3">
                <label for="">Sort</label>
            </div>
            <div class="col-md-9 col-sm-9 left-border-line">
                <div class="row">
                    <div class="col-md-4 col-sm-4">
                        <label for="">
                            New Listings Within the Past:
                        </label>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <select id="as_New_Property" sroperator="<" srfield="listing_age"
                                class="sr-formelement form-control input-sm"
                                title="Select New Listings Within the Past..."
                                name="New_Property">
                            <option value="" selected="selected">All</option>
                            <option value="1">Today</option>
                            <option value="7">7 Days</option>
                            <option value="14">14 Days</option>
                            <option value="21">21 Days</option>
                            <option value="30">30 Days</option>
                        </select>
                    </div>
                </div>
                <div class="row margin-top-15">
                    <div class="col-md-4 col-sm-4"><label for="">Year Built:</label></div>
                    <div class="col-md-8 col-sm-8">
                        <select srfield="year_built" sroperator=">=" srtype="numeric" name="" id="Year-Built"
                                class="form-control sr-formelement">
                            <option value="">Any</option>
                            <option value="2010">2010 +</option>
                            <option value="2005">2005 +</option>
                            <option value="2000">2000 +</option>
                            <option value="1990">1990 +</option>
                            <option value="1980">1980 +</option>
                            <option value="1970">1970 +</option>
                            <option value="1960">1960 +</option>
                            <option value="1950">1950 +</option>
                            <option value="1949">Before 1950</option>
                        </select>
                    </div>
                </div>
                <div class="row margin-top-15">
                    <div class="col-md-4 col-sm-4">
                        <label for="sr-price-sort">Price sort:</label>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <select id="sr-price-sort" class="sr-order form-control">
                            <option srfield="price" srdirection="DESC" selected value="price:DESC">High to Low</option>
                            <option srfield="price" srdirection="ASC" value="price:ASC">Low to High</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if ($this->show_features == "true") {

            ?>
            <div class="row margin-top-40">
                <div class="col-md-3 col-sm-3">
                    <label for="">Features</label>
                </div>
                <div class="col-md-9 col-sm-9 left-border-line">
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <label for="">
                                        Community:
                                    </label>
                                </div>
                                <div class="col-md-8 col-sm-8">
                                    <ul class="ul__features">
                                        <li>
                                            <input id="sr_features-Pool" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Pool">
                                            <label for="sr_features-Pool">Pool</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Golf" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Golf">
                                            <label for="sr_features-Golf">Golf</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Tennis" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Tennis">
                                            <label for="sr_features-Tennis">Tennis</label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <label for="">
                                        Parking:
                                    </label>
                                </div>
                                <div class="col-md-8 col-sm-8">
                                    <ul class="ul__features">
                                        <li>
                                            <input id="sr_features-Garage" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Garage">
                                            <label for="sr_features-Garage">Garage</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Boat" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Boat">
                                            <label for="sr_features-Boat">Boat</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Carport" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Carport">
                                            <label for="sr_features-Carport">Carport</label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <label for="">
                                        Outdoor Living:
                                    </label>
                                </div>
                                <div class="col-md-8 col-sm-8">
                                    <ul class="ul__features">
                                        <li>
                                            <input id="sr_features-Deck" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Deck">
                                            <label for="sr_features-Deck">Deck</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Patio" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Patio">
                                            <label for="sr_features-Patio">Patio</label>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <label for="">
                                        Out Buildings:
                                    </label>
                                </div>
                                <div class="col-md-8 col-sm-8">
                                    <ul class="ul__features">
                                        <li>
                                            <input id="sr_features-Fenced" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Fenced">
                                            <label for="sr_features-Fenced">Fenced</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Sprinkler" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Sprinkler">
                                            <label for="sr_features-Sprinkler">Sprinkler System</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Barn" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Barn">
                                            <label for="sr_features-Barn">Barn</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Greenhouse" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Greenhouse">
                                            <label for="sr_features-Greenhouse">Greenhouse</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Workshop" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Workshop">
                                            <label for="sr_features-Workshop">Workshop</label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <label for="">
                                        Flooring:
                                    </label>
                                </div>
                                <div class="col-md-8 col-sm-8">
                                    <ul class="ul__features">
                                        <li>
                                            <input id="sr_features-Wood" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Wood">
                                            <label for="sr_features-Wood">Wood</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Marble" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Marble">
                                            <label for="sr_features-Marble">Marble</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Carpet" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Carpet">
                                            <label for="sr_features-Carpet">Carpet</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Laminate" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Laminate">
                                            <label for="sr_features-Laminate">Laminate</label>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <label for="">
                                        Interior:
                                    </label>
                                </div>
                                <div class="col-md-8 col-sm-8">
                                    <ul class="ul__features">
                                        <li>
                                            <input id="sr_features-Fireplace" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Fireplace">
                                            <label for="sr_features-Fireplace">Fireplace</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Furnished" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Furnished">
                                            <label for="sr_features-Furnished">Furnished</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Elevator" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Elevator">
                                            <label for="sr_features-Elevator">Elevator</label>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
        <?php if (isset($order[0]) && isset($order[1])): ?>
            <input type="hidden" class="sr-order" srfield="<?php echo $order[0] ?>"
                   srdirection="<?php echo $order[1] ?>"/>
        <?php endif; ?>
        <input type="hidden" class="sr-limit" value="<?php echo isset($params['perpage']) ? $params['perpage'] : 15 ?>">
        <button class="sr-submit">Search</button>
        <p></p>

        <p></p>
    </div>
<?php elseif ($type == "newsearch" || $type == "advanced"):
    wp_enqueue_script('sr_seorets-min');
    wp_print_scripts(array('sr_seorets-min'));
    ?>
    <div></div>
    <script type="text/javascript">
        (function () {
            var m = (function () {
                var s = document.getElementsByTagName('script');
                return s[s.length - 1];
            })();
            seorets.options = {blogurl: "<?php echo get_bloginfo('url')?>"};
            jQuery(function () {
                seorets.startForm(jQuery(m).nextAll('.sr-formsection:first'), function (root) {
                    root.find('.sr-class').change(function () {
                        root.attr("srtype", jQuery(this).attr("srtype"));
                        jQuery.ajax({
                            url: '<?php bloginfo('url') ?>/sr-ajax?action=getOnType',
                            type: 'post',
                            data: {
                                type: jQuery(this).attr("srtype")
                            },
                            success: function (response) {
                                if (response) {
                                    jQuery("#cities").html(' ');
                                    i = 0;
                                    for (i; i <= response.length - 1; i++) {
                                        if (i == 0) {
                                            jQuery("<option value='' selected='selected'>Any</option>").appendTo("#cities");
                                        }

                                        jQuery("<option>" + response[i] + "</option>").appendTo("#cities");
                                    }
                                }
                            }
                        });
                        jQuery.ajax({
                            url: '<?php bloginfo('url') ?>/sr-ajax?action=getOnType',
                            type: 'post',
                            data: {
                                areas: jQuery(this).attr("srtype")
                            },
                            success: function (response) {
                                if (response) {
                                    jQuery("#area").html(' ');
                                    i = 0;
                                    for (i; i <= response.length - 1; i++) {
                                        if (i == 0) {
                                            jQuery("<option value='' selected='selected'>Any</option>").appendTo("#area");
                                        }

                                        jQuery("<option>" + response[i] + "</option>").appendTo("#area");
                                    }
                                }
                            }
                        });
                        jQuery.ajax({
                            url: '<?php bloginfo('url') ?>/sr-ajax?action=getOnType',
                            type: 'post',
                            data: {
                                subd: jQuery(this).attr("srtype")
                            },
                            success: function (response) {
                                if (response) {
                                    jQuery("#subdivision").html(' ');
                                    i = 0;
                                    if (response) {
                                        for (i; i <= response.length - 1; i++) {
                                            if (i == 0) {
                                                jQuery("<option value='' selected='selected'>Any</option>").appendTo("#subdivision");
                                            }

                                            jQuery("<option>" + response[i] + "</option>").appendTo("#subdivision");
                                        }
                                    }
                                }
                            }
                        });
                        jQuery.ajax({
                            url: '<?php bloginfo('url') ?>/sr-ajax?action=getOnType',
                            type: 'post',
                            data: {
                                subt: jQuery(this).attr("srtype")
                            },
                            success: function (response) {
                                if (response) {
                                    jQuery("#sub_type").html(' ');
                                    i = 0;
                                    for (i; i <= response.length - 1; i++) {
                                        if (response != null) {
                                            jQuery("#sb_type").removeClass("disp-none");
                                            if (i == 0) {
                                                jQuery("<option value='' selected='selected'>Any</option>").appendTo("#sub_type");
                                            }

                                            jQuery("<option>" + response[i] + "</option>").appendTo("#sub_type");
                                        } else {
                                            jQuery("#sb_type").addClass("disp-none");
                                        }

                                    }
                                }
                            }
                        });
                        jQuery.ajax({
                            url: '<?php bloginfo('url') ?>/sr-ajax?action=getOnType',
                            type: 'post',
                            data: {
                                subd: jQuery(this).attr("srtype")
                            },
                            success: function (response) {
                                if (response) {
                                    var show = false;
                                    if (response != null) {
                                        jQuery("#subd-none").removeClass("disp-none");
                                    } else {
                                        jQuery("#subd-none").addClass("disp-none");
                                    }
                                }
                            }
                        });
                    });
                    root.find('.sr-class:checked').change();
                });
            });
        })();</script>
    <style>
        .disp-none {
            display: none;
        }
    </style>
    <div class="sr-formsection sr-content" srtype="res" sroperator="AND">
        <?php
        if ($fields != NULL) {
            foreach ($field_A as $key => $values) {
                ?>
                <select style="display: none" class="sr-formelement" srfield="<?= $key; ?>" sroperator="LIKE"
                        multiple="" name="" id="">
                    <?php
                    foreach ($values as $sval) {
                        ?>
                        <option selected value="<?= $sval; ?>"><?= $sval ?></option>
                        <?
                    }
                    ?>
                </select>
                <?
            }
        }
        ?>
        <div class="row">
            <div class="col-md-3 col-sm-3">
                <label for="">
                    Location &amp; Home Type
                </label>
            </div>
            <div class="col-md-9 col-sm-9 left-border-line">
                <div class="row">
                    <div class="col-md-4 col-sm-4">
                        <label for="">Type:</label>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <?php
                        $n = 0;
                        foreach ($sr->metadata as $key => $val) {
                            if ($sr->is_type_hidden($key)) {
                                continue;
                            }
                            if ($n == 0 && $key == 'res') {
                                $n++;

                                echo "\t\t\t\t\t\t\t<input type=\"radio\" name=\"class\" class=\"sr-class\" srtype=\"{$key}\" checked=\"checked\" /> " . (isset($val->pretty_name) ? $val->pretty_name : $key) . "<br />";
                            } else {

                                echo "\t\t\t\t\t\t\t<input type=\"radio\" name=\"class\" class=\"sr-class\" srtype=\"{$key}\" /> " . (isset($val->pretty_name) ? $val->pretty_name : $key) . "<br />";
                            }
                        }
                        ?>
                    </div>
                </div>

                <div id="sb_type" class="row disp-none margin-top-15">
                    <div class="col-md-4 col-sm-4">
                        <label for="">Sub Type:</label>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <select id="sub_type" class="sr-formelement form-control" srfield="sub_type" sroperator="="
                                multiple="">
                            <option value="">All</option>
                            <?php
                            $sub_types = $sr->metadata->res->fields->sub_type->values;
                            sort($sub_types);
                            foreach ($sub_types as $sub_type) {
                                echo "\t\t\t\t\t\t\t\t<option>" . htmlentities($sub_type) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row margin-top-15">
                    <div class="col-md-4 col-sm-4">
                        <label for="">Search Within City:</label>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <select id="cities" class="sr-formelement form-control" srfield="city" sroperator="="
                                multiple="">
                            <option value="">All</option>
                            <?php
                            $cities = $sr->metadata->res->fields->city->values;
                            sort($cities);
                            foreach ($cities as $city) {
                                echo "\t\t\t\t\t\t\t\t<option>" . htmlentities($city) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row margin-top-15">
                    <div class="col-md-4 col-sm-4">
                        <label for="">Search Within Area:</label>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <select id="area" class="sr-formelement form-control" srfield="area" sroperator="="
                                multiple="">
                            <option value="">All</option>
                            <?php
                            $areas = $sr->metadata->res->fields->area->values;
                            sort($areas);
                            foreach ($areas as $area) {
                                echo "\t\t\t\t\t\t\t\t<option>" . htmlentities($area) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row margin-top-15">
                    <div class="col-md-4 col-sm-4"><label for="">Address:</label></div>
                    <div class="col-md-8 col-sm-8">
                        <input type="text" name="dsg84" class="sr-formelement form-control" srfield="address"
                               sroperator="LIKE" srloose=""/>
                    </div>
                </div>
                <div id="subd-none" class="row margin-top-15 disp-none">
                    <div class="col-md-4 col-sm-4"><label for="">Subdivision:</label></div>
                    <div class="col-md-8 col-sm-8">
                        <select id="subdivision" class="sr-formelement form-control" srfield="subdivision"
                                sroperator="="
                                multiple="">
                            <option value="">All</option>
                            <?php
                            $sundivisions = $sr->metadata->res->fields->subdivision->values;
                            sort($sundivisions);
                            foreach ($sundivisions as $sundivision) {
                                echo "\t\t\t\t\t\t\t\t<option>" . htmlentities($sundivision) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row margin-top-15">
                    <div class="col-md-4 col-sm-4"><label for="">MLS#:</label></div>
                    <div class="col-md-8 col-sm-8">
                        <input type="text" name="2k30f" class="sr-formelement form-control" srfield="mls_id"
                               sroperator="LIKE"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row margin-top-40">
            <div class="col-md-3 col-sm-3">
                <label for="">Price & Size</label>
            </div>
            <div class="col-md-9 col-sm-9 left-border-line">
                <div class="row">
                    <div class="col-md-4 col-sm-4">
                        <label for="">
                            Price:
                        </label>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <div class="row">
                            <!--                            <div class="col-md-2 col-sm-2">-->
                            <!--                                <label for="">Min:</label>-->
                            <!--                            </div>-->
                            <div class="col-md-66 col-sm-6">
                                <input type="text" name="f89d5" class="sr-formelement form-control" placeholder="Min:"
                                       srfield="price"
                                       sroperator="&gt;=" srtype="numeric"/>
                            </div>
                            <!--                            <div class="col-md-2 col-sm-2">-->
                            <!--                                <label for="">Max:</label>-->
                            <!--                            </div>-->
                            <div class="col-md-6 col-sm-6">
                                <input type="text" name="pa3jv" class="sr-formelement form-control" placeholder="Max:"
                                       srfield="price"
                                       sroperator="&lt;=" srtype="numeric"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row margin-top-15">
                    <div class="col-md-4 col-sm-4"><label for="">Bedrooms:</label></div>
                    <div class="col-md-8 col-sm-8">
                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                <input type="text" name="d844k" class="sr-formelement form-control" placeholder="Min:"
                                       srfield="bedrooms"
                                       sroperator="&gt;=" srtype="numeric"/>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <input type="text" name="fd8j7" class="sr-formelement form-control" placeholder="Max:"
                                       srfield="bedrooms"
                                       sroperator="&lt;=" srtype="numeric"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row margin-top-15">
                    <div class="col-md-4 col-sm-4">
                        <label for="">Bathrooms:</label>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                <input type="text" name="df69d" class="sr-formelement form-control" placeholder="Min:"
                                       srfield="baths"
                                       sroperator="&gt;=" srtype="numeric"/>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <input type="text" name="hg75n" class="sr-formelement form-control" placeholder="Max:"
                                       srfield="baths"
                                       sroperator="&lt;=" srtype="numeric"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row margin-top-15">
                    <div class="col-md-4 col-sm-4">
                        <label for="">Sqft:</label>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                <input type="text" name="8vfm3" class="sr-formelement form-control" placeholder="Min:"
                                       srfield="sqft"
                                       sroperator="&gt;=" srtype="numeric"/>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <input type="text" name="pkf63" class="sr-formelement form-control" placeholder="Max:"
                                       srfield="sqft"
                                       sroperator="&lt;=" srtype="numeric"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row margin-top-40">
            <div class="col-md-3 col-sm-3">
                <label for="">Sort</label>
            </div>
            <div class="col-md-9 col-sm-9 left-border-line">
                <div class="row">
                    <div class="col-md-4 col-sm-4">
                        <label for="">
                            New Listings Within the Past:
                        </label>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <select id="as_New_Property" sroperator="<" srfield="listing_age"
                                class="sr-formelement form-control input-sm"
                                title="Select New Listings Within the Past..."
                                name="New_Property">
                            <option value="" selected="selected">All</option>
                            <option value="1">Today</option>
                            <option value="7">7 Days</option>
                            <option value="14">14 Days</option>
                            <option value="21">21 Days</option>
                            <option value="30">30 Days</option>
                        </select>
                    </div>
                </div>
                <div class="row margin-top-15">
                    <div class="col-md-4 col-sm-4"><label for="">Year Built:</label></div>
                    <div class="col-md-8 col-sm-8">
                        <select srfield="year_built" sroperator=">=" srtype="numeric" name="" id="Year-Built"
                                class="form-control sr-formelement">
                            <option value="">Any</option>
                            <option value="2010">2010 +</option>
                            <option value="2005">2005 +</option>
                            <option value="2000">2000 +</option>
                            <option value="1990">1990 +</option>
                            <option value="1980">1980 +</option>
                            <option value="1970">1970 +</option>
                            <option value="1960">1960 +</option>
                            <option value="1950">1950 +</option>
                            <option value="1949">Before 1950</option>
                        </select>
                    </div>
                </div>
                <div class="row margin-top-15">
                    <div class="col-md-4 col-sm-4">
                        <label for="sr-price-sort">Price sort:</label>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <select id="sr-price-sort" class="sr-order form-control">
                            <option srfield="price" srdirection="DESC" selected value="price:DESC">High to Low</option>
                            <option srfield="price" srdirection="ASC" value="price:ASC">Low to High</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if ($this->show_features == "true") {

            ?>
            <div class="row margin-top-40">
                <div class="col-md-3 col-sm-3">
                    <label for="">Features</label>
                </div>
                <div class="col-md-9 col-sm-9 left-border-line">
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <label for="">
                                        Community:
                                    </label>
                                </div>
                                <div class="col-md-8 col-sm-8">
                                    <ul class="ul__features">
                                        <li>
                                            <input id="sr_features-Pool" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Pool">
                                            <label for="sr_features-Pool">Pool</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Golf" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Golf">
                                            <label for="sr_features-Golf">Golf</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Tennis" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Tennis">
                                            <label for="sr_features-Tennis">Tennis</label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <label for="">
                                        Parking:
                                    </label>
                                </div>
                                <div class="col-md-8 col-sm-8">
                                    <ul class="ul__features">
                                        <li>
                                            <input id="sr_features-Garage" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Garage">
                                            <label for="sr_features-Garage">Garage</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Boat" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Boat">
                                            <label for="sr_features-Boat">Boat</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Carport" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Carport">
                                            <label for="sr_features-Carport">Carport</label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <label for="">
                                        Outdoor Living:
                                    </label>
                                </div>
                                <div class="col-md-8 col-sm-8">
                                    <ul class="ul__features">
                                        <li>
                                            <input id="sr_features-Deck" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Deck">
                                            <label for="sr_features-Deck">Deck</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Patio" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Patio">
                                            <label for="sr_features-Patio">Patio</label>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <label for="">
                                        Out Buildings:
                                    </label>
                                </div>
                                <div class="col-md-8 col-sm-8">
                                    <ul class="ul__features">
                                        <li>
                                            <input id="sr_features-Fenced" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Fenced">
                                            <label for="sr_features-Fenced">Fenced</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Sprinkler" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Sprinkler">
                                            <label for="sr_features-Sprinkler">Sprinkler System</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Barn" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Barn">
                                            <label for="sr_features-Barn">Barn</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Greenhouse" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Greenhouse">
                                            <label for="sr_features-Greenhouse">Greenhouse</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Workshop" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Workshop">
                                            <label for="sr_features-Workshop">Workshop</label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <label for="">
                                        Flooring:
                                    </label>
                                </div>
                                <div class="col-md-8 col-sm-8">
                                    <ul class="ul__features">
                                        <li>
                                            <input id="sr_features-Wood" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Wood">
                                            <label for="sr_features-Wood">Wood</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Marble" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Marble">
                                            <label for="sr_features-Marble">Marble</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Carpet" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Carpet">
                                            <label for="sr_features-Carpet">Carpet</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Laminate" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Laminate">
                                            <label for="sr_features-Laminate">Laminate</label>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <label for="">
                                        Interior:
                                    </label>
                                </div>
                                <div class="col-md-8 col-sm-8">
                                    <ul class="ul__features">
                                        <li>
                                            <input id="sr_features-Fireplace" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Fireplace">
                                            <label for="sr_features-Fireplace">Fireplace</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Furnished" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Furnished">
                                            <label for="sr_features-Furnished">Furnished</label>
                                        </li>
                                        <li>
                                            <input id="sr_features-Elevator" type="checkbox" class="sr-formelement"
                                                   srfield="features"
                                                   sroperator="LIKE" value="Elevator">
                                            <label for="sr_features-Elevator">Elevator</label>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
        <?php if (isset($order[0]) && isset($order[1])): ?>
            <input type="hidden" class="sr-order" srfield="<?php echo $order[0] ?>"
                   srdirection="<?php echo $order[1] ?>"/>
        <?php endif; ?>
        <input type="hidden" class="sr-limit" value="<?php echo isset($params['perpage']) ? $params['perpage'] : 15 ?>">
        <button class="sr-submit">Search</button>
        <p></p>

        <p></p>
    </div>

<?php else : ?>
    <form action="<?php echo get_bloginfo('url') ?>/sr-search" method="get">
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td valign="top" width='120px' style='border-right: 1px solid #BFBDAB;'>Location &amp; Home Type</td>
                <td width='559px'>
                    <table style="margin-left: 30px;" width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td valign="top">Type</td>
                            <td>
                                <?php
                                $n = 0;
                                foreach ($sr->metadata as $key => $val) {
                                    if ($sr->is_type_hidden($key)) {
                                        continue;
                                    }
                                    if ($n == 0) {
                                        $n++;
                                        echo "\t\t\t\t\t\t\t<input type=\"radio\" name=\"type\" value=\"{$key}\" checked=\"checked\" /> " . (isset($val->pretty_name) ? $val->pretty_name : $key) . "<br />";
                                    } else {
                                        echo "\t\t\t\t\t\t\t<input type=\"radio\" name=\"type\" value=\"{$key}\" /> " . (isset($val->pretty_name) ? $val->pretty_name : $key) . "<br />";
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="19%">Search Within</td>
                            <td width="81%">&nbsp;</td>
                        </tr>
                        <tr>
                            <td valign="top">Area:</td>
                            <td>
                                <select name="conditions[0][value][]" multiple>
                                    <option value="">All</option>
                                    <?php
                                    $cities = $sr->metadata->res->fields->city->values;
                                    sort($cities);
                                    foreach ($cities as $city) {
                                        echo "\t\t\t\t\t\t\t\t<option>" . htmlentities($city) . "</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Address:</td>
                            <td><input type="text" name="conditions[1][value]"/></td>
                        </tr>
                        <tr>
                            <td>Subdivision:</td>
                            <td><input type="text" name="conditions[2][value]"/></td>
                        </tr>
                        <tr>
                            <td>MLS#:</td>
                            <td><input type="text" name="conditions[3][value]"
                                       onchange="this.value=jQuery.trim(this.value);"/></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td valign="top" width='120px' style='border-right: 1px solid #BFBDAB;width: 125px;'>Price &amp; Size
                </td>
                <td width='559px'>
                    <table style="margin-left: 30px;" width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="17%">Price</td>
                            <td width="38%">Min: <input type="text" name="conditions[4][value]" style="width: 8em;"/>
                            </td>
                            <td width="45%">Max: <input type="text" name="conditions[5][value]" style="width: 8em;"/>
                            </td>
                        </tr>
                        <tr>
                            <td>Bedrooms:</td>
                            <td>Min: <input type="text" name="conditions[6][value]" style="width: 8em;"/></td>
                            <td>Max: <input type="text" name="conditions[7][value]" style="width: 8em;"/></td>
                        </tr>
                        <tr>
                            <td>Bathrooms:</td>
                            <td>Min: <input type="text" name="conditions[8][value]" style="width: 8em;"/></td>
                            <td>Max: <input type="text" name="conditions[9][value]" style="width: 8em;"/></td>
                        </tr>
                        <tr>
                            <td>Sqft:</td>
                            <td>Min: <input type="text" name="conditions[10][value]" style="width: 8em;"/></td>
                            <td>Max: <input type="text" name="conditions[11][value]" style="width: 8em;"/></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>
        <br>
        <!-- <?php
        if (isset($sr->metadata->res->fields->features) && $sr->metadata->res->fields->features->type == "array") {
            $i = 70;
            foreach ($sr->metadata->res->fields->features->values as $value) :?>
			<?php echo ucwords($value) ?>: <input type="checkbox" name="conditions[<?php echo $i ?>][value]" value="<?php echo $value ?>" />
			<input type="hidden" name="conditions[<?php echo $i ?>][field]" value="features" />
			<input type="hidden" name="conditions[<?php echo $i++ ?>][operator]" value="=" /><br />
		<?php endforeach;
        }
        ?> -->
        <input type="hidden" name="perpage" value="20"/>
        <input type="hidden" name="conditions[0][field]" value="city"/>
        <input type="hidden" name="conditions[0][operator]" value="="/>
        <input type="hidden" name="conditions[1][field]" value="address"/>
        <input type="hidden" name="conditions[1][operator]" value="LIKE"/>
        <input type="hidden" name="conditions[1][loose]" value="1"/>
        <input type="hidden" name="conditions[2][field]" value="subdivision"/>
        <input type="hidden" name="conditions[2][operator]" value="LIKE"/>
        <input type="hidden" name="conditions[2][loose]" value="1"/>
        <input type="hidden" name="conditions[3][field]" value="mls_id"/>
        <input type="hidden" name="conditions[3][operator]" value="="/>
        <input type="hidden" name="conditions[4][field]" value="price"/>
        <input type="hidden" name="conditions[4][operator]" value="&gt;="/>
        <input type="hidden" name="conditions[5][field]" value="price"/>
        <input type="hidden" name="conditions[5][operator]" value="&lt;="/>
        <input type="hidden" name="conditions[6][field]" value="bedrooms"/>
        <input type="hidden" name="conditions[6][operator]" value="&gt;="/>
        <input type="hidden" name="conditions[7][field]" value="bedrooms"/>
        <input type="hidden" name="conditions[7][operator]" value="&lt;="/>
        <?php if (isset($order[0]) && isset($order[1])): ?>
            <input type="hidden" name="order_wp_sux[0][field]" value="<?php echo $order[0] ?>"/>
            <input type="hidden" name="order_wp_sux[0][order]" value="<?php echo $order[1] ?>"/>
        <?php endif; ?>
        <input type="hidden" name="conditions[8][field]" value="baths"/>
        <input type="hidden" name="conditions[8][operator]" value="&gt;="/>
        <input type="hidden" name="conditions[9][field]" value="baths"/>
        <input type="hidden" name="conditions[9][operator]" value="&lt;="/>
        <input type="hidden" name="conditions[10][field]" value="sqft"/>
        <input type="hidden" name="conditions[10][operator]" value="&gt;="/>
        <input type="hidden" name="conditions[11][field]" value="sqft"/>
        <input type="hidden" name="conditions[11][operator]" value="&gt;="/>
        <input type="submit" value="Search"/>
    </form>
<?php endif; ?>
