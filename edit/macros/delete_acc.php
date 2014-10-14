<?php
include('../../core/inc/config.php');

$result = $db->query("DELETE FROM XENUX_users WHERE id = '$login->id';");
$_SESSION = array();
if(ini_get("session.use_cookies")) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000, $params["path"],
		$params["domain"], $params["secure"], $params["httponly"]
	);
}
session_destroy();
echo "Dein Account wurde gelöscht!";
$db->close(); //close the connection to the db
?>