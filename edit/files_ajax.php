<?php
if(!file_exists("../files/")) {
	mkdir("../files/");
}
$token = $_GET['token'];
switch($token) {
	case "read":
		$handle = opendir("../files/");
		echo '<tr class="head"><th>Dateiname</th><th>Dateityp</th><th>Dateigröße</th><th></th></tr>';
		while($datei = readdir($handle)) {
			$type = pathinfo('../files/'.$datei, PATHINFO_EXTENSION);
			$size = filesize('../files/'.$datei);
			if($size < 1048576) {
				$size = round($size/1024, 2).' KB';
			} else {
				$size = round($size/1048576, 2).' MB';
			}
			if($datei != '.' and $datei != '..') {
				echo "<tr>";
				echo "<td data-title=\"Dateiname\"><a href=\"javascript:window.open ('../files/$datei','Datei zeigen','width=700,height=500,location=0,menubar=0,scrollbars=0,status=0,toolbar=0,resizable=0')\">$datei</a></td>";
				echo "<td data-title=\"Typ\">$type</td>";
				echo "<td data-title=\"Größe\">$size</td>";
				echo "<td data-title=\"\"><a class=\"delete\" data-file=\"$datei\" id=\"edit_href\" style=\"font-size: 0.9em;\">löschen</a></td>";
				echo "</tr>";
			}
		}
		closedir($handle);
		$return['message'] = "Erfolgreich ausgelesen";
		break;
	case "add":
		if(array_key_exists(0,$_FILES)) {
			$umlauts = Array("ä","ö","ü","Ä","Ö","Ü","ß");
			$replace = Array("ae","oe","ue","Ae","Oe","Ue","ss");
			
			$fullfilename = str_replace($umlauts, $replace, basename($_FILES[0]['name']));
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
			if(move_uploaded_file($_FILES[0]['tmp_name'], $uploadfile)) {
				echo "Erfolgreich hochgeladen";
			} else {
				echo "Beim Hochladen der Datei trat folgender Fehler auf";
			}
		}
		break;
	case "delete":
		$file = $_POST['file'];
		unlink('../files/'.$file);
		echo "Die Datei <i>$file</i> wurde soeben erfolgreich gelöscht!";
		break;
}
?>