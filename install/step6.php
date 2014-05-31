<p>Die Installation wurde erfolgreich abgeschlossen!</p>
<p>Sie können ihre Homepage öffnen, sich einloggen und bearbeiten.</p>
<p>Alle hier vorgenommenen Einstellungen können sie in der Datei "config.php" Sowie im Editroom unter "Grundeinstellungen" ändern.</p>
<p>Bitte löschen sie den Ordner "install", da sonst alle Einstellungen durch das erneute ausführen der Installation überschrieben werden. Nach dem löschen werden sie Autmatsch zur Startseite ihrer Homepage weiter geleitet.</p>
<p>Viel Spaß!</p>
<?php
if(!empty($_POST['del'])) {
	$dir = "./";
	if($handle = opendir($dir)) {
		while($file = readdir($handle)) {
			if(!is_dir($file)) {
				unlink($file);
			}
		}
		closedir($handle);
	}
	rmdir('../install/');
	header("Location: ../");
}
?>
<form action="" method="post">
	<input type="submit" name="del" value="Ordner jetzt löschen" />
</form>