<?php
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
		$result = $db->query("SELECT * FROM XENUX_sites WHERE id = '$get->page_id' LIMIT 1;");
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
	<title><?php echo "$main->hp_name | ".(($site->site=='page')?$page->title:$site->title); ?></title>
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
	<link rel="shortcut icon" href="./core/images/<?php echo $main->favicon_src; ?>" />
	<link rel="stylesheet" type="text/css" href="core/css/style.css" media="all"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<script src="core/js/jquery-2.1.1.min.js"></script>
	<script src="core/js/jquery-migrate-1.2.1.min.js"></script>
	<script src="core/js/jquery-ui.js"></script>
	<script src="core/js/jquery.cookie.js"></script>
	<script src="core/js/colResizable-1.3.min.js"></script>
	<script src="https://code-snippets-se.googlecode.com/git/functions.js"></script>
	<script src="core/js/main.js"></script>
	<style>
	html, body {
		background: <?php echo $main->bgcolor; ?>;
		color: <?php echo $main->fontcolor; ?>;
	}
	</style>
</head>
<body>
	<div class="headWrapper">
		<header> 
			<div class="logo">
				<a href="./">
					<img src="./core/images/<?php echo $main->logo_src; ?>" />
				</a>
			</div>
			<ul id="topmenu" class="mobilemenu">
				<li><a href="javascript:openmobilemenu()">Menu</a></li>
				<li><a href="?site=news_list">News</a></li>
				<li><a href="?site=event_list">Termine</a></li>
				<li><a href="./edit?site=login">Login</a></li>
			</ul>
			<ul id="topmenu" class="mainmenu">
				<li><a href='./'>Home</a></li>
				<?php
					$read_category = array();
					
					$result = $db->query("SELECT DISTINCT category FROM XENUX_sites WHERE category != '';");
					while($row = $result->fetch_object()) {
						$menu_category = $row->category;
						logger($row->category);
						echo "<li><a";
						$InnerResult = $db->query("SELECT * FROM XENUX_sites WHERE title = '$menu_category' AND category  = '$menu_category' LIMIT 1;");
						$thispage = $InnerResult->fetch_object();
						if($InnerResult->num_rows > 0) {
							echo " href=\"?site=page&page_id=$thispage->id\"";
						}
						echo ">$menu_category</a><ul>";
						
						$result_point = $db->query("SELECT * FROM XENUX_sites WHERE category = '$menu_category' AND category != '' ORDER by title ASC");
						while($row_point = $result_point->fetch_object()) {
							if(strtolower($menu_category) != strtolower($row_point->title)) {
								echo "<li><a href=\"?site=page&page_id=$row_point->id\">$row_point->title</a></li>";
							}
						}
						echo "</ul></li>";
					}
					$result = $db->query("SELECT * from XENUX_sites WHERE category = '' ORDER by title ASC;");
					while($row = $result->fetch_object()) {
						if(!in_array($row->site, $special_sites) && $row->site != 'home') {
							echo "<li><a href=\"?site=page&page_id=$row->id\">$row->title</a></li>";
						}
					}
				?>
				<li class="search">
					<div id="sb-search" class="sb-search">
						<form action="" method="GET">
							<input type="hidden" name="site" value="search" />
							<input onkeyup="if($(this).val()==''){$('.sb-search-submit').css('z-index', 11);}else{$('.sb-search-submit').css('z-index', 99);}" type="text" class="sb-search-input" name="searchtxt" placeholder="Suche" value="" />
							<input type="submit" class="sb-search-submit" value="" />
							<span onclick="$('div#sb-search').toggleClass('sb-search-open');" class="sb-icon-search"></span>
						</form>
					</div>
				</li>
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
		<div class="leftboxes">
			<?php
			$result = $db->query("SELECT * FROM XENUX_news LIMIT 5;");
			$number = $result->num_rows;
			if($number > 0) {
				?>
				<ul class="news">
					<h3>News:</h3>
					<?php
					while($row = $result->fetch_object()) {
						if(!empty($row->title) && !empty($row->text)) {
							echo "<li><span class=\"title\">$row->title" . /*((isset($login))?"<a id=\"edit_href\" href=\"edit/?site=news_edit&token=edit_news&news_id=$row->id\">Bearbeiten</a>":'') . */"</span>";
							if(strlen($row->text) > 70) {
								echo htmlentities(substr($row->text, 0, strpos($row->text, " ", 70)));
							} else {
								echo htmlentities($row->text);
							}
							echo "...<br /><a href=\"?site=news_view&news_id=$row->id\">&raquo;weiterlesen</a></li>";
						}
					}
					?>
				</ul>
			<?php
			}
			
			$result = $db->query("SELECT *, DATE_FORMAT(date,'%d.%m.%Y %H:%i') as date_formatted FROM XENUX_dates WHERE date >= NOW() ORDER by date LIMIT 5;");
			$number = $result->num_rows;
			if($number > 0) {
				echo "<ul class=\"dates\">
						<h3>Termine:</h3>";
				while($row = $result->fetch_object()) {
					echo "<li><span class=\"title\">$row->name" . ((isset($login))?"<a id=\"edit_href\" href=\"edit/?site=events_edit&token=edit_event&id=$row->id\">Bearbeiten</a>":'') . "</span>
					$row->date_formatted<br/>
					".htmlentities(substr($row->text, 0, 70))."<br />
					<a href=\"?site=event_view&id=$row->id\">&raquo;Termin anzeigen</a></li>";
				}
				echo "<a href=\"?site=event_list\">alle Termine anzeigen</a>
				</ul>";
			} else {
				echo "<p>keine Anstehenden Termine vorhanden!</p>";
			}
			
			/* newest sites */
			$result = $db->query("SELECT * FROM XENUX_sites ORDER by create_date DESC LIMIT 5;");
			$num = $result->num_rows;
			if($num > 0) {
				echo "<ul class=\"newest sites\">
						<h3>neuste Seiten:</h3>";
				while($row = $result->fetch_object()) {
					echo "	<li>
								<a href=\"?site=page&page_id=$row->id\">$row->title</a>
							</li>";
				}
				echo "</ul>";
			}
			
			/* contact persons */
			$result = $db->query("	SELECT * FROM XENUX_site_contactperson
									LEFT JOIN XENUX_sites ON XENUX_site_contactperson.site_id = XENUX_sites.id
									LEFT JOIN XENUX_contactpersons ON XENUX_site_contactperson.contactperson_id = XENUX_contactpersons.id
									WHERE site_id = '".(($site->site == 'page')?$page->id:$site->id)."';");
			$num = $result->num_rows;
			if($num > 0) {
				echo "<ul id=\"box\">
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
						<a href="edit/?site=forgotusername">Benutzernamen vergessen?</a><br />
						<a href="edit/?site=registrieren">Registrieren</a>
					</form>
					<?php
				} else {
					?>
					Hallo <?php echo $login->firstname; ?>, du bist erfolgreich eingeloggt!<br />
					<input type="button" onclick="window.location='?<?php foreach($_GET as $key => $val){if($key!="do")echo "$key=$val&";} ?>do=logout'" value="Logout" />
					<?php
				}
				?>
			</ul>
		</div>
		<main>
			<?php
			if($site->site != 'page') {
				echo "<h1>$site->title";
				if(@$_SESSION["login"] == 1) {
					if(!in_array($site->site, $special_sites) || contains($site->site, 'imprint', 'contact')) {
						echo "<a id=\"edit_href\" href=\"edit/?site=site_edit&token=edit_site&site_id=$site->id\">Bearbeiten</a>";
					}
				}
				echo "</h1>";
			}
			if(in_array($site->site, $special_sites)) {
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
		</main>
		<footer>
			This Side was made with <a href="http://xenux.bplaced.net">Xenux</a>
			<div class="href">
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