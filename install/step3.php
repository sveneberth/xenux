<p>Nun müssen sie hier die Datenbank einrichten.</p>
	<form action="" method="post">
	Datenbank-Server<br />
	<input type="text" name="host" value="localhost" /><br /><br />
	Benutzer<br />
	<input type="text" name="username" value="" /><br /><br />
	Passwort<br />
	<input type="password" name="password" value="" /><br /><br />
	Datenbankname<br />
	<input type="text" name="dbname" value="" /><br /><br />
	Tabellen-Prefix<br />
	<input type="text" value="XENUX_" readonly /><br /><br />
	<input type="hidden" name="submit"  value="submit" />
	<input type="submit" value="speichern" />
</form>
<?php
if(isset($_POST["submit"])) {
	foreach($_POST as $key => $val) {
		$$key = $val;
	}
	if(empty($host) and empty($username) and empty($password) and empty($dbname)) {
		echo "<p>Sie müssen alle Felder ausfüllen!</p>";
	} else {
		$link = mysql_connect($host, $username, $password);
		$db_selected = mysql_select_db($dbname, $link);
		if(!$db_selected){
			echo "<p>Es ist keine Verbindung zu Datenbank möglich, bitte überprüfen sie ihre angaben!</p>";
		} else{
			echo "<p>Verbindung zur Datenbank erfolgreich!</p>";
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
			$erg = mysql_query($sql) or die("<p>Fehler! Es konnte die Tabelle \"XENUX_users\" nicht erstellt werden!</p>");
			//-----------------------------------------------------------------------------------------
			$sql = "CREATE TABLE IF NOT EXISTS `XENUX_pages` (
					`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`filename` VARCHAR(150) NOT NULL ,
					`fullname` VARCHAR(300) NOT NULL,
					`ansprechpartner` VARCHAR(150) NULL,
					`category` VARCHAR(150) NULL
					)";
			$erg = mysql_query($sql) or die("<p>Fehler! Es konnte die Tabelle \"XENUX_pages\" nicht erstellt werden!</p>");
			$sql = "INSERT INTO `XENUX_pages`(`filename`, `fullname`) VALUES
					('news', 'News'),
					('newslist', 'News'),
					('termine', 'Termine'),
					('terminview', 'Termin'),
					('home','Home'),
					('kontakt', 'Kontakt'),
					('impressum', 'Impressum')";
			$erg = mysql_query($sql) or die("<p>Fehler! Es konnten keine Daten in die Tabelle \"XENUX_pages\" eingetragen werden!</p>");
			//-----------------------------------------------------------------------------------------
			$sql = "CREATE TABLE IF NOT EXISTS `XENUX_news` (
					`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`title` text NOT NULL,
					`text` text NOT NULL)";
			$erg = mysql_query($sql) or die("<p>Fehler! Es konnte die Tabelle \"XENUX_news\" nicht erstellt werden!</p>");
			$sql = "INSERT INTO `XENUX_news` (`title`, `text`) VALUES
					('Xenux Installation', 'Xenux wurde erfolgreich installiert!')";
			$erg = mysql_query($sql) or die("<p>Fehler! Es konnten keine Daten in die Tabelle \"XENUX_news\" eingetragen werden!</p>");
			//-----------------------------------------------------------------------------------------
			$sql = "CREATE TABLE IF NOT EXISTS `XENUX_ansprechpartner` (
					`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`name` varchar(150) NOT NULL,
					`position` varchar(150) NOT NULL,
					`email` varchar(150) NOT NULL,
					`text` text NOT NULL);";
			$erg = mysql_query($sql) or die("<p>Fehler! Es konnte die Tabelle \"XENUX_ansprechpartner\" nicht erstellt werden!</p>");
			//-----------------------------------------------------------------------------------------
			$sql = "CREATE TABLE IF NOT EXISTS `XENUX_dates` (
					`id` int(10) NOT NULL AUTO_INCREMENT  PRIMARY KEY,
					`name` varchar(150) NOT NULL,
					`text` text NOT NULL,
					`date` timestamp NOT NULL
					);";
			$erg = mysql_query($sql) or die("<p>Fehler! Es konnte die Tabelle \"XENUX_dates\" nicht erstellt werden!</p>");
			$sql = "INSERT INTO XENUX_dates(name, text, date) VALUES
					('Installation von Xenux', 'Xenux ist ab sofort installiert und kann jetzt genutzt werden.', '".date("Y-m-d H:i:s", time())."')";
			$erg = mysql_query($sql) or die("<p>Fehler! Es konnten keine Daten in die Tabelle \"XENUX_dates\" eingetragen werden!</p>");
			//-----------------------------------------------------------------------------------------
			$sql = "CREATE TABLE IF NOT EXISTS `XENUX_main` (
					`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`name` varchar(150) NOT NULL,
					`value` text NOT NULL,
					`type` varchar(150) NOT NULL,
					`label` varchar(200) NOT NULL);";
			$erg = mysql_query($sql) or die("<p>Fehler! Es konnte die Tabelle \"XENUX_main\" nicht erstellt werden!</p>");
			$sql = "INSERT INTO `xenux_main` (`name`, `value`, `type`, `label`) VALUES
					('bgcolor', '#dddddd', 'color', 'Hintergrundfarbe'),
					('fontcolor', '#333333', 'color', 'Textfarbe'),
					('meta_auhor', 'Xenux', 'text', 'Autor (Meta-Tag)'),
					('hp_name', 'Meine Homepage', 'text', 'Homepagename'),
					('meta_desc', 'Hier die Beschreibung der Homepage, die in den Meta-Tags angezeigt wird', 'textarea', 'Beschreibung der Homepage(Meta-Tag)'),
					('meta_keys', 'Schlüsselwörter der Homepage, die in den Meta-Tags angezeigt werden', 'textarea', 'Schlüsselwörter Homepage (Meta-Tag)'),
					('contact_form_email', 'mail@me.com', 'email', 'E-Mail Adresse (für das Kontaktformular)'),
					('favicon_src', 'logo.ico', 'text', 'Link zum Favicon'),
					('logo_src', 'logo.png', 'text', 'Link zum Logo');";
			$erg = mysql_query($sql) or die("<p>Fehler! Es konnten keine Daten in die Tabelle \"XENUX_main\" eingetragen werden!</p>");//-----------------------------------------------------------------------------------------
			$sql = "CREATE TABLE IF NOT EXISTS `XENUX_form` (
					`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`type` varchar(150) NOT NULL,
					`label` text NOT NULL);";
			$erg = mysql_query($sql) or die("<p>Fehler! Es konnte die Tabelle \"XENUX_form\" nicht erstellt werden!</p>");
			
			echo '<p>Es wurden alle Tabellen erstellt!</p>';
			mysql_close($link);
			$next = true;
		}
	}
}
?>