<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
?>
<p>Du bist bereigtigt zu ...</p>
<ul style="list-style-type:circle;">
	<?php
	//%FIXIT% rework roles and permissions
	if($login['role'] >= '0') {
		echo '<li>Seiten anzeigen</li>';
		echo '<li>Seiten erstellen</li>';
		echo '<li>Seiten bearbeiten</li>';
		echo '<li>News bearbeiten</li>';
		echo '<li>Termine bearbeiten</li>';
		echo '<li>Menü anzeigen</li>';
		echo '<li>Dateien hochladen</li>';
		echo '<li>Dateien anzigen/löschen</li>';
	}
	if($login['role'] >= '1') {
		echo '<li>Menü bearbeiten</li>';
		echo '<li>Ansprechpartner bearbeiten</li>';
	}
	if($login['role'] == '2') {
		echo '<li>Rechte ändern (außer eigene/zuweisen eigener Rolle)</li>';
	}
	if($login['role'] >= '2') {
		echo '<li>Mails versenden</li>';
	}
	if($login['role'] >= '3') {
		echo '<li>Rechte ändern</li>';
	}
	?>
</ul>