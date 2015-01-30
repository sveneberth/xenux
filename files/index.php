<?php
include_once('../core/inc/config.php'); // include config

$filename	= @$_GET['file'];
$fileid		= @$_GET['id'];

$result = $db->query("SELECT *, DATE_FORMAT(lastModified, '%D, %d %M %Y %H:%i:%s') as lastModified_formatted FROM XENUX_files WHERE type = 'file' AND (filename = '$filename' OR SHA1(id) = '$fileid') LIMIT 1;");
if(!$result) {
	header("Status: 500 Internal server error");
	die("<!DOCTYPE html>
<html>
	<head>
		<meta charset=\"UTF-8\" />
		<title>500 Internal server error</title>
	</head>
	<body>
		<h1>500 Internal server error</h1>
		<p>The request failed.</p>
		<hr>
		{$_SERVER['SERVER_SIGNATURE']}
	</body>
</html>");
}
if($result->num_rows == 0) {
	header("Status: 404 Not Found");
	die("<!DOCTYPE html>
<html>
	<head>
		<meta charset=\"UTF-8\" />
		<title>404 Not Found</title>
	</head>
	<body>
		<h1>404 Not Found</h1>
		<p>The request file doesn't exist on this server.</p>
		<hr>
		{$_SERVER['SERVER_SIGNATURE']}
	</body>
</html>");
}

$file = $result->fetch_object();
$typeCategory = substr($file->mime_type, 0, strpos($file->mime_type, "/"));

header("Content-Disposition: inline; filename=\"$file->filename\"");
header("Cache-Control: public, max-age=3600");
header("Last-Modified: $file->lastModified_formatted GMT");
header("Status: 200 OK");

if(isset($_GET['size']) && is_numeric($_GET['size']) && $typeCategory == 'image' && $file->mime_type != "image/svg+xml") {
	$image = imagecreatefromstring($file->data);

	$x = imagesx($image);
	$y = imagesy($image);
	
	if(@$_GET['format'] == "square") {
		$desired_width = $_GET['size'];
		$desired_height = $_GET['size'];
	} else {
		$desired_width = $_GET['size'];
		$desired_height = $y / $x * $desired_width;
	}

	$new = imagecreatetruecolor($desired_width, $desired_height);
	imagealphablending($new, FALSE);
	imagesavealpha($new, TRUE);
	imagecopyresampled($new, $image, 0, 0, 0, 0, $desired_width, $desired_height, $x, $y);
	imagedestroy($image);
	
	if($file->mime_type == "image/jpeg") {
		header("Content-type: image/jpeg");
		imagejpeg($new);
	} elseif($file->mime_type == "image/gif") {
		header("Content-type: image/gif");
		imagegif($new);
	} else {
		header("Content-type: image/png");
		imagepng($new);
	}
} else {
	header("Content-type: $file->mime_type");
	echo $file->data;
}


$db->close(); //close the connection to the db
?>