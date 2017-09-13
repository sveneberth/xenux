{# #FIXME: build me pretty #}
{# #FIXME: include css, actually it works only if there is a wysiwyg field #}
<img class="preview-img" data-for="{{name}}" src="{{URL_MAIN}}/file/{{value}}-s100">
<input type="hidden" name="{{name}}" id="{{name}}" value="{{value}}">
<button class="button btn file-select-btn" data-for="{{name}}">SELECT FILE</button>
<div class="explorer center">
	<h3 class="headline">Browse for files</h3>
	<div class="breadcrumb"></div>
	<div class="browser"></div>
</div>

<script>
// default setting
var folder	= 0;
var size	= 200;

$('body').on('click', '.file-select-btn', function(e) {
	var name  = $(this).data('for');
	var value = $('#' + name).val();

	$('.explorer').show().data('for', name);
	xenuxcloud.loadbrowser(0);
	xenuxcloud.setbreadcrumb(0);
	e.preventDefault();
});

$('body').on('click', '.browser .item', function(e) {
	var name = $('.explorer').data('for');
	var id   = $(this).data('id');
	var type = $(this).data('type');

	if (type == 'file') {
		$('#' + name).val(id)
		$('.preview-img[data-for="' + name + '"]').attr('src', '{{URL_MAIN}}/file/' + id + '-s100');
		$('.explorer').hide();
	} else {
		xenuxcloud.loadbrowser(id);
		xenuxcloud.setbreadcrumb(id);
	}
});

$('body').on('click', '.breadcrumb .treeitem', function(e) {
	var id   = $(this).attr('id');

	xenuxcloud.loadbrowser(id);
	xenuxcloud.setbreadcrumb(id);
});

var xenuxcloud = {
	loadbrowser: function(folderID) {
		$.ajax({
			url: '{{URL_ADMIN}}/modules/cloud/ajax.php',
			type: 'POST',
			dataType: 'json',
			data: {
				task: 'dir_list',
				folder: folderID,
			},
			success: function(response) {
				console.log(response);
				if (response.success == true) {
					var rows = "";
					$.each(response.data, function(key, entry) {
						var filename = entry.filename.replace(/"/g, '&quot;');
						if (entry.type == 'folder') {
							url		= baseurl + '/administration/modules/cloud/folder.svg';
						} else {
							var typeCategory = entry.mime_type.substr(0, entry.mime_type.search("/"));
							if(typeCategory == 'image') {
								url		= baseurl + '/file/' + entry.id + '-' + encodeURI(filename) + '-s100-c';
								var action	= 'insertpicture';
							} else {
								url		= baseurl + '/administration/modules/cloud/document.svg';
								action	= 'insertlinktofile';
							}
						}
						if (entry.type == 'folder' || typeCategory == 'image') {
							rows += '<div data-id="' + entry.id + '" data-type="' + entry.type + '" style="background-image:url(\'' + url + '\');" class="item ' +
								(typeCategory!='image' ? 'no-image' : '') + '"><div class="filename">' +
								filename + '</div></div>';
						}
					});
					$('.browser').html(empty(rows) ? 'leerer Ordner' : rows);
				}
			}
		});
	},

	setbreadcrumb: function (id) {
		$.ajax({
			url: '{{URL_ADMIN}}/modules/cloud/ajax.php',
			type: 'POST',
			dataType: 'json',
			data: {
				task: 'breadcrumb',
				folder: id,
			},
			success: function(response) {
				console.log(response);
				if(response.success == true) {
					var rows = '<span class="treeitem" id="0">root</span>';
					response.data.forEach(function(entry) { // as dataset
						rows += '<span class="treeitem" id="' + entry.id + '">' + entry.filename + '</span>';
					});
					$('.breadcrumb').html(rows);
				}
			}
		});
	},
};
</script>
<style>
.explorer {
    position: absolute;
    display: block;
    width: 80%;
    height: 80%;
    z-index: 9999;
    background: #fff;
    border: 1px solid #ccc;
    padding: 10px;
    display: none;
}
.explorer .breadcrumb {
    margin-bottom: 1em;
}
</style>
