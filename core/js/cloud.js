/* ##############################################
/* Cloud
/* ############################################*/
var defaultDisabled = ['remove', 'move', 'rename'];
$(document).ready(function() {
	dir_list(0); // load root
	$('.explorer, .drop-files').height($(window).height() - 265); // FIXME: use a right value
	$('.explorer').selectable({
		filter: " > div",
		distance: 10, // needed for doublick events
		stop: function() {
			$('.actions > button').removeClass('disabled');
		}
	});

	$('.drop-files').css('top', $('.explorer').offset().top);
		
	// drag/drop upload
	$.event.props.push('dataTransfer');
	$('.explorer').bind('dragenter', function() {
		console.info('file in window');
		$('.drop-files').show();
		return false;
	});
	$('.drop-files').bind('dragleave', function() {
		console.info('file out window');
		$('.drop-files').hide();
		return false;
	});	
	$('.drop-files').bind('drop', function(e) {
		var files = e.dataTransfer.files;
		upload(files);
		$('.drop-files').hide();
		return false;
	});
	
	// click events
		$('.explorer .item').live('click', function() {
		$('.explorer .item').removeClass('ui-selected');
		$(this).addClass('ui-selected');
		$('.actions > button').removeClass('disabled');
	});
	$('.breadcrumb .treeitem').live('click', function() {
		var ID = $(this).attr('id');
		console.info("switched to folder: "+ID);
		
		dir_list(ID);
	});
	$(document).click(function(e) {
		var container = $(".popup-editor");
		if(!$('.actions > button.rename').is(e.target) && !container.is(e.target) && container.has(e.target).length === 0) {
			container.fadeOut(50);
		//	console.log("popup-editor hide");
		}
	});
	
	
	// double click events
	$('.explorer > .item.folder').live('touchstart dblclick', function() {
		var rowID = $(this).attr('id');
		console.info("switched to folder: "+rowID);
		
		dir_list(rowID);
	});
	$('.explorer > .item.file').live('touchstart dblclick', function() {
		var rowID = $(this).attr('id');
		console.info("opened file: "+rowID);
		
		window.open('../files?id='+SHA1(rowID)+'','File','width=800,height=600,location=0,menubar=0,scrollbars=0,status=0,toolbar=0,resizable=0');
	});
	
	// action events
	$('.actions > button.remove').click(function() {
		if($('.explorer > .item.ui-selected').length == 0) {
			console.error('no file select');
			return false;
		}
		$('.explorer > .item.ui-selected').each(function(i) {
			var rowID = $(this).attr('id');
			remove(rowID);
		});
	});
	$('.actions > button.move').click(function() {
		if($('.explorer > .item.ui-selected').length == 0) {
			console.error('no file select');
			return false;
		}
		
		$.ajax({
			url: '../ajax/cloud.php',
			type: 'POST',
			dataType: 'json',
			data: {
				task: 'list_all_dirs',
			},
			success: function(response) {
				console.log(response);
				if(response.success == true) {
					var options = "";
					for(key in response.data) { // as dataset,
						options += "<option value=\""+key+"\">"+response.data[key]+"</option>";
					};
					console.debug(options);
					$('.move-target').show();
					$('.move-target > select').html(options);
				}
			},
			error: function(xhr, textStatus, errorThrown){
				console.log('request failed: '+textStatus+xhr+errorThrown);
			}
		});
	});
	$('.actions > button.rename').click(function() {
		if($('.explorer > .item.ui-selected').length == 0) {
			console.error('no file select');
			return false;
		}
		
		var firstSelObj = $('.explorer > .item.ui-selected').eq(0);
		var filename = firstSelObj.attr('data-filename');
		
		$('.rename').show();
		$('.rename > input[type="text"]').val(filename);		
	});
	
	$('.popup-editor.move-target > input[type="button"]').live('click', function() {
		var to = $('.popup-editor.move-target > select').val();
		$('.explorer > .item.ui-selected').each(function(i) {
			var rowID = $(this).attr('id');
			move(rowID, to);
		});
		$('.popup-editor.move-target').hide();
	});
	$('.popup-editor.rename > input[type="text"]').live('keyup', function(event) {
		if(event.keyCode == 13) {
			$('.popup-editor.rename > input[type="button"]').trigger('click');
		}
	});
	$('.popup-editor.rename > input[type="button"]').live('click', function() {
		var newName = $('.popup-editor.rename > input[type="text"]').val();
		var firstSelObj = $('.explorer > .item.ui-selected').eq(0);
		rename(firstSelObj.attr('id'), newName);
		$('.popup-editor.rename').hide();
	});
	$('.actions > button.create_folder').click(function() {
		var folder_name = prompt("Ordnername");
		$.ajax({
			url: '../ajax/cloud.php',
			type: 'POST',
			dataType: 'json',
			data: {
				task: 'create_folder',
				folder_name: folder_name,
				parent_folder: getFolder(),
			},
			success: function(response) {
				console.log(response);
				dir_list(getFolder()); // refresh
			},
			error: function(xhr, textStatus, errorThrown){
				console.log('request failed: '+textStatus+xhr+errorThrown);
			}
		});
	});
	
	$('.actions > button.upload').click(function() {
		$('.actions > input.file').click();
	});
	$('.actions > input.file').change( function() {
		upload($('input.file')[0].files);
	});
});
var numUpload = 0;
function upload(files) {
	// counter
	numUpload += 1;
	var thisUpload = numUpload;
	console.log('Number of Upload: ' + numUpload);
	
	$('.upload-progress').append('<progress data-upload-num="' + thisUpload + '" class="upload uploading" value="0" max="100"></progress>');
	
	console.log('files: %o', files);
	
	var FileData = new FormData();
	$.each(files, function(key, value) {
		FileData.append(key, value);
	});

	$.ajax({
		xhr: function() {
			var xhr = new window.XMLHttpRequest();
			xhr.upload.addEventListener("progress", function(evt) {
				console.log('Number of this Upload: ' + thisUpload);
				if(evt.lengthComputable) {
					var percentComplete = evt.loaded / evt.total;
					percentComplete=parseInt(percentComplete*100);
					console.log(percentComplete);
					$('.upload-progress > progress.upload[data-upload-num="' + thisUpload + '"]').attr('value', percentComplete);

					if(percentComplete === 100) {
						notifyMe(
							'Upload abgeschlossen',
							'Der Upload in der Xenux-Cloud wurde erfolgreich abgeschlossen',
							function() {
								window.open().close()
								window.focus()
							}
						)
						
						dir_list(getFolder()); // refresh
						$('.upload-progress > progress.upload[data-upload-num="' + thisUpload + '"]').removeClass('uploading');
						setTimeout(function() {
							$('.upload-progress > progress.upload[data-upload-num="' + thisUpload + '"]').height(0);
							setTimeout(function() {
								$('.upload-progress > progress.upload[data-upload-num="' + thisUpload + '"]').remove();
							}, 500);
						}, 3000);
					}

				}
			}, false);
			return xhr;
		},
		url: '../ajax/cloud.php?task=upload&parent_folder='+getFolder(),
		type: 'POST',
		dataType: 'json',
		data: FileData,
		processData: false,
		contentType: false,
		success: function(response) {
			console.log(response);
		},
		error: function(xhr, textStatus, errorThrown){
			console.log('request failed: '+textStatus+xhr+errorThrown);
		}
	});
};
function remove(rowID) {
	$.ajax({
		url: '../ajax/cloud.php',
		type: 'POST',
		dataType: 'json',
		data: {
			task: 'remove',
			id: rowID
		},
		success: function(response) {
			console.log(response);
			dir_list(getFolder()); // refresh
		},
		error: function(xhr, textStatus, errorThrown){
			console.log('request failed: '+textStatus+xhr+errorThrown);
		}
	});
}
function move(id, to) {
	$.ajax({
		url: '../ajax/cloud.php',
		type: 'POST',
		dataType: 'json',
		data: {
			task: 'move',
			id: id,
			to: to,
		},
		success: function(response) {
			console.log(response);
			dir_list(getFolder()); // refresh
		},
		error: function(xhr, textStatus, errorThrown){
			console.log('request failed: '+textStatus+xhr+errorThrown);
		}
	});
	console.log(id, to);
}
function rename(id, newName) {
	$.ajax({
		url: '../ajax/cloud.php',
		type: 'POST',
		dataType: 'json',
		data: {
			task: 'rename',
			id: id,
			newName: newName,
		},
		success: function(response) {
			console.log(response);
			dir_list(getFolder()); // refresh
		},
		error: function(xhr, textStatus, errorThrown){
			console.log('request failed: '+textStatus+xhr+errorThrown);
		}
	});
	console.log(id, newName);
}
function getFolder() {
	var folder = $('.explorer').attr('data-folder-id');
	if(isInt(folder)) {
		return folder;
	} else {
		return 0; // 0 == root
	}
};
function setbreadcrumb(folder) {
	$.ajax({
		url: '../ajax/cloud.php',
		type: 'POST',
		dataType: 'json',
		data: {
			task: 'breadcrumb',
			folder: folder,
		},
		success: function(response) {
			console.log(response);
			if(response.success == true) {
				var rows = "<span class=\"treeitem\" id=\"0\">root</span>";
				response.data.forEach(function(entry) { // as dataset
					rows += "<span class=\"treeitem\" id=\""+entry.id+"\">"+entry.filename+"</span>";
				});
				$('.breadcrumb').html(rows);
			}
		},
		error: function(xhr, textStatus, errorThrown){
			console.log('request failed: '+textStatus+xhr+errorThrown);
		}
	});
}
function dir_list(folder) {
	$('.explorer').html("");
	$('body').css("cursor", "wait").append("<div class='ajax-loader'></div>");
	$('.explorer').attr('data-folder-id', folder);
	setbreadcrumb(folder);
	defaultDisabled.forEach(function(val) {
		$('.actions > button.'+val).addClass('disabled');
	});
	
	$.ajax({
		url: '../ajax/cloud.php',
		type: 'POST',
		dataType: 'json',
		data: {
			task: 'dir_list',
			folder: folder,
		},
		success: function(response) {
			console.log(response);
			if(response.success == true) {
				var rows = "";
				if(response.data != "no entrys"){
					response.data.forEach(function(entry) { // as dataset
						rows += "<div class=\"item "+entry.type+"\" id=\""+entry.id+"\" data-filename=\""+entry.filename+"\">";
						if(entry.type == 'folder') {
							rows += "<img src=\"../core/images/folder_grey.svg\" class=\"image\" />";
						} else {
							var typeCategory = entry.mime_type.substr(0, entry.mime_type.search("/"));
							if(typeCategory == 'image') {
								rows += "<img src=\"../files?id="+SHA1(entry.id)+"&size=32&format=square\" class=\"image\" />";
							} else {
								rows += "<img src=\"../core/images/document_grey.svg\" class=\"image\" />";
							}
						}
							rows += "<span class=\"file filename\">"+entry.filename+"</span>";
						rows += "</div>";
					});
				}
				$('.explorer').html(rows);
				$('.ajax-loader').remove();
				$('body').css("cursor", "");
			}
		},
		error: function(xhr, textStatus, errorThrown){
			console.log('request failed: '+textStatus+xhr+errorThrown);
		}
	});
}