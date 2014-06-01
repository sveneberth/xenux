<?php
SESSION_START();
if(!isset($_GET['userid'])) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if(isset($_GET['userid']) and !empty($_GET['userid'])) {
	
	include('../config.php');
	$link = mysql_connect($MYSQL_HOST, $MYSQL_BENUTZER, $MYSQL_KENNWORT);
	$db_selected = mysql_select_db($MYSQL_DATENBANK, $link);
	if(!$db_selected){
		echo 'Es ist keine Verbindung zur Datenbank möglich!';
		exit;
	}
	mysql_query('SET NAMES "utf8"');
	$sql = "DELETE FROM XENUX_users WHERE id = '".$_GET['userid']."'";
	$erg = mysql_query($sql) or die("Fehler:".mysql_error());
	$_SESSION = array();
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params["path"],
			$params["domain"], $params["secure"], $params["httponly"]
		);
	}
	session_destroy();
	echo "Dein Account wurde gelöscht!";
}
?>