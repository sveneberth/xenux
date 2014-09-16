<?php
SESSION_START();
function contains($var) {
	$array = func_get_args();
	unset($array[0]);
	return in_array($var, $array); 
}
function maxlines($str, $num=10) {
    $lines = explode("\n", $str);
    $firsts = array_slice($lines, 0, $num);
    return implode("\n", $firsts);
}
if(@$_SESSION["login"] == 1 and !empty($_POST['session_delete'])) {
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
include('core/macros/colortext.php');
include('core/macros/escape_mail.php');
include('core/macros/hex2rgb.php');
$link = mysql_connect($MYSQL_HOST, $MYSQL_BENUTZER, $MYSQL_KENNWORT);
$db_selected = mysql_select_db($MYSQL_DATENBANK, $link);
if(!$db_selected) {
	die('Es ist keine Verbindung zur Datenbank möglich!');
}
mysql_query('SET NAMES "utf8"');
$sql = "SELECT * FROM XENUX_pages WHERE filename = '".mysql_real_escape_string($filename)."'";
$erg = mysql_query($sql);
$row = mysql_fetch_assoc($erg);
$fullname = $row['fullname'];
$siteid = $row['id'];
$category = $row['category'];
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
	if($anzahl > 0) {
		$_SESSION["login"] = 1;
		$_SESSION["userid"] = $erg['id'];
	} else{
		$result1 = "<p>Deine Logindaten sind nicht korrekt, oder du wurdest noch nicht freigeschaltet.</p>";
	}
}
if (@$_SESSION['login'] == 1) {
	$sql = "SELECT * FROM XENUX_users WHERE id = '".$_SESSION["userid"]."'";
	$erg = mysql_query($sql);
	$login = mysql_fetch_array($erg);
}
$sql = "SELECT * FROM XENUX_main";
$erg = mysql_query($sql);
while($row = mysql_fetch_array($erg)) {
	foreach($row as $key => $val) {
		$$key = $val;
	}
	$$name = $value;
}
$HP_URL = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].substr($_SERVER['SCRIPT_NAME'],0,-9);
?>
<!DOCTYPE html>
<html lang="de">
<head>
	<title><?php echo "$hp_name | $fullname"; ?></title>
	<meta charset="UTF-8" />
	<meta name="language" content="de"/>
	<meta name="description" content="<?php echo $meta_desc; ?>" />
	<meta name="keywords" content="<?php echo $meta_keys; ?>" />
	<meta name="auhor" content="<?php echo $meta_auhor; ?>" />
	<meta name="publisher" content="<?php echo $meta_auhor; ?>" />
	<meta name="copyright" content="<?php echo $meta_auhor; ?>" />
	<!-- http://xenux.bplaced.net -->
	<meta name="generator" content="Xenux - das kostenlose CMS" />
	<meta name="robots" content="index, follow, noarchive" />
	<link rel="shortcut icon" href="./core/images/<?php echo $favicon_src; ?>"/>
	<link rel="stylesheet" type="text/css" href="core/css/style.css" media="all"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<script src="core/js/jquery-2.1.1.min.js"></script>
	<script src="core/js/jquery-migrate-1.2.1.min.js"></script>
	<script src="core/js/jquery-ui.js"></script>
	<script src="core/js/jquery.cookie.js"></script>
	<script src="core/js/main.js"></script>
	<style>
	html,body{
		background:<?php echo $bgcolor; ?>;
		color:<?php echo $fontcolor; ?>;
	}
	a:focus,a:hover{color: <?php
	echo lighter(hex2RGB($fontcolor)['red'],hex2RGB($fontcolor)['green'],hex2RGB($fontcolor)['blue']);
	?>;
	}
	</style>
