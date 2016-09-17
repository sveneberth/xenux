CKEDITOR.dialog.add('xenux-cloud-dialog', function(editor) {
	return {
		title: 'Xenux Cloud',
		minWidth: 800,
		minHeight: 400,
		contents: [
			{
				id: 'tab-1',
				label: 'Browse for images',
				elements: [
					{
						type: 'html',
						align: 'left',
						id: 'titleid',
						style: 'font-size: 20px; font-weight: bold;',
						html: 'Browse for files'
					},
					{
						type: 'html',
						align: 'left',
						style: '',
						html: '<div id="breadcrumb"></div>'
					},
					{
						type: 'html',
						align: 'left',
						style: '',
						html: '<div id="browser"></div>'
					}
				]
			}
		]
	};
});


CKEDITOR.plugins.add('xenux-cloud', {
	init: function(editor) {
		var pluginDirectory = this.path;

		// add css file
		$('head').append('<link rel="stylesheet" href="'+pluginDirectory + 'style.css">');

		// default setting
		var folder	= 0;
		var size	= 200;

		editor.on('dialogShow', function(event) {
			var dialog = event.data;
			if (dialog.getName() == 'xenux-cloud-dialog') {
				CKEDITOR.tools.xenuxcloud_openfolder(0);
			}
		});

		editor.addCommand('xenux-cloud-start', new CKEDITOR.dialogCommand('xenux-cloud-dialog'));

		CKEDITOR.tools.xenuxcloud_loadbrowser = function(folderID) {
			$.ajax({
				url: CKEDITOR.config.xenuxCloudAjaxURL,
				type: 'POST',
				dataType: 'json',
				data: {
					task: 'dir_list',
					folder: folderID,
				},
				success: function(response) {
					console.log(response);
					if(response.success == true) {
						var rows = "";
						$.each(response.data, function(key, entry) {
							var filename = entry.filename.replace(/"/g, '&quot;');
							if(entry.type == 'folder') {
								url		= baseurl + '/administration/template/images/folder_grey.svg';
								action	= 'openfolder';
							} else {
								var typeCategory = entry.mime_type.substr(0, entry.mime_type.search("/"));
								if(typeCategory == 'image') {
									url		= baseurl + '/file/' + SHA1(entry.id) + '-s100-c';
									action	= 'insertpicture';
								} else {
									url		= baseurl + '/administration/template/images/document_grey.svg';
									action	= 'insertlinktofile';
								}
							}

							rows += '<div onclick="CKEDITOR.tools.xenuxcloud_' + action + "('" + entry.id + "','" + filename +
								'\');" style="background-image:url(\'' + url  + '\');" class="item ' +
								(action!='insertpicture' ? 'no-image' : '') + '"><div class="filename">' +
								filename + '</div></div>';
						});
						$('#browser').html(empty(rows) ? 'leerer Ordner' : rows);
					}
				}
			});
		};

		CKEDITOR.tools.xenuxcloud_insertlinktofile = function(id, filename) {
			var dialog = CKEDITOR.dialog.getCurrent();
			var html = '<a class="cloud-url" target="_blank" href="' + baseurl+'/file/'+SHA1(id) + '">' + filename + '</a>';
			editor.config.allowedContent = true;
			editor.insertHtml(html.trim());
			dialog.hide();
		};

		CKEDITOR.tools.xenuxcloud_insertpicture = function(id, filename) {
			var dialog = CKEDITOR.dialog.getCurrent();
			var html = '<img class="cloud-image" src="' + baseurl+'/file/'+SHA1(id) + '-s' + size +
				'" data-src="' + baseurl+'/file/'+SHA1(id) + '" alt="' + filename + '">';
			editor.config.allowedContent = true;
			editor.insertHtml(html.trim());
			dialog.hide();
		};

		CKEDITOR.tools.xenuxcloud_openfolder = function(id) {
			folder = id;
			CKEDITOR.tools.xenuxcloud_loadbrowser(id);
			CKEDITOR.tools.xenuxcloud_setbreadcrumb(id);
		};

		CKEDITOR.tools.xenuxcloud_setbreadcrumb = function (id) {
			$.ajax({
				url: CKEDITOR.config.xenuxCloudAjaxURL,
				type: 'POST',
				dataType: 'json',
				data: {
					task: 'breadcrumb',
					folder: id,
				},
				success: function(response) {
					console.log(response);
					if(response.success == true) {
						var rows = '<span onclick="CKEDITOR.tools.xenuxcloud_openfolder(0);" class="treeitem" id="0">root</span>';
						response.data.forEach(function(entry) { // as dataset
							rows += '<span onclick="CKEDITOR.tools.xenuxcloud_openfolder(' + entry.id +
								');"  class="treeitem" id="' + entry.id + '">' + entry.filename + '</span>';
						});
						$('#breadcrumb').html(rows);
					}
				}
			});
		}

		editor.ui.addButton('xenux-cloud', {
			label: 'Bilder aus Xenux Cloud einfügen',
			command: 'xenux-cloud-start',
			icon: this.path + 'images/icon.png'
		});
	}
});
