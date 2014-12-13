<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");

if(isset($_POST['submit'])) {
	$result = $db->query("SELECT * FROM XENUX_users WHERE email = '$post->email';");
	$number = $result->num_rows;
	if($number == 0) {
		$result = "Es konnte keinem Account die E-Mail-Adresse <i>$post->email</i> zugeordnet werden.";
	} else {
		$row = $result->fetch_object();
		$message =
'<!DOCTYPE html>
<html>
	<head>
		<meta charset=\"UTF-8\" />
		<title>Benutzername vergessen</title>
	</head>
	<body>
		Hallo!<br />
		Dein Benutzername für <a href="'.BASEURL.'">'.BASEURL.'</a> lautet: '.$row->username.'
		<br /><br />
		<span style="font-family:Verdana;color:#777;border-top: 1px #777 solid;">Diese E-Mail wurde mit Xenux generiert und versendet.</span>
	</body>
</html>';
		$header		 = "From: $main->reply_email \r\n";
		$header		.= "Reply-To: $main->reply_email \r\n";
		$header		.= 'MIME-Version: 1.0' . "\r\n";
		$header		.= 'Content-type: text/html; charset=utf-8' . "\r\n";
		mail($row->email, 'Benutzername vergessen', $message, $header);
		$result = 'Dein Benutzername wurde dir soeben per E-Mail zugeschickt!';
	}
}
?>
<p>Fals du deinen Benutzernamen vergessen kannst, kannst du ihn hier an die Registrierte E-Mail-Adresse schicken.</p>
<form action="" method="post">
	<input type="email" placeholder="E-Mail" name="email" />

	<input type="hidden" name="submit" value="true">
	<input type="submit" value="Benutzernamen zusenden">
</form>