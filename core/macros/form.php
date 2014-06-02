<?php
if(isset($_POST["submit"])) {
	foreach($_POST as $key => $val) {
		$$key = $val;
	}
		$header  = 'MIME-Version: 1.0' . "\r\n";
		$header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$header .= 'To: '.$contact_form_email. "\r\n";
		$header .= 'From: '.$contact_form_email. "\r\n";
		$mailtext = '<!DOCTYPE html><html lang="de"><head><meta charset="UTF-8" /><title>Formular</title></head><body>
<style>
table {font-size:100%;text-align:left;vertical-align:top;}
th,td {text-align:left;vertical-align:top;}
</style>
Hallo!<br />
Es hat jemand auf der Homepage <a href="http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'">http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'</a> das Formular genutzt!<br /><br />
<table>
';
for($c=1;$c<=$absolute;$c++) {
	$d = "field".$c;
	$e = "field".$c."label";
	$mailtext .= "<tr><th>".$$e.":</th><td>".nl2br(htmlentities($$d))."</td></tr>";
}
$mailtext.='
</table>
<br /><br /><span style="font-family:Verdana;color:#808080;border-top: 1px #808080 solid;">Die Mail wurde mit Xenux erstellt</span></body></html>';
		mail(
			$contact_form_email, 
			"Formular", 
			$mailtext,
			$header)
		or die("<p>Die Mail konnte nicht versendet werden.</p>");
		echo '<p>Die Mail wurde erfolgreich versendet!</p>';
		return;
}
$sql = "SELECT * FROM XENUX_form order by label";
$erg = mysql_query($sql);
$i = 0;
echo "<form action=\"\" method=\"POST\">";
while ($row = mysql_fetch_array($erg)) {
	$i++;
	foreach($row as $key => $val) {
		$$key = $val;
	}
	$a = "field".$i;
	if($type == "textarea") {
		echo "$label<br /><textarea name=\"field$i\" placeholder=\"$label\">".@$$a."</textarea><br /><br />";
	} else {
		echo "$label<br /><input type=\"$type\" name=\"field$i\" placeholder=\"$label\" value=\"".@$$a."\" /><br /><br />";
	}
	echo "<input type=\"hidden\" name=\"field".$i."label\" value=\"$label\" />";
}
echo "<input type=\"hidden\" name=\"absolute\" value=\"$i\" />";
echo "<input type=\"hidden\" name=\"submit\" value=\"submit\" />";
echo "<input type=\"submit\" value=\"senden\" />";
echo "</form>";
?>