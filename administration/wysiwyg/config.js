/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.config.xenuxCloudAjaxURL = baseurl + '/administration/modules/cloud/ajax.php';
CKEDITOR.editorConfig = function( config ) {
	config.extraPlugins = 'xenux-cloud';
	config.extraAllowedContent = 'img[data-*](*)';

	config.toolbar = [
		{ name: 'clipboard', items: [ 'Undo', 'Redo' ] },
		{ name: 'editing', items: [ 'Scayt' ] },
		{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
		{ name: 'insert', items: [ 'Image', 'HorizontalRule', 'SpecialChar', 'xenux-cloud' ] },
		{ name: 'tools', items: [ 'Maximize' ] },
		{ name: 'document', items: [ 'Source' ] },
		'/',
		{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
		{ name: 'styles', items: [ 'Format' ] },
		{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] }
	];

	config.format_tags = 'p;h1;h2;h3;h4;h5;h6;pre;code;address;div';
	config.format_code = {element: 'code'};
};

var currentEditor;
for (var i in CKEDITOR.instances) {
	console.log('watch instance %s, %o', i, CKEDITOR.instances[i]);
	CKEDITOR.instances[i].on('change', function(e) {
		isModified = true;
		console.log('change in %s', e.editor.name)
	});
	CKEDITOR.instances[i].on('focus', function(e) {
		currentEditor = e.editor.name;
	});
}
