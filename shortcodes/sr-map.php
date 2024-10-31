<?php
$width = isset($params['width']) ? $params['width'] : 600;
$height = isset($params['height']) ? $params['height'] : 400;
$sr = $seo_rets_plugin;
if ( !$sr->api_key ) return '<p class="sr-error">You must activate the SEO RETS plugin before using shortcodes.</p>';
?>
<iframe style="width:<?php echo $width?>px;height:<?php echo $height?>px;" src="<?php echo get_bloginfo('url')?>/sr-map?params=<?php echo base64_encode(json_encode($params)); ?>"></iframe>
