<?php
if(isset($login)) {
	$_SESSION = array();
	session_destroy();
}
?>
<p>Du wurdest erfolgreich abgemeldet!</p>