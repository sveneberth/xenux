<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");


if(isset($get->token)) {
	switch($get->token) {
		case "new_event":
			$db->query("INSERT INTO XENUX_dates(id) VALUES (NULL);");
			$result = $db->query("SELECT * FROM XENUX_dates ORDER by id DESC LIMIT 1;");
			$newest_event = $result->fetch_object();
			$get->token = "edit_event";
			$get->event_id = $newest_event->id;
			// FIXME
		//	break;
		case "edit_event":
			if(isset($_POST['form'])) {
				$db->query("UPDATE XENUX_dates Set name = '$post->name', date = '$post->date $post->time', text = '$post->text' WHERE id = '$get->event_id';");
			} else {
				$result = $db->query("SELECT *, DATE_FORMAT(date,'%Y-%m-%d') as date_formatted, DATE_FORMAT(date,'%H:%i:%s') as time_formatted FROM XENUX_dates WHERE id = '$get->event_id';");
				$event = $result->fetch_object();
				?>
				<form action="<?php echo "?site=$site&token=edit_event&event_id=$get->event_id"; ?>" method="post">
					
					<input type="text" name="name" placeholder="Name" value="<?php echo $event->name; ?>" />
					<input type="date" name="date" placeholder="Datum" value="<?php echo $event->date_formatted; ?>" />
					<input type="time" name="time" placeholder="Zeit" value="<?php echo $event->time_formatted; ?>" />
					<textarea type="text" name="text" placeholder="Text" class="big"><?php echo $event->text; ?></textarea>
					
					<input type="hidden" name="form" value="form" />
					<input type="submit" value="speichern">
				</form>
				<?php
				return false;
			}
			break;
	}
}
?>
<script>
$(document).ready(function() {
	var table = 'XENUX_dates';
	
	/* remove */
	$('span.remove').live('click', function(event) {
		var	id  = $(this).parent().parent().attr('data-id');
		$.ajax({
			url: "macros/universal.php?table="+table,
			type: 'POST',
			dataType: 'json',
			data: {
				token: 'remove',
				id: id
			},
			success: function(response) {
				console.log(response);
				if(response.status == 'successfull') {
				//	debugMess('erfolgreich entfernt!');
				//	loadtable();
					window.location.reload();
				}
			}
		})
	})
	$("#table1").colResizable({
		liveDrag:true,
		 gripInnerHtml:"<div class='grip'></div>", 
	});
})
</script>
<style>
	#table1 td, #table1 th {
		text-indent: 15px;
	}
	
	.grip {
		width: 0;
		height: 0;
		border-style: solid;
		border-width: 17.3px 10px 0 10px;
		/* border-width: 13.0px 7.5px 0 7.5px; */
		border-color: #245DB6 transparent transparent transparent;
		line-height: 0px;
		cursor: e-resize;
		margin-left: -5px;
	}
	.grip:hover {
		border-color: #3F7EDF transparent transparent transparent;
	}
</style>
<p>Hier kannst du die Termine bearbeiten.</p>
<br />
<table id="table1" class="responsive-table">
<tr class="head">
	<th>Name</th>
	<th>Text</th>
	<th>Datum</th>
	<th style="width:2rem;"></th>
</tr>
<?php
$result = $db->query("SELECT *, DATE_FORMAT(date,'%d.%m.%Y %H:%i') as date_formatted FROM XENUX_dates ORDER by date;");
while($row = $result->fetch_object()) {
	echo "	<tr data-id=\"$row->id\">
				<td><a href=\"?site=$site&token=edit_event&event_id=$row->id\">$row->name</a></td>
				<td>";
	if(strlen($row->text) > 300) {
		echo htmlentities(substr($row->text, 0, strpos($row->text, " ", 300)))."...";
	} else {
		echo htmlentities($row->text);
	}
	echo "		</td>
				<td>$row->date_formatted</td>
				<td>
					<span title=\"entfernen\" class=\"remove remove-icon clickable\"></span>
				</td>";
	echo "</tr>";
}
?>
</table>
<br />
<br />
<a id="edit_href" style="font-size: 1em;" href="./?site=<?php echo $site; ?>&token=new_event">neuer Termin</a>