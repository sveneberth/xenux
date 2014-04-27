<?php
SESSION_START();
function consolelog($value) {
	echo "<script>console.log('".$value."');</script>";
};
include('../config.php');
include('../core/colortext.php');
$link = mysql_connect($MYSQL_HOST, $MYSQL_BENUTZER, $MYSQL_KENNWORT);
$db_selected = mysql_select_db($MYSQL_DATENBANK, $link);
if(!$db_selected){
	echo 'Es ist keine Verbindung zur Datenbank möglich!';
	exit;
}
if ($_SESSION['login'] == 1) {
	$sql = "SELECT * FROM XENUX_users WHERE username='".$_SESSION["user"]['username']."'";
	$erg = mysql_query($sql);
	$login = mysql_fetch_array($erg);
}
if(!isset($_GET['site'])) {
	$site = 'home';
}elseif(empty($_GET['site'])){
	$site = 'home';
}else {
	$site = $_GET['site'];
}
if(!isset($_GET['site']) or !file_exists($_GET['site'].".html") or empty($_GET['site'])){
	$_GET['site'] = "editroom";
}
$all_sites = array(
					"editroom" => "Editroom",
					"login" => "Login",
					"logout" => "Logout",
					"registrieren" => "Registrieren",
					"forgotusername" => "Benutzername vergessen",
					"forgotpassword" => "Passwort vergessen",
					"neue_seite" => "Neue Seite erstellen",
					"seiten_tools" => "Seiten Tools",
					"zeige_menu" => "Menü anzeigen",
					"menu_bearbeiten" => "Menü bearbeiten",
					"news" => "News bearbeiten",
					"daten_aendern" => "Persönliche Daten ändern",
					"passwort_aendern" => "Passwort ändern",
					"rechte_anzeigen" => "Meine Rechte anzeigen",
					"rechte_aendern" => "Rechte ändern",
					"mail" => "Mail senden",
					"datei_hochladen" => "Datei Hochladen",
					"dateitools" => "Datei Tools",
					"freigabe" => "Freigabe"
					);
if (!array_key_exists($site, $all_sites)) {
	$site = "editroom";
}
$HP_URL = $_SERVER['SERVER_NAME'].substr($_SERVER['SCRIPT_NAME'],0,-14);
?>
<!Doctype html>
<html lang="de">
<head>
	<title><?php if(!empty($HP_Prefix)){echo $HP_Prefix." | ";}; echo $all_sites[$site]; if(!empty($HP_Sufix)){echo " | ".$HP_Sufix;}; ?></title>
	<meta charset="UTF-8" >
	<meta name="generator" content="Xenux - das kostenlose CMS">
	<meta name="robots" content="index, follow" />
	<link rel="stylesheet" type="text/css" href="../core/css/style.css" media="all"/>
	<script src="../core/js/formatierungen.js"></script>
</head>
<body>
<div id="wrapper1">
	<div id="header">
		<?php if($_SESSION["login"] == 1 and $site!='logout') {echo '<a class="green" href="./?site=logout">Logout</a>';} ?>
		<a class="yellow" href="./">Editroom</a>
		<a href="../"><span class="topic"><?php echo $HP_Name; ?></span></a><br />
		<span class="motto"><?php echo $HP_Slogan; ?></span>
	</div>
	<div id="content1">
		<h1><?php echo $all_sites[$site]; ?></h1>
		<?php
			if($_SESSION['login'] == 1 or $site == "forgotusername" or $site == "forgotpassword" or $site == "registrieren" or $site == "freigabe") {
				include($site.".html");
			} else {
				include("login.html");
			}
		?>
	</div>
</div>
</body>
</html>
<?php
mysql_close($link);
?>