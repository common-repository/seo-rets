<?php
global $wp_version, $tinymce_version;

$localJsUri = get_option("siteurl") . "/" . WPINC . "/js/";

$sr = $this;

$types = array('area' => 'area', 'zipcode' => 'zip', 'cities' => 'city');
$cities = array();
$area = array();
$zipcode = array();

foreach ( get_object_vars($sr->metadata) as $object ) {
	$fields = $object->fields;
	foreach ($types as $var => $metavar) {
		if (isset($fields->{$metavar}->values) && is_array($fields->{$metavar}->values)) {
			${$var} = array_merge(${$var}, $fields->{$metavar}->values);
		}
	}
}

sort($cities);
sort($area);
sort($zipcode);

$cities = array_unique($cities);
$area = array_unique($area);
$zipcode = array_unique($zipcode);

?>

<!DOCTYPE html>
<html>
<head>
	<title>SEO RETS: Insert Map</title>

	<?php

	wp_enqueue_script('tiny_mce_popup',$localJsUri.'tinymce/tiny_mce_popup.js');
	wp_print_scripts(array('tiny_mce_popup'));

	wp_enqueue_script('tiny_mce_mctabs',$localJsUri.'tinymce//utils/mctabs.js');
	wp_print_scripts(array('tiny_mce_mctabs'));

	wp_enqueue_script('sr_tinymce-maps-dialog',$sr->tinymce_url.'maps/js/dialog.js',array( 'jquery' ));
	wp_print_scripts(array('sr_tinymce-maps-dialog'));

	wp_enqueue_style('sr_tinymce_maps_dialog',$sr->css_resources_dir.'tinymce/maps_dialog.css');
	wp_print_styles(array('sr_tinymce_maps_dialog'));
	?>

</head>
<body>
	<br />

	<div class="tabs">
		<ul>
			<li id="custom_search_tab" class="current"><span><a href="javascript:void(0);" onclick="srListings.changeTab('quick-search')">MULTIPLE PROPERTIES</a></span></li>
		</ul>
	</div>

	<div class="panel_wrapper">
		<div id="multiple-properties" class="panel current">
			<table style="width: 100%;">
				<tr>
					<th style="width: 80px !important;">Type:</th>
					<td style="width: 220px;">
						<select id="area-type" onchange="updateArea();">
							<option value="city">City</option>
							<option value="area">Area</option>
							<option value="zip">Zip</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Area:</th>
					<td>
						<select id="city-name" size='1'>
						<?php
						foreach ($cities as $city) {
							echo "<option value='$city'>$city</option>";
						}
						?>
						</select>
						<select id="community-name" style="display:none;" size='1'>
						<?php
						foreach ($area as $temparea) {
							echo "<option value='$temparea'>$temparea</option>";
						}
						?>
						</select>
						<select id="zip-name" style="display:none;" size='1'>
						<?php
						foreach ($zipcode as $zip) {
							echo "<option value='$zip'>$zip</option>";
						}
						?>
						</select>
						<div style='float:right;'><a href='javascript:void(0);' class='blue' id='multiple-area'>+ Select Multiple</a></div>
					</td>
				</tr>
				<tr>
					<th>Price range:</th>
					<td>
						<input type="text" id="min-price" style="width: 70px;" />
						-
						<input type="text" id="max-price" style="width: 70px;" />
					</td>
				</tr>
				<tr>
					<th>
						Property Types:
					</th>
					<td>
						<div id="property-type-container">
						<?php
						$n = 0;
						foreach ( $sr->metadata as $type ) {
							if ($sr->is_type_hidden($type->system_name)){continue;}
							$type = $type->pretty_name ? $type->pretty_name : $type->system_name;
							
							if ( $n == 0 ) {
								echo "<input type='radio' name='type' id='$type' value='$type' checked /><label for='$type'>$type</label><br />";
							} else {
								echo "<input type='radio' name='type' id='$type' value='$type' /><label for='$type'>$type</label><br />";
							}
							$n++;
						}
						?>
					</div>
					</td>
				</tr>
				<tr>
					<th>Display order</th>
					<td>
						<select id="display-order-column">
							<option value="">None</option>
							<option value="Price|ASC">Price, lowest first</option>
							<option value="Price|DESC">Price, highest first</option>
						</select>
					</td>
				</tr>
			</table>
							<table width='100%'>
							<tr>
				<th><label for="number-to-display">Limit properties to</label></th>
				<td width='50px'><input type="text" id="number-to-display" /><br /></td>
				</tr>
				<!--<tr><td colspan="2">Recommendation: Limit properties to no more than 100, otherwise google map limitations may cause pages to be slow to load.</td></tr>-->
				<tr>
				<th>Only show my listings <span style='font-weight:normal'>(this is set under the prioritization section in the SEO RETS plugin)</span></th>
				<td><input id="only-mine" type='checkbox'></td>
				</tr>
				</table>
				
				
				
				<div style='clear:both'></div>
		</div>
	</div>
	
	<div class="mceActionPanel">
		<div style="float: right">
			<input type="image" src="<?php echo $this->tinymce_url.$method?>/img/insert.png" id="insert" name="insert" value="Insert Map" onclick="srListings.insert();" >
		</div>
	</div>

</body>
</html>

