<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");

$skelName = "News";
$skelTable = "XENUX_news";
$canAddNew = true;
$skel = array (
	"title" => array (
		"title" => "Titel",
		"type" => "string",
		"required" => true,
	),
	"text" => array (
		"title" => "Beschreibung",
		"type" => "text",
		"required" => false,
	),
);

include_once(BASEDIR."/core/inc/universal_edit.php");
?>