<?php
$sr = $seo_rets_plugin;
$this->server_plugin_dir."/includes/";
if ( !defined("DONOTCACHEPAGE") ) {
	define("DONOTCACHEPAGE", true);
}

if ( !$sr->api_key ) return '<p class="sr-error">You must activate the SEO RETS plugin.</p>';

wp_enqueue_style('sr_shortcodes_leadcapture',$this->css_resources_dir.'shortcodes/leadcapture.css');
wp_print_styles(array('sr_shortcodes_leadcapture'));
?>
<script  type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('#sr_lc-submit').click(function(e){
			e.preventDefault();
			var email = jQuery('#sr_lc-email').val();
			var captcha = jQuery('#sr_lp-captcha_code').val();
			if (email==''){
				jQuery('#sr_lc-message').html('Please fill email');
			}
			else if (captcha==''){
				jQuery('#sr_lc-message').html('Please fill captcha');
			}

			else{
				jQuery.ajax({
					type:"POST",
					url:"/sr-ajax?action=leadcapture-send",
					data:jQuery('#sr_lp-form').serialize()
				}).done(function(response){
//					var response=JSON.parse(msg);
					if (response.error=='0'){
						jQuery('#sr_lc-message').removeClass('red');
						jQuery('#sr_lc-message').addClass('green');
						document.getElementById('sr_lp-captcha').src = $sr->plugin_dir.'includes/secureimage/securimage_show.php?' + Math.random();
						jQuery('#sr_lp-form')[0].reset();

					}
					else{
						jQuery('#sr_lc-message').removeClass('green');
						jQuery('#sr_lc-message').addClass('red');

						document.getElementById('sr_lp-captcha').src = $sr->plugin_dir.'includes/secureimage/securimage_show.php?' + Math.random();
						jQuery('#sr_lp-captcha_code').val('');
					}
					jQuery('#sr_lc-message').html(response.mes);
				});
			}
		});
	});
</script>
<h3>Lead Capture</h3>
<div id="lead_container">
	<form method="POST" id="sr_lp-form">
	<table id="lead-capture">
		<tr>
			<td>
				Name:
			</td>
			<td>
				<input type="text" name="name" style="width:100%;" />
			</td>
		</tr>
		<tr>
			<td>
				*Email:
			</td>
			<td>
				<input type="text" name="email" id="sr_lc-email" required="required" value="" style="width:100%;" />
			</td>
		</tr>
		<tr>
			<td>
				Phone:
			</td>
			<td>
				<input type="text" name="phone" style="width:100%;" />
			</td>
		</tr>
		<tr>
			<td>
				Price Range:
			</td>
			<td>
				<select name="low_price">
					<option value="">Any</option>
					<option value="100000">$100,000</option>
					<option value="125000">$125,000</option>
					<option value="150000">$150,000</option>
					<option value="175000">$175,000</option>
					<option value="200000">$200,000</option>
					<option value="225000">$225,000</option>
					<option value="250000">$250,000</option>
					<option value="275000">$275,000</option>
					<option value="300000">$300,000</option>
					<option value="325000">$325,000</option>
					<option value="350000">$350,000</option>
					<option value="375000">$375,000</option>
					<option value="400000">$400,000</option>
					<option value="425000">$425,000</option>
					<option value="450000">$450,000</option>
					<option value="475000">$475,000</option>
					<option value="500000">$500,000</option>
					<option value="600000">$600,000</option>
					<option value="700000">$700,000</option>
					<option value="800000">$800,000</option>
					<option value="900000">$900,000</option>
					<option value="1000000">$1,000,000</option>
					<option value="1500000">$1,500,000</option>
					<option value="2000000">$2,000,000</option>
					<option value="2500000">$2,500,000</option>
					<option value="3000000">$3,000,000</option>
					<option value="3500000">$3,500,000</option>
					<option value="4000000">$4,000,000</option>
					<option value="4500000">$4,500,000</option>
					<option value="5000000">$5,000,000</option>
				</select>
				-
				<select name="high_price">
					<option value="">Any</option>
					<option value="100000">$100,000</option>
					<option value="125000">$125,000</option>
					<option value="150000">$150,000</option>
					<option value="175000">$175,000</option>
					<option value="200000">$200,000</option>
					<option value="225000">$225,000</option>
					<option value="250000">$250,000</option>
					<option value="275000">$275,000</option>
					<option value="300000">$300,000</option>
					<option value="325000">$325,000</option>
					<option value="350000">$350,000</option>
					<option value="375000">$375,000</option>
					<option value="400000">$400,000</option>
					<option value="425000">$425,000</option>
					<option value="450000">$450,000</option>
					<option value="475000">$475,000</option>
					<option value="500000">$500,000</option>
					<option value="600000">$600,000</option>
					<option value="700000">$700,000</option>
					<option value="800000">$800,000</option>
					<option value="900000">$900,000</option>
					<option value="1000000">$1,000,000</option>
					<option value="1500000">$1,500,000</option>
					<option value="2000000">$2,000,000</option>
					<option value="2500000">$2,500,000</option>
					<option value="3000000">$3,000,000</option>
					<option value="3500000">$3,500,000</option>
					<option value="4000000">$4,000,000</option>
					<option value="4500000">$4,500,000</option>
					<option value="5000000">$5,000,000</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				Time Frame:
			</td>
			<td>
				<input name="time_frame" type="text" style="width:100%;" />
			</td>
		</tr>
		<tr>
			<td>
				Prequalified:
			</td>
			<td>
				<select name="prequalified" style="width:100%;">
					<option>Yes</option>
					<option>No</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				Interest:
			</td>
			<td>
				<select name="interest" style="width:100%;">
					<option>Buying</option>
					<option>Selling</option>
					<option>Both</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>

			</td>
			<td>
				<img id="sr_lp-captcha" src="<?echo $this->plugin_dir; ?>/includes/secureimage/securimage_show.php" alt="CAPTCHA Image" />
				<input required="required" type="text" name="captcha_code" id="sr_lp-captcha_code" size="10" maxlength="6" />
				<a href="#" onclick="document.getElementById('sr_lp-captcha').src = '<?echo $this->plugin_dir; ?>/includes/secureimage/securimage_show.php?' + Math.random(); return false">[ Different Image ]</a>
			</td>
		</tr>
	</table>
	<input type="submit" id="sr_lc-submit" value="Submit" />
		</form>
	<div id="sr_lc-message" class="red">

	</div>
</div>

