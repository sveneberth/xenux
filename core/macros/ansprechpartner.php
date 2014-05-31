<?php
$sql = "SELECT * FROM XENUX_ansprechpartner";
$erg = mysql_query($sql);
if(mysql_fetch_array($erg)) {
	echo "<h3>Ansprechpartner</h3>";
	$sql = "SELECT * FROM XENUX_ansprechpartner";
	$erg = mysql_query($sql);
	while($row = mysql_fetch_array($erg)) {
		echo '<div id="box_contact">
		<div class="name">'.$row['name'].'</div>
		<div class="position">'.$row['position'].'</div>
		<div class="desc">'.$row['text'].'</div>
		<div class="email">';
		escapemail($row['email']);
		echo '</div>
		</div>';
	}
	echo '<div style="clear:left;"></div>';
}
?>