<?php
$sr = $seo_rets_plugin;
$plugin_title = $sr->admin_title;
$plugin_id = $sr->admin_id;


//delete_option('sr-shortcode');
?>
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>-->
<style>
    .col-1 {
        width: 100%;
        /*float: left;*/
        position: relative;
        min-height: 1px;
        padding-right: 15px;
        padding-left: 15px;
        box-sizing: border-box;
    }

    .col-2 {
        width: 50%;
        /*float: left;*/
        position: relative;
        min-height: 1px;
        padding-right: 15px;
        padding-left: 15px;
        box-sizing: border-box;
    }

    .col-2-left {
        width: 50%;
        float: left;
        position: relative;
        min-height: 1px;
        padding-right: 15px;
        padding-left: 15px;
        box-sizing: border-box;
    }

    .col-3-left {
        width: 33.333333%;
        float: left;
        position: relative;
        min-height: 1px;
        padding-right: 15px;
        padding-left: 15px;
        box-sizing: border-box;
    }

    .col-4-left {
        width: 25%;
        float: left;
        position: relative;
        min-height: 1px;
        padding-right: 15px;
        padding-left: 15px;
        box-sizing: border-box;
    }

    .col-6-left {
        width: 66.66666667%;
        float: left;
        position: relative;
        min-height: 1px;
        padding-right: 15px;
        padding-left: 15px;
        box-sizing: border-box;
    }

    .listscode {
        margin-top: 5px;
        padding: 10px;
        border-bottom: 1px solid #cfdadd;
    }

    .addButton {
        width: 100px;
        height: 30px;
        float: left;
    }

    /*.select_fields__DIV {*/
    /*margin-left: 100px;*/
    /*}*/

    .hide {
        display: none;
    }

    select.sr-formelement {
        display: none;
    }

    .value {
        padding: 5px 10px;
        background: #ffffff;
        border-radius: 3px;
        border: 1px solid #cfdadd;
        position: relative;
        float: left;
        margin-right: 5px;
        margin-top: 5px;
    }

    .value span {
        padding-left: 10px;
        margin-left: 5px;
        border-left: 1px solid #cfdadd;
        cursor: pointer;
    }

    .value span:hover {
        color: #f05050;
    }

    label {
        font-weight: bold;
        cursor: default !important;
    }

    .showAllFields:before, .selectField:before, .row:before {
        display: table;
        content: " ";
    }

    .showAllFields:after, .selectField:after, .row:after {
        display: table;
        content: " ";
        clear: both;
    }

    .showAllFields, .selectField, .row {
        /*width: 100%;*/
        min-height: 1px;
        margin-right: -15px;
        margin-left: -15px;
        box-sizing: border-box;
    }

    .counter_div {
        background: #fff;
        text-align: center;
        padding: 5px;
        border: 1px solid #cfdadd;
        margin-top: 20px;
    }

    .short {
        background: #fff;
        padding: 5px;
        border: 1px solid #cfdadd;
        /*margin-top: 20px;*/
    }

    .ShortCodeHint {
        color: green;
        /*display: none;*/
    }

    .deleteFiled {
        text-decoration: underline;
        color: #00a0d2;
        cursor: pointer;
    }
</style>


<script type="text/javascript"
        src="<?php echo $sr->plugin_dir; ?>resources/js/seorets.min.js"></script>
