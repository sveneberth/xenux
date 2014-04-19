<!DOCTYPE html>
<html lang="de">
<head>
<title>Xenux Installation</title>
<meta charset="UTF-8" >
<link rel="stylesheet" type="text/css" href="install.css" />
</head>

<body>
	<div id="main">
		<div id="content">
			<div id="header">
				<span class="topic">Xenux</span></a><br />
				<span class="motto">das kostenlose CMS</span>
			</div>
			<ul id="steps">
				<li class="lastStep">Hallo</li>
				<li class="lastStep">Technische Voraussetztungen</li>
				<li class="lastStep">Datenbank</li>
				<li class="lastStep">Datenbank prüfen</li>
				<li class="lastStep">Homepage einrichten</li>
				<li class="lastStep">Administrator</li>
				<li class="actStep">Fertigstellung</li>
			</ul>
			<div id="install">
				<h2>Fertigstellung</h2>
				<p>Sie wurden erfolgreich registriert, somit wurde die Installation erfolgreich abgeschlossen!</p>
				<p>Sie können ihre Seite öffnen, sich einloggen und bearbeiten.</p>
				<p>Alle hier vorgenommenen Einstellungen können sie in der datei "config.php" ändern.</p>
				<p>Bitte löschen sie den Ordner "install", da sonst alle Einstellungen durch das erneute ausführen der Installation überschrieben werden.</p>
				<p>Viel Spaß!</p>
				<?php
				if(!empty($_POST['del'])) {
					unlink('index.php');
					unlink('install.css');
					$i = 2;
					while($i < '8'){
						unlink('step'.$i.'.php');
						$i++;
					}
					rmdir('../install/');
					echo 'der Order <i>install</i> wurde gelöscht!';
				}
				?>
				<form action="" method="post">
					<input type="submit" name="del" value="Ordner jetzt löschen" />
				</form>
				<div class="clear"></div>
				<a href="../" class="next">Weiter</a>
			</div>
			<div class="clear"></div>
		</div><!-- #content -->
	</div><!-- #main -->

</body>
</html>