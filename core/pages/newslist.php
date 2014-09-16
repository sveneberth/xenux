<?php
$sql = "SELECT * FROM XENUX_news ORDER by title";
$erg = mysql_query($sql);
while ($row = mysql_fetch_array($erg)) {
	$id = $row['id'];
	$title = $row['title'];
	$text = $row['text'];
	if(!empty($title) and !empty($text)) {
		echo "<div class=\"newslistbox\"><h3 class=\"title\">$title</h3>";
		if(strlen($text) > 300) {
			echo substr($text, 0, strpos($text, " ", 300));
		} else {
			echo $text;
		}
		echo '...<br /><a href="?site=news&id='.$id.'">&raquo;weiterlesen</a>';
		echo "</div>";
	}
}
mysql_free_result($erg);
?>