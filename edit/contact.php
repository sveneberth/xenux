<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");

$skelName = "Ansprechpartner";
$skelTable = "XENUX_contactpersons";
$canAddNew = true;
$skel = array (
	'name' => array(
		'title' => 'Name',
		'type' => 'string',
		"required" => true,
	),
	'position' => array(
		'title' => 'Position',
		'type' => 'string',
		"required" => true,
	),
	'email' => array(
		'title' => 'E-Mail',
		'type' => 'email',
		"required" => true,
	),
	'text' => array(
		'title' => 'Beschreibung',
		"type" => "text",
		"required" => false,
	),
);

include_once(BASEDIR."/core/inc/universal_edit.php");
?>