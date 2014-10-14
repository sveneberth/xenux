<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");

if(!empty($_POST['form'])) {
	if(!empty($post->password0) and !empty($post->password) and !empty($post->password1)) {
		if($login->password != SHA1($_POST['password0'])) {
			echo 'Das eingegebene Passwort stimmt nicht mit dem jetzigen überein!';
		} else {
			if($post->password != $post->password1) {
				echo 'Die eingegebenen Passwörter stimmen nicht überein!';
			} else {
				$db->query("UPDATE XENUX_users Set password = SHA1('{$_POST['password']}') WHERE id = '$login->id'");
				echo 'Das Passwort wurde geändert!';
			}
		}
	}
}
?>
<p>Hier kannst du dein Passwort ändern.</p>
<form action="" method="post">
	<input <?php if(empty($password0) && isset($_POST['form'])) echo 'class="wrong"'; ?> type="password" placeholder="altes Passwort" name="password0" />
	
	<input <?php if(empty($password) && isset($_POST['form'])) echo 'class="wrong"'; ?> type="password" placeholder="neues Passwort" name="password" />
	
	<input <?php if(empty($password1) && isset($_POST['form'])) echo 'class="wrong"'; ?> type="password" placeholder="Passwort bestätigen" name="password1" />
	
	<input type="hidden" name="form" value="form" />
	<input type="submit" value="ändern" />
</form>