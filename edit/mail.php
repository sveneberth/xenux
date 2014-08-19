<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if($login['role'] < 2) {
	echo '<p>Du bist nicht berechtigt, diese Seite zu öffnen!</p>';
	return;
}
if($_SERVER['REQUEST_METHOD'] == "POST"){
	foreach($_POST as $key => $val) {
		$$key = mysql_real_escape_string($val);
	}
	$text = $_POST['text'];
} else {
	if(isset($_GET['username']) and !empty($_GET['username'])) {
		$to = $_GET['username'];
	};
}
if(!empty($_POST['to']) and !empty($_POST['subject']) and !empty($_POST['text'])) {
	$mailtxt = '<!DOCTYPE html><html lang="de"><head><meta charset="utf-8"/><title>'.$subject.'</title></head><body>'.nl2br($text).'<br /><br /><span style="font-family:Verdana;color:#777;border-top: 1px #777 solid;">Diese Mail wurde mit Xenux erstellt</span></body></html>';
	$header		 = 'From: "'.$login['name'].'"<'.$login['email'].'>'. "\r\n";
	$header		.= 'Reply-To: "'.$login['name'].'"<'.$login['email'].'>'."\r\n";
	$header		.= 'MIME-Version: 1.0' . "\r\n";
	$header		.= 'Content-type: text/html; charset=utf-8' . "\r\n";
	if($to == '%alle%') {
		$sql = "SELECT * FROM users;";
		$res = mysql_query($sql);
		while($row2 = mysql_fetch_object($res)) {
			mail($row2->email, $subject, $mailtxt, $header);
		}
	} else{
		$sql = "SELECT * FROM XENUX_users WHERE username = '$to' LIMIT 1;";
		$res = mysql_query($sql);
		$num = mysql_num_rows($res);
		if($num > 0) {
			mail($row1->email, $subject, $nachricht, $header);
		} else{
			echo '<p>Es existiert kein Account mit dem Benutzernamen <i>'.$to.'</i>!';
			echo '<br /><a href="javascript:history.back()">Zurück</a>';
			return;
		}
	}
	echo '<p>Die Mail wurde gesendet!</p>';
	echo '<p><a href="?site=mail">Weitere Mails senden</a></p>';
	return;
}
?>
<div class="popup">
	<a class="close" href="javascript:popupclosewithoutcontent()">&times;</a>
	<h3>Nutzer</h3>
	<a href="javascript:popupclose('%alle%')">An alle</a>
	<?php
	$sql = "SELECT * FROM XENUX_users ORDER by vorname;";
	$res = mysql_query($sql);
	while($row = mysql_fetch_assoc($res)) {
		echo "<a href=\"javascript:popupclose('{$row['username']}')\">{$row['vorname']} {$row['nachname']}</a>";
	}
	?>
</div>
<p>Hier kannst du eine Mail senden.</p>
<form action="" method="post" name="form">
	<span <?php if (empty($to) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Bitte wähle einen Empfänger (Benutzername) aus<br /> oder %alle% um eine Mail an alle zu senden:</span><br />
	<input style="display: inline-block;" type="text" class="field1" name="to" value="<?php echo @$to; ?>" />&nbsp;<a href="javascript:popupopen()">Nutzer auswählen</a><br />
	<span <?php if (empty($subject) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Betreff:</span><br />
	<input type="text" name="subject" value="<?php echo @$subject; ?>" /><br /><br />
	<div id="page_edit">
		<div id="formatierungen">
			<a id='textb' href='javascript:text()'>farbiger Text</a>
			<a id='linkb' href='javascript:link()'>Link</a>
			<div id="group">
				<strong><a id='fettb' href='javascript:insert("<b>","</b>")'><b>F</b></a>
				<a id='kursivb' href='javascript:insert("<i>","</i>")'><i>K</i></a>
				<a id='unterstrichenb' href='javascript:insert("<u>","</u>")'><u>U</u></a></strong>
			</div>
			<a href='javascript:bild()'>Bild</a>
			<a href='javascript:insert("<br />","")'>Zeilenumbruch</a>
			<a href='javascript:insert("<hr />","")'>horizontale Linie</a>
		</div>
		<textarea name="text" placeholder="Nachricht" id="text"><?php echo @$text ?></textarea>
		<input type="submit" value="senden">
	</div>
</form>