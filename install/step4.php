<?php
include("../config.php");
$link = mysql_connect($MYSQL_HOST, $MYSQL_BENUTZER, $MYSQL_KENNWORT);
$db_selected = mysql_select_db($MYSQL_DATENBANK, $link);
if(!$db_selected){
	echo 'Es ist keine Verbindung zur Datenbank möglich!';
	exit;
}
if(isset($_POST['submit'])) {
	foreach($_POST as $key => $val) {
		$$key = $val;
	}
	if(empty($hpname) and empty($email)){
		echo '<p style="color:red;">Sie müssen alle Felder ausfüllen!</p>';
	} else {
		$datei = fopen("../config.php","a");
		$text = '
$HP_Email			= "'.$email.'"; # E-Mail-Adresse (diese wird u.A. benötigt um Accounts freizuschalten)
?>';
		fwrite($datei, $text);
		fclose($datei);
		$sql = "UPDATE XENUX_main Set value = '".mysql_real_escape_string($hpname)."' WHERE name = 'hp_name'";
		$erg = mysql_query($sql);
		$next = true;
	}
}
?>
<form action="" method="post">
	Homepage Name (angezeigte Name im Kopf)<br />
	<input type="text" name="hpname" value="<?php if(empty($hpname)){echo 'Meine Homepage"';}else{echo $hpname;} ?>" /><br /><br />
	<span <?php if(empty($email) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>E-Mail-Adresse (diese wird u.A. benötigt um Accounts freizuschalten)</span><br />
	<input type="email" name="email" value="<?php echo $email; ?>" /><br /><br />
	<input type="hidden" name="submit" value="submit" />
	<input type="submit" value="speichern" />
</form>