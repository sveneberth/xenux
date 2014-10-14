<?php
$result = $db->query("SELECT * FROM $table;");
if(!$result) {
	echo "Es trat ein Fehler auf!";
	return false;
}
$columns = $result->fetch_fields();
foreach($columns as $key => $val) {
	if($val->name == 'id') unset($columns[$key]);
}
?>

<script>
function debugMess() {
	return true;
}
function adjustcontent () {
	return true;
}

$(document).ready(function() {
	var table = '<?php echo $table; ?>';
	
	loadtable();
	$("#search").keyup(loadtable);
	function loadtable() {
		var searchtxt		= $('#search').val();
		var order_direction	= $('#search').attr('data-order-direction');
		var order_column 	= $('#search').attr('data-order-column');
		$.ajax({
			url: "macros/universal.php?table="+table,
			type: 'POST',
			dataType: 'json',
			data: {
				token: 'load',
				searchtxt: searchtxt,
				order_column: order_column,
				order_direction: order_direction
			},
			success: function(response) {
				console.log(response);
				$('.normaltable').html(response.data);
				$('.normaltable tr th:not(:empty)').css({position: 'relative', cursor: 'pointer'}).append('<span class="clickable order down"></span><span class="clickable order up"></span>');
				if(order_direction == "ASC") {
					$('.normaltable tr th[data-column-name="'+order_column+'"] span.up').show();
				} else {
					$('.normaltable tr th[data-column-name="'+order_column+'"] span.down').show();
				}
				adjustcontent();
			}
		});
	};
	
	$('table tr:first th').live('click', function() {
		var ColumnName = $(this).attr('data-column-name');
		if(ColumnName == $('#search').attr('data-order-column')) {
			if($('#search').attr('data-order-direction') == 'DESC') {
				$('#search').attr('data-order-direction', "ASC");
			} else {
				$('#search').attr('data-order-direction', "DESC");
			}
		} else {
			$('#search').attr('data-order-direction', "ASC");
		}
		$('#search').attr('data-order-column', ColumnName);
		loadtable();
	});
	
	$('td').live('click', function() {
		if(!$(this).is(':last-child')) {
			var	$this = $(this),
				id  = $this.parent().attr('data-id'),
				ColumnName  = $this.attr('data-column-name'),
				ColumnType  = $this.attr('data-column-type'),
				name  = $this.attr('data-name'),
				value  = $this.attr('data-value');
			if(!$this.hasClass('edit')) {
				if(ColumnType == 'varchar') {
					$this.html('<input style="width:90%;display:inline-block;" placeholder="'+name+'" type="text" id="edit" value="'+value+'"/><input style="float:right;display:inline-block;" type="button" class="submit_input auto" value="OK" data-column-name="'+ColumnName+'" />').addClass('edit').children('input#edit').focus();
					$this.children('input#edit').css('width', $this.width()-$this.children('input.submit_input').width()-30);
				} else if(ColumnType == 'text') {
					$this.html('<textarea style="width:90%;display:inline-block;" placeholder="'+name+'" type="text" id="edit">'+value+'</textarea><input style="float:right;display:inline-block;" type="button" class="submit_input auto" value="OK" data-column-name="'+ColumnName+'" />').addClass('edit').children('textarea#edit').focus();
					$this.children('textarea#edit').css('width', $this.width()-$this.children('input.submit_input').width()-30);
				} else if(ColumnType == 'tinyint') {
					$this.html('<input type="button" class="submit auto inline" data-value-boolean="1" data-column-name="'+ColumnName+'" value="Ja" /><input type="button" class="submit auto inline" data-value-boolean="0" data-column-name="'+ColumnName+'" value="Nein" />').addClass('edit');
				} else if(ColumnType == 'int') {
					if(ColumnName == 'role') {
						/* FIXME: 
							if($row->id == $login->id) {
								alert(you can not edit youself)
							} elseif($rowrole >= $login->role) {
								alert(you can't edit users with equal or heigher right than you)
							}
						*/
						$.ajax({
							url: "macros/universal.php?table="+table,
							type: 'POST',
							dataType: 'json',
							data: {
								token: 'load roles',
								SelectedRole: value
							},
							success: function(response) {
								console.log(response);
								if(response.status == 'successfull') {
									$this.html('<select style="display: inline;" class="role " size="1">'+response.data+'</select><input style="float:right;display:inline-block;" type="button" class="submit_select auto" value="OK" data-column-name="'+ColumnName+'" />').addClass('edit');
									$this.children('select').css('width', $this.width()-$this.children('input.submit_input').width()-150);
								}
							}
						})
					} else {
						$this.html('<input style="width:90%;display:inline-block;" placeholder="'+name+'" type="number" id="edit" value="'+value+'"/><input style="float:right;display:inline-block;" type="button" class="submit_input auto" value="OK" data-column-name="'+ColumnName+'" />').addClass('edit').children('input#edit').focus();
						$this.children('input#edit').css('width', $this.width()-$this.children('input.submit_input').width()-30);
					}
				}
			}
		}
	});
	
	/* update */
	$('.submit_input').live('click', function() { // trigger enter event
		var e = $.Event("keyup");
		e.keyCode = 13;
		$(this).parent().children('input[type="text"], textarea').trigger(e);
	});
	$('td.edit > input, td.edit > textarea').live('keyup', function(event) {
		if(event.keyCode == 13) {
			console.log("enter pressed");
			var	$this		= $(this),
				id 			= $this.parent().parent().attr('data-id'),
				ColumnName	= $this.parent().attr('data-column-name'),
				value 		= $this.val();
			
			update($this, id, ColumnName, value);
		}
	});
	$('td.edit > input[type="button"].submit').live('click', function() {
		var	$this		= $(this),
			id 			= $this.parent().parent().attr('data-id'),
			ColumnName	= $this.parent().attr('data-column-name'),
			value		= $this.attr('data-value-boolean');
			
		update($this, id, ColumnName, value);
	});
	$('td.edit > input[type="button"].submit_select').live('click', function() {
		var	$this		= $(this),
			id 			= $this.parent().parent().attr('data-id'),
			ColumnName	= $this.parent().attr('data-column-name'),
			value		= $this.parent().children('select').val();
			
		update($this, id, ColumnName, value);
	});
	function update(element, id, ColumnName, value) {
		$.ajax({
			url: "macros/universal.php?table="+table,
			type: 'POST',
			dataType: 'json',
			data: {
				token: 'update',
				id: id,
				column: ColumnName,
				value: value
			},
			success: function(response) {
				console.log(response);
				if(response.status == 'successfull') {
					debugMess('erfolgreich gespeichert!');
					element.parent().removeClass('edit');
					loadtable();
				}
			}
		})
	};
	
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
					debugMess('erfolgreich entfernt!');
					loadtable();
				}
			}
		})
	})
	
	/* add div show */
	$('span.add').live('click', addToggle);

	/* add */
	$('input.submit_add').live('click', function(event) {
		var allCorrect = true;
		var values = {};
		$('div.add.backend input:not(.submit_add)').each(function() {
			var InputType = $(this).attr('type');
			var ColumnName = $(this).attr('data-column-name');
			if(InputType == 'text' || InputType == 'password' || InputType == 'number') {
				$(this).removeClass('wrong');
				var val =  $(this).val();
				if(empty(val)) {
					$(this).addClass('wrong');
					allCorrect = false;
				}
			} else if(InputType == 'checkbox') {
				var val = ($(this).is(':checked'))?1:0;
			}
			values[ColumnName] = val;
		});
		
		if(!allCorrect) return false;
		console.log(values);
		
		$.ajax({
			url: "macros/universal.php?table="+table,
			type: 'POST',
			dataType: 'json',
			data: {
				token: 'add',
				values: values,
			},
			success: function(response) {
				console.log(response);
				if(response.status == 'successfull') {
					debugMess('erfolgreich hinzugefügt!');
					addToggle();
					loadtable();
					$('div.add.backend input[type="text"]').val('')
					$('div.add.backend input[type="checkbox"]').removeAttr('checked');
				}
			}
		})
	})
	$('div.add.backend .close').live('click', addToggle);
})

