<form action="{{action}}" method="{{method}}" class="{{class}}">
	{{error_msg}}
	{{fields}}
	
	#if(requiredInfo):
	<p><?= __('with a star marked fields are mandatory fields') ?></p>
	#endif
</form>