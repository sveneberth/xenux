<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
?>
<p>Du bist berechtigt, folgendes zu tun ...</p>
<ul style="list-style-type:circle;">
	<?php
	if($login->role >= 0) {
		echo '<li>Seiten bearbeiten</li>';
		echo '<li>News bearbeiten</li>';
		echo '<li>Termine bearbeiten</li>';
		echo '<li>Dateien verwalten</li>';
	}
	if($login->role >= 1) {
		echo '<li>Ansprechpartner bearbeiten</li>';
		echo '<li>Mails versenden</li>';
	}
	if($login->role >= 2) {
		echo '<li>Rechte ändern</li>';
		echo '<li>Homepage Einstellungen ändern</li>';
	}
	if($login->role == 3) {
		echo '<li>alle Root-Rechte</li>';
	}
	?>
</ul>