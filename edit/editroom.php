<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
?>
<p>Hallo <?php echo $login['vorname']; ?>!<br />
Hier kannst du die Homepage bearbeiten und verwalten!</p>
<div id="editroom">
	<?php
	foreach($all_sites as $key => $val) {
		if(is_array($val)) {
			echo "<ul id=\"editroombox\"><h4>$key</h4>";
			foreach($val as $key => $val) {
				echo "<li><a href=\"./?site=$key\">$val</a></li>";
			}
			echo "</ul>";
		};
	}
	?>
</div>