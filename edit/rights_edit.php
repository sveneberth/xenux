<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if($login->role < 2) {
	echo '<p>Du bist nicht berechtigt, diese Seite zu öffnen!</p>';
	//return;
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
		"editable" => false,
	),
	'lastname' => array(
		'title' => 'Nachname',
		"type" => "string",
		"required" => true,
		"editable" => false,
	),
	'email' => array(
		'title' => 'E-Mail',
		"type" => "email",
		"required" => true,
		"editable" => false,
	),
	'username' => array(
		'title' => 'Benutzername',
		"type" => "string",
		"required" => true,
		"editable" => false,
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
		"editable" => false,
	),
);


$result = $db->query("SELECT * FROM $skelTable WHERE id = '".@$get->id."';");
$row = $result->fetch_object();

if( @$get->task == 'edit' && @$get->id == $login->id ) {
	echo "<p>Sie können sich nicht selbst bearbeiten!</p>";
	return false;
}
if( @$get->task == 'edit' && @$row->role > $login->role ) {
	echo "<p>Sie können sich nicht Nutzer mit höheren Rechten, als sie, bearbeiten!</p>";
	return false;
}

include_once(BASEDIR."/core/inc/universal_edit.php");
?>