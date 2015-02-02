<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if($login->role < 2) {
	echo '<p>Du bist nicht berechtigt, diese Seite zu öffnen!</p>';
	return false;
}

if(isset($post->submit_mail)) {
	if(isset($post->to) && !empty($post->subject) && !empty($post->message)) {
		$mailtxt = 
"<!DOCTYPE html>
<html>
	<head>
		<meta charset=\"utf-8\"/>
		<title>{$_POST['subject']}</title>
	</head>
	<body>".
		nl2br($_POST['message'])."
		<br /><br />
		<span style=\"font-family:Verdana;color:#777;border-top: 1px #777 solid;\">Diese E-Mail wurde mit Xenux erstellt</span>
	</body>
</html>";
		$header		 = "From: \"$login->firstname $login->lastname\"<$login->email> \r\n";
		$header		.= 'MIME-Version: 1.0' . "\r\n";
		$header		.= 'Content-type: text/html; charset=utf-8' . "\r\n";
		if($_POST['to'][0] == '%alle%') {
			$result = $db->query("SELECT * FROM users;");
			while($row = $result->fetch_object()) {
				mail($row->email, $post->subject, $mailtxt, $header);
			}
		} else {
			foreach($_POST['to'] as $val) {
				$result = $db->query("SELECT * FROM XENUX_users WHERE username = '$val' LIMIT 1;");
				$num = $result->num_rows;
				if($num > 0) {
					$row = $result->fetch_object();
					mail($row1->email, $post->subject, $mailtxt, $header);
				} else {
					echo '<p>Es existiert kein Account mit dem Benutzernamen <i>'.$post->to.'</i>!';
				//	echo '<br /><a href="javascript:history.back()">Zurück</a>';
				//	return false;
				}
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

<script>
				$(document).ready(function() {
					CKEDITOR.replace('ckeditor', {
						toolbar: [
							{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
							{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', '-', 'Undo', 'Redo' ] },
							{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll' ] },
							'/',
							{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
							{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
							{ name: 'insert', items: [ 'Image',  'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak' ] },
							'/',
							{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
							{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
							{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
						]
					});
				});
				</script>
<p>Hier kannst du eine Mail senden.</p>
<form action="" method="post" name="form">
	<select name="to[]" size="4" multiple placeholder="Empfänger">
		<option value="%alle%">An Alle</option>
		<option disabled>----------</option>
		<?php
		$result = $db->query("SELECT * FROM XENUX_users ORDER by firstname ASC;");
		while($row = $result->fetch_object()) {
			echo "<option value=\"$row->username\">$row->firstname $row->lastname</option>";
		}
		?>
	</select>
	<input type="text" placeholder="Betreff" name="subject" value="<?php echo @$post->subject; ?>" />
	<textarea name="message" placeholder="Nachricht" id="ckeditor" class="ckeditor nolabel"><?php echo htmlentities(@$post->message); ?></textarea>
	
	<input type="hidden" name="submit_mail" value="senden">
	<input type="submit" value="senden">
</form>