<?php
if(isset($_POST['submit'])) {
	if(!empty($post->hpname) && !empty($post->email)) {
		$db->query	("UPDATE XENUX_main Set value = '$post->hpname' WHERE name = 'hp_name';");
		$db->query	("UPDATE XENUX_main Set value = '$post->email' WHERE name = 'noreplay_email';");
		$next = true;
		$db->close(); //close the connection to the db
	}
}
?>
<form action="" method="post">
	Homepage Name (angezeigte Name im Kopf)<br />
	<input type="text" name="hpname" value="<?php if(empty(@$post->hpname))echo 'Meine Homepage"';}else{echo @$post->hpname;} ?>" /><br /><br />
	<span <?php if(empty(@$post->email)) echo 'class="wrong"'; ?>>E-Mail-Adresse (diese wird u.A. benötigt um Accounts freizuschalten)</span><br />
	<input type="email" name="email" value="<?php echo @$post->email; ?>" /><br /><br />
	<input type="hidden" name="submit" value="submit" />
	<input type="submit" value="speichern" />
</form>