<script>
    function SelectText(element) {
        var doc = document,
            text = doc.getElementById(element),
            range,
            selection;
        if (doc.body.createTextRange) {
            range = document.body.createTextRange();
            range.moveToElementText(text);
            range.select();
        } else if (window.getSelection) {
            selection = window.getSelection();
            range = document.createRange();
            range.selectNodeContents(text);
            selection.removeAllRanges();
            selection.addRange(range);
        }
        try {
            var successful = document.execCommand('copy');
            var msg = successful ? 'successful' : 'unsuccessful';
            if (msg = 'successful') {
                jQuery('.ShortCodeHint').html('Now your shortcode is copied to clipboard and you can put it on any wordpress page where you want show this lisitngs.');
                jQuery('.ShortCodeHint').fadeIn();
                jQuery('.ShortCodeHint').delay(10000).fadeOut("slow");

            }
            console.log('Copying text command was ' + msg);
        } catch (err) {
            console.log('Oops, unable to copy');
        }
    }
    jQuery(document).ready(function () {

        jQuery('.short').click(function () {
            SelectText('shortCodeSelect')
        });
        seorets.startForm(jQuery('.sr-formsection'), function (root) {
            var fieldsSet = [];
            var tempField = [];
            var showFields = [];
            var timeS = 0;

            root.find('#property-type').change(function () {
                root.attr("srtype", jQuery(this).val());
                jQuery('.short').html('[sr-listings type="' + jQuery('#property-type').val() + '"]');
                updateCounter();
                updShortcode();
                jQuery('#select_values').html();

            });
            jQuery('#showListings').click(function () {
                window.open(
                    "<? echo get_bloginfo('url') ?>/sr-search?" + encodeURIComponent(Base64.encode(JSON.stringify(seorets.getFormRequest(root)))),
                    '_blank' // <- This is what makes it open in a new window.
                );
//                window.location = "<?// echo get_bloginfo('url') ?>///sr-search?" + encodeURIComponent(Base64.encode(JSON.stringify(seorets.getFormRequest(root))));

            });
            jQuery('.short').html('[sr-listings type="' + jQuery('#property-type').val() + '"]');
            jQuery.ajax({
                url: '<?php bloginfo('url') ?>/sr-ajax?action=getFields',
                type: 'post',
                data: {
                    type: jQuery('#property-type').val()
                },
                success: function (response) {
                    jQuery("#select_fields").html(' ');
                    i = 0;
                    console.log(response.length);
                    for (i; i <= response.length - 1; i++) {
                        jQuery("<option data-field-name='" + response[i] + "' id='" + response[i] + "-s'>" + response[i] + "</option>").appendTo("#select_fields");
                    }
                    updValues();
                    tempField.push(jQuery("#select_fields").val());

                }
            });
            function updValues() {
                var valS = jQuery("#" + jQuery('#select_fields').val()).val();
                var selVal = [];
                if (valS) {
                    for (var r = 0; r <= valS.length; r++) {
                        selVal.push(valS[r]);
                    }
                }
                jQuery.ajax({
                    url: '<?php bloginfo('url') ?>/sr-ajax?action=getFieldsValue',
                    type: 'post',
                    data: {
                        type: jQuery('#property-type').val(),
                        fields: jQuery('#select_fields').val()
                    },
                    success: function (response) {
                        console.log(response);
                        if (response == '') {
                            jQuery("#select_values").fadeOut();
                            jQuery(".hide").fadeIn();
                        } else {
                            jQuery("#select_values").fadeIn();
                            jQuery(".hide").fadeOut();
                        }

                        jQuery("#select_values").html(' ');
                        var i = 0;
                        console.log(response.length);
                        for (i; i <= response.length - 1; i++) {
                            var strOp = response[i].toLowerCase();
                            strOp = strOp.replace(/\s+/g, '');
                            if (selVal.indexOf(response[i]) > -1) {
                                jQuery("<option selected id='" + strOp + "-ms'>" + response[i] + "</option>").appendTo("#select_values");
                            } else {
                                jQuery("<option id='" + strOp + "-ms'>" + response[i] + "</option>").appendTo("#select_values");
                            }
                        }
                    }
                });
            }

            jQuery('#property-type').change(function () {
                jQuery('#select_values').html(' ');
                if (jQuery('#select_fields').find('option:selected')) {
                    var selectId = jQuery('#select_fields').find('option:selected').attr('data-field-name');
                }

                if (jQuery('#property-type').val() != '') {
                    jQuery.ajax({
                        url: '<?php bloginfo('url') ?>/sr-ajax?action=getFields',
                        type: 'post',
                        data: {
                            type: jQuery('#property-type').val()
                        },
                        success: function (response) {
                            jQuery("#select_fields").html(' ');
                            i = 0;
                            console.log(response.length);
                            for (i; i <= response.length - 1; i++) {
                                if (selectId && response[i] == selectId) {
                                    jQuery("<option selected data-field-name='" + response[i] + "' id='" + response[i] + "-s'>" + response[i] + "</option>").appendTo("#select_fields");
                                    updValues();
                                } else {
                                    jQuery("<option data-field-name='" + response[i] + "' id='" + response[i] + "-s'>" + response[i] + "</option>").appendTo("#select_fields");
                                }
                            }
                        }
                    });


                }
            });

            jQuery('#select_fields').change(function () {
                timeS = 0;
                updValues();
                updateCounter();

            });


            var showsFields = function showsFields(i) {
                var ind = showFields.indexOf(i);
                var val = jQuery('#' + i).val();
                var valLength = val.length;
                if (ind < 0) {
                    var obg = {};
                    obg[jQuery("#select_fields").val()] = 0;
                    showFields.push(jQuery("#select_fields").val());
                    console.log(i);
                    var values = jQuery('#' + i).val();
                    var htm = '';
                    for (var t = 0; t <= values.length - 1; t++) {
                        var str_t = values[t].toLowerCase();
                        str_t = str_t.replace(/\s+/g, '');
                        str_t = str_t.replace(/[^a-zA-Z0-9]/g, '');
                        htm = htm + "<div id='" + str_t + "-d' class='value'>" + values[t] + "<span data-option-val='" + str_t + "' data-field-val='" + i + "' class='clickBooton'>X</span></div>"
                    }
                    var fieldsHtml =
                        "<div class='showAllFields' id='" + jQuery("#select_fields").val() + "-af'><div class='col-4-left'>" +
                        "<label for= ''>" + jQuery("#select_fields").val() + "  <span data-filed-value='" + jQuery("#select_fields").val() + "' class='deleteFiled'>(Remove)</span>: </label></div><div class='col-6-left'>" +
                        "<div class='valueLists' id='" + jQuery("#select_fields").val() + "-v'>" + htm +
                        "</div></div></div>";
                    jQuery(fieldsHtml).appendTo('#fieldsList');
                } else {
                    var values = jQuery('#' + i).val();
                    var htm = '';
                    for (var t = 0; t <= values.length - 1; t++) {
                        var str_t = values[t].toLowerCase();
                        str_t = str_t.replace(/\s+/g, '');
                        str_t = str_t.replace(/[^a-zA-Z0-9]/g, '');
                        htm = htm + "<div id='" + str_t + "-d' class='value'>" + values[t] + "<span data-option-val='" + str_t + "' data-field-val='" + i + "' class='clickBooton'>X</span></div>"
                    }
                    jQuery("#" + jQuery("#select_fields").val() + "-v").html(htm);
                }
            };

            function updShortcode() {
                var htm = '';
                if (fieldsSet.length >= 1) {
                    for (var r = 0; r <= fieldsSet.length - 1; r++) {
                        if (root.find(jQuery("#" + fieldsSet[r]))['length'] != 0) {
                            if (jQuery("#" + fieldsSet[r]).val() != null) {
                                if (fieldsSet[r] == 'price1' || fieldsSet[r] == 'price2') {
                                    htm = ' ' + htm + fieldsSet[r] + '="' + jQuery("#" + fieldsSet[r]).attr('data-operator') + jQuery("#" + fieldsSet[r]).val() + '" ';
                                } else {
                                    htm = ' ' + htm + fieldsSet[r] + '="' + jQuery("#" + fieldsSet[r]).val() + '" ';
                                }
                            }
                            else {
                                htm = ' ' + htm + fieldsSet[r] + '="" ';
                            }
                        }
                    }
                    jQuery('.short').html('[sr-listings type="' + jQuery('#property-type').val() + '"' + htm + ']');
                } else {
                    jQuery('.short').html('[sr-listings type="' + jQuery('#property-type').val() + '"' + htm + ']');

                }
            }

            jQuery('#add').click(function () {
                var sol = jQuery('#' + jQuery("#select_fields").val()).val();
                var ind = fieldsSet.indexOf(jQuery("#select_fields").val());
                if (sol.length && ind < 0) {
                    fieldsSet.push(jQuery("#select_fields").val());
                }
                var tem = tempField.indexOf(jQuery("#select_fields").val());
                if (tem > -1) {
                    tempField.splice(tem, 1);
                }
                var htm = '';
                jQuery('#add').val('Change');
                updShortcode();
                timeS = 0;
            });
            jQuery('#select_values').change(function () {
                if (timeS != 0 || fieldsSet.indexOf(jQuery("#select_fields").val()) >= 0) {
                    var str_i = jQuery("#select_values").val();
//                    alert(str_i);
                    jQuery("#" + jQuery("#select_fields").val()).html('');
                    if (str_i.length >= 1) {
                        jQuery("#" + jQuery("#select_fields").val()).html('');
                        for (var o = 0; o <= str_i.length - 1; o++) {
                            var str_l = str_i[o].toLowerCase();
                            str_l = str_l.replace(/\s+/g, '');
                            str_l = str_l.replace(/[^a-zA-Z0-9]/g, '');
                            jQuery('<option id="' + str_l + '-o" selected value="' + str_i[o] + '">' + str_i[o] + '</option>').appendTo('#' + jQuery("#select_fields").val());
                        }
                    } else {
                        jQuery('<option selected value="' + jQuery("#select_values").val() + '">' + jQuery("#select_values").val() + '</option>').appendTo('#' + jQuery("#select_fields").val());
                    }
                    updShortcode();
                    showsFields(jQuery("#select_fields").val());
                } else {
                    timeS++;
                    jQuery('<select id="' + jQuery("#select_fields").val() + '" class="sr-formelement" sroperator="=" multiple srfield="' + jQuery("#select_fields").val() + '"></select>').appendTo('.selectField');
                    var str = jQuery("#select_values").val();
//                    alert(str);
                    if (str.length >= 1) {
                        jQuery("#" + jQuery("#select_fields").val()).html('');
                        for (var i = 0; i <= str.length - 1; i++) {
                            var str_e = str[i].toLowerCase();
                            str_e = str_e.replace(/\s+/g, '');
                            str_e = str_e.replace(/[^a-zA-Z0-9]/g, '');
                            jQuery('<option id="' + str_e + '-o" selected value="' + str[i] + '">' + str[i] + '</option>').appendTo('#' + jQuery("#select_fields").val());
                        }
                    } else {
                        jQuery('<option selected value="' + jQuery("#select_values").val() + '">' + jQuery("#select_values").val() + '</option>').appendTo('#' + jQuery("#select_fields").val());
                    }
                    var sol = jQuery('#' + jQuery("#select_fields").val()).val();
                    var ind = fieldsSet.indexOf(jQuery("#select_fields").val());
                    if (sol.length && ind < 0) {
                        fieldsSet.push(jQuery("#select_fields").val());
                    }
                    updShortcode();
                    showsFields(jQuery("#select_fields").val());

                }
                updateCounter();

            });
            var updateCounter = function updateCounter() {
//                console.log(seorets.getFormRequest(root));
                jQuery.ajax({
                    type: "GET",
                    dataType: 'json',
                    url: "/sr-ajax?action=get-listings-amount&conditions=" + encodeURIComponent(Base64.encode(JSON.stringify(seorets.getFormRequest(root)))),
                    success: function (response) {
                        jQuery('.counter').html(response.mes.count);
                        console.log(response);
                    }
                });
            };
            jQuery.ajax({
                type: "GET",
                dataType: 'json',
                url: "<?php bloginfo('url') ?>/sr-ajax?action=get-listings-amount&conditions=" + encodeURIComponent(Base64.encode(JSON.stringify(seorets.getFormRequest(root)))),
                success: function (response) {
                    console.log(response);
                    jQuery('.counter').html(response.mes.count);
                }
            });
            jQuery('#fieldsList').on('click', 'span.deleteFiled', function () {
                var c = jQuery(this).attr('data-filed-value');
                jQuery('#' + c).remove();
                jQuery('#' + c + '-af').remove();
                var tem = fieldsSet.indexOf(c);
                if (tem > -1) {
                    fieldsSet.splice(tem, 1);
                    showFields.splice(tem, 1);
                }
                timeS = 0;

                updateCounter();
                updShortcode();
                updValues();
                if (c == jQuery('#select_fields').val()) {
                    jQuery('#add').html('Add Field');
                }
            });
            jQuery('#fieldsList').on('click', 'span.clickBooton', function () {
                var c = jQuery(this).attr('data-field-val');
                var i = jQuery(this).attr('data-option-val');

                jQuery('#' + c).find(jQuery('#' + i + '-o')).remove();
                jQuery('#' + c + '-v').find(jQuery('#' + i + '-d')).remove();
                updateCounter();
                updShortcode();
                updValues();
            });
            jQuery('.priceField').keyup(function () {
                updateCounter();
            });
            jQuery('.order').change(function () {
                if (fieldsSet.indexOf(jQuery(this).attr('id')) >= 0) {
                    console.log('YEs');
                    if (jQuery(this).val() == "none") {
                        var tems = fieldsSet.indexOf(jQuery(this).attr('id'));
                        if (tems > -1) {
                            fieldsSet.splice(tems, 1);
                            console.log(fieldsSet);
                            updShortcode();
                        }
                    }
                } else {
                    if (jQuery(this).val() != "none") {
                        console.log('no');
                        fieldsSet.push(jQuery(this).attr('id'));
                    }
                }
                updShortcode();

            });
            jQuery('.perpage').change(function () {
                if (fieldsSet.indexOf(jQuery(this).attr('id')) >= 0) {
                    console.log('YEs');
                    if (jQuery(this).val() == "10") {
                        var tems = fieldsSet.indexOf(jQuery(this).attr('id'));
                        if (tems > -1) {
                            fieldsSet.splice(tems, 1);
                            console.log(fieldsSet);
                            updShortcode();
                        }
                    }
                } else {
                    if (jQuery(this).val() != "10") {
                        console.log('no');
                        fieldsSet.push(jQuery(this).attr('id'));
                    }
                }
                updShortcode();
            });
            jQuery('.onlymylistings').change(function () {
                if (fieldsSet.indexOf(jQuery(this).attr('id')) >= 0) {
                    if (jQuery(this).is(':checked')) {
                        console.log('YEs');
                    } else {
                        var temps = fieldsSet.indexOf(jQuery(this).attr('id'));
                        if (temps > -1) {
                            fieldsSet.splice(temps, 1);
                            updShortcode();
                        }
                    }
                } else {
                    if (jQuery(this).is(':checked')) {
                        fieldsSet.push(jQuery(this).attr('id'));
                    }
                    console.log('no');
                }
                updShortcode();
            });
            jQuery('.refine').change(function () {
                if (fieldsSet.indexOf(jQuery(this).attr('id')) >= 0) {
                    if (jQuery(this).is(':checked')) {
                        console.log('YEs');
                    } else {
                        var temps = fieldsSet.indexOf(jQuery(this).attr('id'));
                        if (temps > -1) {
                            fieldsSet.splice(temps, 1);
                            updShortcode();
                        }
                    }
                } else {
                    if (jQuery(this).is(':checked')) {
                        fieldsSet.push(jQuery(this).attr('id'));
                    }
                    console.log('no');
                }
                updShortcode();
            });
            jQuery('.priceField').focusout(function () {
                updateCounter();
                if (fieldsSet.indexOf(jQuery(this).attr('id')) >= 0) {
                    console.log('YEs');
                    if (jQuery(this).val() == "") {
                        var tems = fieldsSet.indexOf(jQuery(this).attr('id'));
                        if (tems > -1) {
                            fieldsSet.splice(tems, 1);
                            console.log(fieldsSet);
                            updShortcode();
                        }
                    }
                } else {
                    if (jQuery(this).val() != "") {
                        console.log('no');
                        fieldsSet.push(jQuery(this).attr('id'));
                    }
                }
                updShortcode();
            });
        });
        jQuery('body').on('click', 'a.deleteShortcode', function (e) {
            e.preventDefault();
            jQuery.ajax({
                url: '<?php bloginfo('url') ?>/sr-ajax?action=deleteShortcode',
                type: 'post',
                data: {
                    shortcode: jQuery(this).attr('data-shortcode')
                },
                success: function (response) {
                    console.log(response);
                    var htmResponse = '';
                    for (var i = 0; i <= response.length - 1; i++) {
                        htmResponse = htmResponse + "<div class='row listscode'><div class='col-6-left'>" + response[i]
                            + "</div><div class='col-3-left'><a data-shortcode='" + response[i] + "' class='deleteShortcode' href=''>Delete</a></div></div>";
                    }
                    jQuery('.shortList').html(htmResponse);

                }
            });
        });
        jQuery('#showAllShortcode').click(function (e) {
            e.preventDefault();
            jQuery.ajax({
                url: '<?php bloginfo('url') ?>/sr-ajax?action=getAllShortcode',
                type: 'post',
                data: {
                    type: 'all'
                },
                success: function (response) {
                    console.log(response);
                    var htmResponse = '';
                    if (response != null) {
                        for (var i = 0; i <= response.length - 1; i++) {
                            htmResponse = htmResponse + "<div class='shortList'><div class='row listscode'><div class='col-6-left'>" + response[i]
                                + "</div><div class='col-3-left'><a data-shortcode='" + response[i] + "' class='deleteShortcode' href=''>Delete</a></div></div></div>";
                        }
                        var htm = '<div id="sr-popup2" class="zoom-anim-dialog">' + htmResponse + '</div>';
                        jQuery.magnificPopup.open({
                            items: {
                                src: htm,
                                type: 'inline'
                            },
                            fixedContentPos: false,
                            fixedBgPos: true,

                            overflowY: 'auto',

                            closeBtnInside: true,
                            preloader: false,

                            midClick: true,
                            closeOnBgClick: false,
                            removalDelay: 300,
                            mainClass: 'my-mfp-zoom-in'
                        });
                    }
                }
            });
        });
        jQuery('#saveShortcode').click(function (e) {
            e.preventDefault();
            console.log(jQuery('#shortCodeSelect').html());
            jQuery.ajax({
                url: '<?php bloginfo('url') ?>/sr-ajax?action=saveShortcode',
                type: 'post',
                data: {
                    shortcode: jQuery('#shortCodeSelect').html()
                },
                success: function (response) {
                    console.log(response);
                    jQuery('.ShortCodeHint').html(response);
                    jQuery('.ShortCodeHint').fadeIn();
                    jQuery('.ShortCodeHint').delay(800).fadeOut("slow");
                }
            });
        });

    });
