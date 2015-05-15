<script src="{{TEMPLATE_PATH}}/js/cloud.js"></script>
<style>
	.actions {
		background: #eee;
		padding: 15px;
		height: 30px;
	}
	.actions > button[data-title]:not(.disabled) {
		position: relative;
	}
	.actions > button[data-title]:not(.disabled):after {
		display: block;
		position: absolute;
		left: -999em;
		top: 35px;
		padding: .3em;
		border: 1px solid #2a2a2a;
		color: #fff;
		background: #333;
		text-decoration: none;
		content: attr(data-title);
		font-size: 14px;
		width: 150px;
		z-index: 9999;
		opacity: 0;
		transition: opacity .4s .3s ease;
	}
	.actions > button[data-title]:not(.disabled):hover:after {
		left: -61px;
		opacity: 1;
	}
	.actions > button:not(.disabled):hover {border: 1px solid #157efb;}
	.actions > button {
		width: auto;
		display: inline-block;
		margin: 0 5px 0 0;
		width: 30px;
		height: 30px;
		padding: 10px;
		background-color: #fff;
		background-position: center;
		background-size: 90%;
		background-repeat: no-repeat;
		border: 1px solid #929292;
	}
	.actions > button.disabled {
		box-shadow: inset 1px 1px 2px rgba(0, 0, 0, 0.4);
		background-color: #DBDBDB;
		cursor: no-drop;
	}


	.breadcrumb {
		background: #eee;
		padding: 5px 15px;
		margin-bottom: 0;
		border-top: 2px solid #2C6DD2;
		border-bottom: 2px solid #2C6DD2;
		line-height: 30px;
	}
	.breadcrumb .treeitem {
		margin-right: 10px;
		font-size: 1.2em;
		color: #333;
		display: inline-block;
	}
	.breadcrumb .treeitem:hover {
		color: #777;
		cursor: pointer;
	}
	.breadcrumb .treeitem:not(:last-child):after {
		content: '›';
		display: inline-block;
		margin-left: 10px;
	}

	.explorer {
		background: #fff;
		color: #333;
		overflow-y: auto;
	}
	.explorer > .item {
		padding: 10px;
		line-height: 2em;
		display: block;
	}
	.explorer > .item:nth-child(odd) {
		background: #fff;
	}
	.explorer > .item:nth-child(even) {
		background: #eee;
	}
	.explorer > .item.select {
		padding: 20px 10px;
		background: #4C90DD;
	}
	.explorer > .item .image {
		height: 2em;
		width: 2em;
		margin-right: 10px;
		display: inline-block;
	}
	.upload-progress {
		position: fixed;
		bottom: 0px;
		width: 100%;
		left: 0;
		max-height: 200px;
		display: block;
		overflow: auto;
		background: #fff;
		transition: .5s linear all;
	}
	.upload-progress > progress {
		position: relative;
		margin-top: 10px;
		width: 30%;
		left: 35%;
		height: 50px;
		transition: .5s linear all;
		display: block;
	}
	.upload-progress > progress:before {
		position: absolute;
		line-height: 1.5em;
		font-size: 2em;
		text-align: center;
		width: 100%;
	}
	.upload-progress > progress.uploading:before {
		content: 'hochladen - ' attr(value) '%';
	}
	.upload-progress > progress[value="100"]:before {
		content: 'hochladen abgeschlossen';
	}
	.upload-progress > progress[value]::-webkit-progress-value {
		transition: .5s linear all;
	}
	.ajax-loader {
		position: fixed;
		top: 40%;
		left: 48%;
		height: 50px;
		width: 50px;
		z-index: 100;
		background-position: center;
		background-size: 100%;
		background-image: url('{{TEMPLATE_PATH}}/images/ajax-loader.gif');
	}
	 #feedback { font-size: 1.4em; }
	.explorer .ui-selecting {background: #80BBFF!important;}
	.explorer .ui-selected { background: #4C90DD!important; }
	.ui-selectable-helper {z-index:100;border: 1px dotted #225793;position: absolute;background: rgba(34, 87, 147, 0.2); }

	.drop-files {
		background: rgba(255, 255, 255, 0.9);
		width: 90%;
		height: 90%;
		top: 5%;
		left: 5%;
		border: 2px solid #2c6dd2;
		position: fixed;
		display: none;
		box-sizing: border-box;
		color: #2C6DD2;
		z-index: 999999;
		box-shadow: 0 0 20px rgba(0, 0, 0, 0.9);
	}
	.drop-files > div {
		position: relative;
		width: 100%;
		height: 100%;
	}
	.drop-files > div > div {
		position: absolute;
		top: 0;
		left: 0;
		bottom: 0;
		right: 0;
		margin: auto;
		display: block;
		line-height: 60px;
		font-size: 60px;
		text-align: center;
		width: 100%;
		height: 60px;
	}
	.popup-editor {
		position: fixed;
		top: 30%;
		width: 40%;
		left: 30%;
		box-sizing: border-box;
		z-index: 9999;
		background: rgba(255, 255, 255, .95);
		color: #333;
		padding: 20px;
		border: 2px solid #333;
		text-align: center;
		display: none;
		box-shadow: 0 0 10px rgba(0, 0, 0, 0.6);
	}
	.popup-editor > span {
		font-size: 2em;
		line-height: 2em;
	}
	.popup-editor > input,
	.popup-editor > select {
		display: block;
		margin: 0; 
		width: 100%;
	}
	.popup-editor > input[type="button"] {
		display: block;
		margin: 0;
		margin-top: 10px;
		width: 100%;
	}
	.actions > button.upload {background-image: url('{{TEMPLATE_PATH}}/images/upload_grey.svg');}
	.actions > button.upload:not(.disabled):hover {background-image: url('{{TEMPLATE_PATH}}/images/upload_blue.svg');}
	.actions > button.create_folder {background-image: url('{{TEMPLATE_PATH}}/images/folder-add_grey.svg');}
	.actions > button.create_folder:not(.disabled):hover {background-image: url('{{TEMPLATE_PATH}}/images/folder-add_blue.svg');}
	.actions > button.remove {background-image: url('{{TEMPLATE_PATH}}/images/trash_grey.svg');}
	.actions > button.remove:not(.disabled):hover {background-image: url('{{TEMPLATE_PATH}}/images/trash_blue.svg');}
	.actions > button.move {background-image: url('{{TEMPLATE_PATH}}/images/move_grey.svg');}
	.actions > button.move:not(.disabled):hover {background-image: url('{{TEMPLATE_PATH}}/images/move_blue.svg');}
	.actions > button.rename {background-image: url('{{TEMPLATE_PATH}}/images/document-edit_grey.svg');}
	.actions > button.rename:not(.disabled):hover {background-image: url('{{TEMPLATE_PATH}}/images/document-edit_blue.svg');}

	div#contextmenu {
		position: fixed;
		display: none;
		z-index: 1000;
		border: 2px solid #000;
		background: #fff;
		padding: 5px 0;
		font-size: .9em;
	}
	div#contextmenu > * {
		display: block;
		margin: 2px 0;
		padding: 5px 15px;
		color: #222;
		text-decoration: none;
		cursor: pointer;;
	}
	div#contextmenu > *:hover {
		background: #ddd;
	}

	#info-popup {
		position: fixed;
		top: 30%;
		width: 30%;
		left: 35%;
		box-sizing: border-box;
		z-index: 9999;
		background: rgba(255, 255, 255, .95);
		color: #333;
		padding: 20px;
		border: 2px solid #333;
		display: none;
		box-shadow: 0 0 10px rgba(0, 0, 0, 0.6);
	}
	#info-popup span.headline {
		font-size: 1.5em;
		margin-bottom: 15px;
		display: block;
	}
	#info-popup .details span {
		font-size: 1em;
		line-height: 1em;
		display: block;
		padding-left: 100px;
		word-wrap: break-word;
		margin-bottom: 4px;
	}
	#info-popup .details span:before {
		content: '';
		content: attr(data-label) ":";
		display: inline-block;
		width: 100px;
		font-weight: bold;
		margin-left: -100px;
	}
