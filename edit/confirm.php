<?php
$result = $db->query("UPDATE XENUX_users Set confirmed  = true WHERE username = '$get->usernam';");
$message = '<!DOCTYPE html><html lang="de"><head><meta charset="utf-8"/><title>Accountfreischaltung</title></head><body>
Hallo!<br />
Dein Account auf der Homepage '.@$HP_URL.' wurde freigegeben!<br /><br />
<span style="font-family:Verdana;color:#777;border-top: 1px #777 solid;">Die Mail wurde mit Xenux erstellt</span></body></html>';
$header		 = "From: $main->noreplay_email \r\n";
$header		.= "Reply-To: $main->noreplay_email \r\n";
$header		.= 'MIME-Version: 1.0' . "\r\n";
$header		.= 'Content-type: text/html; charset=utf-8' . "\r\n";
mail($get->email, 'Accountfreischaltung', $message, $header);
echo '<p>Der Account von '.$get->username.' wurde soeben freigegeben.</p>';
?>