<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if(!empty($_POST['submit'])) {
	$email = mysql_real_escape_string($_POST['email']);
	if($email == '')	{$email_fill='n';}
	if($email != '') {
		$sql = "SELECT * FROM XENUX_users WHERE email='$email'";
		$erg = mysql_query($sql);
		$anzahl = mysql_num_rows($erg);
		if($anzahl == 0) {
			$result = "Es konnte keinem Account die E-Mail-Adresse <i>$email</i> zugeordnet werden.";
		} else {
			$sql = "SELECT * FROM XENUX_users WHERE email = '$email'";
			$erg = mysql_query($sql);
			$row = mysql_fetch_array($erg);
			$nachricht = '<html></head><title>Benutzername vergessen</title></head><body>
Hallo!<br />
Dein Benutzername für <a href="http://'.$HP_URL.'">http://'.$HP_URL.'</a> lautet: '.$row['username'].'
<br /><br />
<span style="font-family:Verdana;color:#777;border-top: 1px #777 solid;">Die Mail wurde mit Xenux erstellt</span></body></html>';
			$header		 = 'From: '.$HP_Email."\r\n";
			$header		.= 'Reply-To: '.$HP_Email."\r\n";
			$header		.= 'MIME-Version: 1.0' . "\r\n";
			$header		.= 'Content-type: text/html; charset=utf-8' . "\r\n";
			mail($row['email'], 'Benutzername vergessen', $nachricht, $header);
			$result = 'Dein Benutzername wurde dir soeben per E-Mail zugeschickt!';
		}
	}
}
?>
<p>Fals du deinen Benutzernamen vergessen kannst, kannst du ihn hier an die Registrierte E-Mail-Adresse schicken.</p>
<form action="" method="post">
E-Mail:<br />
<input type="email" placeholder="E-Mail" name="email" value=<?php echo'"'.@$email.'"'; if(@$email_fill=='n'){echo 'class="notfill"';} ?> />
<p><?php echo @$result ?></p>
<input name="submit" type="submit" value="Benutzernamen zusenden">
</form>