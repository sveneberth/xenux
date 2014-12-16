<?php
include_once('../core/inc/config.php'); // include config
error_reporting(0);

$images	= array();

$allowedExts = array("gif", "jpeg", "jpg", "png");

$handle = opendir("../files/");
while($file = readdir($handle)) {
	$temp = explode(".", $file);
	$extension = end($temp);
	if(!is_dir($file) && in_array($extension, $allowedExts)) {
		$images[]['url'] = XENUX_URL."/files/$file";
	}
}
closedir($handle);

header('Content-type: application/json');  
echo json_encode($images);
$db->close(); //close the connection to the db
?>