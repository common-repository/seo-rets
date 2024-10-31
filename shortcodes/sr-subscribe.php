<?php
$sr = $seo_rets_plugin;

if (!$sr->api_key) return '<p class="sr-error">You must activate the SEO RETS plugin before using shortcodes.</p>';
?>
<script type="text/javascript">

    function validateForm(form) {
        if (!validateEmail(form["email"].value)) {
            alert("Invalid email address");
            return false;
        }
        return true;
    }

    function validateEmail(email) {
        var re = /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Za-z]{2}|com|org|net|edu|gov|mil|biz|info|mobi|name|aero|asia|jobs|museum)\b$/;
        return re.test(email);
    }
</script>
<div class="sr-content">
    <form action="<?php echo get_bloginfo('url') ?>/sr-subscribe" method="get" onsubmit="return validateForm(this)">
        <div class="row">
            <div class="col-md-2 col-sm-2">
                <label for="">Email:</label>
            </div>
            <div class="col-md-5 col-sm-5">
                <input type="text" class="form-control" name="email"/>
            </div>
        </div>
        <div class="row margin-top-10">
            <div class="col-md-2 col-sm-2">
                <label for="">Name:</label>
            </div>
            <div class="col-md-5 col-sm-5">
                <input type="text" class="form-control" name="sr-name"/>
            </div>
        </div>
        <div class="row margin-top-20">
            <div class="col-md-3 col-ms-3">
                <span>Location &amp; Home Type</span>
            </div>
            <div class="col-md-9 col-ms-9 left-border-line">
                <div class="row">
                    <div class="col-md-3 col-sm-3">
                        <p><strong>City:</strong><br/>
                            Use control + click to select multiple</p>
                    </div>
                    <div class="col-md-9 col-sm-9">
                        <select name="conditions[3][value][]" multiple=
                        "multiple" class="form-control" size="8">
                            <option value="">All</option>
                            <?php $cities = $sr->metadata->res->fields->city->values;
                            sort($cities);
                            foreach ($cities as $city): ?>
                                <option><?php echo $city ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row margin-top-20">
            <div class="col-md-3 col-sm-3">
                <span>Price &amp; Size</span>
            </div>
            <div class="col-md-9 col-sm-9 left-border-line">
                <div class="row">
                    <div class="col-md-3 col-sm-3">
                        <label>
                            <strong>Price:</strong>
                        </label>
                    </div>
                    <div class="col-md-9 col-sm-9">
                        <div class="row">
                            <div class="col-md-2 col-sm-2">
                                <label for="">Min:</label>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <input type="text" name="conditions[1][value]" class="form-control"/>
                            </div>
                            <div class="col-md-2 col-sm-2">
                                <label for="">Max:</label>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <input type="text" name="conditions[2][value]" class="form-control"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row margin-top-10">
                    <div class="col-md-3 col-sm-3">
                        <label>
                            <strong>Bedrooms:</strong>
                        </label>
                    </div>
                    <div class="col-md-9 col-sm-9">
                        <div class="row">
                            <div class="col-md-2 col-sm-2">
                                <label for="">Min:</label>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <input type="text" name="conditions[5][value]" class="form-control"/>
                            </div>
                            <div class="col-md-2 col-sm-2">
                                <label for="">Max:</label>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <input type="text" name="conditions[6][value]" class="form-control"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row margin-top-10">
                    <div class="col-md-3 col-sm-3">
                        <label>
                            <strong>Bathrooms:</strong>
                        </label>
                    </div>
                    <div class="col-md-9 col-sm-9">
                        <div class="row">
                            <div class="col-md-2 col-sm-2">
                                <label for="">Min:</label>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <input type="text" name="conditions[7][value]" class="form-control"/>
                            </div>
                            <div class="col-md-2 col-sm-2">
                                <label for="">Max:</label>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <input type="text" name="conditions[8][value]" class="form-control"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row margin-top-10">
                    <div class="col-md-3 col-sm-3">
                        <label>
                            <strong>Sqft:</strong>
                        </label>
                    </div>
                    <div class="col-md-9 col-sm-9">
                        <div class="row">
                            <div class="col-md-2 col-sm-2">
                                <label for="">Min:</label>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <input type="text" name="conditions[31][value]" class="form-control"/>
                            </div>
                            <div class="col-md-2 col-sm-2">
                                <label for="">Max:</label>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <input type="text" name="conditions[32][value]" class="form-control"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="type" value="Homes"/>
        <input type="hidden" name="perpage" value="20"/>
        <input type="hidden" name="conditions[3][operator]" value="LIKE"/>
        <input type="hidden" name="conditions[3][loose]" value="1"/>
        <input type="hidden" name="conditions[3][field]" value="city"/>
        <!--<input type="hidden" name="conditions[0][operator]" value="LIKE" />
        <input type="hidden" name="conditions[0][loose]" value="1" />
        <input type="hidden" name="conditions[0][field]" value="address" />-->
        <input type="hidden" name="conditions[1][operator]" value="&gt;="/>
        <input type="hidden" name="conditions[1][field]" value="price"/>
        <input type="hidden" name="conditions[2][operator]" value="&lt;="/>
        <input type="hidden" name="conditions[2][field]" value="price"/>
        <!--<input type="hidden" name="conditions[4][operator]" value="LIKE" />
        <input type="hidden" name="conditions[4][loose]" value="1" />
        <input type="hidden" name="conditions[4][field]" value="subdivision" />
        <input type="hidden" name="conditions[4][operator]" value="=" />
        <input type="hidden" name="conditions[4][field]" value="mls_id" />-->
        <input type="hidden" name="conditions[5][operator]" value="&gt;="/>
        <input type="hidden" name="conditions[5][field]" value="bedrooms"/>
        <input type="hidden" name="conditions[6][operator]" value="&lt;="/>
        <input type="hidden" name="conditions[6][field]" value="bedrooms"/>
        <input type="hidden" name="conditions[7][operator]" value="&gt;="/>
        <input type="hidden" name="conditions[7][field]" value="baths_full"/>
        <input type="hidden" name="conditions[8][operator]" value="&lt;="/>
        <input type="hidden" name="conditions[8][field]" value="baths_full"/>
        <input type="hidden" name="conditions[31][operator]" value="&gt;="/>
        <input type="hidden" name="conditions[31][field]" value="sqft"/>
        <input type="hidden" name="conditions[32][operator]" value="&lt;="/>
        <input type="hidden" name="conditions[32][field]" value="sqft"/>
        <!-- Commented by David Pope - Broken needs rewrite
	<!--<script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=6Lfki9ESAAAAAPsFuGq1nSfRWQgO8nZItbl5Q6ML"></script>
        <noscript>
		<iframe src="http://www.google.com/recaptcha/api/noscript?k=6Lfki9ESAAAAANZ3ZaQPg6l7W6v2hV3TrayhR9_j" height="300" width="500" frameborder="0"></iframe><br/>
		<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
		<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
	</noscript>-->
        <br/>
        <input type="submit" value="Subscribe"/>
    </form>
</div>