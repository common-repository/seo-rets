<?php
$text_settings = get_option('sr-plugintext');

if ( $text_settings ) {
	$text_settings_put = $text_settings;
} else {
	$text_settings_put = $this->text_defaults;
}
?>
<p><?php eval("?>" . $text_settings_put['forgot']) ?></p>
<?php if ( count($errors) > 0 ): foreach ( $errors as $error ): ?>
<p style="color: red;">* <?php echo $error?></p>
<?php endforeach; endif; ?>
<form action="" method="post">
	<table>
		<tr>
			<td>Email: </td>
			<td><input type="text" name="email" value="<?php echo htmlentities($_POST['email'])?>" /></td>
			<td><input type="submit" name="submit" value="Submit" /></td>
		</tr>
	</table>
</form>