</head>
<body>
	<div id="headWrapper">
		<div id="head"> 
			<div class="logo">
				<a href="./">
					<img src="./core/images/<?php echo $logo_src; ?>" />
				</a>
			</div>
			<ul id="topmenu" class="mobilemenu">
				<li><a href="javascript:openmobilemenu()">Menu</a></li>
				<li><a href="?site=newslist">News</a></li>
				<li><a href="?site=termine">Termine</a></li>
				<li><a href="./edit?site=login">Login</a></li>
			</ul>
			<ul id="topmenu" class="mainmenu">
				<li><a href='./'>Home</a></li>
				<?php
					$unallowedsites = array	(
												"impressum",
												"kontakt",
												"home",
												"news",
												"newslist",
												"termine",
												"terminview",
												"page",
												"search",
											);
					$read_category = array();
					$sql = "SELECT DISTINCT category FROM XENUX_pages WHERE category != '';";
					$erg = mysql_query($sql) or die(mysql_error());
					while($row = mysql_fetch_array($erg)) {
						$menu_category = $row['category'];
						$catergorypoint = $menu_category;
						echo "<li><img src=\"core/images/right.png\" class=\"".strtolower(preg_replace("/[^a-zA-Z0-9_]/" , "" , $menu_category))." openpoints\" onclick=\"javascript:openmenupoints('".strtolower(preg_replace("/[^a-zA-Z0-9_]/" , "" , $menu_category))."')\"><a";
						$thissql = mysql_query("SELECT * FROM XENUX_pages WHERE fullname = '$menu_category' AND category  = '$menu_category' LIMIT 1;");
						$thispage = mysql_fetch_object($thissql);
						if(mysql_num_rows($thissql) > 0) {
							echo " href=\"?site=page&page_id=$thispage->id\"";
						}
						echo ">$menu_category</a><ul id=\"".strtolower(preg_replace("/[^a-zA-Z0-9_]/" , "" , $menu_category))."\">";
						$sql1 = "SELECT * FROM XENUX_pages WHERE category = '$menu_category' AND category != '' ORDER by fullname";
						$erg1 = mysql_query($sql1);
						while($row1 = mysql_fetch_array($erg1)) {
							foreach($row1 as $key1 => $val1) {
								$a = "menu_$key1";
								$$a = $val1;
							}
							if(strtolower($catergorypoint) != strtolower(@$menu_fullname)) {
								echo "<li><a href=\"?site=page&page_id=$menu_id\">$menu_fullname</a></li>";
							}
						}
						echo "</ul></li>";
					}
					$sql = "SELECT * from XENUX_pages WHERE category = '' ORDER by fullname";
					$erg = mysql_query($sql);
					while($row = mysql_fetch_array($erg)) {
						foreach($row as $key => $val) {
							$a = "menu_$key";
							$$a = $val;
						}
						if(!in_array($menu_filename, $unallowedsites)) {
							echo "<li><a href=\"?site=page&page_id=$menu_id\">$menu_fullname</a></li>";
						}
					}
				?>
			</ul>
		</div>
	</div>
