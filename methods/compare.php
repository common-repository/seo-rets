<?php
session_start();
//error_reporting(E_ALL);
global $seo_rets_plugin;
$sr = $seo_rets_plugin;
//echo "<pre>";
//print_r($_SESSION['compare']);
//echo "</pre>";
$listings = array();
foreach ($_SESSION['compare'] as $key => $l) {
    $request = $sr->api_request('get_listings', array(
        'type' => $l['type'],
        'query' => array(
            'boolopr' => 'AND',
            'conditions' => array(
                array(
                    'field' => 'mls_id',
                    'operator' => '=',
                    'value' => $l['mls']
                )
            )
        ),
        'limit' => array(
            'range' => 1,
            'offset' => 0
        )
    ));
    $listings[$key] = json_decode(json_encode($request), true);
}
$server_name = $sr->feed->server_name;

$photo_dir = "http://img.seorets.com/" . $server_name;

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<!-- Latest compiled and minified CSS -->

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
      integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css"
      integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
        integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
        crossorigin="anonymous"></script>
<style>
    table#compare-listings-view th, table#compare-listings-view td {
        width: 180px;
        color: #434341;
        font-size: 82%;
        text-align: left;
        padding: 4px 5px;
        border-right: 1px solid #E3E3E0;
        border-bottom: 1px solid #E3E3E0;
        cursor: default;
    }

    table#compare-listings-view th {
        text-transform: uppercase;
        font-weight: bold;
        white-space: nowrap;
    }

    table#compare-listings-view tr:hover th, table#compare-listings-view tr:hover td {
        background-color: #546E7A;
        border-bottom: 1px solid #263238;
        font-weight: bold;
        color: #fff;
    }

    /* Address Header */
    table#compare-listings-view tr.address-header th {
        padding: 10px 5px 4px;
        vertical-align: top;
        background: #ECEFF1;
        border-bottom: 1px solid #BBB;
        border-top: 3px solid #37474F;
        color: #000;
    }

    table#compare-listings-view tr.address-header:hover th.address-item {
        font-weight: normal;
        color: #000;
    }

    table#compare-listings-view th.address-item {
        white-space: normal;
        font-weight: normal;
        text-transform: capitalize;
    }

    table#compare-listings-view th.address-item input {
        float: left;
        margin: 0;
        padding: 0;
    }

    table#compare-listings-view th.address-item label {
        display: block;
        margin-left: 18px;
    }

    /* Individual Items */
    tr.compare-mls td, tr.compare-price td, tr.compare-sqft td {
        font-family: Verdana, Arial, Geneva, Helvetica, sans-serif;
    }

    tr.compare-sqft td span {
        margin-left: 3px;
    }

    tr.compare-subdivision td {
        text-transform: capitalize;
    }

    /* Compare Photo */
    tr.compare-photo th {
        vertical-align: top;
    }

    tr.compare-photo td img {
        display: block;
        width: 150px;
        height: 112px;
        overflow: hidden;
        border: 2px solid #DDD;
        padding: 1px;
        background: #999;
        margin: auto;
    }

    /* Compare Features */
    table#compare-listings-view th.compare-feature-header, table#compare-listings-view th.compare-feature-header:hover {
        color: #FFF;
        background-color: #737370;
        border-bottom: 0px none;
    }

    table#compare-listings-view th.compare-feature {
        padding-left: 27px;
        /*background-image: url(../../images/compare-listings-feature-item-indent.gif);*/
        background-position: 0% 50%;
        background-repeat: no-repeat;
    }

    /* Disclaimer */
    div.disclaimer table td {
        font-size: 82%;
    }

    /* Compare Row Hover Effect for IE6 */
    table#compare-listings-view tr.compare-row-hover {
        background-color: #6a675b;
        border-bottom: 1px solid #FF6000;
        font-weight: bold;
        color: #fff;
    }

    /* Compare Column Selected */
    col.compare-column-selected {
        background-color: #ECEFF1;
    }

    .col-left {
        float: left;
        width: 200px;
        margin-left: 15px;
        margin-right: 15px;
        padding: 10px;
    }

    .compare_address, .listings_photo {
        text-align: center;
    }

    .listings_photo img {
        max-height: 150px;
        display: inherit;
    }

    .compare_address {
        min-height: 20px;
    }
