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
<script>
	function sort() {
		location.href="?site=termine&order="+$('#sortselector').val();
	}
</script>
<select style="width:150px;" onchange="sort()" id="sortselector">
	<option>Sortierung</option>
	<option value="name ASC">Name aufsteigend</option>
	<option value="name DESC">Name absteigend</option>
	<option value="date ASC">Datum aufsteigend</option>
	<option value="date DESC">Datum absteigend</option>
	<option value="text ASC">Text aufsteigend</option>
	<option value="text DESC">Text absteigend</option>
</select>
<table id="dates">
<?php
# Hier bitte nichts ändern!
$variants = array("date", "date DESC",  "date ASC", "name", "name DESC",  "name ASC", "text", "text DESC",  "text ASC");
if(!isset($_GET['order'])) {
	$order = 'date';
} elseif(empty($_GET['order'])) {
	$order = 'date';
} elseif(in_array($_GET['order'], $variants)) {
	$order = $_GET['order'];
} else {
	$order = 'date';
}
$sql = "SELECT *, DATE_FORMAT(date,'%d.%m.%Y %H:%i') as dat FROM XENUX_dates ORDER by $order";
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