</script>

<div class="wrap">
    <div id="icon-tools" class="icon32"></div>
    <h2><?php echo $plugin_title ?> :: Shortcode Generator<sup>
            <small><i>(beta v-0.1.4)</i></small>
        </sup></h2>


    <div class="sr-sh_generator sr-formsection" srtype="res" sroperator="AND">
        <div class="row">
            <div class="selectType col-2">
                <label for="">
                    Select a type:
                </label>
                <select id="property-type" class="form-control"
                        name="type">
                    <?php
                    $n = 0;
                    foreach ($sr->metadata as $key => $val) {
                        if ($sr->is_type_hidden($key)) {
                            continue;
                        }
                        if (($key == 'res') || ($key == 'cre')) {
                            echo "<option selected  value='$key' /> " . (isset($val->pretty_name) ? $val->pretty_name : $key) . "</option>";
                        } else {
                            echo "<option   value='$key' /> " . (isset($val->pretty_name) ? $val->pretty_name : $key) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="selectField">
            <div class="col-2-left">
                <!--                <div class="addButton">-->
                <!--                    <input type="submit" id="add" class="button-primary" value="Add Field"-->
                <!--                           style="margin-top: 20px;"/>-->
                <!--                </div>-->
                <div class="select_fields__DIV">
                    <label for="">Select fields:</label>
                    <select name="" id="select_fields" class="form-control"></select>
                </div>
            </div>
            <div class="col-2-left">
                <div class="multiplySelectValues col-6-left">
                    <label for="">Select possible Value</label>
                    <select name="" id="select_values" class="form-control" multiple></select>
                    <input type="text" class="hide form-control" id="value">
                </div>
                <div class="counter_div col-3-left">
                    <label for="">Number of listings:</label>

                    <div class="counter"></div>
                    <input type="submit" id="showListings" class="button-primary" value="Show Listings"
                           style="margin-top: 5px;"/>
                </div>
            </div>

        </div>
        <div style="margin-top: 20px" class="row">
            <div class="col-2-left">
                <div class="row">
                    <div class="col-3-left"><label for="">Price Range:</label></div>
                    <div class="col-3-left">
                        <input type="text" id="price1" name="f89d5" class="sr-formelement priceField form-control"
                               placeholder="Min:" srfield="price" data-operator="&gt;:"
                               sroperator="&gt;=" srtype="numeric"/>
                    </div>
                    <div class="col-3-left">
                        <input type="text" id="price2" name="pa3jv" class="sr-formelement priceField form-control"
                               placeholder="Max:" srfield="price" data-operator="&lt;:"
                               sroperator="&lt;=" srtype="numeric"/>
                    </div>
                </div>
            </div>
            <div class="col-2-left">
                <div class="row">
                    <div class="col-3-left"><label for="">Display order:</label></div>
                    <div class="col-2-left">
                        <select class="form-control order" name="" id="order">
                            <option selected value="none">None</option>
                            <option value="price:ASC">Price, lowest first</option>
                            <option value="price:DESC">Price, highest</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 20px">
            <div class="col-3-left">
                <div class="row">
                    <div class="col-6-left"><label for="">Listings to display per page:</label></div>
                    <div class="col-3-left">
                        <input type="number" id="perpage" value="10" class="form-control perpage">
                    </div>
                </div>

            </div>
            <div class="col-3-left">
                <div class="row">
                    <div class="col-6-left"><label for="onlymylistings">Only show my listings (this is set under the
                            prioritization tab in the SEO RETS menu):</label></div>
                    <div class="col-3-left">
                        <input type="checkbox" data-val="yes" value="yes" class="onlymylistings" id="onlymylistings">
                    </div>
                </div>
            </div>
            <div class="col-3-left">
                <div class="row">
                    <div class="col-6-left"><label for="refine">Show refine search:</label></div>
                    <div class="col-3-left">
                        <input type="checkbox" class="refine" value="yes" id="refine">
                    </div>
                </div>
            </div>
        </div>
        <div id="fieldsList" style="margin-top: 20px">
            <div class="shortCode col-1" style="margin-bottom: 20px">
                <div class="row">
                    <div style="padding: 5px 0" class="col-2"><label style="float: left">Just click on shortCode to copy
                            it.</label>
                        <label style="float: right;margin-left: 10px" for=""><a id="saveShortcode" href="#save">Save
                                Shortcode</a></label>
                        <label style="float: right" for=""><a id="showAllShortcode" href="#showAllShortcode">Show Saved
                                Shortcodes</a></label>
                    </div>
                </div>
                <div class="row">
                    <div id="shortCodeSelect" class="short col-2-left"></div>
                    <div class="ShortCodeHint col-2-left"></div>
                </div>
            </div>

        </div>

    </div>
</div>

