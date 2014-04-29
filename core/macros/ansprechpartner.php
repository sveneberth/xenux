<style>
#box_contact {
	border: 1px solid #aaa;
	padding: 5px 10px;
	height: 210px;
	width: 350px;
	margin: 0 10px 20px 0;
	float:left;
}
#box_contact > .name {
	font-size: 150%;
	font-weight: 200;
	margin-bottom: 5px;
}
#box_contact > .position {
	margin-bottom: 2px;
	font-size: 110%;
}
#box_contact > .desc {
	margin-bottom: 2px;
}
#box_contact > .email > a:link,
#box_contact > .email > a:visited,
#box_contact > .email > a:active {
	color: #333;
	text-decoration: none;
}
#box_contact > .email > a:hover {
	color: #999;
}
</style>
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