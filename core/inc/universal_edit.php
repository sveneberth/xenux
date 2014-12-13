<?php
foreach($skel as $name => $props) {
	if(!isset($props['required']))			$skel[$name]['required']		= false;
	if(!isset($props['editable']))			$skel[$name]['editable']		= true;
	if(!isset($props['wysiwyg-editor']))	$skel[$name]['wysiwyg-editor']	= true;
}


if(isset($_REQUEST['task'])) {
	switch($_REQUEST['task']) {
		case "remove":
			$result = $db->query("DELETE FROM $skelTable WHERE id = '$get->id';");
			if(!$result)
				echo $db->error;
			break;
			
		case "new":
		case "edit":
			if(isset($_POST['form'])) {
				if($_REQUEST['task'] == "new") {
					$sql_columns	= "";
					$sql_values		= "";
					$first			= true;
					
					foreach($skel as $name => $props) {
						if($props['editable']) {
							if($props['type'] == 'date') {
								$value = $post->{$name."_date"}." ".$post->{$name."_time"};
							} else {
								$value = $post->$name;
							}
								
								$sql_columns	.= (($first)?'':',')."$name";
								$sql_values		.= (($first)?'':',')."'$value'";
							
								$first = false;
						}
					}
					
					$sql = "INSERT INTO `$skelTable`($sql_columns) VALUES($sql_values);";
					$result = $db->query($sql);
					if(!$result)
						echo $db->error;
						
				} elseif($_REQUEST['task'] == "edit") {
					foreach($skel as $name => $props) {
						if($props['editable']) {
							if($props['type'] == 'date') {
								$db->query("UPDATE $skelTable SET $name = '".$post->{$name."_date"}." ".$post->{$name."_time"}."' WHERE id = '$get->id';");
							} else {
								$db->query("UPDATE $skelTable SET $name = '".$post->$name."' WHERE id = '$get->id';");
							}
						}
					}
				}
				header("Location: ?site=$site");
			} else {
				if($_REQUEST['task'] == "edit") {
					$result = $db->query("SELECT * FROM $skelTable WHERE id = '$get->id';");
					$row = $result->fetch_object();
				}
				?>
				<script>
				$(document).ready(function() {
					CKEDITOR.replace('text', {
						toolbar: [
							{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
							{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', '-', 'Undo', 'Redo' ] },
							{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll' ] },
							'/',
							{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
							{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
							{ name: 'insert', items: [ 'Image',  'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak' ] },
							'/',
							{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
							{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
							{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
						]
					});
				});
				</script>
				<form action="" method="post">
					<?php
						foreach($skel as $name => $props) {
							if($props['editable']) {
								switch($props['type']) {
									case 'email':
										echo "<input type=\"email\" placeholder=\"{$props['title']}\" value=\"".@$row->$name."\" name=\"$name\" ".(($props['required']==true)?'required':'')." />";
										break;
									case 'string':
										echo "<input type=\"text\" placeholder=\"{$props['title']}\" value=\"".@$row->$name."\" name=\"$name\" ".(($props['required']==true)?'required':'')." />";
										break;
									case 'number':
										echo "<input type=\"number\" placeholder=\"{$props['title']}\" value=\"".@$row->$name."\" name=\"$name\" ".(($props['required']==true)?'required':'')." />";
										break;
									case 'date':
										echo "<input type=\"date\" placeholder=\"{$props['title']} (Datum)\" value=\"".(isset($row->$name)?date("Y-m-d", strtotime(@$row->$name)):'')."\" name=\"".$name."_date\" ".(($props['required']==true)?'required':'')." />";
										echo "<input type=\"time\" placeholder=\"{$props['title']} (Zeit)\" value=\"".(isset($row->$name)?date("H:i:s", strtotime(@$row->$name)):'')."\" name=\"".$name."_time\" ".(($props['required']==true)?'required':'')." />";
										break;
									case 'text':
										echo "<textarea ".(($props['wysiwyg-editor'])?'class="ckeditor nolabel"':'')." placeholder=\"{$props['title']}\" class=\"$name\" id=\"$name\" name=\"$name\" ".(($props['required']==true)?'required':'').">".@$row->$name."</textarea>";
										break;
									#FIXME: add case bool
								}
							}
						}
					?>
					
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

<div style="display: block;overflow: auto;width:100%;"> 
	<table id="table1" class="responsive-table">
	<tr class="head">
	<?php
		foreach($skel as $name => $props) {
			echo "<th>".$props['title']."</th>";
		}
	?>
		<th style="width:4rem;"></th>
	</tr>
	<?php
	reset($skel);
	$first_key = key($skel);
	
	$order = (isset($order))?$order:$first_key." ASC";

	$result = $db->query("SELECT * FROM $skelTable ORDER by $order;");
	if(!$result)
		echo $db->error;
	
	while($row = $result->fetch_object()) {
		echo "<tr>";
		foreach($skel as $name => $props) {
			echo "<td>".shortstr(strip_tags($row->$name))."</td>";
		}
		echo "	<td style=\"text-align: center;\">
						<a href=\"?site=$site&task=edit&id=$row->id&backbtn\" title=\"bearbeiten\" class=\"edit edit-btn clickable\" style=\"display: inline-block;margin: 0;\"></a>
						<a href=\"?site=$site&task=remove&id=$row->id\" title=\"entfernen\" class=\"remove remove-icon clickable\" style=\"display: inline-block;margin: 0;\"></a>
					</td>";
		echo "</tr>";
	}
	?>
	</table>
</div>
<?php
if($canAddNew) {
?>
<br />
<a class="btn" href="./?site=<?php echo $site; ?>&task=new&backbtn">neu</a>
<?php
}
?>