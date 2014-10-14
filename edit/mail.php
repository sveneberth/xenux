<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if($login->role < 2) {
	echo '<p>Du bist nicht berechtigt, diese Seite zu öffnen!</p>';
	return false;
}

if(isset($post->submit_mail)) {
	if(!empty($post->to) and !empty($post->subject) and !empty($post->message)) {
		$mailtxt = '<!DOCTYPE html><html lang="de"><head><meta charset="utf-8"/><title>'.$post->subject.'</title></head><body>'.nl2br($post->message).'<br /><br /><span style="font-family:Verdana;color:#777;border-top: 1px #777 solid;">Diese E-Mail wurde mit Xenux erstellt</span></body></html>';
		$header		 = "From: \"$login->firstname $login->lastname\"<$login->email> \r\n";
		$header		.= "From: \"$login->firstname $login->lastname\"<$login->email> \r\n";
		$header		.= 'MIME-Version: 1.0' . "\r\n";
		$header		.= 'Content-type: text/html; charset=utf-8' . "\r\n";
		if($post->to == '%alle%') {
			$result = $db->query("SELECT * FROM users;");
			while($row2 = $result->fetch_object()) {
				mail($row2->email, $post->subject, $mailtxt, $header);
			}
		} else {
			$result = $db->query("SELECT * FROM XENUX_users WHERE username = '$post->to' LIMIT 1;");
			$num = $result->num_rows;
			if($num > 0) {
				$row1 = $result->fetch_object();
				mail($row1->email, $post->subject, $mailtxt, $header);
			} else {
				echo '<p>Es existiert kein Account mit dem Benutzernamen <i>'.$post->to.'</i>!';
				echo '<br /><a href="javascript:history.back()">Zurück</a>';
				return false;
			}
		}
		echo '<p>Die Mail wurde gesendet!</p>';
		echo '<p><a href="?site=mail">Weitere Mails senden</a></p>';
		return false;
	}
}
?>
<div class="popup">
	<a class="close" href="javascript:popupclosewithoutcontent()">&times;</a>
	<h3>Nutzer</h3>
	<a href="javascript:popupclose('%alle%')">An alle</a>
	<?php
	$result = $db->query("SELECT * FROM XENUX_users ORDER by firstname ASC;");
	while($row = $result->fetch_object()) {
		echo "<a href=\"javascript:popupclose('$row->username')\">$row->firstname $row->lastname</a>";
	}
	?>
</div>
<p>Hier kannst du eine Mail senden.</p>
<form action="" method="post" name="form">
	<input style="display: inline-block;" placeholder="Empfänger" type="text" class="field1" name="to" value="<?php echo @$post->to; ?>" />&nbsp;<a href="javascript:popupopen()">Nutzer auswählen</a><br />
	<input type="text" placeholder="Betreff" name="subject" value="<?php echo @$post->subject; ?>" />
	<textarea name="message" placeholder="Nachricht"><?php echo @$post->message ?></textarea>
	
	<input type="hidden" name="submit_mail" value="senden">
	<input type="submit" value="senden">
</form>