</style>

<section class="box-shadow floating one-column-box">
	<div class="actions">
		<input type="file" class="file" value="Datei" multiple style="display:none;" />
		<button class="upload" data-title="hochladen" />
		<button class="create_folder" data-title="Ordner erstellen" />
		<button class="remove" data-title="löschen" />
		<button class="move" data-title="Verschieben" />
		<button class="rename" data-title="Unbenennen" />
	</div>
	<div class="upload-progress"></div>
	<div class="move-target popup-editor">
		<span>Verschieben nach</span>
		<select class="nolabel" size="1"></select>
		<input type="button" value="Verschieben" />
	</div>
	<div class="rename popup-editor">
		<span>Unbenennen nach</span>
		<input type="text" value="" class="nolabel" />
		<input type="button" value="Unbenennen" />
	</div>
	<div class="breadcrumb"></div>
	<div class="explorer"></div>
	<div ondragover="return false" class="drop-files">
		<div>
			<div>
				Dateien zum hochladen hier ablegen
			</div>
		</div>
	</div>
	<div id="contextmenu">
		<span class="open"><?= __('open') ?></span>
		<span class="download"><?= __('download') ?></span>
		<span class="move"><?= __('move') ?></span>
		<span class="remove"><?= __('remove') ?></span>
		<span class="rename"><?= __('rename') ?></span>
		<span class="fileinfo"><?= __('fileinfo') ?></span>
	</div>
	<div id="info-popup">
		<span class="headline"><?= __('fileinfos') ?></span>
		<div class="details">
			<span class="filename" data-label="<?= __('filename') ?>"></span>
			<span class="mimetype" data-label="<?= __('mimetype') ?>"></span>
			<span class="size" data-label="<?= __('size') ?>"></span>
			<span class="lastModified" data-label="<?= __('lastModified') ?>"></span>
			<span class="author" data-label="<?= __('author') ?>"></span>
		</div>
	</div>
</section>