<?php
$result = $db->query("SELECT * FROM XENUX_news ORDER by create_date DESC, title ASC;");
while($row = $result->fetch_object()) {
	if(!empty($row->title) and !empty($row->text)) {
		echo "	<div class=\"newslistbox\">
					<h3 class=\"title\">$row->title</h3>
					<span class=\"date\">".pretty_date($row->create_date)."</span>".
					shortstr(strip_tags($row->text))."
					<br />
					<a style=\"display:inline-block;margin-top:10px;\" href=\"?site=news_view&news_id=$row->id\">&raquo;News lesen</a>
				</div>";
	}
}
?>