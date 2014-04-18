<?php
include('../config.php')
?>
<!DOCTYPE html>
<html lang="de">
<head>
	<title>Xenux Installation</title>
	<meta charset="UTF-8" >
	<link rel="stylesheet" type="text/css" href="install.css" />
	<?php
	function nextpage(){
	echo '<meta http-equiv="refresh" content="0; URL=step7.php">';
	}
	?>
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
				<li class="lastStep">Datenbank prüfen</li>
				<li class="lastStep">Homepage einrichten</li>
				<li class="actStep">Administrator</li>
				<li class="nextStep">Fertigstellung</li>
			</ul>
			<div id="install">
				<h2>Hauptadministrator einrichen</h2>
				<?php
				$username_exist = false;
				$email_exist = false;
				$pw_stimmt = true;
				$link = mysql_connect($MYSQL_HOST, $MYSQL_BENUTZER, $MYSQL_KENNWORT);
				$db_selected = mysql_select_db($MYSQL_DATENBANK, $link);
				if(!$db_selected){
					echo 'Es ist keine Verbindung zur Datenbank möglich!';
					exit;
				}
				if(!isset($_POST['submit'])) {
					$FirstName	= "";
					$LastName	= "";
					$eMail		= "";
					$username	= "";
					$password	= "";
					$password1	= "";
				}else{
					$FirstName	= mysql_real_escape_string($_POST['FirstName']);
					$LastName	= mysql_real_escape_string($_POST['LastName']);
					$eMail		= mysql_real_escape_string($_POST['eMail']);
					$username	= mysql_real_escape_string($_POST['username']);
					$password	= mysql_real_escape_string($_POST['password']);
					$password1	= mysql_real_escape_string($_POST['password1']);

					if($password == $password1) {
						$pw_stimmt=true;
					} else {
						$pw_stimmt=false;
					}

					if(!empty($FirstName) and !empty($LastName) and !empty($eMail) and !empty($username) and !empty($password) and $pw_stimmt){
						$sql = "SELECT COUNT(username) AS anzahl FROM XENUX_users WHERE username = '".mysql_real_escape_string($username)."'";
						$erg = mysql_query($sql);
						$var = mysql_fetch_object($erg);
						if($var->anzahl >= 1) {
							$username_exist = true;
						} else {
							$username_exist = false;
						}
						$sql = "SELECT COUNT(email) AS anzahl FROM XENUX_users WHERE email = '".mysql_real_escape_string($eMail)."'";
						$erg = mysql_query($sql);
						$var = mysql_fetch_object($erg);
						if($var->anzahl >= 1) {
							$email_exist = true;
						} else {
							$email_exist = false;
						}
						if(!$username_exist and !$email_exist) {
							$sql = "INSERT INTO `XENUX_users` (`id`, `nachname`, `vorname`, `email`, `username`, `pw`, `admin`, `role`) VALUES (NULL, '".$LastName."', '".$FirstName."', '".$eMail."', '".$username."', 'xkanf".md5($password)."v4sf5w', 'yes', '3');";
							$db_erg = mysql_query($sql) 
								or die("Anfrage fehlgeschlagen.");
							echo '<br/>Sie wurden erfolgreich registriert!';
							nextpage();
						}
						mysql_close($link);
					}
				}
				?>
				<form action="" method="post">
					<span <?php if (empty($FirstName) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Vorname</span><br/>
					<input type="text" name="FirstName" size="70" value="<?php echo $FirstName; ?>" /><br/><br/>
					<span <?php if (empty($LastName) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Nachname</span><br/>
					<input type="text" name="LastName" size="70" value="<?php echo $LastName; ?>" /><br/><br/>
					<span <?php if (empty($eMail) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>E-Mail</span><br/>
					<input type="email" name="eMail" size="70" value="<?php echo $eMail; ?>" /><br/>
					<?php
					if($email_exist){echo 'Ein Account mit dieser E-Mail-Adresse existiert schon, zwei Accounts über eine E-Mail Adresse sind nicht zulässig!<br/>';}
					?><br/>
					<span <?php if (empty($username) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Benutzername</span><br/>
					<input type="text" name="username" size="70" value="<?php echo $username; ?>" /><br/>
					<?php
					if($username_exist){echo 'Der Benutzername ist schon vergeben, bitte wähle einen anderen!<br/>';}
					?><br/>
					<span <?php if (empty($password) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Passwort</span><br/>
					<input type="password" name="password" size="70" value="<?php echo $password; ?>" /><br/><br/>
					<span <?php if (empty($password1) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Passwort bestätigen</span><br/>
					<input type="password" name="password1" size="70" value="<?php echo $password1; ?>" /><br/>
					<?php
					if(!$pw_stimmt){echo 'Die angegebenen Passwörter stimmen nicht überein!<br/>';}
					?>
					<input type="submit" name="submit" class="next" value="Weiter"/>
				</form>
			</div>
			<div class="clear"></div>
		</div><!-- #content -->
	</div><!-- #main -->

</body>
</html>