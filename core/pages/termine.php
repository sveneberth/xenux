<?php
	include_once('core/macros/calender.php');
?>
<br />
<br />
<script>
	function sort() {
		location.href="?site=termine&timestamp=<?php echo @$_GET['timestamp']; ?>&order="+$('#sortselector').val();
	}
</script>
<select style="width:150px;" onchange="sort()" id="sortselector">
	<option>Sortierung</option>
	<option <?php if(@$_GET['order']=="name ASC") echo "selected" ?> value="name ASC">Name aufsteigend</option>
	<option <?php if(@$_GET['order']=="name DESC") echo "selected" ?> value="name DESC">Name absteigend</option>
	<option <?php if(@$_GET['order']=="date ASC") echo "selected" ?> value="date ASC">Datum aufsteigend</option>
	<option <?php if(@$_GET['order']=="date DESC") echo "selected" ?> value="date DESC">Datum absteigend</option>
	<option <?php if(@$_GET['order']=="text ASC") echo "selected" ?> value="text ASC">Text aufsteigend</option>
	<option <?php if(@$_GET['order']=="text DESC") echo "selected" ?> value="text DESC">Text absteigend</option>
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