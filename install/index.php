<?php
if(isset($_POST['deldir']) and $_GET['step'] == 6) {
	$dir = "./";
	if($handle = opendir($dir)) {
		while($file = readdir($handle)) {
			if(!is_dir($file)) {
				unlink($file);
			}
		}
		closedir($handle);
	}
	rmdir('../install');
	header("Location: ../");
}

$steps = array
(
	1 => "Hallo!",
	2 => "Technische Voraussetzungen",
	3 => "Datenbank einrichten",
	4 => "Homepage einrichten",
	5 => "Administrator einrichten",
	6 => "Fertigstellung",
);

if(!isset($_GET['step']) || empty($_GET['step']) || !is_numeric($_GET['step']) || !array_key_exists($_GET['step'], $steps)) {
	$step = 1;
} else {
	$step = $_GET['step'];
}

if($step == 1) {
	/* start session and delete */
	session_start();
	$_SESSION = array();
	session_destroy();
}
if($step == 4 || $step == 5) {
	include("../core/inc/config.php"); // include config
}

$next = false;
?>
<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="UTF-8" />
	<title>Xenux Installation | Schritt <?php echo $step; ?></title>
	<link rel="stylesheet" type="text/css" href="install.css" />
</head>
<body>
	<div class="wrapper">
		<header>
			<span class="title">Xenux</span>
			<span class="slogan">das kostenlose CMS</span>
		</header>
		<ul class="steps">
			<?php
				foreach($steps as $key => $val) {
					echo "<li class=\"".($key==$step ? 'active-step' : ($step > $key ? 'last-step' : 'next-step'))."\">$val</li>";
				}
			?>
		</ul>
		<main>
			<h1><?php echo $steps[$step]; ?></h1>
			<?php
			include_once("step$step.php");
			
			if($next) {
				echo "<a class=\"next\" href=\"?step=".($step+1)."\">Weiter</a>";
			}
			?>
		</main>
		<div class="clear"></div>
	</div>
</body>
</html>