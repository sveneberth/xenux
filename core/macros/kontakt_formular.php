<br />
<?php
if(!isset($filename)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
$name = "";
$eMail = "";
$nachricht = "";
if(isset($_POST['form'])) {
	$name = htmlentities($_POST['name']);
	$eMail = htmlentities($_POST['eMail']);
	$nachricht = htmlentities($_POST['nachricht']);
	if(!empty($eMail) and !empty($nachricht)) {
		$header  = 'MIME-Version: 1.0' . "\r\n";
		$header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$header .= 'From: "'.$name.'"<'.$eMail.'>' . "\r\n";
		$mailtext = '<!DOCTYPE html><html lang="de"></head><meta charset="utf-8" /><title>Kontakt</title></head><body>
Hallo!<br />
Es hat ihnen jemand auf der Homepage <a href="http://'.$HP_URL.'">http://'.$HP_URL.'</a> eine Nachricht geschickt!<br /><br />
<table style="font-size:100%;text-align:left;vertical-align:top;">
<tr><th style="text-align:left;vertical-align:top;">Name:</th style="text-align:left;vertical-align:top;"><td data-title=\"\">'.$name.'</td></tr>
<tr><th style="text-align:left;vertical-align:top;">E-Mail:</th style="text-align:left;vertical-align:top;"><td data-title=\"\">'.$eMail.'</td></tr>
<tr><th style="text-align:left;vertical-align:top;">Nachricht:</th style="text-align:left;vertical-align:top;"><td data-title=\"\">'.nl2br($nachricht).'</td></tr>
</table>
<br /><br /><span style="font-family:Verdana;color:#808080;border-top: 1px #808080 solid;">Die Mail wurde mit Xenux erstellt</span></body></html>';
		mail(
			$contact_form_email, 
			"Kontakt", 
			$mailtext,
			$header)
		or die("<p>Die Mail konnte nicht versendet werden.</p>");
		echo '<p>Die Mail wurde erfolgreich versendet!</p>';
		return;
	}
}    

?>

<form action="" method="post">
	<h3>Kontaktformular</h3>
	Ihr Name:<br />
	<input type="text" name="name" placeholder="Name" value="<?php echo $name; ?>" /><br /><br />
	<span <?php if(isset($_POST['form']) and empty($_POST['eMail'])){echo 'style="color:#F00;"';} ?>>Ihre E-Mail</span><span class="pf">*</span>:<br />
	<input type="email" name="eMail" placeholder="E-Mail" value="<?php echo $eMail; ?>" /><br /><br />
	<span <?php if(isset($_POST['form']) and empty($_POST['nachricht'])){echo 'style="color:#F00;"';} ?>>Ihre Nachricht</span><span class="pf">*</span>:<br />
	<textarea name="nachricht" placeholder="Nachricht"><?php echo $nachricht; ?></textarea><br /><br />
	<input type="hidden" name="form" value="form" />
	<input type="submit" value="Senden" />
	<input type="reset" value="Felder Zurücksetzen" onclick="return confirm('Wirklich löschen?')" />
	<span class="pf">*</span>Pflichtfelder
</form>