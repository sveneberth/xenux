$(function() {
	$(document).click(function(e) {
		var container = $('.explorer');
		if (!$('.explorer-file-select-btn').is(e.target) && !container.is(e.target) && container.has(e.target).length === 0) {
			container.fadeOut(50);
			console.info('hide explorer');
		}
	});

	$('body').on('click', '.explorer-file-select-btn', function(e) {
		var name  = $(this).data('for');
		var value = $('#' + name).val();

		$('#explorer-' + name).show();
		xenuxcloud.loadbrowser(0);
		xenuxcloud.setbreadcrumb(0);
		e.preventDefault();
	});

	$('body').on('click', '.explorer .item', function(e) {
		var name = $('.explorer').attr('id').replace('explorer-', '');
		var id   = $(this).attr('id');
		var type = $(this).data('type');

		if (type == 'file') {
			$('#' + name).val(id)
			$('.explorer-preview-img[data-for="' + name + '"]').attr('src', baseurl + '/file/' + id + '-s100');
			$('#explorer-' + name).hide();
		} else {
			xenuxcloud.loadbrowser(id);
			xenuxcloud.setbreadcrumb(id);
		}
	});

	$('body').on('click', '.explorer .breadcrumb .item', function(e) {
		var id   = $(this).attr('id');

		xenuxcloud.loadbrowser(id);
		xenuxcloud.setbreadcrumb(id);
	});

	var xenuxcloud = {
		loadbrowser: function(folderID) {
			$.ajax({
				url: baseurl + '/administration/modules/cloud/ajax.php',
				type: 'POST',
				dataType: 'json',
				data: {
					task: 'dir_list',
					folder: folderID,
				},
				success: function(response) {
					console.log(response);
					if (response.success == true) {
						var allowedTypes = $('.explorer:visible').data('allowedtypes');
						var rows         = '';
						$.each(response.data, function(key, entry) {
							var filename = escapeHtml(entry.filename);
							var ext = entry.type == 'file' ? '.' + escapeHtml(entry.file_extension) : '';
							var typeCategory = entry.type == 'file' ? entry.mime_type.substr(0, entry.mime_type.search('/')) : '';
							if (entry.type == 'folder' || allowedTypes == '*' || allowedTypes.indexOf(entry.mime_type) > -1 || allowedTypes.indexOf(typeCategory + '/*') > -1)
							{
								rows += '<div class="item ' + entry.type + '" id="' + entry.id + '" data-type="' + entry.type + '">';
								if (entry.type == 'folder') {
									rows += '<img src="' + baseurl + '/administration/modules/cloud/folder.svg" class="image center has-space">';
								} else {
									if (typeCategory == 'image') {
										rows += '<img src="' + baseurl + '/file/' + entry.id + '-' + encodeURI(entry.filename) + '-s100-c" class="image is-img center">';
									} else {
										rows += '<img src="' + baseurl + '/administration/modules/cloud/document.svg" class="image center has-space">';
									}
								}
								rows += '<div class="file filename">' + filename + ext + '</div>';
								rows += '</div>';
							}

						});
						$('.explorer .browser').html(empty(rows) ? 'leerer Ordner' : rows);
					}
				}
			});
		},

		setbreadcrumb: function (id) {
			$.ajax({
				url: baseurl + '/administration/modules/cloud/ajax.php',
				type: 'POST',
				dataType: 'json',
				data: {
					task: 'breadcrumb',
					folder: id,
				},
				success: function(response) {
					console.log(response);
					if (response.success == true) {
						var rows = '<span class="item" id="0">root</span>';
						response.data.forEach(function(entry) { // as dataset
							rows += '<span class="item" id="' + entry.id + '">' + escapeHtml(entry.filename) + '</span>';
						});
						$('.explorer .breadcrumb').html(rows);
					}
				}
			});
		},
	};
});
