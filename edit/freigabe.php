<?php
$username = $_GET['username'];
$email = $_GET['email'];
$sql = "UPDATE XENUX_users Set admin = 'yes' WHERE username = '".mysql_real_escape_string($username)."'";
$erg = mysql_query($sql);

$nachricht = '<!DOCTYPE html><html lang="de"><head><meta charset="utf-8"/><title>Accountfreischaltung</title></head><body>
Hallo!<br />
Dein Account auf der Homepage '.$HP_URL.' wurde freigegeben!<br /><br />
<span style="font-family:Verdana;color:#777;border-top: 1px #777 solid;">Die Mail wurde mit Xenux erstellt</span></body></html>';
$header		 = 'From: '.$HP_Email."\r\n";
$header		.= 'Reply-To: '.$HP_Email."\r\n";
$header		.= 'MIME-Version: 1.0' . "\r\n";
$header		.= 'Content-type: text/html; charset=utf-8' . "\r\n";
mail($email, 'Accountfreischaltung', $nachricht, $header);
echo '<p>Der Account von '.$username.' wurde soeben freigegeben.</p>';
?>