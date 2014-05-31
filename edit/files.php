<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if(!empty($_GET['delfile']) and file_exists('../files/'.$_GET['delfile'])) {
	unlink('../files/'.$_GET['delfile']);
	echo 'Die Datei <i>'.$_GET['delfile'].'</i> wurde soeben erfolgreich gelöscht!<br /><br />';
}

?>
<table id="table1">
	<tr><th>Dateiname</th><th>Type</th><th>Größe</th><th></th></tr>
	<?php
	if(file_exists("../files/")) {
		$handle=opendir ("../files/");
		while ($datei = readdir ($handle)) {
			$type = pathinfo('../files/'.$datei, PATHINFO_EXTENSION);
			$size = filesize('../files/'.$datei);
			if($size < 1048576) {
				$size = round($size/1024, 2).' KB';
			} else {
				$size = round($size/1048576, 2).' MB';
			}
			if($datei != '.' and $datei != '..') {
				echo "<tr>";
				echo "<td><a href=\"javascript:window.open ('../files/$datei','Datei zeigen','width=700,height=500,location=0,menubar=0,scrollbars=0,status=0,toolbar=0,resizable=0')\">$datei</a></td>";
				echo "<td>$type</td>";
				echo "<td>$size</td>";
				echo "<td><a id=\"edit_href\" style=\"font-size: 0.9em;\" href=\"?site=$site&delfile=$datei\">löschen</a></td>";
				echo "</tr>";
			}
		}
		closedir($handle);
	} else {
		echo "Verzeichnis existiert nicht!";
	}
	?>
</table>
<h3>Datei hochladen</h3>
<form enctype="multipart/form-data" action="" method="POST">
	<input type="hidden" name="MAX_FILE_SIZE" value="31457280" />
	Diese Datei hochladen: <input name="userfile" type="file" />
	<input type="submit" name="submit" value="Datei hochladen" />
</form>
<?php
$umlaute = Array("ä","ö","ü","Ä","Ö","Ü","ß");
$replace = Array("ae","oe","ue","Ae","Oe","Ue","ss");
$Fehlercodes = array(
						0 => "Es trat kein Fehler, die Datei wurde erfolgreich hochgeladen",
						1 => "Die hochgeladene Datei überschreitet die Maximale Größe von 30 MB (ini.php)",
						2 => "Die hochgeladene Datei überschreitet die Maximale Größe von 30 MB",
						3 => "Die hochgeladene Datei wurde nur teilweise hochgeladen",
						4 => "Es wurde keine Datei hochgeladen",
						6 => "Es fehlt ein temporären Ordner"
); 
if(!file_exists("../files/")) {
	mkdir("../files/");
}
if(!empty($_POST['submit'])) {
	$fullfilename = str_replace($umlaute, $replace, basename($_FILES['userfile']['name']));
	$filename = substr($fullfilename,0,strpos($fullfilename, "."));
	$filetyp = substr($fullfilename,strpos($fullfilename, "."));
	if(file_exists('../files/'.$filename.$filetyp)) {
		$i = 1;
		while(file_exists('../files/'.$filename.'_'.$i.$filetyp)){
		$i++;
		}
		$uploadfile = '../files/'.$filename.'_'.$i.$filetyp;
	} else {
		$uploadfile = '../files/'.$filename.$filetyp;
	};
	if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
		echo "Die Datei wurde erfolgreich hochgeladen!<br />";
	} else {
		echo "<p style=\"color:red;\">Beim Hochladen der Datei trat folgender Fehler auf:<br />";
		echo $Fehlercodes[$_FILES["userfile"]["error"]];
		echo "</p>";
	}
}
?>