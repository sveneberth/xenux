<?php
echo $site->text;

if(!empty($main->contact_form_email)) {
	if(isset($post->contact_submitted)) {
		if(!empty($post->email) and !empty($post->message)) {
			$header  = 'MIME-Version: 1.0' . "\r\n";
			$header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$header .= "From: \"$post->name\"<$post->email> \r\n";
			$mailtext = "<!DOCTYPE html>
<html>
	<head>
		<meta charset=\"utf-8\" />
		<title>Kontaktaufnahme über das Kontaktformular auf ihrer Homepage</title>
	</head>
	<body>
		Hallo!<br />
		Es hat ihnen jemand auf der Homepage <a href=\"".BASEURL."\">".BASEURL."</a> eine Nachricht geschickt!<br /><br />
		<p>
			<b>Absender</b>
			<br />
			Name: $post->name
			<br />
			E-Mail: $post->email
		</p>
		<p>
			<b>Nachricht</b><br />
			".nl2br($_POST['message'])."
		</p>
		<br /><br />
		<span style=\"font-family:Verdana;color:#808080;border-top: 1px #808080 solid;\">Diese E-Mail wurde mit Xenux generiert und versendet.</span>
	</body>
</html>";
			mail(
				$main->contact_form_email, 
				"Kontaktaufnahme ueber das Kontaktformular auf ihrer Homepage", 
				$mailtext,
				$header)
			or die("<p>Die Nachricht konnte nicht versendet werden.</p>");
			echo '<p>&nbsp;</p><p>Die Nachricht wurde erfolgreich versendet!</p>';
			unset($post->contact_submitted);
		//	return false;
		}
	}    

	?>

	<form action="" method="post">
		<h3>Kontaktformular</h3>
		<input type="text" name="name" placeholder="Name*" value="<?php echo @$name; ?>" />
		<input <?php if(empty($post->email) && isset($post->contact_submitted)) echo 'class="wrong"'; ?> type="email" name="email" placeholder="E-Mail*" value="<?php echo @$eMail; ?>" />
		<textarea <?php if(empty($post->message) && isset($post->contact_submitted)) echo 'class="wrong"'; ?> name="message" placeholder="Nachricht"><?php echo @$nachricht; ?></textarea>
		
		<input type="hidden" name="contact_submitted" value="true" />
		<input type="submit" value="Senden" />
		<p>Alle mit einem Stern gekennzeichnete Felder sind Pflichtfelder</p>
	</form>
	<p>&nbsp;</p>
	<?php
}

$result = $db->query("SELECT * FROM XENUX_contactpersons;");
$num = $result->num_rows;
if($num > 0) {
	echo "<h3>Ansprechpartner</h3>";
	while($row = $result->fetch_object()) {
		echo "	<div class=\"contactbox\">
					<div class=\"name\">$row->name</div>
					<div class=\"position\">$row->position</div>
					<div class=\"desc\">$row->text</div>
					<div class=\"email\">".escapemail($row->email)."</div>
				</div>";
	}
	echo "<div style=\"clear:left;\"></div>";
}
?>