function addToggle() {
	$('span.add').fadeToggle(300);
	$('div.add').slideToggle(300);
	setTimeout(adjustcontent, 300);
}
</script>
<input id="search" type="text" value="<?php echo @$getsearchtxt; ?>" name="searchtxt" placeholder="<?php echo $name; ?> suchen" />
<div class="backend add" style="display: none;">
	<span class="clickable close">&times;</span>
	<h5><?php echo $name; ?> hinzufügen</h5>
	<?php
		foreach($columns as $val) {
			if($database_table[$table][$val->name]['show_in_add']) {
				switch($mysql_data_type[$val->type]) {
					case 'text':
						echo "<textarea type=\"text\" data-column-name=\"$val->name\" placeholder=\"{$database_table[$table][$val->name]['title']}\"></textarea>";
						break;
					case 'varchar':
						echo "<input type=\"text\" data-column-name=\"$val->name\" placeholder=\"{$database_table[$table][$val->name]['title']}\" />";
						break;
					case 'tinyint':
						echo "<div style=\"font-size: 20px;font-weight: 100;vertical-align: middle;border: 1px solid #aaa;width:400px;display:inline-block;box-sizing:border-box;padding: 2px 8px;max-width: 100%;border-radius: 5px;\">
							{$database_table[$table][$val->name]['title']}
							<div class=\"yesnoswitch\">
								<input type=\"checkbox\" id=\"yesnoswitch\" data-column-name=\"$val->name\" class=\"yesnoswitch-checkbox\">
								<label class=\"yesnoswitch-label\" for=\"yesnoswitch\">
									<span class=\"yesnoswitch-inner\"></span>
									<span class=\"yesnoswitch-switch\"></span>
								</label>
							</div>
						</div>";
						break;
				}
			}
		}
	?>
	<input type="button" class="submit_add" value="hinzufügen" />
</div>
<p><span class="add clickable btn"><?php echo $name; ?> hinzufügen</span></p>
<table id="table1" class="normaltable responsive-table"></table>