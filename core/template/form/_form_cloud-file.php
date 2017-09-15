{# #FIXME: show preview-img only if filetype = image, or show document icon #}
#if(showLabel):
	<label for="{{name}}">{{label}}</label>
#endif
<input type="hidden" name="{{name}}" id="{{name}}" value="{{value}}">
<img class="explorer-preview-img" data-for="{{name}}" src="{{MAIN_URL}}/file/{{value}}-s200">
<button class="btn explorer-file-select-btn" data-for="{{name}}"><?= __('select file'); ?></button>
<button class="btn explorer-file-remove-btn" data-for="{{name}}"><?= __('remove file'); ?></button>

<div class="explorer center" id="explorer-{{name}}" data-allowedtypes='{{allowedTypes}}'>
	<h3 class="headline"><?= __('select file'); ?></h3>
	<div class="breadcrumb"></div>
	<div class="browser"></div>
</div>
