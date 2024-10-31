<?php
$text_settings = get_option('sr-plugintext');
				
if ( $text_settings ) {
	$text_settings_put = $text_settings;
} else {
	$text_settings_put = $this->text_defaults;
}
?>
<p><?php eval("?>" . $text_settings_put['signup']) ?></p>
<?php if ( count($errors) > 0 ): foreach ( $errors as $error ): ?>
<p style="color: red;">* <?php echo $error?></p>
<?php endforeach; endif; ?>
<form action="" method="post">
	<table>
		<tr>
			<td>Name: </td>
			<td><input type="text" name="full-name" value="<?php echo htmlentities($_POST['full-name'])?>" /></td>
		</tr>
		<tr>
			<td>Email: </td>
			<td><input type="text" name="email" value="<?php echo htmlentities($_POST['email'])?>" /></td>
		</tr>
		<tr>
			<td>Password: </td>
			<td><input type="password" name="password" /></td>
		</tr>
		<tr>
			<td>Password Again: </td>
			<td><input type="password" name="password-again" /></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" name="submit" value="Sign Up" /></td>
		</tr>
	</table>
</form>
