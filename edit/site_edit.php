<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");

if(isset($get->token)) {
	switch($get->token) {
		case "edit_site":
			if(!is_numeric($get->site_id)) {Request_failed(); return false;};
			$result = $db->query("SELECT * FROM XENUX_sites WHERE id = '$get->site_id' LIMIT 1;");
			if($result->num_rows == 0) {Request_failed(); return false;};
			$edit_site = $result->fetch_object();
			
			if(isset($_POST['editor'])) {
				// update
				$db->query("DELETE FROM XENUX_site_contactperson WHERE site_id = $edit_site->id;");
				foreach($_POST as $key => $val) {
					if(strpos($key, 'contact_') !== false) {
						$contactperson_id = substr($key, 8);
						$db->query("INSERT INTO XENUX_site_contactperson(site_id, contactperson_id) VALUES('$edit_site->id', '$contactperson_id');");
					}
				}
				$post->title = preg_replace("/[^a-zA-Z0-9_üÜäÄöÖ&#,.()[]{}*\/ ]/" , "" , $post->title);
				$post->category = preg_replace("/[^a-zA-Z0-9_üÜäÄöÖ&#,.()[]{}*\/ ]/" , "" , $post->category);
				$db->query("UPDATE XENUX_sites SET text = '$post->text', title = '$post->title', category = '$post->category' WHERE id = '$edit_site->id';");
				echo "<p>Seite wurde gespeichert.</p>";
				echo "<p><a href=\"../?site=page&page_id=$edit_site->id\">Zur Seite $edit_site->title</a></p>";
			} else {
				// read & edit
				?>
				<script>
				$(document).ready(function() {
					CKEDITOR.replace('text', {
						toolbar: [
							{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
							{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
							{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll' ] },
							'/',
							{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
							{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
							{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
							'/',
							{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
							{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
							{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
							{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
						]
					});
				})
				</script>
				<form action="" method="post" name="form">
					<input type="text" placeholder="Seitenname" name="title" value="<?php echo $edit_site->title; ?>">
					<input type="text" placeholder="Kategorie" name="category" value="<?php echo $edit_site->category; ?>">
					<textarea class="ckeditor nolabel" placeholder="Seiteninhalt" name="text" id="text"><?php echo $edit_site->text ?></textarea>
					
					<div class="contact-persons">
						<h3>Ansprechpartner</h3>
						<?php
						$result = $db->query("SELECT * FROM XENUX_contactpersons;");
						while($row = $result->fetch_object()) {
							echo "<input ";
							$InnerResult = $db->query("SELECT * FROM XENUX_site_contactperson WHERE site_id = $edit_site->id AND contactperson_id = $row->id;");
							if($InnerResult->num_rows >= 1) echo 'checked';
							echo " type=\"checkbox\" id=\"contact_$row->id\" name=\"contact_$row->id\" value=\"true\"><label for=\"contact_$row->id\">$row->name</label>";
						}
						?>
					</div>
					
					<input type="hidden" name="editor" value="editor" />
					<input type="submit" value="Seite speichern">
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
	var table = 'XENUX_sites';
	
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
<table id="table1" class="responsive-table">
	<tr class="head">
		<th>Seitentitel</th>
		<th>Kategorie</th>
		<th>Erstelldatum</th>
		<th>ID</th>
		<th style="width:2rem;"></th>
	</tr>
	<?php
	$result = $db->query("SELECT * FROM XENUX_sites order by title ASC;");
	while($row = $result->fetch_object()) {
		if(!contains($row->site, 'news_list', 'news_view', 'event_list', 'event_view', 'error', 'page', 'search')) {
			echo "	<tr data-id=\"$row->id\">
						<td>
							<a href=\"?site=$site&token=edit_site&site_id=$row->id\" title=\"Klicken, um die Seite zu bearbeiten\">$row->title</a>
						</td>
						<td>
							$row->category
						</td>
						<td>
							
							".date("d.m.Y H:i", strtotime($row->create_date))."
						</td>
						<td>
							$row->id
						</td>
						<td>
							<span title=\"entfernen\" class=\"remove remove-icon clickable\"></span>
						</td>
					</tr>";
		}
	}
	?>
</table>
<br />
<a class="btn" href="./?site=site_new">neue Seite</a>