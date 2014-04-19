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
			<li class="actStep">Datenbank</li>
			<li class="nextStep">Datenbank prüfen</li>
			<li class="nextStep">Homepage einrichten</li>
			<li class="nextStep">Administrator</li>
			<li class="nextStep">Fertigstellung</li>
		</ul>
		<div id="install">
			<h2>Datenbank</h2>
			<p>Nun müssen sie hier die Datenbank einrichten.</p>
				<form action="step4.php" method="post">
				Datenbank-Server<br />
				<input type="text" name="host" size="70" value="localhost" /><br /><br />
				Benutzer<br />
				<input type="text" name="username" size="70" value="" /><br /><br />
				Passwort<br />
				<input type="password" name="password" size="70" value="" /><br /><br />
				Datenbankname<br />
				<input type="text" name="dbname" size="70" value="" /><br /><br />
				Tabellen-Prefix<br />
				<input type="text" size="70" value="XENUX_" readonly />
				<div class="clear"></div>
				<input type="submit" name="submit" class="next" value="Weiter"/>
			</form>
		</div>
		<div class="clear"></div>
	</div><!-- #content -->
</div><!-- #main -->

</body>
</html>