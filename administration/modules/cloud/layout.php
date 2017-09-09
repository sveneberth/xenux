<div class="grid-row">
	<section class="box-shadow grid-col">
		<div class="actions">
			<input type="file" class="file hide" value="<?= __('file') ?>" multiple>
			<button class="action-button upload" data-title="<?= __('upload') ?>">
				<svg xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 0 32 32" width="32">
					<path d="M15.36 2.667L11.2 7l-.96-1L16 0l5.76 6-.96 1-4.16-4.333v14.666h-1.28V2.667zM21.76 20h8.672l-5.6-9.333H17.92V9.333h7.68L32 20v12H0V20L6.4 9.333h7.68v1.334H7.168L1.568 20h8.672v2.667a2.56 2.667 0 0 0 2.556 2.666h6.408a2.552 2.659 0 0 0 2.556-2.666V20z"/>
				</svg>
			</button>
			<button class="action-button create_folder" data-title="<?= __('create folder') ?>">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="32" height="32">
				  <path d="M26 23h-3v-1h3v-3h1v3h3v1h-3v3h-1v-3zm-4.978 3H1.992A1.997 1.997 0 0 1 0 24.01V13h29v3.498A6.5 6.5 0 0 0 21.022 26zM0 12V5.99C0 4.89.897 4 2.003 4H13l2 4h11.994A2 2 0 0 1 29 9.995V12H0zm26.5 16a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11z"/>
				</svg>
			</button>
			<button class="action-button remove" data-title="<?= __('remove') ?>">
				<svg xmlns="http://www.w3.org/2000/svg" height="32px" version="1.1" viewBox="0 0 32 32" width="32px">
					 <path fill-rule="evenodd" d="M21.333 3.556h4.741V4.74H5.926V3.556h4.74V2.37c0-1.318 1.06-2.37 2.368-2.37h5.932a2.37 2.37 0 0 1 2.367 2.37v1.186zM5.926 5.926v22.517A3.55 3.55 0 0 0 9.482 32h13.036a3.556 3.556 0 0 0 3.556-3.557V5.926H5.926zm4.74 3.555v18.963h1.186V9.481h-1.185zm4.741 0v18.963h1.186V9.481h-1.186zm4.741 0v18.963h1.185V9.481h-1.185zm-7.107-8.296c-.657 0-1.19.526-1.19 1.185v1.186h8.297V2.37c0-.654-.519-1.185-1.189-1.185h-5.918z"/>
				</svg>
			</button>
			<button class="action-button move" data-title="<?= __('move') ?>">
				<svg xmlns="http://www.w3.org/2000/svg" height="32px" version="1.1" viewBox="0 0 32 32" width="32px">
					<path d="M18 20v6h4l-6 6-6-6h4v-6M14 12V6h-4l6-6 6 6h-4v6M12 18H6v4l-6-6 6-6v4h6M20 14h6v-4l6 6-6 6v-4h-6"/>
				</svg>
			</button>
			<button class="action-button rename" data-title="<?= __('rename') ?>">
				<svg xmlns="http://www.w3.org/2000/svg" height="32px" version="1.1" viewBox="0 0 32 32" width="32px">
					<path fill-rule="evenodd" d="M27.61 10.845L14.319 24.237l-3.069-3.088 13.3-13.385 3.062 3.08zm.833-.839l2.036-2.052a1.192 1.192 0 0 0-.005-1.678l-1.39-1.394a1.174 1.174 0 0 0-1.668-.004L25.38 6.926l3.062 3.08zM10.486 22.057l-.753 3.69 3.692-.732-2.939-2.958zm11.891-4.28v11.856A2.357 2.357 0 0 1 20.025 32H2.352C1.05 32 0 30.935 0 29.62V2.38C0 1.064 1.06 0 2.366 0h11.767v7.113a2.36 2.36 0 0 0 2.363 2.368H21.2l5.394-5.428a2.338 2.338 0 0 1 3.333-.011l1.39 1.398c.914.92.912 2.425-.012 3.354l-8.928 8.984zM15.311 0v7.108c0 .656.531 1.188 1.167 1.188h5.9L15.31 0z"/>
				</svg>
			</button>
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
		<div class="ajax-loader hide">
			<?php echo embedSVG(PATH_ADMIN . '/template/images/spinner.svg'); ?>
		</div>
	</section>
</div>
