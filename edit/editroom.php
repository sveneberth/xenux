<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
?>
<p>Hallo <?php echo $login->firstname; ?>!<br />
Hier kannst du die Homepage bearbeiten und verwalten!</p>
<div class="editroomboxes">
	<?php
	foreach($all_sites as $key => $val) {
		if(is_array($val)) {
			echo "\n<ul class=\"editroombox\">\n\t<h4>$key</h4>";
			foreach($val as $key => $val) {
				echo "\n\t<li>\n\t\t<a href=\"./?site=$key\">$val</a>\n\t</li>";
			}
			echo "\n</ul>";
		};
	}
	?>
</div>