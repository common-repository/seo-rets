<?php

if (!$seo_rets_plugin->api_key) {
	echo '<p class="sr-error">You must activate the SEO RETS plugin before using shortcodes.</p>';
	return;
}
echo $seo_rets_plugin->include_return('templates/splitsearch.php', array("sr" => $seo_rets_plugin, "params" => $params));
