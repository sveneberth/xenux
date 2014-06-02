<?php
SESSION_START();
function logger($value) {
	echo "<script>console.log('".$value."');</script>";
};
include('../config.php');
include('../core/macros/colortext.php');
include('../core/macros/escape_mail.php');
$link = mysql_connect($MYSQL_HOST, $MYSQL_BENUTZER, $MYSQL_KENNWORT);
$db_selected = mysql_select_db($MYSQL_DATENBANK, $link);
if(!$db_selected){
	echo 'Es ist keine Verbindung zur Datenbank möglich!';
	exit;
}
mysql_query('SET NAMES "utf8"');
if(@$_SESSION['login'] == 1) {
	$sql = "SELECT * FROM XENUX_users WHERE id = '".$_SESSION["userid"]."'";
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
if(!isset($_GET['site']) or !file_exists($_GET['site'].".php") or empty($_GET['site'])){
	$_GET['site'] = "editroom";
}
$all_sites = array(
					"editroom" => "Editroom",
					"Seiten" => array (
										"site_new" => "Neue Seite erstellen",
										"site_edit" => "Seiten bearbeiten",
										"mainsettings" => "Grundeinstellungen",
										),
					"Sonstiges" => array (
										"news_edit" => "News bearbeiten",
										"dates_edit" => "Termine bearbeiten",
										"files" => "Dateien",
										"contact" => "Ansprechpartner",
										"form" => "Formular",
										),
					"Account" => array (
										"personal_data_change" => "Persönliche Daten ändern",
										"password_change" => "Passwort ändern",
										"rights_show" => "Meine Rechte anzeigen",
										"rights_edit" => "Rechte ändern",
										"mail" => "Mail senden",
										"logout" => "Logout",
										),
					/* Login etc */
						"login" => "Login",
						"registrieren" => "Registrieren",
						"forgotusername" => "Benutzername vergessen",
						"forgotpassword" => "Passwort vergessen",
						"freigabe" => "Freigabe",
						"delete_acc" => "Account löschen",
					/* Login etc */
					);
$sites = array();
foreach($all_sites as $key => $val) {
	if(is_array($val)) {
		foreach($val as $key => $val) {
			$sites[$key] = $val;
		}
	} else {
		$sites[$key] = $val;
	}
}
if (!array_key_exists($site, $sites)) {
	$site = "editroom";
}
$HP_URL = $_SERVER['SERVER_NAME'].substr($_SERVER['SCRIPT_NAME'],0,-14);
$sql = "SELECT * FROM XENUX_main";
$erg = mysql_query($sql);
while($row = mysql_fetch_array($erg)) {
	foreach($row as $key => $val) {
		$$key = $val;
	}
	$$name = $value;
}
?>
<!Doctype html>
<html lang="de">
<head>
	<title><?php if(!empty($HP_Prefix)){echo $HP_Prefix." | ";}; echo $sites[$site]; if(!empty($HP_Sufix)){echo " | ".$HP_Sufix;}; ?></title>
	<meta charset="UTF-8" >
	<meta name="generator" content="Xenux - das kostenlose CMS">
	<meta name="robots" content="index, follow" />
	<link rel="stylesheet" type="text/css" href="../core/css/style.css" media="all"/>
	<link rel="shortcut icon" href="../core/images/<?php echo $favicon_src; ?>" />
	<script src="../core/js/formatierungen.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<script src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
	<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	<script src="../core/js/main.js"></script>
	<style>
	html,body{
		background:<?php echo $bgcolor; ?>;
		color:<?php echo $fontcolor; ?>;
	}
	</style>
</head>
<body>
<script>
$(window).bind('keydown', function(event) {
	if (event.ctrlKey || event.metaKey) {
		switch (String.fromCharCode(event.which).toLowerCase()) {
		case 's':
			event.preventDefault();
			document.forms[0].submit();
			break;
		}
	}
});
</script>
<div id="headWrapper">
		<div id="head"> 
			<div class="logo">
				<a href="../">
					<img src="../core/images/<?php echo $logo_src; ?>" />
				</a>
			</div>
			<ul id="topmenu" class="mobilemenu">
				<li><a href="javascript:openmobilemenu()">Menu</a></li>
				<?php
				if(@$_SESSION['login'] == 1) {
				?>
					<li><a href="?site=logout">Logout</a></li>
				<?php
				} else {
				?>
				<li><a href="?site=login">Login</a></li>
				<?php
				}
				?>
			</ul>
			<ul id="topmenu" class="mainmenu">
				<li><a href="./">Editroom</a></li>
				<?php
				foreach($all_sites as $key => $val) {
					if(is_array($val)) {
						echo "<li><img src=\"../core/images/right.png\" class=\"".strtolower(preg_replace("/[^a-zA-Z0-9_]/" , "" , $key))." openpoints\" onclick=\"javascript:openmenupoints('".strtolower(preg_replace("/[^a-zA-Z0-9_]/" , "" , $key))."')\"><a>$key</a><ul id=\"".strtolower(preg_replace("/[^a-zA-Z0-9_]/" , "" , $key))."\">";
						foreach($val as $key => $val) {
							echo "<li><a href=\"./?site=$key\">$val</a></li>";
						}
						echo "</ul></li>";
					};
				}
				?>
			</ul>
		</div>
	</div>
<div id="wrapper">
	<div id="content" style="width: calc(100% - 10px);float:none;">
		<h1><?php echo $sites[$site]; ?></h1>
		<?php
			if(isset($_GET['id'])) {
				echo "<a style=\"float: right;\" href=\"./?site=$site\">zur Auswahl</a>";
			}
			if(@$_SESSION['login'] == 1 or $site == "forgotusername" or $site == "forgotpassword" or $site == "registrieren" or $site == "freigabe") {
				include($site.".php");
			} else {
				include("login.php");
			}
		?>
	</div>
	
	<div id="footer">
		This Side was made with <a href="http://xenux.sven-eberth.bplaced.net/">Xenux</a>
		<div class="href">
			<a href="../">Homepage</a>
			<a href="../?site=kontakt">Kontakt</a>
			<a href="../?site=impressum">Impressum</a>
		</div>
	</div>
</div>
</body>
</html>
<?php
mysql_close($link);
?>