<div id="dir-password">
	<h2>You need a password to view <strong><?= $relative ?: '/'?></strong></h2>
	<form style="padding:8px 0" method="POST" action="<?= \dirp\app::get_request()->get_base_uri() ?>/dir/login">
		<label for="password">password:</label>
		<input type="password" name="password" maxlength="256">
		<input type="submit" value="enter">
	</form>
</div>