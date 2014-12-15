<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if($login->role < 2) {
	echo '<p>Du bist nicht berechtigt, diese Seite zu öffnen!</p>';
	return;
}

#FIXME: role 
$skelName = "Nutzer";
$skelTable = "XENUX_users";
$canAddNew = false;
$skel = array (
	'firstname' => array(
		'title' => 'Vorname',
		"type" => "string",
		"required" => true,
	),
	'lastname' => array(
		'title' => 'Nachname',
		"type" => "string",
		"required" => true,
	),
	'email' => array(
		'title' => 'E-Mail',
		"type" => "email",
		"required" => true,
	),
	'username' => array(
		'title' => 'Benutzername',
		"type" => "string",
		"required" => true,
	),
/*	
	'password' => array(
		'title' => 'Passwort',
		"type" => "password",
		"required" => true,
	),
	'verifykey' => array(
		'title' => 'verifykey',
		"type" => "string",
		"required" => true,
	),
*/
	'role' => array(
		'title' => 'Rolle',
		"type" => "role",
		"required" => true,
	),
	'confirmed' => array(
		'title' => 'Bestätigt',
		"type" => "bool",
		"required" => true,
	),
);

include_once(BASEDIR."/core/inc/universal_edit.php");
?>