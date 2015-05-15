#if(showLabel):
	<label for="{{name}}">{{label}}</label> 
#endif
<input type="text" name="{{name}}" id="{{name}}" value="{{value}}" readonly title="<?= __('you cannot change this value') ?>" />