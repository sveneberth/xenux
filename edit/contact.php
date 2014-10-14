<?php
if(!isset($site)) echo("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");

if($login->role < 1) {
	echo '<p>Du bist nicht berechtigt, diese Seite zu öffnen!</p>';
	return false;
}

$table = 'XENUX_contactpersons';
$name = 'Ansprechpartner';
include_once('macros/universal_inc.php');
?>