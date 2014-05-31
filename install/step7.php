<!DOCTYPE html>
<html lang="de">
<head>
<title>Xenux Installation</title>
<meta charset="UTF-8" >
<link rel="stylesheet" type="text/css" href="install.css" />
<style>
html,body{background-color:#ddd;font-family:Arial;font-size:17px;margin:0;padding:0;}.clear{clear:both;}h2{font-size:20px;color:#666666;border-bottom:1pxsolid#DADADA;padding:007px0;}#main{margin:30px5px5px;}#content{width:900px;margin:auto;background:#FFFFFF;border:1pxsolid#947D24;border-radius:8px;font-size:90%;line-height:1.5em;padding:10px;}#header{height:80px;padding:0px;width:100%}.topic{font-family:ComicSansMS;font-size:50px;color:#744520;position:relative;left:0px;top:20px;}.motto{font-family:ComicSansMS;font-size:15px;color:#8F2525;position:relative;left:40px;top:15px;}#steps{float:left;margin-left:-10px;width:20%;list-style-type:decimal;}#install{width:75%;float:right;}input,textarea{font-family:Arial;font-size:100%;border:#AAAAAA1pxsolid;color:#3A2B16;padding:2px3px;resize:none;}.next{text-decoration:none;color:#707070;font-size:130%;float:right;margin:15px30px10px0;background:#BBBBBB;border:1pxsolid#888;border-radius:3px;padding:3px5px;cursor:pointer;}.next:hover{background:#ddd;}
</style>
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