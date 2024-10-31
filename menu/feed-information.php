<?php
$sr = $seo_rets_plugin;
$plugin_title = $sr->admin_title;
$plugin_id = $sr->admin_id;
?>



<script type="text/javascript">

jQuery(function($) {
	$(".property-fields h4 a").click(function() {
		$(this).parent().parent().children(".field-values").slideToggle("slow");
	});
});

</script>

<div class="wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2><?php echo $plugin_title ?> :: Feed Information</h2>
	<div class="tool-box">

		<p>Below you will find all of the property types and the fields associated with them. Click the plus button on a field to show the avaliable values.</p>

		<?php foreach ( $sr->metadata as $property_type ): ?>
			<div class="property-type">
				<h2 title="<?php echo $property_type->pretty_name; ?>"><?php echo $property_type->system_name; ?></h2>
				<div class="property-fields">
					<?php foreach ( $property_type->fields as $field ): ?>
						<div>
						<?php if ( $field->type == "enumeration" || $field->type == "array"): ?>
							<h4 title="<?php echo $field->pretty_name; ?>"><?php echo $field->system_name; ?> <a href="javascript:void(0);" title="Show Values">+</a></h4>
							
							<div class="field-values">
								<?php foreach ( $field->values as $value ): ?>
									<p><?php echo $value; ?></p>
								<?php endforeach; ?>
							</div>
						<?php else: ?>
							<h4 title="<?php echo $field->pretty_name; ?>"><?php echo $field->system_name; ?></h4>
						<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>

