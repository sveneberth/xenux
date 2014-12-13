<?php
$result = $db->query("SELECT * FROM XENUX_users WHERE username = '$get->username' LIMIT 1;");
if(!$result)
	echo $db->error;

$num = $result->num_rows;
if($num == 0) {
	request_failed();
	return false;
}

$user = $result->fetch_object();
	
if($user->confirmed == true) {
	echo "<p>Der Benutzer wurde bereits freigegeben!</p>";
	return false;
}
	
$result = $db->query("UPDATE XENUX_users Set confirmed = true WHERE username = '$get->username';");

$message =
'<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title>Accountfreischaltung</title>
	</head>
	<body>
		Hallo '. $user->firstname .' '. $user->lastname .'!<br />
		Dein Account auf der Homepage '.BASEURL.' wurde freigegeben!<br /><br />
		<span style="font-family:Verdana;color:#777;border-top: 1px #777 solid;">Diese E-Mail wurde mit Xenux generiert und versendet.</span>
	</body>
</html>';
$header		 = "From: $main->reply_email \r\n";
$header		.= "Reply-To: $main->reply_email \r\n";
$header		.= 'MIME-Version: 1.0' . "\r\n";
$header		.= 'Content-type: text/html; charset=utf-8' . "\r\n";
mail($user->email, 'Accountfreischaltung', $message, $header);
echo '<p>Der Account von '.$get->username.' wurde soeben freigegeben.</p>';
?>