<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if($login->role < 2) {
	echo '<p>Du bist nicht berechtigt, diese Seite zu öffnen!</p>';
	return;
}


$table = 'XENUX_users';
$name = 'Nutzer';
include_once('macros/universal_inc.php');
?>