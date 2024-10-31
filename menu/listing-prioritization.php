<?php
$sr = $seo_rets_plugin;
$plugin_title = $sr->admin_title;
$plugin_id = $sr->admin_id;
?>
<script type="text/javascript">
var delfunc = function() {
	var p = jQuery(this).parent().parent();
	
	jQuery.ajax({
		url: "<?php echo get_bloginfo('url')?>/sr-ajax?action=delete-id",
		type: 'POST',
		data: {
			id: p.children("td:nth-child(1)").html(),
		}
	});

	p.fadeOut("slow");
};

jQuery(function($) {
	
	var mess = $("#message");
	var checkmess = $("#checkmessage");
	
	$("#add").click(function() {
		$.ajax({
			url: '<?php echo get_bloginfo('url')?>/sr-ajax?action=add-id',
			type: 'POST',
			data: {
				id: $("#id").val(),
				field: $("#field").val()
			},
			success: function(obj) {
				if ( obj.error == 0 ) {
					mess.css("color", "green");
					$('<tr><td>' + $("#id").val() + '</td><td>' + $("#field").val() + '</td><td><a href="javascript:void(0);" style="color:red;font-weight:bold;text-decoration:none;" class="del">X</a></td></tr>').insertBefore("#controlrow").find(".del").click(delfunc);
				} else {
					mess.css("color", "red");
				}
				$("#id").val('');
				mess.text(obj.mes);
				mess.fadeIn("slow").delay(1500).fadeOut("slow");
				
			}
		});
	});
	
	$("#check").click(function() {
		checkmess.fadeOut("slow");
		$.ajax({
			url: "<?php echo get_bloginfo('url')?>/sr-ajax?action=lookup-agent",
			type: 'POST',
			data: {
				id: $("#mlsid").val()
			},
			success: function(obj) {
				if (obj.error == 0) {
					checkmess.css("color", "green");
				} else {
					checkmess.css("color", "red");
				}
				$("#mlsid").val('');
				checkmess.text(obj.mes).fadeIn("slow");
			}
		});
	});
	
	$(".del").click(delfunc);
	
});
</script>

<div class="wrap">
	<div id="icon-options-general" class="icon32"></div>
	<h2><?php echo $plugin_title ?> :: Listing Prioritization</h2>
	<div class="tool-box">
		<?php
		$prioritization = get_option('sr_prioritization');
		$prioritization = ($prioritization === false) ? array() : $prioritization;
		?>

		<h3>Listing Prioritization</h3>
		<p>You can add an office or agent ID below. We will show your listings first, and you have the option to only display your listings.</p>

		<table style="margin-bottom: 1em;text-align:center;" id="ptable">

			<?php if ( count($prioritization) == 0 || $prioritization == false ): ?>
				<!--<tr><td style="text-align:left;">None added yet.</td></tr>-->
			<?php else: foreach ( $prioritization as $p ): ?>
					<tr><td><?php echo $p['id']?></td><td><?php echo $p['field']?></td><td><a href="javascript:void(0);" style="color:red;font-weight:bold;text-decoration:none;" class="del">X</a></td></tr>
			<?php endforeach; endif; ?>
			
			<tr id="controlrow">
				<td style="padding-top:10px;"><input id="id" type="text" placeholder="Agent or Office ID" style="width:100%;" /></td>
				<td style="padding-top:10px;"><select id="field"><option value="agent_id">Agent ID</option><option value="office_id">Office ID</option></select></td>
				<td style="padding-top:10px;"> <input type="submit" class="button-primary" value="Add" id="add" /></td>
				<td style="padding-top:10px;" id="message"></td>
			</tr>
		
		
		
		</table>
		
		<h3>Lookup Agent or Office ID</h3>
		<p style="max-width:650px;">The login ID might not be the same as the Agent ID that we need to prioritize listing with depending on the board. If you have any trouble using the provided Agent ID, just enter the MLS ID of one of your listings below to lookup the Agent or Office ID that we have on record.</p>
		
		<table style="margin-bottom: 1em;text-align:center;" id="qtable">
		<tr id="controlrow"><!--<td style="padding-top:10px;">Enter MLS ID here</td>--><td style="padding-top:10px;"><input id="mlsid" type="text" placeholder="MLS ID" style="width:100%;" /></td><td style="padding-top:10px;"> <input type="submit" class="button-primary" value="Check" id="check" /></td><td style="padding-top:10px;" id="checkmessage"></td></tr>
		</table>
		
	</div>
</div>
