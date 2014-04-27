<!DOCTYPE html>
<html lang="de">
<head>
<title>Xenux Installation</title>
<meta charset="UTF-8" >
<link rel="stylesheet" type="text/css" href="install.css" />
<?php
function nextpage(){
echo '<meta http-equiv="refresh" content="0; URL=step6.php">';
}
?>
</head>

<body>
	<div id="main">
		<div id="content">
			<div id="header">
				<span class="topic">Xenux</span></a><br />
				<span class="motto">das kostenlose CMS</span>
			</div>
			<ul id="steps">
				<li class="lastStep">Hallo</li>
				<li class="lastStep">Technische Voraussetztungen</li>
				<li class="lastStep">Datenbank</li>
				<li class="lastStep">Datenbank prüfen</li>
				<li class="actStep">Homepage einrichten</li>
				<li class="nextStep">Administrator</li>
				<li class="nextStep">Fertigstellung</li>
			</ul>
			<div id="install">
				<h2>Homepage einrichen</h2>
				<?php
				if(!isset($_POST['submit'])){
					$hpname = "";
					$hpslogan = "";
					$titleprefix = "";
					$titlesufix = "";
					$kontaktemail = "";
					$description = "";
					$keywords = "";
					$email = "";
				} else {
					$hpname = $_POST['hpname'];
					$hpslogan = $_POST['hpslogan'];
					$titleprefix = $_POST['titleprefix'];
					$titlesufix = $_POST['titlesufix'];
					$kontaktemail = $_POST['kontaktemail'];
					$description = $_POST['description'];
					$keywords = $_POST['keywords'];
					$email = $_POST['email'];
					if(empty($hpname) and empty($hpslogan) and empty($description) and empty($keywords) and empty($email)){
						echo '<p style="color:red;">Sie müssen alle Felder ausfüllen!</p>';
					} else {
						$datei = fopen("../config.php","a");
						$text = '
# Homepage-Daten
$HP_Name			= "'.$hpname.'"; # Homepagename (im Kopf)
$HP_Slogan			= "'.$hpslogan.'"; # Homepage-Slogan (im Kopf)
$HP_Prefix			= "'.$titleprefix.'"; # Title Prefix (Optional)
$HP_Sufix			= "'.$titlesufix.'"; # Title Sufix (Optional)
$HP_Kontaktemail	= "'.$kontaktemail.'"; # E-Mail Adresse für das Kontaktformular
$HP_Beschreibung	= "'.$description.'"; # Beschreibung der Homepage (max 150 Zeichen)
$HP_Keywords		= "'.$keywords.'"; # Schlüsselwörter der Homepage (mit Komma trennen)
$HP_Email			= "'.$email.'"; # E-Mail-Adresse (diese wird u.A. benötigt um Accounts freizuschalten)
?>';
						fwrite($datei, $text);
						fclose($datei);
						nextpage();
					}
				}
				?>
				<form action="" method="post">
					Homepage Name (angezeigte Name im Kopf)<br />
					<input type="text" name="hpname" size="70" value="<?php if(empty($hpname)){echo 'Meine Homepage"';}else{echo $hpname;} ?>" /><br />
					<span <?php if (empty($hpslogan) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Slogan(Schriftzug unter dem Name im Kopf)</span><br />
					<input type="text" name="hpslogan" size="70" value="<?php echo $hpslogan; ?>" /><br /><br />
					Title Prefix (Optional)<br />
					<input type="text" name="titleprefix" size="70" value="<?php echo $titleprefix; ?>" /><br />
					Title Sufix (Optional)<br />
					<input type="text" name="titlesufix" size="70" value="<?php echo $titlesufix; ?>" 
					/><br /><br />
					Möchten sie auf der Seite "Kontakt" ein Kontaktformular? Falls sie ein Kontaktformular wünschen, müssen sie hier ihre E-Mail-Adresse angeben, das die Nachricht an sie geschickt werden kann. Falls sie kein Kontaktformular wünschen lassen sie diese Feld einfach leer.<br />
					<input type="text" name="kontaktemail" size="70" value="<?php echo $kontaktemail; ?>" /><br /><br />
					<span <?php if (empty($description) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Beschreibung der Homepage (max. 150 Zeichen)</span><br />
					<input type="text" name="description" size="70" maxlength="150" value="<?php echo $description; ?>" /><br />
					<span <?php if (empty($keywords) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Schlüsselwörter der Homepage (durch Komma getrennt)</span><br />
					<input type="text" name="keywords" size="70" value="<?php echo $keywords; ?>"
					/><br /><br />
					<span <?php if (empty($email) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>E-Mail-Adresse (diese wird u.A. benötigt um Accounts freizuschalten)</span><br />
					<input type="text" name="email" size="70" value="<?php echo $email; ?>" 
					/><br /><br />
					<div class="clear"></div>
					<input type="submit" name="submit" class="next" value="Weiter"/>
				</form>
			</div>
			<div class="clear"></div>
		</div><!-- #content -->
	</div><!-- #main -->

</body>
</html>