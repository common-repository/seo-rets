<?php
header("Content-Type: text/css");
echo get_option('sr_css') ? get_option('sr_css') : $this->include_return("resources/defaults/template-styles.css");
exit;
