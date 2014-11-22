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
if(!isset($_GET['step']) or empty($_GET['step']) or !is_numeric($_GET['step'])) {
	$step = 1;
} else {
	$step = $_GET['step'];
}
if($step == 1) {
	session_start();
	$_SESSION = array();
	session_destroy();
}
$steps = array(
				1 => "Hallo",
				2 => "Technische Voraussetztungen",
				3 => "Datenbank",
				4 => "Homepage einrichten",
				5 => "Administrator",
				6 => "Fertigstellung",
				);
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