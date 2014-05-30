<?php
$FirstName	= "";
$LastName	= "";
$eMail		= "";
$result		= "";
if(!empty($_POST['sub1'])) {
	$FirstName	= $_POST['FirstName'];
	$LastName	= $_POST['LastName'];
	$eMail		= $_POST['eMail'];

	if(!empty($FirstName) and !empty($LastName) and !empty($eMail)) {
		$sql = "UPDATE XENUX_users Set vorname = '".mysql_real_escape_string($FirstName)."', nachname = '".mysql_real_escape_string($LastName)."', email = '".mysql_real_escape_string($eMail)."' WHERE username = '".$_SESSION['user']['username']."'";
		$erg = mysql_query($sql);
		$result = 'Die Daten wurden geändert!';
	}
}else {
	$sql = "SELECT * FROM XENUX_users WHERE username = '".$_SESSION['user']['username']."'";
	$erg = mysql_query($sql);
	$row = mysql_fetch_array($erg);
	$FirstName	= $row['vorname'];
	$LastName	= $row['nachname'];
	$eMail		= $row['email'];
}
//%FIXIT% you can change your username and delete your account from xenux
?>
<p>Hier kannst du deine Daten ändern.</p>
<form action="" method="post">
	<span <?php if (empty($FirstName) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Vorname</span><br />
	<input type="text" name="FirstName" size="70" value="<?php echo $FirstName; ?>" /><br /><br />
	<span <?php if (empty($FirstName) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Nachname</span><br />
	<input type="text" name="LastName" size="70" value="<?php echo $LastName; ?>" /><br /><br />
	<span <?php if (empty($FirstName) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>E-Mail</span><br />
	<input type="text" name="eMail" size="70" value="<?php echo $eMail; ?>" /><br /><br />
	<p><?php echo $result ?></p>
	<input name="sub1" type="submit" value="ändern">
</form>