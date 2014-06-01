<?php
if(empty($_GET['id']) or !isset($_GET['id'])) {
	echo 'Bei der Anfrage trat ein Fehler auf, möglicherweise haben sie auf einen fehlerhaften Link geklickt...';
	return;
}
$id = $_GET['id'];
$sql = "SELECT *, DATE_FORMAT(date,'%d.%m.%Y %H:%i') as dat FROM XENUX_dates WHERE id ='$id'";
$erg = mysql_query($sql);
$row = mysql_fetch_array($erg);
foreach($row as $key => $val) {
	$$key = $val;
}
echo "<h3 style=\"margin: 20px 0 5px 0;\">$name</h3>";
echo "<strong>$dat</strong><br />";
echo "$text";
?>
<br />
<br />
<a href="?site=termine">&raquo;zur Übersicht</a>