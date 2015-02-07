<?php
$index = 'backend';
include_once('../core/inc/config.php'); // include config

if(!isset($_GET['site'])) { //read the site
	$site = 'editroom';
} elseif(empty($_GET['site'])) {
	$site = 'editroom';
} else {
	$site = $db->real_escape_string($_GET['site']);
}

$all_sites = array
(
	"editroom"	=> "Editroom",
	"Seiten"	=> array	(
								"site_edit" => "Seiten bearbeiten",
								"mainsettings" => "Grundeinstellungen",
							),
	"Sonstiges"	=> array	(
								"news_edit" => "News bearbeiten",
								"event_edit" => "Termine bearbeiten",
								"files" => "Dateien",
								"contact" => "Ansprechpartner",
							),
	"Account"	=> array	(
								"personal_data_change" => "Persönliche Daten ändern",
								"password_change" => "Passwort ändern",
								"rights_show" => "Meine Rechte anzeigen",
								"rights_edit" => "Rechte ändern",
								"mail" => "Mail senden",
								"logout" => "Logout",
							),
	/* login etc */
	"login"				=> "Login",
	"register"			=> "Registrieren",
	"forgotusername" 	=> "Benutzername vergessen",
	"forgotpassword"	=> "Passwort vergessen",
	"confirm"			=> "Freigabe",
	"delete_acc"		=> "Account löschen",
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
ob_start();
?>
<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="robots" content="index, follow, noarchive" />

	<title><?php echo $sites[$site]." | $main->hp_name Administration"; ?></title>

	<meta name="description" content="<?php echo $main->meta_desc; ?>" />
	<meta name="keywords" content="<?php echo $main->meta_keys; ?>" />
	<meta name="auhor" content="<?php echo $main->meta_auhor; ?>" />
	<meta name="publisher" content="<?php echo $main->meta_auhor; ?>" />
	<meta name="copyright" content="<?php echo $main->meta_auhor; ?>" />

	<!-- http://xenux.bplaced.net -->
	<meta name="generator" content="Xenux - das kostenlose CMS" />

	<link rel="shortcut icon" href="<?php echo (substr($main->favicon_src, 0, 1)=='/') ? '..'.$main->favicon_src : $main->favicon_src; ?>" />
	
	<!-- css -->
	<link rel="stylesheet" type="text/css" href="../core/css/style.css" media="all"/>
	
	<!-- jquery + plugins + ckeditor -->
	<script src="../core/js/jquery-2.1.1.min.js"></script>
	<script src="../core/js/jquery-migrate-1.2.1.min.js"></script>
	<script src="../core/js/jquery-ui.js"></script>
	<script src="../core/js/jquery.ui.touch-punch.min.js"></script>
	<script src="../core/js/jquery.cookie.js"></script>
	<script src="../wysiwyg/ckeditor.js"></script>
	<script src="../core/js/jquery.mjs.nestedSortable.js"></script>

	<!-- scripts -->
	<script src="../core/js/functions.js?from=https://code-snippets-se.googlecode.com/"></script>
	<script src="../core/js/main.js"></script>
	
	<style>
		html, body {
			background: <?php echo $main->bgcolor; ?>;
			color: <?php echo $main->fontcolor; ?>;
		}
	</style>
</head>
<body id="top">
	<script>
	$(window).bind('keydown', function(event) {
		if (event.ctrlKey || event.metaKey) {
			switch (String.fromCharCode(event.which).toLowerCase()) {
			case 's':
				event.preventDefault();
				$(window).off('beforeunload');
				document.forms[0].submit();
				break;
			}
		}
	});
	</script>
	<div class="headWrapper">
		<header>
			<a href="javascript:openmobilemenu();" class="menu-icon"></a>
			<a class="logo" href="../">
				<img src="<?php echo (substr($main->logo_src, 0, 1)=='/') ? '..'.$main->logo_src : $main->logo_src; ?>" class="nojsload" />
			</a>
			<ul class="topmenu mobilemenu">
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
			<ul class="topmenu mainmenu">
				<li>
					<a href="./">Editroom</a>
				</li>
<?php
				foreach($all_sites as $key => $val) {
					if(is_array($val)) {
						echo "\n<li>\n\t<a>$key</a>\n\t<ul>";
						foreach($val as $key => $val) {
							echo "\n\t\t<li>\n\t\t\t<a href=\"./?site=$key\">".nbsp($val)."</a>\n\t\t</li>";
						}
						echo "\n\t</ul>\n</li>";
					};
				}
				?>
			</ul>
		</header>
	</div>
	<div class="wrapper">
		<noscript>
			<div class="warning-noscript">
				<div>
					In deinem Browser ist JavaScript deaktiviert. Um den vollen Funktionsumfang dieser Webseite zu nutzen, benötigst du JavaScript.
				</div>
			</div>
		</noscript>
		<div class="fontsize">
			&nbsp;<a title="Schrift kleiner" class="decrease"></a>
			&nbsp;<a title="Schrift normal" class="reset"></a>
			&nbsp;<a title="Schrift größer" class="recrease"></a>
		</div>
		<main style="width:100%;float:none;">
			<h1 class="page-headline"><?php echo $sites[$site]; ?></h1>
			<?php
				if(isset($_GET['backbtn'])) {
					echo "<a style=\"float: right;\" href=\"./?site=$site\">Schließen</a>";
				}
				
				###### get output ######
				$page_output = ob_get_contents();
				ob_end_clean();
				########################

				
				ob_start();
					if(isset($login) or $site == "forgotusername" or $site == "forgotpassword" or $site == "register" or $site == "confirm") {
						include($site.".php");
					} else {
						include("login.php");
					}	
				$output = ob_get_contents();
				ob_end_clean();
				
				if(strpos($output, "<!DOCTYPE html>") === false) {
					echo $page_output;
				}
				
				echo $output;
			?>
		</main>
		
		<footer>
			this site was made with <a href="http://xenux.bplaced.net">Xenux</a>
			<div class="links">
				<a href="../">Homepage</a>
				<a href="../?site=contact">Kontakt</a>
				<a href="../?site=imprint">Impressum</a>
			</div>
		</footer>
	</div>
</body>
</html>
<?php
$db->close(); //close the connection to the db
?>