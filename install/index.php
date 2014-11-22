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
	1 => "Hallo",
	2 => "Technische Voraussetzungen",
	3 => "Datenbank",
	4 => "Homepage einrichten",
	5 => "Administrator",
	6 => "Fertigstellung",
);

if(!isset($_GET['step']) || empty($_GET['step']) || !is_numeric($_GET['step']) || $_GET['step'] > count($steps)) {
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
	<meta charset="UTF-8" >
	<title>Xenux Installation | Schritt <?php echo $step; ?></title>
	<link rel="stylesheet" type="text/css" href="install.css" />
</head>
<body>
	<div id="main">
		<div id="content">
			<div id="header">
				<span class="topic">Xenux</span></a><br />
				<span class="motto">das kostenlose CMS</span>
			</div>
			<ul id="steps">
				<?php
					for($i=1;$i<=6;$i++) {
						echo "<li class=\"";
						if($step == $i) {
							echo "actStep";
						} elseif($step > $i) {
							echo "lastStep";
						} else {
							echo "nextStep";
						}
						echo "\">".$steps[$i]."</li>";
					}
				?>
			</ul>
			<div id="install">
				<h2><?php echo $steps[$step]; ?></h2>
				<?php
				include_once("step$step.php");
				
				if($next) {
					echo "<a class=\"next\" href=\"?step=".($step+1)."\">Weiter</a>";
				}
				?>
			</div>
			<div class="clear"></div>
		</div>
	</div>
</body>
</html>