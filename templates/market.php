
<?php
wp_enqueue_style('sr_templates_market',$this->css_resources_dir.'templates/market.css');
wp_print_styles(array('sr_templates_market'));
?>


<div class="sr-market">
	<h1>Market Research Data</h1>

	<div class="marketButtons">
		<label>(Select Tab)</label>
		<?php if ( $res->count > 0 ) { ?>
			<input type="button" value="Cities" class="mButton">
		<?php }
		if ( $subdivisions->count > 0 ) { ?>
			<input type="button" value="Subdivisions" class="mButton">
		<?php }
		if ( $condos->count > 0 ) { ?>
			<input type="button" value="Condos" class="mButton">
		<?php } ?>
	</div>
	<div class="marketMain" id="marketMain">
		<?php if ( $res->count > 0 ) { ?>
			<div class="mCities">
				<h4 class="marketIntrText">Here are current listing stats for Cities.</h4>
				<table border="1">
					<tr>
						<th>
							City
						</th>
						<th class="priceWidth">
							Min Price
						</th>
						<th class="priceWidth">
							Avg Price
						</th>
						<th class="priceWidth">
							Max Price
						</th>
						<th class="sqftWidth">
							Min SQFT
						</th>
						<th class="sqftWidth">
							Avg SQFT
						</th>
						<th class="sqftWidth">
							Max SQFT
						</th>
						<th class="yearWidth">
							Min Built Year
						</th>
						<th class="yearWidth">
							Avg Built Year
						</th>
						<th class="yearWidth">
							Max Built Year
						</th>
						<th class="sqftWidth">
							Total Amount
						</th>
					</tr>
					<?php
					foreach ( $res->result as $city ) {
						if ( empty( $city->maxPrice ) || $city->maxPrice < 1 ) {
							$city->maxPrice = '';
						}
						if ( empty( $city->minPrice ) || $city->minPrice < 1 ) {
							$city->minPrice = '';
						}
						if ( empty( $city->minSqft ) || $city->minSqft < 1 ) {
							$city->minSqft = '';
						}
						if ( empty( $city->maxSqft ) || $city->maxSqft < 1 ) {
							$city->maxSqft = '';
						}
						if ( empty( $city->minBuiltYear ) || $city->minBuiltYear < 1 ) {
							$city->minBuiltYear = '';
						}
						if ( empty( $city->maxBuiltYear ) || $city->maxBuiltYear < 1 ) {
							$city->maxBuiltYear = '';
						}
						if ( ! empty( $city->totalBuiltYear ) ) {
							$averageYear = round( $city->totalBuiltYear / $city->total );
							if ( $averageYear > $city->maxBuiltYear || $averageYear < $city->minBuiltYear ) {
								$averageYear = round( ( $city->maxBuiltYear + $city->minBuiltYear ) / 2 );
							}
						} else {
							$averageYear = '';
						}
						if ( ! empty( $city->totalSqft ) ) {
							$averageSqft = round( $city->totalSqft / $city->total );
							if ( $averageSqft > $city->maxSqft || $averageSqft < $city->minSqft ) {
								$averageSqft = round( ( $city->maxSqft + $city->minSqft ) / 2 );
							}
						} else {
							$averageSqft = '';
						}
						if ( ! empty( $city->totalPrice ) ) {
							$averagePrice = round( $city->totalPrice / $city->total );
							if ( $averagePrice > $city->maxPrice || $averagePrice < $city->minPrice ) {
								$averagePrice = round( ( $city->maxPrice + $city->minPrice ) / 2 );
							}
						} else {
							$averagePrice = '';
						}
						echo '<tr><td>' . $city->city . '</td><td>$' . number_format( $city->minPrice ) . '</td><td>$' . number_format( $averagePrice ) . '</td><td>$' . number_format( $city->maxPrice ) . '</td><td>' . number_format( (int) $city->minSqft ) . '</td><td>' . number_format( $averageSqft ) . '</td><td>' . number_format( (int) $city->maxSqft ) . '</td><td>' . $city->minBuiltYear . '</td><td>' . $averageYear . '</td><td>' . $city->maxBuiltYear . '</td><td>' . number_format( $city->total ) . '</td><tr>';
					} ?>
				</table>
			</div>
		<?php }
		if ( $condos->count > 0 ) { ?>
			<div class="mCondos srHide">
				<h4 class="marketIntrText">Here are current listing stats for Condos.</h4>
				<table border="1">
					<tr>
						<th>
							Condo
						</th>
						<th class="priceWidth">
							min Price
						</th>
						<th class="priceWidth">
							Avg Price
						</th>
						<th class="priceWidth">
							Max Price
						</th>
						<th class="sqftWidth">
							Min SQFT
						</th>
						<th class="sqftWidth">
							Avg SQFT
						</th>
						<th class="sqftWidth">
							Max SQFT
						</th>
						<th class="sqftWidth">
							Min Built Year
						</th>
						<th class="sqftWidth">
							Avg Built Year
						</th>
						<th class="sqftWidth">
							Max Built Year
						</th>
						<th class="sqftWidth">
							Total Amount
						</th>
					</tr>
					<?php
					foreach ( $condos->result as $city ) {
						if ( empty( $city->maxPrice ) || $city->maxPrice < 1 ) {
							$city->maxPrice = '';
						}
						if ( empty( $city->minPrice ) || $city->minPrice < 1 ) {
							$city->minPrice = '';
						}
						if ( empty( $city->minSqft ) || $city->minSqft < 1 ) {
							$city->minSqft = '';
						}
						if ( empty( $city->maxSqft ) || $city->maxSqft < 1 ) {
							$city->maxSqft = '';
						}
						if ( empty( $city->minBuiltYear ) || $city->minBuiltYear < 1 ) {
							$city->minBuiltYear = '';
						}
						if ( empty( $city->maxBuiltYear ) || $city->maxBuiltYear < 1 ) {
							$city->maxBuiltYear = '';
						}
						if ( ! empty( $city->totalBuiltYear ) ) {
							$averageYear = round( $city->totalBuiltYear / $city->total );
							if ( $averageYear > $city->maxBuiltYear || $averageYear < $city->minBuiltYear ) {
								$averageYear = round( ( $city->maxBuiltYear + $city->minBuiltYear ) / 2 );
							}
						} else {
							$averageYear = '';
						}
						if ( ! empty( $city->totalSqft ) ) {
							$averageSqft = round( $city->totalSqft / $city->total );
							if ( $averageSqft > $city->maxSqft || $averageSqft < $city->minSqft ) {
								$averageSqft = round( ( $city->maxSqft + $city->minSqft ) / 2 );
							}
						} else {
							$averageSqft = '';
						}
						if ( ! empty( $city->totalPrice ) ) {
							$averagePrice = round( $city->totalPrice / $city->total );
							if ( $averagePrice > $city->maxPrice || $averagePrice < $city->minPrice ) {
								$averagePrice = round( ( $city->maxPrice + $city->minPrice ) / 2 );
							}
						} else {
							$averagePrice = '';
						}
						echo '<tr><td>' . $city->$condosKey . '</td><td>$' . number_format( $city->minPrice ) . '</td><td>$' . number_format( $averagePrice ) . '</td><td>$' . number_format( $city->maxPrice ) . '</td><td>' . number_format( (int) $city->minSqft ) . '</td><td>' . number_format( $averageSqft ) . '</td><td>' . number_format( (int) $city->maxSqft ) . '</td><td>' . $city->minBuiltYear . '</td><td>' . $averageYear . '</td><td>' . $city->maxBuiltYear . '</td><td>' . number_format( $city->total ) . '</td><tr>';
					} ?>
				</table>
			</div>
		<?php }
		if ( $subdivisions->count > 0 ) { ?>
			<div class="mSubdivisions srHide">
				<h4 class="marketIntrText">Here are current listing stats for Subdivisions.</h4>
				<table border="1">
					<tr>
						<th class="subdTitle">
							Subdivision
						</th>
						<th class="priceWidth">
							Min Price
						</th>
						<th class="priceWidth">
							Avg Price
						</th>
						<th class="priceWidth">
							Max Price
						</th>
						<th class="sqftWidth">
							Min SQFT
						</th>
						<th class="sqftWidth">
							Avg SQFT
						</th>
						<th class="sqftWidth">
							Max SQFT
						</th>
						<th class="sqftWidth">
							Min Built Year
						</th>
						<th class="sqftWidth">
							Avg Built Year
						</th>
						<th class="sqftWidth">
							Max Built Year
						</th>
						<th class="sqftWidth">
							Total Amount
						</th>
					</tr>
					<?php
					foreach ( $subdivisions->result as $city ) {
						if ( empty( $city->maxPrice ) || $city->maxPrice < 1 ) {
							$city->maxPrice = '';
						}
						if ( empty( $city->minPrice ) || $city->minPrice < 1 ) {
							$city->minPrice = '';
						}
						if ( empty( $city->minSqft ) || $city->minSqft < 1 ) {
							$city->minSqft = '';
						}
						if ( empty( $city->maxSqft ) || $city->maxSqft < 1 ) {
							$city->maxSqft = '';
						}
						if ( empty( $city->minBuiltYear ) || $city->minBuiltYear < 1 ) {
							$city->minBuiltYear = '';
						}
						if ( empty( $city->maxBuiltYear ) || $city->maxBuiltYear < 1 ) {
							$city->maxBuiltYear = '';
						}
						if ( ! empty( $city->totalBuiltYear ) ) {
							$averageYear = round( $city->totalBuiltYear / $city->total );
							if ( $averageYear > $city->maxBuiltYear || $averageYear < $city->minBuiltYear ) {
								$averageYear = round( ( $city->maxBuiltYear + $city->minBuiltYear ) / 2 );
							}
						} else {
							$averageYear = '';
						}
						if ( ! empty( $city->totalSqft ) ) {
							$averageSqft = round( $city->totalSqft / $city->total );
							if ( $averageSqft > $city->maxSqft || $averageSqft < $city->minSqft ) {
								$averageSqft = round( ( $city->maxSqft + $city->minSqft ) / 2 );
							}
						} else {
							$averageSqft = '';
						}
						if ( ! empty( $city->totalPrice ) ) {
							$averagePrice = round( $city->totalPrice / $city->total );
							if ( $averagePrice > $city->maxPrice || $averagePrice < $city->minPrice ) {
								$averagePrice = round( ( $city->maxPrice + $city->minPrice ) / 2 );
							}
						} else {
							$averagePrice = '';
						}
						echo '<tr><td>' . $city->subdivision . '</td><td>$' . number_format( $city->minPrice ) . '</td><td>$' . number_format( $averagePrice ) . '</td><td>$' . number_format( $city->maxPrice ) . '</td><td>' . number_format( (int) $city->minSqft ) . '</td><td>' . number_format( $averageSqft ) . '</td><td>' . number_format( (int) $city->maxSqft ) . '</td><td>' . $city->minBuiltYear . '</td><td>' . $averageYear . '</td><td>' . $city->maxBuiltYear . '</td><td>' . number_format( $city->total ) . '</td><tr>';
					} ?>
				</table>
			</div>
		<?php } ?>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function () {
		jQuery('.marketButtons input').click(function () {
			var object = jQuery(this).val();
			jQuery('.marketMain div').hide();
			jQuery('.m' + object).show();

		})
	});
</script>
<?php
?>