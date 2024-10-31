<?php
$sr = $seo_rets_plugin;
 if ( isset($_POST['submit']) ):
			
	$test = $sr->register($_POST['key']);
	
	if ($test === true): ?>
		<script type="text/javascript">
		document.location = '<?php echo get_bloginfo('url') . '/wp-admin/admin.php?page=seo-rets'?>';
		</script>
	<?php else: ?>

	<div class="wrap">
		<div id="icon-tools" class="icon32"></div>
		<h2>SEO RETS :: Setup</h2>
		<div class="tool-box">
			<table>
				<tr>
					<td valign="top" style="padding-right:20px;">
						<img src="<?php echo $sr->plugin_dir?>resources/images/seorets-logo.png" style="margin-top: 16px;" />
					</td>
					<td>
						<h3 class="title">Welcome to SEO RETS!</h3>
						<p>
							Thank you for your interest in the SEO RETS WordPress plugin.
						</p>
						<p>
							Before you can get started, you will need to enter the license key you received. Need to get a license key? One can be purchased <a href="http://seorets.com/getting-started/">here</a>
						</p>
						<p>
							<form action="" method="post">
								<table class="form-table" style="width:auto;">
									<tbody>
									<tr valign="top">
										<th scope="row" style="width: auto !important;">
											<label for="key">License Key:</label>
										</th>
										<td>
											<input type="text" name="key" id="key" value="<?php echo htmlentities($_POST['key'])?>" class="regular-text"> <span style="color:red;"><?php echo $test?></span><br />
											<span class="description">Note: You can only register your license on one server.</span>
										</td>
									</tr>
			
									<tr valign="top">
										<th scope="row" style="width: auto !important;">
										</th>
										<td>
											<input type="submit" name="submit" class="button-primary" value="Register Plugin">
										</td>
									</tr>
			
									</tbody>
								</table>
							</form>
						</p>
				
					</td>
				</tr>
			</table>

	<?php endif;
else: 
	$errors = $sr->check_requirements();
	?>
			<div class="wrap">
		<div id="icon-tools" class="icon32"></div>
		<h2>SEO RETS :: Setup</h2>
		<div class="tool-box" style="margin-top:20px;">
				<table>
					<tr>
						<td valign="top" style="padding-right:20px;">
							<img src="<?php echo $sr->plugin_dir?>resources/images/seorets-logo.png" style="margin-top: 16px;" />
						</td>
						<td>
							<h3 class="title">Welcome to SEO RETS!</h3>
							<p>
							Thank you for your interest in the SEO RETS WordPress plugin.
						</p>
						<p>
							Before you can get started, you will need to enter the license key you received.
						</p>
						<p>
							Need a license key? One can be purchased <a href="http://seorets.com/getting-started/">here</a>.
						</p>
							<?php if ($errors): ?>
							<p style="color:red;">
								We're sorry, it doesn't look like your system meets the minimum requirements to run this plugin.<br />
								Please correct the following issues and try again.
								<ul style="list-style-type: disc; margin-left: 15px; color:red;">
									<?php foreach ($errors as $error): ?>
										<li><strong><?php echo $error?></strong></li>
									<?php endforeach; ?>
								</ul>
							</p>
							<?php endif; ?>
							<p>
								<form action="" method="post">
									<table class="form-table" style="width:auto;">
										<tbody>
										<tr valign="top">
											<th scope="row" style="width: auto !important;">
												<label for="key">License Key:</label>
											</th>
											<td>
												<input type="text" name="key" id="key" class="regular-text"<?php echo  $errors ? ' disabled="disabled"' : "" ?> value="" /><br />
												<span class="description">Note: You can only register your license on one server.</span>
											</td>
										</tr>
										
										<tr valign="top">
											<th scope="row" style="width: auto !important;">
											</th>
											<td>
												<input type="submit" name="submit" class="button-primary" value="Register Plugin"<?php echo  $errors ? ' disabled="disabled"' : "" ?> />
											</td>
										</tr>
					
										</tbody>
									</table>
								</form>
							</p>
						
						</td>
					</tr>
					
				</table>
				
			<?php endif; ?>
		</div>
	</div>
