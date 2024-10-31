<?php if ( count($errors) > 0 ): foreach ( $errors as $error ): ?>
<p style="color: red;">* <?php echo $error?></p>
<?php endforeach; endif; ?>
<form action="" method="post">
	<table>
		<tr>
			<td>Password:</td>
			<td><input type="password" name="password" /></td>
		</tr>
		<tr>
			<td>Password Again:</td>
			<td><input type="password" name="password-again" /></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" name="submit" value="Login" /></td>
		</tr>

	</table>
</form>
