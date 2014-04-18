<br />
<?php
if(empty($filename)){exit;};//wenn direkt aufgerufen wird
$name = "";
$eMail = "";
$nachricht = "";
if (isset($_POST['name'])) {
	$name = htmlentities($_POST['name']);
	$eMail = htmlentities($_POST['eMail']);
	$nachricht = htmlentities($_POST['nachricht']);
	if(!empty($eMail) and !empty($nachricht)) {
		$header  = 'MIME-Version: 1.0' . "\r\n";
		$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$header .= 'To: '.$HP_Kontaktemail. "\r\n";
		$header .= 'From: "'.$name.'"<'.$eMail.'>' . "\r\n";
		$mailtext = '<html lang="de"></head><title>Kontakt</title></head><body>
Hallo!<br />
Es hat ihnen jemand auf der Homepage <a href="http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'">http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'</a> eine Nachricht geschickt!<br /><br />
<table style="font-size:100%;text-align:left;vertical-align:top;">
<tr><th style="text-align:left;vertical-align:top;">Name:</th style="text-align:left;vertical-align:top;"><td>'.$name.'</td></tr>
<tr><th style="text-align:left;vertical-align:top;">E-Mail:</th style="text-align:left;vertical-align:top;"><td>'.$eMail.'</td></tr>
<tr><th style="text-align:left;vertical-align:top;">Nachricht:</th style="text-align:left;vertical-align:top;"><td>'.nl2br($nachricht).'</td></tr>
</table>
<br /><br /><span style="font-family:Verdana;color:#808080;border-top: 1px #808080 solid;">Die Mail wurde mit Xenux erstellt</span></body></html>';
		mail(
			$HP_Kontaktemail, 
			"Kontakt", 
			$mailtext,
			$header)
		or die("<p>Die Mail konnte nicht versendet werden.</p>");
		echo '<p>Die Mail wurde erfolgreich versendet!</p>';
		exit;
	}
}    

?>

<form action="" method="post">
	<h3>Kontaktformular</h3>
	Ihr Name:<br />
	<input type="text" name="name" size="50" value="<?php echo $name; ?>" /><br /><br />
	<span <?php if(!empty($_POST['submit']) and empty($_POST['eMail'])){echo 'style="color:#F00;"';} ?>>Ihre E-Mail</span><span class="pf">*</span>:<br />
	<input type="email" name="eMail" size="50" value="<?php echo $eMail; ?>" /><br /><br />
	<span <?php if(!empty($_POST['submit']) and empty($_POST['nachricht'])){echo 'style="color:#F00;"';} ?>>Ihre Nachricht</span><span class="pf">*</span>:<br />
	<textarea name="nachricht" rows="10" cols="80"><?php echo $nachricht; ?></textarea><br /><br />
	<input type="submit" value="Senden" />
	<input type="reset" value="Felder Zurücksetzen" onclick="return confirm('Wirklich löschen?')" />
	<span class="pf">*</span>Pflichtfelder
</form>