<?php
if($login->role < 1) {
	echo '<p>Du bist nicht berechtigt, diese Seite zu öffnen!</p>';
	return false;
}
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
?>
<script src="../core/js/cloud.js"></script>
<style>
	.actions > button.upload {background-image: url('../core/../core/images/upload_grey.svg');}
	.actions > button.upload:not(.disabled):hover {background-image: url('../core/images/upload_blue.svg');}
	.actions > button.create_folder {background-image: url('../core/images/folder-add_grey.svg');}
	.actions > button.create_folder:not(.disabled):hover {background-image: url('../core/images/folder-add_blue.svg');}
	.actions > button.remove {background-image: url('../core/images/trash_grey.svg');}
	.actions > button.remove:not(.disabled):hover {background-image: url('../core/images/trash_blue.svg');}
	.actions > button.move {background-image: url('../core/images/move_grey.svg');}
	.actions > button.move:not(.disabled):hover {background-image: url('../core/images/move_blue.svg');}
	.actions > button.edit {background-image: url('../core/images/document-edit_grey.svg');}
	.actions > button.edit:not(.disabled):hover {background-image: url('../core/images/document-edit_blue.svg');}
</style>
<div class="actions">
	<input type="file" class="file" value="Datei" multiple style="display:none;" />
	<button class="upload" mytitle="hochladen" />
	<button class="create_folder" mytitle="Ordner erstellen" />
	<button class="remove" mytitle="löschen" />
	<button class="move" mytitle="Verschieben" />
	<!-- <button class="edit" mytitle="Unbenennen" /> -->
</div>
<div class="move-target popup-editor">
	<span>Verschieben nach</span>
	<select size="1"></select>
	<input type="button" value="Verschieben" />
</div>
<div class="rename popup-editor">
	<span>Unbenennen nach</span>
	<select size="1"></select>
	<input type="button" value="Verschieben" />
</div>
<div class="breadcrumb"></div>
<div class="explorer"></div>
<div ondragover="return false" class="drop-files">
	Dateien zum hochladen hier ablegen
</div>