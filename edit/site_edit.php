<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
$a = array('[bunt]', '[/bunt]','[form][/form]');
$b = array('<?php colortext("', '"); ?>', '<?php include("core/macros/form.php");  ?>');
$tags = '<p><a><span><img><b><i><u><marquee><br><hr><sup><sub><em><strong><code><iframe><table><tr><td data-title=\"\"><th><pre>';

if(!empty($_GET['delfile']) and !empty($_GET['delid']) and file_exists('../core/pages/'.$_GET['delfile'].'.php') and $_GET['delfile']!="kontakt" and $_GET['delfile']!="impressum" and $_GET['delfile']!="home") {
	unlink('../core/pages/'.$_GET['delfile'].'.php');
	$sql = "DELETE FROM XENUX_pages WHERE id = '".$_GET['delid']."'";
	$erg = mysql_query($sql);
	echo 'Die Seite <i>'.$_GET['delfile'].'</i> wurde soeben erfolgreich gelöscht!<br />';
}
if(isset($_GET['id'])) {
	$id = $_GET['id'];
	$sql = "SELECT * FROM XENUX_pages WHERE id = '$id'";
	$erg = mysql_query($sql);
	$row = mysql_fetch_array($erg);
	$filename_old = $row['filename'];
	$fullname = $row['fullname'];
	$file = '../core/pages/'.$filename_old.'.php';
	if(!empty($id)){
		if(isset($_POST['editor'])) {
			foreach($_POST as $key => $val) {
					$$key = $val;
			}
			$fp = fopen($file, "w");
			if($fp) {
				fputs($fp, str_replace($a, $b, stripslashes(strip_tags($text, $tags))));
				fclose($fp);
				$contact = "";
				$sql = "SELECT * FROM XENUX_ansprechpartner";
				$erg = mysql_query($sql);
				while($row = mysql_fetch_array($erg)) {
					if(isset($_POST['contact_'.$row['id']])) {
							$contact .= "|".$row['id'];
						}
				}
				$sql = "UPDATE XENUX_pages Set filename = '$filename', fullname = '$fullname', category = '$category', ansprechpartner = '$contact' WHERE id = '$id'";
				$erg = mysql_query($sql);
				rename("../core/pages/".$filename_old.".php", "../core/pages/".$filename.".php");
				echo "Seite wurde gespeichert.<br />";
			} else {
				echo "<p>Fehler: kann die Seite <i>".$fullname."</i> nicht öffnen!</p>";
			}
		} else {
			if(file_exists($file)) {
				$fp = fopen($file, "r");
				if($fp) {
					$text = '';
					while(!feof($fp)) {
						$text .= str_replace($b, $a, fgets($fp));
					}
					fclose($fp);
				}
				$sql = "SELECT * FROM XENUX_pages WHERE id = '$id'";
				$erg = mysql_query($sql);
				$row = mysql_fetch_array($erg);
				foreach($row as $key => $val) {
					$$key = $val;
				}
				$zerlegen = explode("|", $ansprechpartner);
				?>
				<script>
					$(document).ready(function() {
						$(".alowedhtmltags").hide();
						$(".alowedhtmltags").click(function() {
							$(".alowedhtmltags").toggle("fast");
						})
						$(".htmltags").click(function() {
							$(".alowedhtmltags").toggle("fast");
						})
					})
				</script>
				<div class="alowedhtmltags"><strong>Erlaubte HTML-Tags:</strong><br /><?php echo htmlentities($tags); ?></div>
				<p>Du bearbeitest gerade die Seite <i><?php echo $fullname; ?></i><br />
				Es werden nur <a href="javascript:void(0)" class="htmltags">bestimmte HTML-Befehle</a> unterstützt.<br />
				<form action="" method="post" name="form">
				Seitenkurzname:<br />
				<input type="text" name="filename" value="<?php echo $filename; ?>"><br /><br />
				Seitenname:<br />
				<input type="text" name="fullname" value="<?php echo $fullname; ?>"><br /><br />
				Kategorie:<br />
				<input type="text" name="category" value="<?php echo $category; ?>"><br /><br />
				<div id="page_edit">
					<div id="formatierungen">
						<a id='textb' href='javascript:text()'>farbiger Text</a>
						<a id='linkb' href='javascript:link()'>Link</a>
						<div id="group">
							<strong><a id='fettb' href='javascript:insert("<b>","</b>")'><b>F</b></a>
							<a id='kursivb' href='javascript:insert("<i>","</i>")'><i>K</i></a>
							<a id='unterstrichenb' href='javascript:insert("<u>","</u>")'><u>U</u></a></strong>
						</div>
						<a href='javascript:bild()'>Bild</a>
						<a href='javascript:insert("<br />","")'>Zeilenumbruch</a>
						<a href='javascript:insert("<hr />","")'>horizontale Linie</a>
						<a id="bunt" href='javascript:insert("[bunt]","[/bunt]")'>bunter Text</a>
						<a id="bunt" href='javascript:insert("<p>","</p>")'>Absatz</a>
					</div>
					<textarea name="text" id="text"><?php echo $text ?></textarea><br />
					<input type="hidden" name="editor" value="editor" />
					<input type="submit" value="Seite speichern">
				</div>
				<strong>Ansprechpartner</strong><br />
				<?php
				$sql = "SELECT * FROM XENUX_ansprechpartner";
				$erg = mysql_query($sql);
				while($row = mysql_fetch_array($erg)) {
					echo '<input ';
					for($i=1;isset($zerlegen[$i]);$i++) {
						if($zerlegen[$i] == $row['id']) {
							echo "checked";
						}
					}
					echo ' type="checkbox" name="contact_'.$row['id'].'" value="yes">'.$row['name'].'<br />';
				}
				?>
				</form>
				<?php
				return;
			} else {
				echo "<p>Die Seite <i>".$fullname."</i> existiert nicht!</p>";
			}
		}
	} else {
		echo '<p style="color:red;">Es wurde keine Datei ausgewählt!</p>';
	}
}
?>
<p>Hier kannst du die Seiten anzeigen und bearbeiten.</p>
<p>Bitte wähle eine Seite aus:</p>
<table id="table1" class="responsive-table">
	<tr class="head"><th>Kurzname</th><th>Seitentitel</th><th>Kategorie</th><th></th></tr>
	<?php
	$sql = "SELECT * FROM XENUX_pages order by filename";
	$erg = mysql_query($sql);
	while ($zeile = mysql_fetch_array($erg)) {
		if($zeile['filename'] != 'news' and $zeile['filename'] != 'error' and $zeile['filename'] != 'termine' and $zeile['filename'] != 'terminview') {
			echo "<tr>";
			echo "<td data-title=\"Kurzname\">".$zeile['filename']."</td>";
			echo "<td data-title=\"Seitentitel\"><a href=\"../?site=".$zeile['filename']."\" title=\"Klicken, um die Seite anzuzeigen\">".$zeile['fullname']."</a></td>";
			echo "<td data-title=\"Kategorie\">".$zeile['category']."</td>";
			echo "<td data-title=\"\"><a id=\"edit_href\" style=\"font-size: 0.9em;\" href=\"./?site=$site&id=".$zeile['id']."\">Bearbeiten</a> <a id=\"edit_href\" style=\"font-size: 0.9em;\" href=\"./?site=$site&delfile=".$zeile['filename']."&delid=".$zeile['id']."\">löschen</a></td>";
			echo "</tr>";
		}
	}
	mysql_free_result($erg);
	?>
</table>
<br />
<a id="edit_href" style="font-size: 1em;" href="./?site=site_new">neue Seite</a>