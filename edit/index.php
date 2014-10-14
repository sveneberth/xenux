<?php
include_once('../core/inc/config.php'); // include config

if(!isset($_GET['site'])) { //read the site
	$site = 'editroom';
} elseif(empty($_GET['site'])) {
	$site = 'editroom';
} else {
	$site = $db->real_escape_string($_GET['site']);
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
										"event_edit" => "Termine bearbeiten",
										"files" => "Dateien",
										"contact" => "Ansprechpartner",
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
						"register" => "Registrieren",
						"forgotusername" => "Benutzername vergessen",
						"forgotpassword" => "Passwort vergessen",
						"confirm" => "Freigabe",
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
if(!array_key_exists($site, $sites)) {
	$site = "editroom";
}

define('BASEURL', $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].substr($_SERVER['SCRIPT_NAME'],0,-14));
?>
<!Doctype html>
<html lang="de">
<head>
	<title><?php echo "$main->hp_name Administration | ".$sites[$site]; ?></title>
	<meta charset="UTF-8" >
	<meta name="description" content="<?php echo $main->meta_desc; ?>" />
	<meta name="keywords" content="<?php echo $main->meta_keys; ?>" />
	<meta name="auhor" content="<?php echo $main->meta_auhor; ?>" />
	<meta name="publisher" content="<?php echo $main->meta_auhor; ?>" />
	<meta name="copyright" content="<?php echo $main->meta_auhor; ?>" />
	<!-- http://xenux.bplaced.net -->
	<meta name="generator" content="Xenux - das kostenlose CMS" />
	<meta name="robots" content="noindex, nofollow, noarchive" />
	<link rel="stylesheet" type="text/css" href="../core/css/style.css" media="all"/>
	<link rel="shortcut icon" href="../core/images/<?php echo $main->favicon_src; ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<script src="../core/js/jquery-2.1.1.min.js"></script>
	<script src="../core/js/jquery-migrate-1.2.1.min.js"></script>
	<script src="../core/js/jquery-ui.js"></script>
	<script src="../core/js/jquery.cookie.js"></script>
	<script src="../core/ckeditor/ckeditor.js"></script>
	<script src="../core/js/colResizable-1.3.min.js"></script>
	<script src="https://code-snippets-se.googlecode.com/git/functions.js"></script>
	<script src="../core/js/main.js"></script>
	<style>
	html, body {
		background: <?php echo $main->bgcolor; ?>;
		color: <?php echo $main->fontcolor; ?>;
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
	<div class="headWrapper">
		<header> 
			<div class="logo">
				<a href="../">
					<img src="../core/images/<?php echo $main->logo_src;; ?>" />
				</a>
			</div>
			<ul id="topmenu" class="mobilemenu">
				<li><a href="javascript:openmobilemenu()">Menu</a></li>
				<?php
				if(isset($login)) {
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
						echo "<li><a>$key</a><ul>";
						foreach($val as $key => $val) {
							echo "<li><a href=\"./?site=$key\">$val</a></li>";
						}
						echo "</ul></li>";
					};
				}
				?>
			</ul>
		</header>
	</div>
	<div class="wrapper">
		<div class="fontsize">
			Schrift
			&nbsp;<a title="Schrift kleiner" href="javascript:fontsizedecrease()">-</a>
			&nbsp;<a title="Schrift normal" href="javascript:fontsizereset()">O</a>
			&nbsp;<a title="Schrift größer" href="javascript:fontsizerecrease()">+</a>
		</div>
		<main style="width: calc(100% - 10px);float:none;">
			<h1><?php echo $sites[$site]; ?></h1>
			<?php
				if(isset($_GET['id'])) {
					echo "<a style=\"float: right;\" href=\"./?site=$site\">zur Auswahl</a>";
				}
				if(isset($login) or $site == "forgotusername" or $site == "forgotpassword" or $site == "register" or $site == "confirm") {
					include($site.".php");
				} else {
					include("login.php");
				}
			?>
		</main>
		
		<footer>
			This Side was made with <a href="http://xenux.bplaced.net">Xenux</a>
			<div class="href">
				<a href="../">Homepage</a>
				<a href="../?site=kontakt">Kontakt</a>
				<a href="../?site=impressum">Impressum</a>
			</div>
		</footer>
	</div>
</body>
</html>
<?php
$db->close(); //close the connection to the db
?>