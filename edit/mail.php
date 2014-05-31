<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if($login['role'] < 1) {
	echo '<p>Du bist nicht berechtigt, diese Seite zu öffnen!</p>';
	return;
}
$to = "";
$subject = "";
$text = "";
if(isset($_POST['to'])){
	$to = mysql_real_escape_string($_POST['to']);
	$subject = $_POST['subject'];
	$text = $_POST['text'];
}else {
	if(isset($_GET['username']) and !empty($_GET['username'])) {
		$to = $_GET['username'];
	};
}
if(!empty($_POST['to']) and !empty($_POST['subject']) and !empty($_POST['text'])) {
	$nachricht = '<html></head><title>'.$subject.'</title></head><body>'.nl2br($text).'<br /><br /><span style="font-family:Verdana;color:#777;border-top: 1px #777 solid;">Die Mail wurde mit Xenux erstellt</span></body></html>';
	$header		 = 'From: "'.$login['vorname'].' '.$login['nachname'].'"<'.$login['email'].'>'. "\r\n";
	$header		.= 'Reply-To: "'.$login['vorname'].' '.$login['nachname'].'"<'.$login['email'].'>'."\r\n";
	$header		.= 'MIME-Version: 1.0' . "\r\n";
	$header		.= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$sql = "SELECT * FROM XENUX_users WHERE username='".$to."'";
	$erg = mysql_query($sql);
	$row1 = mysql_fetch_object($erg);
	if($to == '%alle%') {
		$sql = "SELECT * FROM XENUX_users";
		$erg = mysql_query($sql);
		while($row2 = mysql_fetch_object($erg)) {
			mail($row2->email, $subject, $nachricht, $header);
		}
	} else{
		$sql = "SELECT * FROM XENUX_users WHERE username='".$to."'LIMIT 1";
		$res = mysql_query($sql, $link);
		$anzahl = mysql_num_rows($res);
		if ($anzahl > 0) {
			mail($row1->email, $subject, $nachricht, $header);
		} else{
			echo '<p>Es existiert kein Account mit dem Benutzernamen <i>'.$to.'</i>!';
			echo '<br /><a href="javascript:history.back()">Zurück</a>';
			return;
		}
	}
	echo 'Die Mail wurde gesendet!<br />';
	echo '<a href="?site=mail">Weitere Mails senden</a>';
	return;
}
?>
<div id="popup">
<div class="close" id="close"><a href="javascript:popupclosewithoutcontent()">&times;</a></div>
<h3>Nutzer</h3>
<?php
$sql = "SELECT * FROM XENUX_users ORDER by vorname";
$erg = mysql_query($sql);
while($row = mysql_fetch_assoc($erg)) {
	echo "<a href=\"javascript:popupclose('".$row['username']."')\">".$row['vorname']." ".$row['nachname']."</a>";
}
?>
</div>
<p>Hier kannst du eine Mail senden.</p>
<br />
<form action="" method="post" name="form">
	<span <?php if (empty($to) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Bitte wähle einen Empfänger (Benutzername) aus<br /> oder %alle% um eine Mail an alle zu senden:</span><br />
	<input type="text" id="field1" name="to" value="<?php echo $to; ?>" />&nbsp;<a href="javascript:popupopen()">Nutzer auswählen</a><br /><br />
	<span <?php if (empty($subject) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Betreff:</span><br />
	<input type="text" name="subject" value="<?php echo $subject; ?>" /><br /><br />
	<span <?php if (empty($text) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Text</span><br />
	<div id="page_edit">
		<div id="formatierungen">
			<a id='textb' href='javascript:text()'>farbiger Text</a>
			<a id='linkb' href='javascript:link()'>Link</a>
			<div id="group">
				<strong><a id='fettb' href='javascript:insert("<b>","</b>")'><b>F</b></a>
				<a id='kursivb' href='javascript:insert("<i>","</i></7a>")'><i>K</i></a>
				<a id='unterstrichenb' href='javascript:insert("<u>","</u>")'><u>U</u></a></strong>
			</div>
			<a href='javascript:bild()'>Bild</a>
			<a href='javascript:insert("<br />","")'>Zeilenumbruch</a>
			<a href='javascript:insert("<hr />","")'>horizontale Linie</a>
		</div>
		<textarea name="text" id="text"><?php echo $text ?></textarea><br />
		<input type="submit" value="senden">
	</div>
</form>