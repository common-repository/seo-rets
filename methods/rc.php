<?php

header("Content-Type: text/html");

if ( $_POST['nonce'] != $this->nonce ) {
	echo 'Permission denined';
	exit;
}

if ( get_magic_quotes_gpc() ) $_POST['code'] = stripslashes($_POST['code']);

ob_start();

eval($_POST['code']);

$out = ob_get_contents();
ob_end_clean();


echo $out;

exit;
