<?php
if(isset($_POST['submit'])) {
	if(!empty($post->hpname) && !empty($post->email)) {
		$db->query	("UPDATE XENUX_main Set value = '$post->hpname' WHERE name = 'hp_name';");
		$db->query	("UPDATE XENUX_main Set value = '$post->email' WHERE name = 'reply_email';");
		
		echo '<p>Eingaben gespeichert!</p>';
		$next = true;
		
		$db->close(); //close the connection to the db
		return false;
	}
}
?>
<form action="" method="post">
	<label for="hpname">Homepage Name</label>
	<input type="text" id="hpname" name="hpname" placeholder="Homepage Name" value="<?php echo empty(@$post->hpname) ? 'Meine Homepage"' : @$post->hpname; ?>" />
	
	<label for="email">E-Mail-Adresse (diese wird u.A. benötigt um Accounts freizuschalten)</label>
	<input type="email" <?php if(empty(@$post->email) && isset($post->email)) echo 'class="wrong"'; ?> id="email" name="email" placeholder="E-Mail-Adresse" value="<?php echo @$post->email; ?>" placeholder="E-Mail-Adresse" />
	
	<input type="hidden" name="submit" value="submit" />
	<input type="submit" value="speichern" />
</form>