<script src="{{URL_ADMIN}}/wysiwyg/ckeditor.js"></script>
<script>
	var url = window.location.href;
	var title = document.title;
	var newUrl = url.substring(0, url.indexOf('?')) + window.location.hash;
	// replace new url
	if(window.history.replaceState) {
		window.history.replaceState(null, null, newUrl);
	}


	var isModified = false;
	$(function() {
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
				{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
				{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
				{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
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

<style>
.contact-persons-wrapper {
	margin: 1em 0;
}
.contact-persons-wrapper h3 {
	font-size: 1.3em;
	font-weight: bold;
}
.contact-persons-wrapper input[type="checkbox"] {
	display: none;
}
.contact-persons-wrapper input[type="checkbox"]:checked + label{
	background: rgba(54, 98, 148, 0.8);
	position: relative;
}
.contact-persons-wrapper input[type="checkbox"]:checked + label:after {
	background-image: url('{{TEMPLATE_PATH}}/images/checkround.svg');
	background-position: center;
	background-size: 100%;
	height: 1.5rem;
	width: 1.5rem;
	position: absolute;
	top: .4rem;
	right: 10px;
	content: '';
}
.contact-persons-wrapper label {
	display: inline-block;
	background: rgba(54, 98, 148, 1);
	border: 1px solid #fff;
	padding: 8px 12px;
	width: 20%;
	box-sizing: border-box;
	transition: .1s ease background;
	margin: 0 5px 5px 0;
	color: #fff;
	font-weight: normal;
}
.contact-persons-wrapper label:hover {
	background: #555;
}
</style>

{{messages}}

<div class="grid-row">
	<section class="box-shadow grid-col">
		#if(new):
			<p><?= __('here can you add a new site') ?></p>
		#else:
			<p><?= __('here can you edit the site') ?></p>
		#endif

		<div class="form">
			{{form}}
		</div>
	</section>
</div>