<div id="wrapper">
	<div class="fontsize">
		Schrift
		&nbsp;<a title="Schrift kleiner" href="javascript:fontsizedecrease()">-</a>
		&nbsp;<a title="Schrift normal" href="javascript:fontsizereset()">O</a>
		&nbsp;<a title="Schrift größer" href="javascript:fontsizerecrease()">+</a>
	</div>
	<div id="leftboxes">
		<div id="box">
			<h3>Suche:</h3>
			<form action="" method="GET">
				<input type="hidden" name="site" value="search" />
				<input type="text" name="searchtxt" placeholder="Suche">
			</form>
		</div>
		<?php
		$sql = "SELECT * FROM XENUX_news;";
		$result = mysql_query($sql);
		$number = mysql_num_rows($result);
		if($number > 0) {
			?>
			<ul id="box">
				<h3>News:</h3>
				<?php
				$sql = "SELECT * FROM XENUX_news LIMIT 5;";
				$erg = mysql_query($sql);
				while ($zeile = mysql_fetch_array($erg)) {
					$id = $zeile['id'];
					$title = $zeile['title'];
					$text = $zeile['text'];
					if($title != '' and $text != '') {
						echo '<li><span class="title">'.$title;
						if (@$_SESSION["login"] == 1) {
							echo '<a id="edit_href" href="edit/?site=news_edit&id='.$id.'">Bearbeiten</a>';
						}
						echo '</span>';
						if(strlen($text) > 70) {
							echo htmlentities(substr($text, 0, strpos($text, " ", 70)));
						} else {
							echo htmlentities($text);
						}
						echo '...<br /><a href="?site=news&id='.$id.'">&raquo;weiterlesen</a></li>';
					}
				}
				mysql_free_result($erg);
				?>
			</ul>
		<?php
		}
		$sql = "SELECT * FROM XENUX_dates;";
		$erg = mysql_query($sql);
		$anzahl = mysql_num_rows($erg);
		if($anzahl > 0) {
			echo '<ul class="dates" id="box"><h3>Termine:</h3>';
			$sql1 = "SELECT *, DATE_FORMAT(date,'%d.%m.%Y %H:%i') as dat FROM XENUX_dates WHERE date >= NOW() ORDER by date LIMIT 5;";
			$erg1 = mysql_query($sql1);
			if(mysql_num_rows($erg1) == 0) {
				echo "<p>keine Anstehenden Termine vorhanden!</p>";
			}
			while($row1 = mysql_fetch_array($erg1)) {
				echo '<li><span class="title">'.$row1['name'];
				if (@$_SESSION["login"] == 1) {
					echo '<a id="edit_href" href="edit/?site=dates_edit&id='.$row1['id'].'">Bearbeiten</a>';
				}
				echo '</span>';
				echo $row1['dat'].'<br/>'.htmlentities(substr($row1['text'],0,70)).'<br /><a href="?site=terminview&id='.$row1['id'].'">&raquo;Termin anzeigen</a></li>';
			}
			echo '<a href="?site=termine">alle Termine anzeigen</a></ul>';
		}
		
		$sql = "SELECT * FROM XENUX_pages WHERE filename = '$filename';";
		$erg = mysql_query($sql);
		$row = mysql_fetch_array($erg);
		if(!empty($row['ansprechpartner'])) {
			echo '<ul id="box"><h3>Ansprechpartner:</h3>';
			$zerlegen = explode("|", $row['ansprechpartner']);
			for($i=1;isset($zerlegen[$i]);$i++) {
				$sql1 = "SELECT * FROM XENUX_ansprechpartner WHERE id = '$zerlegen[$i]'";
				$erg1 = mysql_query($sql1);
				$row1 = mysql_fetch_array($erg1);
				echo '<li><span class="title">'.$row1['name'].'</span>';
				echo $row1['position'].'<br/>';
				escapemail($row1['email']);
				echo '</a></li>';
			};
			echo '</ul>';
		}
		?>
		<div id="box">
			<h3>Login:</h3>
			<?php
			if(isset($del)){
				echo $del;
			}
			if (@$_SESSION["login"] == 0) {
				?>
				<form action="" method="POST">
				<input type="text" name="username" placeholder="Benutzername"><br />
				<a href="edit/?site=forgotusername">Benutzernamen vergessen?</a><br />
				<input type="password" name="password" placeholder="Passwort"><br />
				<a href="edit/?site=forgotpassword">Passwort vergessen?</a><br />
				<?php echo @$result1; ?>
				<input type="submit" name="submit" value="Einloggen"><br />
				<a href="edit/?site=registrieren">Registrieren</a>
				</form>
				<?php
			} else {
				?>
				Hallo <?php echo $login['vorname']; ?>, du bist erfolgreich eingeloggt!<br />
				<form action="" method="POST">
					<input type="submit" name="session_delete" value="Logout">
				</form>	
				<?php
			}
			?>
		</div>
	</div>
	<div id="content">
		<?php
		if($filename != 'page') {
			echo "<h1>$fullname";
			if (@$_SESSION["login"] == 1) {
				if(!contains($filename, 'search', 'news', 'error', 'termine', 'terminview', 'page')) {
					echo '<a id="edit_href" href="edit/?site=site_edit&id='.$siteid.'">Bearbeiten</a>';
				}
			}
			echo "</h1>";
		}
		if(contains($filename, 'newslist', 'news', 'error', 'termine', 'terminview', 'page', 'search')) {
			include('core/pages/'.$filename.'.php');
		} else {
			$sql = "SELECT * FROM XENUX_pages WHERE filename = '$filename' LIMIT 1;";
			$erg = mysql_query($sql);
			$row = mysql_fetch_object($erg);
			echo /*nl2br*/($row->text);
		}
		if($filename == 'kontakt') {
			include('core/macros/ansprechpartner.php');
		}
		if($filename == 'kontakt' and !empty($contact_form_email)) {
			include ('core/macros/kontakt_formular.php');
		}
		?>
	</div>
	<div id="footer">
		This Side was made with <a href="http://xenux.bplaced.net">Xenux</a>
		<div class="href">
			<a href="./edit/">Editroom</a>
			<a href="./?site=kontakt">Kontakt</a>
			<a href="./?site=impressum">Impressum</a>
		</div>
	</div>
</div>
</body>
</html>
<?php
mysql_close($link);
?>