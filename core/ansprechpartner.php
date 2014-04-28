<style>
#box_contact {
	border: 1px solid #aaa;
	padding: 5px 10px;
	height: 200px;
	width: 350px;
	margin: 0 10px 20px 0;
	float:left;
}
#box_contact > .name {
	font-size: 150%;
	font-weight: 200;
	margin-bottom: 10px;
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
<h3>Ansprechpartner</h3>
<?php
$sql = "SELECT * FROM XENUX_ansprechpartner";
$erg = mysql_query($sql);
while($row = mysql_fetch_array($erg)) {
	echo '<div id="box_contact">
	<div class="name">'.$row['name'].'</div>
	<div class="desc">'.$row['text'].'</div>
	<div class="email"><a href="mailto:'.$row['email'].'">'.$row['email'].'</a></div>
	</div>';
}
?>
<div style="clear:left;"></div>