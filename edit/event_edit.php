<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");

$skelName = "Termine";
$skelTable = "XENUX_dates";
$canAddNew = true;
$skel = array (
	"name" => array (
		"title" => "Name",
		"type" => "string",
		"required" => true,
	),
	"text" => array (
		"title" => "Beschreibung",
		"type" => "text",
		"required" => false,
	),
	"date" => array (
		"title" => "Datum",
		"type" => "date",
		"required" => true,
	),
);

include_once(BASEDIR."/core/inc/universal_edit.php");
?>