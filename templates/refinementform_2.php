<?
wp_enqueue_script('sr_seorets-min');
wp_print_scripts(array('sr_seorets-min'));
?>
<div></div>
<script type="text/javascript">
    jQuery(function () {
        var form = jQuery("#refinesearch");
        var refinebtn = jQuery("#sr-refinebtn");
        var bedsfield = jQuery("#sr-bedsfield");
        var bathsfield = jQuery("#sr-bathsfield");
        var pricefieldl = jQuery("#sr-pricefieldl");
        var pricefieldh = jQuery("#sr-pricefieldh");
        var priceSort = jQuery("#sr-price-sort");

        var request = <?php echo json_encode($query)?>;
        console.log(request);
        var query = request.q;
        form.attr("srtype", request.t);
        if (request.o) {
            if (request.o[0]['f'] == 'price') {
                if (request.o[0]['o'] == 0) {
                    priceSort.val('price:DESC');
                } else {
                    priceSort.val('price:ASC');
                }
            }
        }
        if (query.b !== 1) {
            query = {b: 1, c: [query]};
        }

        for (var i = 0; i < query.c.length; i++) {
            var cond = query.c[i];

            if (sr_parse_condition(cond)) {
                query.c.splice(i, 1);
                i--;
            }
        }

        function format_money(amount, decimals, decimal_sep, thousands_sep) {
            var n = amount,
                c = isNaN(decimals) ? 2 : Math.abs(decimals),
                d = decimal_sep || '.',
                t = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                sign = (n < 0) ? '-' : '',
                i = parseInt(n = Math.abs(n).toFixed(c)) + '',
                j = ((j = i.length) > 3) ? j % 3 : 0;
            return sign + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');
        }

        function sr_parse_condition(condition) {
            if (condition.f == "bedrooms" && (condition.o == ">=" || condition.o == ">")) {
                if (condition.o == ">") {
                    condition.v++;
                }
                bedsfield.find("option[value=" + condition.v + "]").attr("selected", "selected");
            } else if (condition.f == "baths" && (condition.o == ">=" || condition.o == ">")) {
                if (condition.o == ">") {
                    condition.v++;
                }
                bathsfield.find("option[value=" + condition.v + "]").attr("selected", "selected");
            } else if (condition.f == "price") {
                if (condition.o == ">" || condition.o == ">=") {
                    pricefieldl.val("$" + format_money(condition.v, 0));
                } else if (condition.o == "<" || condition.o == "<=") {
                    pricefieldh.val("$" + format_money(condition.v, 0));
                }
            }
            else {
                return false;
            }
            return true;
        }


        refinebtn.click(function () {
            var newrequest = seorets.getFormRequest(form);
            request.q = newrequest.q;
            request.o = [newrequest.o[0]];
            request.q.c = request.q.c.concat(query.c);
            request.g = 1;
            window.location = "?" + encodeURIComponent(Base64.encode(JSON.stringify(request)));
        });
    });
</script>
<div class="sr-content" id="refinesearch">
    <div class="row">
        <div class="col-md-6 col-sm-6">
            <span>Refine Your Search</span>
        </div>
        <div style="text-align: right" class="col-md-6 col-sm-6">
            <?
            $url = $_SERVER['REQUEST_URI'];
            $link = str_replace('/sr-search', '/sr-search-fav', $url);
            $link = str_replace('?', '?add=', $link);
            date_default_timezone_set("US/Central");

            function searchURLinStatistic($array, $key, $value)
            {
                $results = array();

                if (is_array($array)) {
                    if (isset($array[$key]) && $array[$key] == $value) {
                        $results[] = $array;
                    }

                    foreach ($array as $index => $subarray) {
                        $subarray['index'] = $index;
                        $results = array_merge($results, searchURLinStatistic($subarray, $key, $value));
                    }
                }

                return $results;
            }


            $searchViewStatistic = get_option('sr_searchViewStatistic');
            $searchView = searchURLinStatistic($searchViewStatistic, 'link', $url);
            if (isset($searchView) && !empty($searchView)) {
                $searchViewStatistic[$searchView[0]['index']]['count']++;
                $searchViewStatistic[$searchView[0]['index']]['last_view'] = date("Y-m-d H:i:s");
                update_option('sr_searchViewStatistic', $searchViewStatistic);
            } else {
                $searchViewA = array(
                    'link' => $url,
                    'count' => 1,
                    'last_view' => date("Y-m-d H:i:s"),
                );
                $searchViewStatistic[] = $searchViewA;
                update_option('sr_searchViewStatistic', $searchViewStatistic);
            }


            ?>
            <span><a href="<?php echo $link ?>">Save Your Search</a></span>
        </div>
    </div>
    <div class="row margin-top-10">
        <div class="col-md-4 col-sm-4">
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <label for="sr-bedsfield">Bedrooms:</label>
                    <select class="sr-formelement form-control" id="sr-bedsfield" name="fk30c" srfield="bedrooms"
                            srtype="numeric" sroperator=">=">
                        <option value="">Any</option>
                        <option value="1">1+</option>
                        <option value="2">2+</option>
                        <option value="3">3+</option>
                        <option value="4">4+</option>
                        <option value="5">5+</option>
                        <option value="6">6+</option>
                        <option value="7">7+</option>
                        <option value="8">8+</option>
                    </select>
                </div>
                <div class="col-md-6 col-sm-6">
                    <label for="sr-bathsfield">Baths:</label>
                    <select class="sr-formelement form-control" id="sr-bathsfield" name="3jc7q" srfield="baths"
                            srtype="numeric" sroperator=">=">
                        <option value="">Any</option>
                        <option value="1">1+</option>
                        <option value="2">2+</option>
                        <option value="3">3+</option>
                        <option value="4">4+</option>
                        <option value="5">5+</option>
                        <option value="6">6+</option>
                        <option value="7">7+</option>
                        <option value="8">8+</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-4">
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <label for="sr-pricefieldl">Price:</label>
                    <input type="text" id="sr-pricefieldl" name="sd94j" placeholder="Min"
                           class="sr-formelement form-control"
                           srfield="price" srtype="numeric" sroperator=">=" size="9"/>
                </div>
                <div class="col-md-6 col-sm-6">
                    <label for="">&nbsp;</label>
                    <input type="text" id="sr-pricefieldh" name="mkx73" placeholder="Max"
                           class="sr-formelement form-control"
                           srfield="price" srtype="numeric" sroperator="<=" size="9"/>
                </div>

            </div>
        </div>
        <div class="col-md-4 col-sm-4">
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <label for="sr-price-sort">Price sort:</label>

                    <select id="sr-price-sort" class="sr-order form-control">
                        <option srfield="price" srdirection="DESC" value="price:DESC">High to Low</option>
                        <option srfield="price" srdirection="ASC" value="price:ASC">Low to High</option>
                    </select>
                </div>
                <div class="col-md-6 col-sm-6">
                    <label for="">&nbsp;</label>
                    <input type="submit" class="form-control" id="sr-refinebtn" value="Refine"/>
                </div>
            </div>
        </div>
    </div>
</div>