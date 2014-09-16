<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
$tags = '<p><a><span><img><b><i><u><marquee><br><hr><sup><sub><em><strong><code><iframe><table><tr><td><th><pre><h1><h2><h3><h4><h5><h6><ul><li><ol>';

if(!empty($_GET['delid']) and is_numeric($_GET['delid']) and !contains($_GET['delfile'], "kontakt", "impressum", "home")) {
	$sql = "SELECT * FROM XENUX_pages WHERE id = '".$_GET['delid']."'";
	$erg = mysql_query($sql);
	$row = mysql_fetch_array($erg);
	$sql = "DELETE FROM XENUX_pages WHERE id = '".$_GET['delid']."'";
	$erg = mysql_query($sql);
	echo 'Die Seite <i>'.$row['fullname'].'</i> wurde soeben erfolgreich gelöscht!<br />';
}
if(isset($_GET['id'])) {
	$id = $_GET['id'];
	$sql = "SELECT * FROM XENUX_pages WHERE id = '$id'";
	$erg = mysql_query($sql);
	$row = mysql_fetch_array($erg);
	$fullname = $row['fullname'];
	if(!empty($id)){
		if(isset($_POST['editor'])) {
			foreach($_POST as $key => $val) {
					$$key = mysql_real_escape_string($val);
			}
			$contact = "";
			$sql = "SELECT * FROM XENUX_ansprechpartner";
			$erg = mysql_query($sql);
			while($row = mysql_fetch_array($erg)) {
				if(isset($_POST['contact_'.$row['id']])) {
						$contact .= "|".$row['id'];
					}
			}
			$fullname = preg_replace("/[^a-zA-Z0-9_üÜäÄöÖ&#,.()[]{}*\/ ]/" , "" , $fullname);
			$category = preg_replace("/[^a-zA-Z0-9_üÜäÄöÖ&#,.()[]{}*\/ ]/" , "" , $category);
			$sql = "UPDATE XENUX_pages Set text = '".strip_tags($text, $tags)."', fullname = '$fullname', category = '$category', ansprechpartner = '$contact' WHERE id = '$id'";
			$erg = mysql_query($sql);
			echo "<p>Seite wurde gespeichert.</p>";
			echo "<p><a href=\"../?site=page&page_id=$id\">Zur Seite $fullname</a></p>";
		} else {
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
					$(".htmltags").hover( // fixit if curser over .alowedhtmltags than show this and break toogle
						function() {
							$(".alowedhtmltags").fadeIn("fast");
						}
						,function() {
							if(!$(".alowedhtmltags").is(':hover')) {
								$(".alowedhtmltags").fadeOut("fast");
							}
						}
					)
					$(".alowedhtmltags").hover( // fixit if curser over .alowedhtmltags than show this and break toogle
						function() {
							$(".alowedhtmltags").fadeIn("fast");
						}
						,function() {
							$(".alowedhtmltags").fadeOut("fast");
						}
					)
				})
			</script>
			<div class="alowedhtmltags"><strong>Erlaubte HTML-Tags:</strong><br /><?php echo htmlentities($tags); ?></div>
			<p>Du bearbeitest gerade die Seite <a href="../?site=<?php echo $filename; ?>"><i><?php echo $fullname; ?></i></a><br />
			Es werden nur <a href="javascript:void(0)" class="htmltags">bestimmte HTML-Befehle</a> unterstützt.<br />
			<form action="" method="post" name="form">
			Seitenname:<br />
			<input type="text" placeholder="Seitenname" name="fullname" value="<?php echo $fullname; ?>"><br /><br />
			Kategorie:<br />
			<input type="text" placeholder="Kategorie" name="category" value="<?php echo $category; ?>"><br /><br />
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
					<a id="bunt" href='javascript:insert("<p>","</p>")'>Absatz</a>
				</div>
				<textarea placeholder="Seiteninhalt" name="text" id="text"><?php echo $text ?></textarea><br />
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
				echo ' type="checkbox" id="contact_'.$row['id'].'" name="contact_'.$row['id'].'" value="yes"><label for="contact_'.$row['id'].'">'.$row['name'].'</label><br />';
			}
			?>
			</form>
			<?php
			return;
		}
	} else {
		echo '<p style="color:red;">Es wurde keine Seite ausgewählt!</p>';
	}
}
?>
<p>Hier kannst du die Seiten anzeigen und bearbeiten.</p>
<p>Bitte wähle eine Seite aus:</p>
<table id="table1" class="responsive-table">
	<tr class="head"><th>Seitentitel</th><th>Kategorie</th><th></th></tr>
	<?php
	$sql = "SELECT * FROM XENUX_pages order by filename";
	$erg = mysql_query($sql);
	while ($zeile = mysql_fetch_array($erg)) {
		if(!contains($zeile['filename'], 'news', 'newslist', 'error', 'termine', 'terminview', 'page')) {
			echo "<tr>";
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