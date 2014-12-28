<?php
/**
 * @package    Xenux
 *
 * @link       http://www.xenux.bplaced.net
 * @version    1.4-beta
 * @author     Sven Eberth <mail@sven-eberth.de.hm>
 * @copyright  Copyright (c) 2013 - 2014, Sven Eberth.
 * @license    GNU General Public License version 3, see LICENSE.txt
 */

if(!file_exists("mysql.conf")) {
	header("Location: ./install/");
}

include_once('core/inc/config.php'); // include config

if(!isset($_GET['site'])) { //read the site
	$site = 'home';
} elseif(empty($_GET['site'])) {
	$site = 'home';
} else {
	$site = $db->real_escape_string($_GET['site']);
}

$result = $db->query("SELECT * FROM XENUX_sites WHERE site = '$site'"); //read site information
$num = $result->num_rows;
if($num > 0) { //if site exists, than readout all the site informations
	$site = $result->fetch_object();
	if($site->site == 'page') {
		$result = $db->query("SELECT * FROM XENUX_sites WHERE id = '$get->page_id' AND site = '' LIMIT 1;");
		$num = $result->num_rows;
		if($num > 0) {
			$page = $result->fetch_object();
		} else { //if page not exists, than set site as error
			$page = new stdClass();
			$page->site = "error";
			$page->title = "Error 404 - Seite nicht gefunden";
		}
	}
} else { //if site not exists, than set site as error
	$site = new stdClass();
	$site->site = "error";
	$site->title = "Error 404 - Seite nicht gefunden";
}

if($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['loginform'])) { // if loginform submit
	$username = $db->real_escape_string($_POST["username"]);
	$password = $db->real_escape_string($_POST["password"]);
	$result = $db->query("SELECT * FROM XENUX_users WHERE username = '$username' LIMIT 1;");
	$number = $result->num_rows;
	if($number > 0) {
		$row = $result->fetch_object();
		if(!$row->confirmed) { // if user not confirmed
			$loginsuccess = false;
			$returnlogin = "Du bist noch nicht freigeschaltet!";
		} else {
			if(SHA1($password) == $row->password) {
				# Password valid
				$_SESSION["login_xenux"] = 1;
				$_SESSION['userid_xenux'] = $row->id;
				$loginsuccess = true;
				$result = $db->query("UPDATE XENUX_users SET lastlogin_date = NOW(), lastlogin_ip = '{$_SERVER['REMOTE_ADDR']}' WHERE id = '{$_SESSION['userid_xenux']}';");
				$result = $db->query("SELECT * FROM XENUX_users WHERE id = '{$_SESSION['userid_xenux']}';");
				$login = $result->fetch_object();
			} else {
				# Password invalid!
				$loginsuccess = false;
				$returnlogin = "Passwort und Benutzername stimmen nicht überein!";
			}
		}
	} else {
		$loginsuccess = false;
		$returnlogin = "Es wurde kein Account mit diesem Benutzernamen gefunden!";
	}
}
if(isset($login)) {
	if(@$_GET['do'] == "logout") {
		$_SESSION = array();
		session_destroy();
		unset($login);
		$donelogout = true;
	}
}

define('BASEURL', $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].substr($_SERVER['SCRIPT_NAME'],0,-9));
?>
<!DOCTYPE html>
<html lang="de">
<head>
	<title><?php echo ($site->site=='page' ? $page->title : $site->title) . " | $main->hp_name"; ?></title>
	<meta charset="UTF-8" />
	<meta name="language" content="de"/>
	<meta name="description" content="<?php echo $main->meta_desc; ?>" />
	<meta name="keywords" content="<?php echo $main->meta_keys; ?>" />
	<meta name="auhor" content="<?php echo $main->meta_auhor; ?>" />
	<meta name="publisher" content="<?php echo $main->meta_auhor; ?>" />
	<meta name="copyright" content="<?php echo $main->meta_auhor; ?>" />
	<!-- http://xenux.bplaced.net -->
	<meta name="generator" content="Xenux - das kostenlose CMS" />
	<meta name="robots" content="index, follow, noarchive" />
	<link rel="shortcut icon" href="<?php echo (substr($main->favicon_src, 0, 1)=='/') ? '.'.$main->favicon_src : $main->favicon_src; ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	
	<link rel="stylesheet" type="text/css" href="core/css/style.css" media="all"/>
	
	<!-- jquery + plugins -->
	<script src="core/js/jquery-2.1.1.min.js"></script>
	<script src="core/js/jquery-migrate-1.2.1.min.js"></script>
	<script src="core/js/jquery-ui.js"></script>
	<script src="core/js/jquery.ui.touch-punch.min.js"></script>
	<script src="core/js/jquery.cookie.js"></script>
	
	<!-- fancybox -->
	<script type="text/javascript" src="core/fancybox/jquery.fancybox.pack.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="core/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
	
	<!-- own scripts -->
	<script src="core/js/functions.js?from=https://code-snippets-se.googlecode.com/"></script>
	<script src="core/js/main.js"></script>
	
	<style>
		html, body {
			background: <?php echo $main->bgcolor; ?>;
			color: <?php echo $main->fontcolor; ?>;
		}
	</style>
