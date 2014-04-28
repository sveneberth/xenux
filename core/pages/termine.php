<style>
#dates {
	border-collapse: collapse;
	border: 2px solid #333;
	width: 750px;
}
#dates tr:nth-child(odd) {background: #eee; }
#dates tr:nth-child(even) {background: #e7e7e7; }
#dates th, #dates td {
	vertical-align: top;
	text-align: left;
	padding: 3px 5px;
}
#dates tr:nth-child(2n-1) > td:nth-child(1) {
}
#dates tr:nth-child(2n-1) > td:nth-child(2) {
	text-align: right;
}
#dates tr:nth-child(2n+0) {
	border-top: 1px solid #999;
	border-bottom: 2px solid #333;
	margin-bottom: 10px;
}

}</style>
<table id="dates">
<?php
# Hier bitte nichts ändern!
$sql = "SELECT *, DATE_FORMAT(date,'%d.%m.%Y %H:%i') as dat FROM XENUX_dates ORDER by date";
$erg = mysql_query($sql);
while($row = mysql_fetch_array($erg)) {
	echo	'<tr>
				<td>'.$row["name"].'</td>
				<td>'.$row["dat"].'</td>
			</tr>
			<tr>
				<td colspan="2">'.$row["text"].'</td>
			</tr>';
}
?>
</table>