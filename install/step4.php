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
				<span class="topic">Xenux</span></a><br/>
				<span class="motto">das kostenlose CMS</span>
			</div>
			<ul id="steps">
				<li class="lastStep">Hallo</li>
				<li class="lastStep">Technische Voraussetztungen</li>
				<li class="lastStep">Datenbank</li>
				<li class="actStep">Datenbank prüfen</li>
				<li class="nextStep">Homepage einrichten</li>
				<li class="nextStep">Administrator</li>
				<li class="nextStep">Fertigstellung</li>
			</ul>
			<div id="install">
				<h2>Datenbank prüfen & Tabellen anlegen</h2>
				<p>
				<?php
				$submit = $_POST['submit'];
				$host = $_POST['host'];
				$username = $_POST['username'];
				$password = $_POST['password'];
				$dbname = $_POST['dbname'];
				if($submit=='Weiter') {
					if(empty($host) and empty($username) and empty($password) and empty($dbname)){
						echo 'Sie müssen alle Felder ausfüllen!<br/><a href="javascript:history.back()" class="last">Zurück</a><style>.next{display:none;}</style>';
					} else {
						$link = mysql_connect($host, $username, $password);
						$db_selected = mysql_select_db($dbname, $link);
						if(!$db_selected){
							echo "Es ist keine Verbindung zu Datenbank möglich, bitte überprüfen sie ihre angaben!";
							echo '<br/><a href="javascript:history.back()" class="last">Zurück</a><style>.next{display:none;}</style>';
						}else{
							echo 'Verbindung erfolgreich!';
							$dates_correct = 'true';
						}
					}
					if($dates_correct == 'true') { //Tabellen bauen
						$datei = fopen("../config.php","w");
						$text = '<?php
# In dieser Datei können sie alle Einstellungen ändern,
# die sie auch schon in der Installation vorgenommen haben!

# MySQL-Daten:
$MYSQL_HOST			= "'.$host.'"; # Servername
$MYSQL_BENUTZER		= "'.$username.'"; # Benutzername
$MYSQL_KENNWORT		= "'.$password.'"; # Kennwort
$MYSQL_DATENBANK	= "'.$dbname.'"; # Datenbankname
';
						fwrite($datei, $text);
						fclose($datei);
						$sql = "CREATE TABLE IF NOT EXISTS `XENUX_users` (
								`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
								`nachname` varchar(150) NOT NULL,
								`vorname` varchar(150) NOT NULL,
								`email` varchar(150) NOT NULL,
								`username` varchar(150) NOT NULL,
								`pw` varchar(100) NOT NULL,
								`admin` varchar(100) NOT NULL,
								`role` varchar(100) NOT NULL
								)";
						$db_erg = mysql_query($sql)
						or die('<br/>Fehler! Es konnte die Tabelle "XENUX_users" nicht erstellt werden!<br/><a href="javascript:history.back()" class="last">Zurück</a><style>.next{display:none;}</style>');
						//
						$sql = "CREATE TABLE IF NOT EXISTS `XENUX_pages` (
								`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
								`filename` VARCHAR(150) NOT NULL ,
								`fullname` VARCHAR(300) NOT NULL
								)";
						$db_erg = mysql_query($sql)
						or die('<br/>Fehler! Es konnte die Tabelle "XENUX_pages" nicht erstellt werden!<br/><a href="javascript:history.back()" class="last">Zurück</a><style>.next{display:none;}</style>');
						$sql = "INSERT INTO `XENUX_pages`(`filename`, `fullname`) VALUES
								('news', 'News'),
								('home','Home'),
								('kontakt', 'Konatkt'),
								('impressum', 'Impressum')";
						$db_erg = mysql_query($sql)
						or die('<br/>Fehler! Es konnten keine Daten in die Tabelle "XENUX_pages" eingetragen werden!<br/><a href="javascript:history.back()" class="last">Zurück</a><style>.next{display:none;}</style>');
						//
						$sql = "CREATE TABLE IF NOT EXISTS `XENUX_menu` (
								`menupoint` VARCHAR(10) NOT NULL ,
								`menuunder` VARCHAR(10) NOT NULL ,
								`href` VARCHAR(150),
								`name` VARCHAR(300)
								)";
						$db_erg = mysql_query($sql)
						or die('<br/>Fehler! Es konnte die Tabelle "XENUX_menu" nicht erstellt werden!<br/><a href="javascript:history.back()" class="last">Zurück</a><style>.next{display:none;}</style>');
						//
						$Menupoint = "1";
						while($Menupoint <= "8") {
							$Menuunder = "0";
							while($Menuunder <= "5") {
								$Wert = $Menupoint."_".$Menuunder;
								$sql = "INSERT INTO `XENUX_menu`(`menupoint`, `menuunder`, `href`, `name`)VALUES('".$Menupoint."', '".$Menuunder."', '', '');";
								$db_erg = mysql_query($sql)
								or die('<br/>Fehler! Es konnten keine Daten in die Tabelle "XENUX_menu" eingetragen werden!<br/><a href="javascript:history.back()" class="last">Zurück</a><style>.next{display:none;}</style>');
								$Menuunder++;
							};
							$Menupoint++;
						}
						//
						$sql = "CREATE TABLE IF NOT EXISTS `XENUX_news` (
								`id` text NOT NULL,
								`title` text NOT NULL,
								`text` text NOT NULL
								)";
						$db_erg = mysql_query($sql)
						or die('<br/>Fehler! Es konnte die Tabelle "XENUX_news" nicht erstellt werden!<br/><a href="javascript:history.back()" class="last">Zurück</a><style>.next{display:none;}</style>');
						$sql = "INSERT INTO `XENUX_news` (`id`, `title`, `text`) VALUES
								('1', '', ''),
								('2', '', ''),
								('3', '', ''),
								('4', '', ''),
								('5', '', ''),
								('6', '', '');";
						$db_erg = mysql_query($sql)
						or die('<br/>Fehler! Es konnten keine Daten in die Tabelle "XENUX_pages" eingetragen werden!<br/><a href="javascript:history.back()" class="last">Zurück</a><style>.next{display:none;}</style>');
						//
						
						
						echo '<p>Es wurden alle Tabellen erstellt!</p>';
						mysql_close($link);
					}
				} else {
				echo 'Sie müssen schon die Installation von vorne beginnen!<br/><a href="./" class="last">Zum Anfang</a><style>.next{display:none;}</style>';
				}
				?>
				</p>
				<a href="step5.php" class="next">Weiter</a>
			</div>
			<div class="clear"></div>
		</div><!-- #content -->
	</div><!-- #main -->

</body>
</html>