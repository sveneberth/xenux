<?php
include_once('../core/inc/config.php'); // include config
error_reporting(0);

$images	= array();

$result = $db->query("SELECT id, type, mime_type, filename FROM XENUX_files WHERE type = 'file' ORDER by filename ASC;");

while($row = $result->fetch_object()) {
	$typeCategory = substr($row->mime_type, 0, strpos($row->mime_type, "/"));
	
	if($typeCategory == 'image') {
		$images[]['url'] = XENUX_URL."/files/output.php?id=".SHA1($row->id);
	}
}

header('Content-type: application/json');  
echo json_encode($images);
$db->close(); //close the connection to the db
?>