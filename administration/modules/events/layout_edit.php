<script>
	var url = window.location.href;
	var title = document.title;
	var newUrl = url.substring(0, url.indexOf('?')) + window.location.hash;
	// replace new url
	if(window.history.replaceState) {
		window.history.replaceState(null, null, newUrl);
	}
</script>
<script src="{{URL_ADMIN}}/wysiwyg/ckeditor.js"></script>
<script>
var isModified = false;
$(document).ready(function() {
	$(window).on('beforeunload', function(){
		if(isModified)
			return 'Alle nicht gespeicherte Daten gehen verloren!';
	});
	$(document).on("submit", "form", function(){
		$(window).off('beforeunload');
	});
	
	/**
	* settings for the wysiwyg-editor, a ckeditor
	*/
	CKEDITOR.config.extraPlugins = 'xenux-cloud';
	CKEDITOR.config.xenuxCloudAjaxURL = '{{URL_ADMIN}}/modules/cloud/ajax.php';

	var editor = CKEDITOR.replace('text', {
		toolbar: [
			{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
			{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll' ] },
			'/',
			{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
			{ name: 'insert', items: [ 'Link', 'Unlink', 'Image', 'Table', 'HorizontalRule', 'Smiley', 'PageBreak'] },
			'/',
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
			{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
			{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
			{ name: 'Xenux Cloud', items: [ 'xenux-cloud' ] },
		],
		extraAllowedContent: {
			img: {
				attributes: [ '!src', 'alt', 'width', 'height' , 'data-*' ],
				classes: { tip: true }
			},
		},
	});
	editor.on( 'instanceReady', function() {
		console.log( editor.filter.allowedContent );
		this.on('key', function(e) {
			isModified = true;
		});
	});

	$('input[name="title"], input[name="public"], input[name^="contact_"]').on('change', function(e) {
		isModified = true;
	});
});

</script>

{{messages}}

<section class="box-shadow floating one-column-box no-margin">
	#if(new):
		<p><?= __('here can you add a new event') ?></p>
	#else:
		<p><?= __('here can you edit the event') ?></p>
	#endif

	<div class="form">
		{{form}}
	</div>
</section>