</style>

<script>
    jQuery(document).ready(function () {
        jQuery('#removeSelected').click(function () {
            console.log('click');
            var check = new Array;
            jQuery('#compare-listings-view').find(":checked").each(function () {
                check.push(jQuery(this).data('value'));
            });
            function unique(list) {
                var result = [];
                $.each(list, function (i, e) {
                    if ($.inArray(e, result) == -1) result.push(e);
                });
                return result;
            }

            jQuery.ajax({
                url: '<?php bloginfo('url') ?>/sr-ajax?action=removeCompareListings',
                type: 'post',
                data: {
                    type: 'remove',
                    mls: unique(check)
                },
                success: function (response) {
                    console.log(response);
                    var len = jQuery.map(response, function (n, i) {
                        return i;
                    }).length;
                    console.log(len);
                    if (len > 0) {
                        location.reload();
                    } else {
                        window.close();
                    }
                }
            });
            console.log(unique(check));
        });
    });
</script>
<div id="removese" class="row hide">
    <button id="removeSelected">Remove selected</button>
</div>
<table cellpadding="0" cellspacing="0" id="compare-listings-view">
    <thead>
    <tr class="address-header">
        <th>
            <nobr>Address</nobr>
        </th>
        <?php
        foreach ($listings as $key => $l) {
            ?>
            <th class="address-item">
                <input type="checkbox" data-value="<?php echo $key; ?>"
                       id="cbx_<?php echo md5($l['result'][0]['address']); ?>"
                       name="cbx_<?php echo md5($l['result'][0]['address']); ?>"
                       value="<?php echo md5($l['result'][0]['address']); ?>"
                       onclick="_selectEntireColumn(this)"/>
                <label
                    for="<?php echo md5($l['result'][0]['address']); ?>"><?php echo $l['result'][0]['address']; ?></label>
            </th>
        <?php } ?>
    </tr>
    </thead>
    <col></col>
    <?php foreach ($listings as $key => $l) {
        ?>
        <col id="col_<?php echo md5($l['result'][0]['address']); ?>"></col>

    <?php } ?>
    <tbody>
    <tr class="compare-mls" title="MLS Number">
        <th>
            <nobr>MLS Number</nobr>
        </th>
        <?php
        foreach ($listings as $key => $l) {

            ?>
            <td><?php echo $l['result'][0]['mls_id']; ?>&nbsp;</td>
        <?php } ?>
    </tr>
    <tr class="compare-photo">
        <th>
            <nobr>Main Image</nobr>
        </th>
        <?php
        foreach ($listings as $key => $l) {

            ?>
            <td>
                <img
                    src="<?= $photo_dir ?>/<?= $l['result'][0]['seo_url'] ?>-<?= $l['result'][0]['mls_id'] ?>-1.jpg"/>
            </td>
        <?php } ?>

    </tr>

    <tr class="compare-photo-count" title="Additional Photos">
        <th>
            <nobr>Additional Photos</nobr>
        </th>
        <?php
        foreach ($listings as $key => $l) {
            ?>
            <td><?= $l['result'][0]['photos'] ?>&nbsp;</td>
        <?php } ?>
    </tr>
    <tr class="compare-subdivision" title="Subdivsion">
        <th>
            <nobr>Subdivision</nobr>
        </th>
        <?php
        foreach ($listings as $key => $l) {
            ?>
            <td><?= $l['result'][0]['subdivision'] ?>&nbsp;</td>
        <?php } ?>
    </tr>
    <tr class="compare-city" title="City">
        <th>
            <nobr>City</nobr>
        </th>
        <?php
        foreach ($listings as $key => $l) {
            ?>
            <td><?= $l['result'][0]['city'] ?>&nbsp;</td>
        <?php } ?>
    </tr>
    <tr class="compare-county" title="County">
        <th>
            <nobr>County</nobr>
        </th>
        <?php
        foreach ($listings as $key => $l) {
            ?>
            <td><?= $l['result'][0]['county'] ?>&nbsp;</td>
        <?php } ?>
    </tr>
    <tr class="compare-price" title="Price">
        <th>
            <nobr>Price</nobr>
        </th>
        <?php foreach ($listings as $key => $l) {
            ?>
            <td>$ <?= number_format($l['result'][0]['price'], 2); ?>&nbsp;</td>
        <?php } ?>
    </tr>
    <tr class="compare-listing-type" title="Listing Type">
        <th>
            <nobr>Listing Type</nobr>
        </th>
        <?php foreach ($listings as $key => $l) {
            ?>
            <td><?= $l['result'][0]['property_type']; ?>&nbsp;</td>
        <?php } ?>
    </tr>
    <tr class="compare-bedrooms" title="Bedrooms">
        <th>
            <nobr>Bedrooms</nobr>
        </th>
        <?php foreach ($listings as $key => $l) {
            ?>
            <td><?= $l['result'][0]['bedrooms']; ?>&nbsp;</td>
        <?php } ?>
    </tr>
    <tr class="compare-total-baths" title="Total Bathrooms">
        <th>
            <nobr>Total Baths</nobr>
        </th>
        <?php foreach ($listings as $key => $l) {
            ?>
            <td><?= $l['result'][0]['baths_full']; ?>&nbsp;</td>
        <?php } ?>
    </tr>
    <tr class="compare-sqft" title="Estimated Square Feet">
        <th>
            <nobr>SqFt</nobr>
        </th>
        <?php foreach ($listings as $key => $l) {
            ?>
            <td><?= $l['result'][0]['sqft']; ?><span>Sq. Ft.</span>&nbsp;</td>
        <?php } ?>
    </tr>
    <tr class="compare-fireplaces" title="Fireplaces">
        <th>
            <nobr>FirePlaces</nobr>
        </th>
        <?php foreach ($listings as $key => $l) {
            ?>
            <td><?= $l['result'][0]['fireplace']; ?>&nbsp;</td>
        <?php } ?>
    </tr>
    <tr class="compare-garage" title="Garage Spaces">
        <th>
            <nobr>Garage Spaces</nobr>
        </th>
        <?php foreach ($listings as $key => $l) {
            ?>
            <td><?= $l['result'][0]['garageparkingattachedgaragespaces']; ?>&nbsp;</td>
        <?php } ?>
    </tr>
    <tr class="compare-acres" title="Acres">
        <th>
            <nobr>Acreage</nobr>
        </th>
        <?php foreach ($listings as $key => $l) {
            ?>
            <td><?= $l['result'][0]['acreage']; ?>&nbsp;</td>
        <?php } ?>
    </tr>
    <tr class="compare-lot" title="Lot Dimensions">
        <th>
            <nobr>Lot Dimensions</nobr>
        </th>
        <?php foreach ($listings as $key => $l) {
            ?>
            <td><?= $l['result'][0]['lot_dimensions']; ?>&nbsp;</td>
        <?php } ?>
    </tr>
    <tr class="compare-year" title="Year Built">
        <th>
            <nobr>Year Built</nobr>
        </th>
        <?php foreach ($listings as $key => $l) {
            ?>
            <td><?= $l['result'][0]['year_built']; ?>&nbsp;</td>
        <?php } ?>
    </tr>
    <tr class="compare-hoa" title="Home Owner's Association?">
        <th>
            <nobr>Home Owners Assoc.</nobr>
        </th>
        <?php foreach ($listings as $key => $l) {
            ?>
            <td><?= $l['result'][0]['hoa']; ?>&nbsp;</td>
        <?php } ?>
    </tr>
    <tr class="address-header">
        <th>
            <nobr>School</nobr>
        </th>
        <?php foreach ($listings as $key => $l) {
            ?>
            <th></th>
        <?php } ?>
    </tr>
    <tr class="compare-elementary" title="Elementary School">
        <th>
            <nobr>Elementary School</nobr>
        </th>
        <?php foreach ($listings as $key => $l) {
            ?>
            <td><?= $l['result'][0]['elem_school']; ?>&nbsp;</td>
        <?php } ?>
    </tr>
    <tr class="compare-middle" title="Middle School">
        <th>
            <nobr>Middle School</nobr>
        </th>
        <?php foreach ($listings as $key => $l) {
            ?>
            <td><?= $l['result'][0]['middle_school']; ?>&nbsp;</td>
        <?php } ?>
    </tr>
    <tr class="compare-high" title="High School">
        <th>
            <nobr>High School</nobr>
        </th>
        <?php foreach ($listings as $key => $l) {
            ?>
            <td><?= $l['result'][0]['high_school']; ?>&nbsp;</td>
        <?php } ?>
    </tr>
    <tr class="address-header">
        <th>
            <nobr>Features</nobr>
        </th>
        <?php foreach ($listings as $key => $l) {
            ?>
            <th></th>
        <?php } ?>
    </tr>
    <?php
    $features_list = array();
    foreach ($listings as $key => $l) {
        foreach ($l['result'][0]['features'] as $f) {
            $features_list[] = $f;
        }
    }
    $result = array_unique($features_list);
    ?>
    <?php
    foreach ($result as $k => $r) {
        ?>
        <tr>
            <th class="compare-feature"><?php echo $r; ?></th>
            <?php
            foreach ($listings as $key => $l) {
                if (in_array($r, $l['result'][0]['features'])) {
                    ?>
                    <td>Yes</td>
                    <?php
                } else {
                    ?>
                    <td>No</td>
                    <?php
                }
            }
            ?>
        </tr>
        <?php
    }
    ?>
    <tr class="address-header">
        <th>
            <nobr>Address</nobr>
        </th>
        <?php
        foreach ($listings as $key => $l) {
            ?>
            <th class="address-item">
                <input type="checkbox" data-value="<?php echo $key; ?>"
                       id="cbx_<?php echo md5($l['result'][0]['address']); ?>"
                       name="cbx_<?php echo md5($l['result'][0]['address']); ?>"
                       value="<?php echo md5($l['result'][0]['address']); ?>"
                       onclick="_selectEntireColumn(this)"/>
                <label
                    for="<?php echo md5($l['result'][0]['address']); ?>"><?php echo $l['result'][0]['address']; ?></label>
            </th>
        <?php } ?>
    </tr>

    </tbody>
</table>

<script type="text/javascript">
    function addClass(obj, n) {
        if (typeof(obj) !== 'undefined' && obj !== null) {
            obj.className += n;
        }
    }
    function removeClass(obj, n) {
        if (typeof(obj) !== 'undefined' && obj !== null) {
            obj.className = obj.className.replace(n, '');
        }
    }
    function _selectEntireColumn(sender) {
        if (document.getElementById) {
            try {
                var arr = document.getElementsByName(sender.name);
                var col = document.getElementById('col_' + sender.value);
                var rem = document.getElementById('removese');
                if (sender.checked) {
                    for (var i = 0; i < arr.length; i++) {
                        arr[i].checked = true;
                    }
                    removeClass(rem, 'hide');
                    addClass(col, 'compare-column-selected');
                } else if (!sender.checked) {
                    for (var i = 0; i < arr.length; i++) {
                        arr[i].checked = false;
                    }
                    addClass(rem, 'hide');
                    removeClass(col, 'compare-column-selected');
                }
            } catch (err) {
                return;
            }
        }
    }
</script>