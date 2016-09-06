<style>
	/* must be here, because of the absolute paths */
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

</style>

<section class="box-shadow floating one-column-box">
	<div class="actions">
		<input type="file" class="file" value="<?= __('file') ?>" multiple style="display:none;">
		<button class="upload" data-title="<?= __('upload') ?>">
		<button class="create_folder" data-title="<?= __('create folder') ?>">
		<button class="remove" data-title="<?= __('remove') ?>">
		<button class="move" data-title="<?= __('move') ?>">
		<button class="rename" data-title="<?= __('rename') ?>">
	</div>
	<div class="upload-progress"></div>
	<div class="move-target popup-editor">
		<span><?= __('move to') ?></span>
		<select size="1"></select>
		<input type="button" value="<?= __('move') ?>">
	</div>
	<div class="rename popup-editor">
		<span><?= __('rename to') ?></span>
		<input type="text" value="">
		<input type="button" value="<?= __('rename') ?>">
	</div>
	<div class="breadcrumb"></div>
	<div class="explorer"></div>
	<div ondragover="return false" class="drop-files">
		<div>
			<div>
				<?= __('drag files to uplaod here') ?>
			</div>
		</div>
	</div>
	<div id="contextmenu">
		<span class="open"><?= __('open') ?></span>
		<span class="download"><?= __('download') ?></span>
		<span class="move"><?= __('move') ?></span>
		<span class="remove"><?= __('remove') ?></span>
		<span class="rename"><?= __('rename') ?></span>
		<span class="fileinfo"><?= __('properties') ?></span>
	</div>
	<div id="info-popup">
		<span class="headline"><?= __('properties') ?></span>
		<div class="details">
			<span class="filename" data-label="<?= __('filename') ?>"></span>
			<span class="mimetype" data-label="<?= __('mimetype') ?>"></span>
			<span class="size" data-label="<?= __('size') ?>"></span>
			<span class="lastModified" data-label="<?= __('lastModified') ?>"></span>
			<span class="author" data-label="<?= __('author') ?>"></span>
		</div>
	</div>
</section>