</head>
<body id="top">
	<a href="#top" class="toTop"></a>
	<div class="headWrapper">
		<header> 
			<a href="javascript:openmobilemenu();" class="menu-icon"></a>
			<a class="logo" href="./">
				<img src="<?php echo (substr($main->logo_src, 0, 1)=='/') ? '.'.$main->logo_src : $main->logo_src; ?>" class="nojsload" />
			</a>
			<ul class="topmenu mobilemenu">
				<li><a href="./edit?site=login">Login</a></li>
			</ul>
			<ul class="topmenu mainmenu">
				<li><a href='./'>Home</a></li>
<?php
					$menu_order = "position_left ASC";
					
					$result1 = $db->query("SELECT * FROM XENUX_sites WHERE parent_id = 0 ORDER BY $menu_order;");
					while($rank1 = $result1->fetch_object()) {
						if(in_array($rank1->site, $special_sites) || $rank1->site == 'home')
							continue;
						echo "<li>\n\t<a href=\"?site=page&page_id=$rank1->id\">".nbsp($rank1->title)."</a>\n";
						
						$result2 = $db->query("SELECT * FROM XENUX_sites WHERE parent_id = $rank1->id ORDER BY $menu_order;");
						if($result2->num_rows > 0) {
							echo "\t<ul>";
							while($rank2 = $result2->fetch_object()) {
								echo "\n\t\t<li>\n\t\t\t<a href=\"?site=page&page_id=$rank2->id\">".nbsp($rank2->title)."</a>\n";
								
								$result3 = $db->query("SELECT * FROM XENUX_sites WHERE parent_id = $rank2->id ORDER BY $menu_order;");
								if($result3->num_rows > 0) {
									echo "\t\t\t<ul>";
									while($rank3 = $result3->fetch_object()) {
										echo "\n\t\t\t\t<li>\n\t\t\t\t\t<a href=\"?site=page&page_id=$rank3->id\">".nbsp($rank3->title)."</a>\n\t\t\t\t</li>\n";
									}
									echo "\t\t\t</ul>";
								}
								echo "\n\t\t</li>";
								
							}
							echo "\n\t</ul>";
						}
						
						echo "\n</li>\n";
					}
				?>
				<li class="search">
					<div id="sb-search" class="sb-search">
						<form action="" method="GET">
							<input type="hidden" name="site" value="search" />
							<input onkeyup="if($(this).val()==''){$('.sb-search-submit').css('z-index', 11);}else{$('.sb-search-submit').css('z-index', 99);}" type="search" class="sb-search-input" name="q" placeholder="Suche" value="<?php if($site->site =='search')echo @$get->q; ?>" />
							<input type="submit" class="sb-search-submit" value="" />
							<span onclick="$('div#sb-search').toggleClass('sb-search-open');" class="sb-icon-search"></span>
						</form>
					</div>
				</li>
				<li class="mobilemenu"><a href="?site=news_list">News</a></li>
				<li class="mobilemenu"><a href="?site=event_list">Termine</a></li>
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
		<div class="leftboxes">
			<?php
			/* news */
			?>
			<ul class="news">
				<h3>News:</h3>
				<?php
				$result = $db->query("SELECT * FROM XENUX_news ORDER by create_date DESC, title ASC LIMIT 5;");
				$number = $result->num_rows;
				if($number > 0) {
					while($row = $result->fetch_object()) {
						if(!empty($row->title) && !empty($row->text)) {
							echo "	<li>
										<span class=\"title\">$row->title" . ((isset($login))?"<a class=\"edit-btn\" style=\"height: 1.2em;width:1.2em;\" href=\"edit/?site=news_edit&task=edit&id=$row->id&backbtn\"></a>":'') . "</span>
										<span class=\"date\">".pretty_date($row->create_date)."</span>".
										shortstr(strip_tags($row->text), 50)."<br />
										<a href=\"?site=news_view&news_id=$row->id\">&raquo;weiterlesen</a>
									</li>";
						}
					}
				} else {
					echo "<p style=\"margin:5px 0;\">keine News vorhanden!</p>";
				}
				?>
				<a href="?site=news_list">alle News anzeigen</a>
			</ul>
			<?php			
			
			/* dates */
			?>
			<ul class="dates">
				<h3>Termine:</h3>
				<?php
				$result = $db->query("SELECT *, DATE_FORMAT(date,'%d.%m.%Y %H:%i') as date_formatted FROM XENUX_dates WHERE date >= NOW() ORDER by date LIMIT 5;");
				$number = $result->num_rows;
				if($number > 0) {
					while($row = $result->fetch_object()) {
						echo "	<li>
									<span class=\"title\">$row->name" . ((isset($login))?"<a class=\"edit-btn\" style=\"height: 1.2em;width:1.2em;\" href=\"edit/?site=event_edit&task=edit&id=$row->id&backbtn\"></a>":'') . "</span>
									<span class=\"date\">$row->date_formatted</span>".
									shortstr(strip_tags($row->text), 50)."<br />
									<a href=\"?site=event_view&event_id=$row->id\">&raquo;Termin anzeigen</a>
								</li>";
					}
				} else {
					echo "<p style=\"margin:5px 0;\">keine anstehenden Termine vorhanden!</p>";
				}
				?>
				<a href="?site=event_list">alle Termine anzeigen</a>
			</ul>
			<?php
			
			
			/* newest sites */
			$result = $db->query("SELECT * FROM XENUX_sites WHERE
			(
						site	!=		'home'
				AND		site	!=		'event_view'
				AND		site	!=		'event_list'
				AND		site	!=		'page'
				AND		site	!=		'news_list'
				AND		site	!=		'news_view'
				AND		site	!=		'error'
				AND		site	!=		'search'
				AND		site	!=		'contact'
				AND		site	!=		'imprint'
			)
			ORDER BY create_date DESC LIMIT 5;");
			if(!$result)
				echo $db->error;
			$num = $result->num_rows;
			if($num > 0) {
				echo "<ul class=\"newest-sites\">
						<h3>neuste Seiten:</h3>";
				while($row = $result->fetch_object()) {
					echo "	<li>
								<a href=\"?site=page&page_id=$row->id\">$row->title</a>
							</li>";
				}
				echo "</ul>";
			}
			
			
			/* contact persons */
			if($site->site != 'error' && @$page->site != 'error') {
				$result = $db->query("	SELECT * FROM XENUX_site_contactperson
										LEFT JOIN XENUX_sites ON XENUX_site_contactperson.site_id = XENUX_sites.id
										LEFT JOIN XENUX_contactpersons ON XENUX_site_contactperson.contactperson_id = XENUX_contactpersons.id
										WHERE site_id = '".(($site->site == 'page')?$page->id:$site->id)."';");
				$num = $result->num_rows;
				if($num > 0) {
					echo "<ul class=\"contactpersons\">
							<h3>Ansprechpartner:</h3>";
					while($row = $result->fetch_object()) {
						echo "	<li>
									<span class=\"title\">$row->name</span>
									$row->position<br/>
									".escapemail($row->email)."
								</li>";
					}
					echo "</ul>";
				}
			}
			?>
			<ul>
				<h3>Login:</h3>
				<?php
				if(!isset($login)) {
					?>
					<form action="?<?php foreach($_GET as $key => $val){if($key!="do")echo "$key=$val&";} ?>do=login" method="POST">
						<input type="hidden" name="loginform" value="true">
						<input type="text" name="username" placeholder="Benutzername">
						<input type="password" name="password" placeholder="Passwort">
						<?php echo @$returnlogin; ?>
						<input style="margin: 5px 0;" type="submit" value="Einloggen">
						<a href="edit/?site=forgotpassword">Passwort vergessen?</a><br />
						<a href="edit/?site=forgotusername">Benutzername vergessen?</a><br />
						<a href="edit/?site=register">Registrieren</a>
					</form>
					<?php
				} else {
					?>
					Hallo <?php echo $login->firstname; ?>, du bist erfolgreich eingeloggt!<br />
					<a href="./edit/">&raquo;zum Editroom</a>
					<input type="button" onclick="window.location='?<?php foreach($_GET as $key => $val){if($key!="do")echo "$key=$val&";} ?>do=logout'" value="Logout" />
					<?php
				}
				?>
			</ul>
		</div>
		<main>
			<?php
			if(!contains($site->site, 'page', 'news_view')) {
				echo "<h1>$site->title";
				if(isset($login)) {
					if(!in_array($site->site, $special_sites) || contains($site->site, 'home', 'imprint', 'contact')) {
						echo "<a class=\"edit-btn\" title=\"bearbeiten\" href=\"edit/?site=site_edit&token=edit_site&site_id=$site->id&backbtn&gotosite\"></a>";
					}
				}
				echo "</h1>";
			}
			if(in_array($site->site, $special_sites) && $site->site != 'imprint') {
				if(file_exists("core/pages/$site->site.php")) {
					include("core/pages/$site->site.php");
				} else {
					request_failed();
				};
			} else {
				$result = $db->query("SELECT * FROM XENUX_sites WHERE site = '$site->site' LIMIT 1;");
				$row = $result->fetch_object();
				echo $row->text;
			}
			?>
			<img src="http://localhost/xenux_testing/files/output.php?id=902ba3cda1883801594b6e1b452790cc53948fda" data-href="http://localhost/xenux_testing/files/output.php?id=902ba3cda1883801594b6e1b452790cc53948fda" />
		</main>
		<footer>
			this site was made with <a href="http://xenux.bplaced.net">Xenux</a>
			<div class="links">
				<a href="./edit/">Editroom</a>
				<a href="./?site=contact">Kontakt</a>
				<a href="./?site=imprint">Impressum</a>
			</div>
		</footer>
	</div>
</body>
</html>
<?php
$db->close(); //close the connection to the db
?>