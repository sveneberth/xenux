<?php
if($login['role'] < 1) {
	echo '<p>Du bist nicht berechtigt, diese Seite zu öffnen!</p>';
	return;
}
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
?>
<style>
p.info {
	color: rgb(31, 61, 88);
	display: none;
	font-weight: 600;
}
</style>
<table id="table1" class="responsive-table"></table>
<h3 style="margin-top: 20px;">Datei hochladen</h3>
<script>
$(document).ready(function() {
	function read() {
		$("#table1").fadeOut(100);
		$.ajax({
			url: 'files_ajax.php?token=read',
			success: function(response) {
				$("#table1").html(response).fadeIn(100);
			}
		});
	}
	read();
	$(".delete").live('click',function() {
		console.log('clicked: '+$(this).attr('data-file'));
		$.ajax({
			url: 'files_ajax.php?token=delete',
			type: 'POST',
			data: {
				file: $(this).attr('data-file')
			},
			success: function(response) {
				console.log(response);
				$(".info").html(response).fadeIn(100);
				setTimeout(function() {
					$(".info").fadeOut(500);
				}, 2000);
				read();
			}
		});
	});
	$(".upload").click(function() {
		var data = new FormData();
		data.append(0, $('input[type="file"]')[0].files[0]);
		$.ajax({
			url: 'files_ajax.php?token=add',
			type: 'POST',
			cache: false,
			processData: false,
			contentType: false,
			data: data,
			success: function(response) {
				console.log(response);
				$(".info").text(response).fadeIn(100);
				setTimeout(function() {
					$(".info").fadeOut(500)
				}, 2000);
				read();
			},
			error: function(xhr, textStatus, errorThrown){
				console.log('request failed: '+textStatus+xhr+errorThrown);
			}
		});
	});
});
</script>
<input type="file" class="file" />
<input type="button" class="upload" value="Datei hochladen" />
<p class="info"></p>