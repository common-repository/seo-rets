<?php
$sr = $seo_rets_plugin;
$plugin_title = $sr->admin_title;
$plugin_id = $sr->admin_id;

if ( isset($_POST['deactivate']) ): 
	$sr->deregister();
?>
	<script type="text/javascript">
	document.location = '<?php echo get_bloginfo('url')?>/wp-admin/admin.php?page=<?php echo $plugin_id ?>';
	</script>	
<?php else: ?>

<div class="wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2><?php echo $plugin_title ?> :: Status</h2>
	<div class="tool-box">
		<table>
			<tr>
				<td valign="top" style="padding-right:20px;">
					<?php if($plugin_title == "BrokerIDX") { ?>
						<img src="<?php echo $sr->plugin_dir?>resources/images/logoIDXBroker.png" style="margin-top: 70px;" />
					<?php } else { ?>
						<img src="<?php echo $sr->plugin_dir?>resources/images/seorets-logo.png" style="margin-top: 70px;" />
					<?php } ?>
				</td>
				<td>
					<?php if($plugin_title !== "BrokerIDX") { ?>
						<h3>Getting Started</h3>
						<p>Don't know where to start, or need help using the <?php echo $plugin_title ?> plugin? Check out the <?php /* <a href="<?php echo get_bloginfo('url')?>/wp-admin/admin.php?page=seo-rets-user-guide#getting-started">getting started</a> */ ?><a href="http://manual.seorets.com/">getting started</a> section of the user guide.</p>
					<?php } ?>
						<h3>License Information</h3>
						<p>
							This plugin has been licensed to <strong><?php echo htmlentities($sr->feed->client_name)?></strong>.
							<table>

								<tr>
									<td style="padding-right:10px;"><strong>Feed Name:</strong></td>
									<td>
										<?php echo htmlentities($sr->feed->feed_name)?>
									</td>
								</tr>
								<tr>
									<td style="padding-right:10px;"><strong>License Key:</strong></td>
									<td>
										<?php echo htmlentities($sr->feed->key)?>
									</td>
								</tr>
								<tr>
									<td style="padding-right:10px;"><strong>MLS Server:</strong></td>
									<td>
										<?php echo htmlentities($sr->feed->server_name)?>
									</td>
								</tr>
								<tr>
									<td style="padding-right:10px;"><strong>Plugin Version:</strong></td>
									<td>
										v<?php echo $sr->plugin_version?>
									</td>
								</tr>
								<tr>
									<td style="padding-right:10px;"><strong>API Version:</strong></td>
									<td>
										v<?php echo $sr->api_version?>
									</td>
								</tr>
								
							</table>
						</p>
						<p>
							Your feed <strong><?php echo htmlentities($sr->feed->feed_name)?></strong> is registered to this server running at <strong><?php echo htmlentities($sr->feed->ip_address)?></strong>.<br />
							If you are changing servers or need to register your feed to a different IP address,<br />click the "Deregister Plugin" button below.
						</p>
						<p>
							<span style="color:red;font-weight:bold;">Warning!</span><br />
							Clicking the Deregister Plugin button will disable the <?php echo $plugin_title ?> plugin and<br />all of its features on your site. In order to reregister, you must use enter<br />
							this key: <strong><?php echo htmlentities($sr->feed->key)?></strong> on the <?php echo $plugin_title ?> Setup page.
						</p>
						<p>
							<script type="text/javascript">
							var confirmsubmit = true;
							</script>
							<form action="" method="post" onsubmit="if ( confirmsubmit ) { return confirm('Are you sure you want to deregister the <?php echo $plugin_title ?> plugin?'); } return true;">
							<input type="submit" name="deactivate" onclick="confirmsubmit=true" class="button-primary" value="Deregister Plugin" />
							<input type="submit" name="refresh" onclick="confirmsubmit=false" class="button-primary" value="Refresh Feed Information" />
							<?php
							if (isset($_POST['refresh']) && $sr->refresh_feed()) {
								echo '<span style="color:green;">Feed Information Refreshed</span>';
							}
							?>
							</form>
						</p>
					<?php endif; ?>
					</td>
			</tr>
		</table>
	</div>
</div>
