<?php
SESSION_START();
if($_SESSION["login"] == 1 and !empty($_POST['session_delete'])) {
	$_SESSION = array();
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params["path"],
			$params["domain"], $params["secure"], $params["httponly"]
		);
	}
	session_destroy();
	$del = 'Du wurdest erfolgreich ausgelogt!<br />';
}
if(!file_exists('config.php')) {
	header('Location: ./install/index.php');
}
if(!isset($_GET['site'])) {
	$filename = 'home';
}elseif(empty($_GET['site'])){
	$filename = 'home';
}else{
	$filename = $_GET['site'];
}
include('config.php');
include('core/colortext.php');
$link = mysql_connect($MYSQL_HOST, $MYSQL_BENUTZER, $MYSQL_KENNWORT);
$db_selected = mysql_select_db($MYSQL_DATENBANK, $link);
if(!$db_selected) {
	echo 'Es ist keine Verbindung zur Datenbank möglich!';
}
$sql = "SELECT * FROM XENUX_pages WHERE filename = '".mysql_real_escape_string($filename)."'";
$erg = mysql_query($sql);
$row = mysql_fetch_assoc($erg);
$fullname = $row['fullname'];
mysql_free_result($erg);
if($fullname == '') {
	$filename = 'error';
	$fullname = 'Error 404 - Seite nicht gefunden';
}
if(isset($_POST["username"])) {
	$username = mysql_real_escape_string($_POST["username"]);
	$password = mysql_real_escape_string($_POST["password"]);
	$sql = "SELECT * FROM XENUX_users WHERE username='".$username."' AND pw='xkanf".md5($password)."v4sf5w' AND admin = 'yes' LIMIT 1";
	$res = mysql_query($sql);
	$anzahl = mysql_num_rows($res);
	$erg = mysql_fetch_array($res);
	if ($anzahl > 0) {
		$_SESSION["login"] = 1;
		$_SESSION["user"]['username'] = $erg['username'];
	} else{
		$result1 = "Deine Logindaten sind nicht korrekt, oder du wurdest noch nicht freigeschaltet.<br />";
	}
}
if ($_SESSION['login'] == 1) {
	$sql = "SELECT * FROM XENUX_users WHERE username='".$_SESSION["user"]['username']."'";
	$erg = mysql_query($sql);
	$login = mysql_fetch_array($erg);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
	<title><?php if(!empty($HP_Prefix)){echo $HP_Prefix." | ";}; echo $fullname; if(!empty($HP_Sufix)){echo " | ".$HP_Sufix;}; ?></title>
	<meta charset="UTF-8" />
	<meta name="language" content="de"/>
	<meta name="description" content="<?php echo $HP_Beschreibung; ?>" />
	<meta name="keywords" content="<?php echo $HP_Keywords; ?>" />
	<meta name="generator" content="Xenux - das kostenlose CMS" />
	<meta name="robots" content="index, follow" />
	<link rel="icon" href="core/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="core/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="core/css/style.css" media="all"/>
</head>
<body>
<div id="wrapper">
	<div id="header">
		<a class="green" href="./?site=impressum">Impressum</a>
		<a class="yellow" href="./?site=kontakt">Kontakt</a>
		<?php if ($_SESSION["login"] == 1){echo '<a class="red" href="./edit/">Editroom</a>';} ?>
		<a href="./"><span class="topic"><?php echo $HP_Name; ?></span></a><br />
		<span class="motto"><?php echo $HP_Slogan; ?></span>
	</div>

	<ul id='topmenu'>
		<li><a href='./'>Home</a></li>
		<?php
		$Menupoint = "1";
		while($Menupoint <= "8") {
			$Menuunder = "1";
			$query = "SELECT * FROM `XENUX_menu` WHERE `menupoint` = '".$Menupoint."' AND `menuunder` = '0'";
			$result = mysql_query($query);
			$row = mysql_fetch_assoc($result);
			$name = $row['name'];
			$href = $row['href'];
			if($name !='' and $href !='') {
				echo "<li><a href='./?site=".$href."'>".$name."</a><ul>";
				while($Menuunder <= "5") {
					$query = "SELECT * FROM `XENUX_menu` WHERE `menupoint` = '".$Menupoint."' AND `menuunder` = '".$Menuunder."'";
					$result = mysql_query($query);
					$row = mysql_fetch_assoc($result);
					$name = $row['name'];
					$href = $row['href'];
					if($name !='' and $href !='') {
					echo "<li><a href='./?site=".$href."'>".$name."</a></li>";}
					$Menuunder++;
				};
				echo "</ul></li>";
			};
			$Menupoint++;
		}
		?>
	</ul>
	<div id="rightbox">
		<div id="login-right">
			<h3>Login:</h3>
			<?php
			$result1 = "";
			if(isset($del)){
				echo $del;
			}
			if ($_SESSION["login"] == 0) {
				echo '<form action="" method="POST">';
				echo '<input type="text" name="username" placeholder="Benutzername"><br />';
				echo '<a href="edit/?site=forgotusername">Benutzernamen vergessen?</a><br />';
				echo '<input type="password" name="password" placeholder="Passwort"><br />';
				echo '<a href="edit/?site=forgotpassword">Passwort vergessen?</a><br />';
				echo $result1;
				echo '<input type="submit" name="submit" value="Einloggen"><br />';
				echo '<a href="edit/?site=registrieren">Registrieren</a>';
				echo '</form>';
				}else {
					$sql = "SELECT * FROM XENUX_users WHERE username='".$_SESSION["user"]['username']."'";
					$erg = mysql_query($sql);
					$login = mysql_fetch_object($erg);
					echo "Hallo $login->vorname, du bist erfolgreich eingeloggt!<br />";
					echo '<form action="" method="POST">';
					echo '<input type="submit" name="session_delete" value="Logout"><br />';
					echo '</form>';
				}
			?>
		</div>
		<ul id="pages-right">
			<span class="pages-right_topic">Neuste Seiten:</span>
			<?php
			$sql = "SELECT * FROM XENUX_pages ORDER BY id DESC LIMIT 5";
			$erg = mysql_query($sql);
			while ($zeile = mysql_fetch_array($erg)) {
				if($zeile['filename'] != 'news') {
					echo '<li><a href="?site='.$zeile['filename'].'">'.$zeile['fullname'].'</a></li>';
				}
			}
			mysql_free_result($erg);
			?>
		</ul>
		<ul id="news-right">
			<span class="news-right_topic">News:</span>
			<?php
			$sql = "SELECT * FROM XENUX_news";
			$erg = mysql_query($sql);
			while ($zeile = mysql_fetch_array($erg)) {
				$id = $zeile['id'];
				$title = $zeile['title'];
				$text = $zeile['text'];
				if($title != '' and $text != '') {
					echo '<li><span class="news-right_title">'.$title;
					if ($_SESSION["login"] == 1) {
						echo '<a id="edit_href" href="edit/?site=news&newspoint='.$id.'">Bearbeiten</a>';
					}
					echo '</span>';
					if(strlen($text) > 70) {
						echo substr($text, 0, strpos($text, " ", 70));
					}else {
						echo $text;
					}
					echo '... <a href="?site=news&id='.$id.'">weiterlesen</a></li>';
				}
			}
			mysql_free_result($erg);
			?>
		</ul>
	</div>
	<div id="content">
		<h1>
			<?php
			echo $fullname;
			if ($_SESSION["login"] == 1) {
				if($filename!='news' and $filename!='error') {
					echo '<a id="edit_href" href="edit/?site=seiten_tools&pagename='.$filename.'">Bearbeiten</a>';
				}
			}
			?>
		</h1>
		<?php
		include('core/pages/'.$filename.'.php');
		if($filename == 'kontakt' and !empty($HP_Kontaktemail)) {
			include ('core/kontakt_formular.php');
		}
		?>
	</div>
	<div id="footer">
	This Side was made with <a href="http://xenux.sven-eberth.bplaced.net/">Xenux</a>
	</div>
</div>
</body>
</html>
<?php
mysql_close($link);
?>