<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if(isset($_POST['submit_form'])) {
	if(!empty($firstname) and !empty($lastname)  and !empty($username) and !empty($email)) {
		$result = $db->query("UPDATE XENUX_users Set firstname = '$firstname', lastname = '$lastname', username = '$username', email = '$email' WHERE id = '$login->id';");
	}
	$result = $db->query("SELECT * FROM XENUX_users WHERE id = {$_SESSION['userid_xenux']};");
	$login = $result->fetch_object(); // set login with userdata
}
?>
<p>Hier kannst du deine Daten ändern.</p>
<form action="" method="post">
	<input <?php if(empty($firstname) && isset($_POST['submit_form'])) echo 'class="wrong"'; ?> type="text" placeholder="Vorname" name="firstname" value="<?php echo $login->firstname; ?>" />
	
	<input <?php if(empty($lastname) && isset($_POST['submit_form'])) echo 'class="wrong"'; ?> type="text" placeholder="Nachname" name="lastname" value="<?php echo $login->lastname; ?>" />
	
	<input <?php if(empty($username) && isset($_POST['submit_form'])) echo 'class="wrong"'; ?> type="text" placeholder="Benutzername" name="username" value="<?php echo $login->username; ?>" />
	
	<input <?php if(empty($email) && isset($_POST['submit_form'])) echo 'class="wrong"'; ?> type="text" placeholder="E-Mail" name="email" value="<?php echo $login->email; ?>" />
	
	<input type="hidden" name="submit_form" value="true" />
	<input type="submit" value="ändern" />
</form>
<script>
function delacc() {
	messagebox(30,20,'Meldung',"Möchtest du wirklich deinen Account löschen? Das löschen kann nicht rückgängig gemacht werden!<br /><input type=\"button\" id=\"confirmdelacc\" value=\"löschen bestätigen\"/>");
	$("#confirmdelacc").click(function() {
		$(".transparent").remove();
		$(".message").remove();
		$.ajax({
			url: "macros/delete_acc.php",
			success: function(content) {
				messagebox(30,20,'Meldung',content);
			}
		});
	})
}
</script>
<p><a href="javascript:delacc();">Account löschen</a></p>