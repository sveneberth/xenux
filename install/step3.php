<p>Nun müssen sie hier die Datenbank einrichten.</p>
	<form action="" method="post">
	Datenbank-Server<br />
	<input <?php if(empty(@$post->host) && isset($post->host)) echo 'class="wrong"'; ?> type="text" name="host" value="localhost" /><br /><br />
	Benutzer<br />
	<input <?php if(empty(@$post->username) && isset($post->username)) echo 'class="wrong"'; ?> type="text" name="username" value="" /><br /><br />
	Passwort<br />
	<input <?php if(empty(@$post->password) && isset($post->password)) echo 'class="wrong"'; ?> type="password" name="password" value="" /><br /><br />
	Datenbankname<br />
	<input <?php if(empty(@$post->dbname) && isset($post->dbname)) echo 'class="wrong"'; ?> type="text" name="dbname" value="" /><br /><br />
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
		$db = new MySQLi($host, $username, $password, $dbname); // connect with database
		if($db->connect_errno) { // if conntection failed
			echo "<p>Es ist keine Verbindung zu Datenbank möglich, bitte überprüfen sie ihre angaben!</p>";
			return;
		} else {
			echo "<p>Verbindung zur Datenbank erfolgreich!</p>";
			$db->query("SET NAMES 'utf8'"); // define database as utf-8
			$file = fopen("../mysql.conf","w");
			$text = "<?php
define('MYSQL_HOST',	'$host');
define('MYSQL_USER',	'$username');
define('MYSQL_PW',		'$password');
define('MYSQL_DB',		'$dbname');
?>";
			fwrite($file, $text);
			fclose($file);
			
			/* ##############################
			** create tables
			** ##############################*/
			// table
			$result = $db->query	("	DROP TABLE IF EXISTS `XENUX_users`;");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			$result = $db->query	("	CREATE TABLE `XENUX_users` (
											`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
											`lastname` varchar(200) NOT NULL,
											`firstname` varchar(200) NOT NULL,
											`email` varchar(200) NOT NULL,
											`username` varchar(200) NOT NULL,
											`password` varchar(200) NOT NULL,
											`confirmed` tinyint(1) NOT NULL DEFAULT '0',
											`role` int(2) NOT NULL DEFAULT '1',
											`verifykey` varchar(200) NOT NULL
										);
									");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			#############################################################################
			// table
			$result = $db->query	("	DROP TABLE IF EXISTS `XENUX_sites`;");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			$result = $db->query	("	CREATE TABLE `XENUX_sites` (
											`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
											`site` varchar(150) NOT NULL,
											`title` varchar(300) NOT NULL,
											`text` text NOT NULL,
											`create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
											`parent_id` int(10) NOT NULL,
											`position_left` int(10) NOT NULL
										);
									");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			// data
			$result = $db->query	("	INSERT INTO `XENUX_sites` (`site`, `title`, `text`) VALUES
											('home', 'Home', 'Die Installation von Xenux hat geklappt!\nIch wünsche ihnen viel Spaß bei der Nutzung von Xenux'),
											('contact', 'Kontakt', ''),
											('imprint', 'Impressum', '<p>Eine gute Seite zum erstellen eines Impressum: <a href=\"http://www.e-recht24.de/impressum-generator.html\">www.e-recht24.de</a></p>'),
											('news_view', 'News', ''),
											('news_list', 'News', 'alle News im Überblick'),
											('event_view', 'Termin', ''),
											('event_list', 'Termine', 'alle Termine im Überblick'),
											('page', 'Page', ''),
											('search', 'Suchergebnisse', '');
									");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			#############################################################################
			// table
			$result = $db->query	("	DROP TABLE IF EXISTS `XENUX_news`;");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			$result = $db->query	("	CREATE TABLE IF NOT EXISTS `XENUX_news` (
											`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
											`title` varchar(200) NOT NULL,
											`text` text NOT NULL,
											`create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
										);
									");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			// data
			$result = $db->query	("	INSERT INTO `XENUX_news` (`title`, `text`) VALUES
											('Xenux Installation', 'Xenux wurde erfolgreich installiert!');
									");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			#############################################################################
			// table
			$result = $db->query	("	DROP TABLE IF EXISTS `XENUX_dates`;");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			$result = $db->query	("	CREATE TABLE IF NOT EXISTS `XENUX_dates` (
											`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
											`name` varchar(150) NOT NULL,
											`text` text NOT NULL,
											`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
										);
									");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			// data
			$result = $db->query	("	INSERT INTO XENUX_dates(name, text, date) VALUES
											('Installation von Xenux', 'Xenux ist ab sofort installiert und kann jetzt genutzt werden.', NOW());
									");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			#############################################################################
			// table
			$result = $db->query	("	DROP TABLE IF EXISTS `XENUX_main`;");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			$result = $db->query	("	CREATE TABLE IF NOT EXISTS `XENUX_main` (
											`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
											`name` varchar(150) NOT NULL,
											`value` text NOT NULL,
											`type` varchar(150) NOT NULL,
											`label` varchar(200) NOT NULL
										);
									");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			// data
			$result = $db->query	("	INSERT INTO `XENUX_main` (`name`, `value`, `type`, `label`) VALUES
										('bgcolor', '#dddddd', 'color', 'Hintergrundfarbe'),
										('fontcolor', '#333333', 'color', 'Textfarbe'),
										('meta_auhor', 'Xenux', 'text', 'Autor (Meta-Tag)'),
										('hp_name', 'Meine Homepage', 'text', 'Homepagename'),
										('meta_desc', 'Hier die Beschreibung der Homepage, die in den Meta-Tags angezeigt wird', 'textarea', 'Beschreibung der Homepage(Meta-Tag)'),
										('meta_keys', 'Schlüsselwörter der Homepage, die in den Meta-Tags angezeigt werden', 'textarea', 'Schlüsselwörter Homepage (Meta-Tag)'),
										('contact_form_email', 'mail@me.com', 'email', 'E-Mail Adresse (für das Kontaktformular)'),
										('favicon_src', '/core/images/logo.ico', 'text', 'Link zum Favicon'),
										('logo_src', '/core/images/logo.png', 'text', 'Link zum Logo'),
										('reply_email', 'noreply@localhost', 'email', 'E-Mail Adresse als Absender (bei Registrierungen o.Ä.')');
								");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			#############################################################################
			// table
			$result = $db->query	("	DROP TABLE IF EXISTS `XENUX_contactpersons`;");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			$result = $db->query	("	CREATE TABLE IF NOT EXISTS `XENUX_contactpersons` (
											`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
											`name` varchar(150) NOT NULL,
											`position` varchar(150) NOT NULL,
											`email` varchar(150) NOT NULL,
											`text` text NOT NULL
											);
									");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			#############################################################################
			// table
			$result = $db->query	("	DROP TABLE IF EXISTS `XENUX_site_contactperson`;");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			$result = $db->query	("	CREATE TABLE IF NOT EXISTS `XENUX_site_contactperson` (
											`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
											`site_id` int(11) NOT NULL,
											`contactperson_id` int(11) NOT NULL
										);
									");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			#############################################################################
			// table
			$result = $db->query	("	DROP TABLE IF EXISTS `XENUX_files`;");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			$result = $db->query	("		CREATE TABLE `XENUX_files` (
												`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
												`type` varchar(50) NOT NULL,
												`mime_type` varchar(200) DEFAULT NULL,
												`data` longblob,
												`filename` varchar(200) DEFAULT NULL,
												`size` int(20) NOT NULL,
												`lastModified` timestamp NULL DEFAULT NULL,
												`parent_folder_id` int(10) NOT NULL
											);
									");
			if (!$result) {
				printf("Errormessage: %s\n", $db->error);
			}
			
			echo '<p>Es wurden alle Tabellen erstellt!</p>';
			$db->close(); //close the connection to the db
			$next = true;
		}
	}
}
?>