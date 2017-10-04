var ajaxURL = '{{ADMIN_URL}}/modules/cloud/ajax.php';
var imageURL = '{{ADMIN_URL}}/modules/cloud/';
var requestType = 'GET';
var defaultDisabled = ['remove', 'move', 'rename'];

$(function() {
	var url = window.location.href;
	var folderID = url.substr(url.indexOf(/cloud/) + 7);
	if (isInt(folderID)) {
		dir_list(folderID); // load folder
	} else {
		dir_list(0); // load root
	}

	$('.explorer').height($(window).height() - 254);
	$('.explorer').selectable({
		filter: ' > div',
		distance: 10, // needed for doubleclick events
		start: function() {
			$('body').css('cursor', 'crosshair');
		},
		stop: function() {
			$('.actions > button').removeClass('disabled');
			$('body').css('cursor', '');
		},
	});


	// history event
	window.addEventListener('popstate', function(e) {
		var state = e.state;
		console.log('my state: %s', state);
		if (state == null) {
			dir_list(0);
			document.title = 'root \u2013 <?= addslashes(__('cloud')) ?>';
		} else {
			dir_list(state.id);
			document.title = state.title + ' \u2013 <?= addslashes(__('cloud')) ?>';
		}
	})


	// drag&drop upload
	//$.event.props.push('dataTransfer');
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
		e.preventDefault();
		e.stopPropagation();
		var files = e.originalEvent.dataTransfer.files;
		upload(files);
		$('.drop-files').hide();
		return false;
	});


	// click events
	$('body').on('click', '.explorer .item', function(e) {
		if (!e.ctrlKey) { // if ctrl is pressed don't unselect the other items
			$('.explorer .item').removeClass('ui-selected');
		}
		$(this).toggleClass('ui-selected');
		$('.actions > button').removeClass('disabled');
	});
	$('body').on('click', '.explorer .item.ui-selected .filename:not(:has(input))', function(e) {
		var ID       = $(this).parent().attr('id');
		var filename = $(this).parent().data('filename');
		var ext      = $(this).parent().data('ext');
		console.log(ID, filename);

		// remove other inputs, has no effect on new input - its only tidier
		$('.explorer .item .filename:has(input)').each(function(entry) {
			var filename = $(this).parent().data('filename');
			$(this).text(filename);
		});

		$(this).html('<input class="rename-input" type="text" value="' + escapeHtml(filename) + '">&nbsp;' + escapeHtml(ext));
	});
	$('body').on('click', '.breadcrumb .treeitem', function() {
		var ID = $(this).attr('id');
		var filename = $(this).html();

		console.info('switched to folder: %s:%s', ID, filename);

		history.pushState({id: ID, title: filename}, null, '{{ADMIN_URL}}/cloud/' + ID);
		document.title = filename + ' \u2013 <?= addslashes(__('cloud')) ?>';

		dir_list(ID);
	});
	$(document).click(function(e) {
		var container = $('.popup-editor, #info-popup');
		if (
			!$('#contextmenu > *').is(e.target) &&
			!$('.actions > button.rename, .actions > button.rename *').is(e.target) &&
			!container.is(e.target) && container.has(e.target).length === 0
		) {
			container.fadeOut(50);
			console.info('popup hide');
		}
	});


	// double click events
	$('body').on('dblclick', '.explorer > .item.folder', function() {
		var ID = $(this).attr('id');
		var filename = $(this).data('filename');

		console.info('switched to folder: %s:%s', ID, filename);

		history.pushState({id: ID, title: filename}, null, '{{ADMIN_URL}}/cloud/' + ID);
		document.title = filename + ' \u2013 <?= addslashes(__('cloud')) ?>';

		dir_list(ID);
	});
	$('body').on('dblclick', '.explorer > .item.file', function() {
		var ID = $(this).attr('id');
		var filename = $(this).data('filename');
		console.info('opened file: ' + ID);

		window.open('{{MAIN_URL}}/file/' + ID + '-' + encodeURI(filename),'File','width=800,height=600,location=0,menubar=0,scrollbars=0,status=0,toolbar=0,resizable=0');
	});


	// right click
	var x, y;
	var contextmenuHeight = $('#contextmenu').outerHeight();
	var contextmenuWidth  = $('#contextmenu').outerWidth();
	document.oncontextmenu = function(e) {
		var target = e.target

		if ($(e.target).is('.item > *')) // if clicked on a child
			target = $(e.target).parent();

		if (!$(target).is('.item, .item *'))
			return true; // true show the browser default contextmenu and false show nothing

		e.preventDefault();

		$('.explorer > .item')	.removeClass('ui-selected');
		$(target)				.addClass('ui-selected');

		var id   = $(target).attr('id');
		var type = $(target).hasClass('file') ? 'file' : 'folder';

		$('#contextmenu').data('targetID',       id);
		$('#contextmenu').data('targetType',     type);

		x = e.clientX+contextmenuWidth	> $(window).width() 	? e.clientX-contextmenuWidth	: e.clientX;
		y = e.clientY+contextmenuHeight	> $(window).height()	? e.clientY-contextmenuHeight	: e.clientY;

		$('#contextmenu').css('left', x + 'px');
		$('#contextmenu').css('top' , y + 'px');
		$('#contextmenu').show();
	};
	$(document).mousedown(function(e) {
		if (!(e.clientX >= x && e.clientX <= (x + $('#contextmenu').width()) && e.clientY >= y && e.clientY <= (y + $('#contextmenu').height()))) {
			$('#contextmenu').hide();
		}
	});
	$(window).scroll(function () {
		$('#contextmenu').hide();
	});
	$('#contextmenu > .remove').click(function() {
		var id = $('#contextmenu').data('targetID');

		remove(id);
		$('#contextmenu').hide();
	});
	$('#contextmenu > .open').click(function() {
		var id       = $('#contextmenu').data('targetID');
		var type     = $('#contextmenu').data('targetType');
		var filename = $('.explorer > .item#' + id).data('filename');

		if (type == 'file') {
			openInNewTab('{{MAIN_URL}}/file/' + id + '-' + encodeURI(filename));
		} else {
			dir_list(id);
		}
		$('#contextmenu').hide();
	});
	$('#contextmenu > .download').click(function() {
		var id       = $('#contextmenu').data('targetID');
		var type     = $('#contextmenu').data('targetType');
		var filename = $('.explorer > .item#' + id).data('filename');

		openInNewTab('{{MAIN_URL}}/file/' + id + '-' + encodeURI(filename) + '-d');

		$('#contextmenu').hide();
	});
	$('#contextmenu > .fileinfo').click(function() {
		var id = $('#contextmenu').data('targetID');

		fileinfo(id);
		$('#contextmenu').hide();
	});
	$('#contextmenu > .rename').click(function() {
		var id       = $('#contextmenu').data('targetID');
		var filename = $('.explorer > .item#' + id).data('filename');

		$('.rename > input[type="text"]').val(filename);
		$('.rename').show();

		$('#contextmenu').hide();
	});
	$('#contextmenu > .move').click(function() {
		var id = $('#contextmenu').data('targetID');

		$.ajax({
			url: ajaxURL,
			type: requestType,
			dataType: 'json',
			data: {
				task: 'list_all_dirs',
			},
			success: function(response) {
				console.log(response);
				if (response.success == true) {
					var options = '';
					for(key in response.data) { // as dataset
						options += '<option value="' + key + '">' + response.data[key] + '</option>';
					};
					console.debug(options);
					$('.move-target > select').html(options);
					$('.move-target').show();
				}
			},
			error: function(xhr, textStatus, errorThrown){
				console.log('request failed: %s / %o / %s ', textStatus, xhr, errorThrown);
				alert('something went wrong...please try again');
			}
		});

		$('#contextmenu').hide();
	});


	// action events
	$('.actions > button.remove').click(function() {
		if ($('.explorer > .item.ui-selected').length == 0) {
			console.error('no item selected');
			return false;
		}
		$('.explorer > .item.ui-selected').each(function(i) {
			var ID = $(this).attr('id');
			remove(ID);
		});
	});
	$('.actions > button.move').click(function() {
		if ($('.explorer > .item.ui-selected').length == 0) {
			console.error('no item selected');
			return false;
		}

		$.ajax({
			url: ajaxURL,
			type: requestType,
			dataType: 'json',
			data: {
				task: 'list_all_dirs',
			},
			success: function(response) {
				console.log(response);
				if (response.success == true) {
					var options = '';
					for(key in response.data) { // as dataset,
						options += '<option value="' + key + '">' + response.data[key] + '</option>';
					};
					console.debug(options);
					$('.move-target').show();
					$('.move-target > select').html(options);
				}
			},
			error: function(xhr, textStatus, errorThrown){
				console.log('request failed: %s / %o / %s ', textStatus, xhr, errorThrown);
				alert('something went wrong...please try again');
			}
		});
	});
	$('.actions > button.rename').click(function() {
		if ($('.explorer > .item.ui-selected').length == 0) {
			console.error('no item selected');
			return false;
		}

		var firstSelObj = $('.explorer > .item.ui-selected').eq(0);
		var filename = firstSelObj.data('filename');

		$('.rename').show();
		$('.rename > input[type="text"]').val(filename);
	});

	$('body').on('click', '.popup-editor.move-target > input[type="button"]', function() {
		var to = $('.popup-editor.move-target > select').val();
		$('.explorer > .item.ui-selected').each(function(i) {
			var ID = $(this).attr('id');
			move(ID, to);
		});
		$('.popup-editor.move-target').hide();
	});
	$('body').on('keyup', '.popup-editor.rename > input[type="text"]', function(event) {
		if (event.keyCode == 13) {
			$('.popup-editor.rename > input[type="button"]').trigger('click');
		}
	});
	$('body').on('keyup', '.explorer .item.ui-selected .filename .rename-input', function(event) {
		if (event.keyCode == 13) {
			var ID = $(this).parent().parent().attr('id');
			var filename = $(this).val();
			console.log(ID, filename);
			rename(ID, filename);
		}
	});
	$('body').on('click', '.popup-editor.rename > input[type="button"]', function() {
		var newName = $('.popup-editor.rename > input[type="text"]').val();
		var firstSelObj = $('.explorer > .item.ui-selected').eq(0);
		rename(firstSelObj.attr('id'), newName);
		$('.popup-editor.rename').hide();
	});
	$('.actions > button.create_folder').click(function() {
		var folder_name = prompt('Ordnername');
		if (folder_name) {
			$.ajax({
				url: ajaxURL,
				type: requestType,
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
					console.log('request failed: %s / %o / %s ', textStatus, xhr, errorThrown);
					alert('something went wrong...please try again');
				}
			});
		}
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
			xhr.upload.addEventListener('progress', function(evt) {
				console.log('Number of this Upload: ' + thisUpload);
				if (evt.lengthComputable) {
					var percentComplete = evt.loaded / evt.total;
					percentComplete=parseInt(percentComplete*100);
					console.log(percentComplete);
					$('.upload-progress > progress.upload[data-upload-num="' + thisUpload + '"]').attr('value', percentComplete);

					if (percentComplete === 100) {
						notifyMe(
							'<?= addslashes(__('upload completed')) ?>',
							'<?= addslashes(__('upload completed long')) ?>',
							function() {
								window.open().close()
								window.focus()
							}
						);

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
		url: ajaxURL + '?task=upload&parent_folder=' + getFolder(),
		type: 'POST',
		dataType: 'json',
		data: FileData,
		processData: false,
		contentType: false,
		success: function(response) {
			console.log(response);
		},
		error: function(xhr, textStatus, errorThrown){
			console.log('request failed: %s / %o / %s ', textStatus, xhr, errorThrown);
			alert('something went wrong...please try again');
		}
	});
};
function remove(ID) {
	$.ajax({
		url: ajaxURL,
		type: requestType,
		dataType: 'json',
		data: {
			task: 'remove',
			id: ID
		},
		success: function(response) {
			console.log(response);
			dir_list(getFolder()); // refresh
		},
		error: function(xhr, textStatus, errorThrown){
			console.log('request failed: %s / %o / %s ', textStatus, xhr, errorThrown);
			alert('something went wrong...please try again');
		}
	});
}
function move(id, to) {
	$.ajax({
		url: ajaxURL,
		type: requestType,
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
			console.log('request failed: %s / %o / %s ', textStatus, xhr, errorThrown);
			alert('something went wrong...please try again');
		}
	});
	console.log(id, to);
}
function rename(id, newName) {
	$.ajax({
		url: ajaxURL,
		type: requestType,
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
			console.log('request failed: %s / %o / %s ', textStatus, xhr, errorThrown);
			alert('something went wrong...please try again');
		}
	});
	console.log(id, newName);
}
function getFolder() {
	var folder = $('.explorer').data('folder-id');
	if (isInt(folder)) {
		return folder;
	} else {
		return 0; // 0 == root
	}
};
function setbreadcrumb(folder) {
	$.ajax({
		url: ajaxURL,
		type: requestType,
		dataType: 'json',
		data: {
			task: 'breadcrumb',
			folder: folder,
		},
		success: function(response) {
			console.log(response);
			var rows = '<span class="treeitem" id="0">root</span>';
			if (response.success == true) {
				response.data.forEach(function(entry) { // as dataset
					rows += '<span class="treeitem" id="' + entry.id + '">' + entry.filename + '</span>';
				});
			} else if (response.status == 404) {
				$('.explorer').html('<?= addslashes(__('error')) ?> 404 - <?= addslashes(__('error404msg')) ?>');
			}
			$('.breadcrumb').html(rows);
		},
		error: function(xhr, textStatus, errorThrown){
			console.log('request failed: %s / %o / %s ', textStatus, xhr, errorThrown);
			alert('something went wrong...please try again');
		}
	});
}
function fileinfo(id) {
	// #TODO: folder selected -> show amount of containing items

	$.ajax({
		url: ajaxURL,
		type: requestType,
		dataType: 'json',
		data: {
			task: 'getFileInfo',
			id: id,
		},
		success: function(response) {
			console.log(response);
			if (response.success == true) {
				console.log(response.data);
				$('#info-popup span.filename').text(response.data.filename);
				$('#info-popup span.size').text(FileSizeConvert(response.data.size));
				$('#info-popup span.mimetype').text(response.data.mime_type);
				$('#info-popup span.lastModified').text(response.data.lastModified);
				$('#info-popup span.author').text(response.data.author);
				$('#info-popup').fadeIn(50);
			}
		},
		error: function(xhr, textStatus, errorThrown){
			console.log('request failed: %s / %o / %s ', textStatus, xhr, errorThrown);
			alert('something went wrong...please try again');
		}
	});
}
function dir_list(folder) {
	$('.explorer').html('');
	$('body').css('cursor', 'wait');
	$('.ajax-loader').show();
	$('.explorer').data('folder-id', folder);
	setbreadcrumb(folder);
	defaultDisabled.forEach(function(val) {
		$('.actions > button.' + val).addClass('disabled');
	});

	$.ajax({
		url: ajaxURL,
		type: requestType,
		dataType: 'json',
		data: {
			task: 'dir_list',
			folder: folder,
		},
		success: function(response) {
			console.log(response);
			if (response.success == true) {
				var rows = '';
				$.each(response.data, function(key, entry) { // as dataset
					var filename = escapeHtml(entry.filename);
					var ext = entry.type == 'file' ? '.' + escapeHtml(entry.file_extension) : '';
					rows += '<div class="item ' + entry.type + '" id="' + entry.id + '" data-filename="' + filename + '" data-ext="' + ext + '">';
					if (entry.type == 'folder') {
						rows += '<img src="' + imageURL + 'folder.svg" class="image">';
					} else {
						var typeCategory = entry.mime_type.substr(0, entry.mime_type.search('/'));
						if (typeCategory == 'image') {
							rows += '<img src="' + '{{MAIN_URL}}/file/' + entry.id + '-' + encodeURI(entry.filename) + '-s32-c" class="image">';
						} else {
							rows += '<img src="' + imageURL + 'document.svg" class="image">';
						}
					}
					rows += '<span class="file filename">' + filename + ext + '</span>';
					rows += '</div>';
				});
				$('.explorer').html(rows);
				$('.ajax-loader').hide();
				$('body').css('cursor', '');

				$('.explorer .item').draggable({
					handle: '.image, .filename',
					start: function(e, ui) {
						if (!$(this).hasClass('ui-selected')) {
							$(this).addClass('ui-selected');
						}
					},
					drag: function(e, ui) {
						$('.ui-selected').not(this).css({top: ui.position.top, left: ui.position.left});
					},
					stop: function(e, ui) {
						$('.ui-selected').animate({top:0, left:0}); // revert to origin position
					}
				});
				$('.explorer .item.folder').droppable({
					drop: function(e, ui) {
						var target = $(this).attr('id');
						$('.ui-selected').each(function() {
							var id = $(this).attr('id');
							move(id, target)
						});
					},
					start: function(e, ui) {
						console.log('s', e, ui);
					},
					drag: function(e, ui) {
						console.log('d', e, ui);
					},
				});
				/*
					#FIXME: drop in breadcrumb doesnt work
					it works only with helper: 'clone' in .draggable()
					and only by the direct selected item

					$('.breadcrumb .treeitem').droppable({
						drop: function(e, ui) {
							console.log(e, ui);
							var target = $(this).attr('id');
							$('.ui-selected').each(function() {
								var id = $(this).attr('id');
								move(id, target)
							});
						}
					});
				*/
			}
		},
		error: function(xhr, textStatus, errorThrown){
			console.log('request failed: %s / %o / %s ', textStatus, xhr, errorThrown);
			alert('something went wrong...please try again');
		}
	});
}
