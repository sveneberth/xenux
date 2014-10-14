<?php
$result = $db->query("SELECT * FROM XENUX_news ORDER by title ASC;");
while($row = $result->fetch_object()) {
	if(!empty($row->title) and !empty($row->text)) {
		echo "	<div class=\"newslistbox\">
					<h3 class=\"title\">$row->title</h3>";
					if(strlen($row->text) > 300) {
						echo substr($row->text, 0, strpos($row->text, " ", 300));
					} else {
						echo $row->text;
					}
		echo "		...<br />
					<a href=\"?site=news_view&news_id=$row->id\">&raquo;weiterlesen</a>
				</div>";
	}
}
?>