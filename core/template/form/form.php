<form action="{{action}}" method="{{method}}" id="{{id}}" class="{{class}}" enctype="multipart/form-data">
	{{error_msg}}
	{{fields}}
{#
	#if(requiredInfo):
	<p class="required-info"><small><?= __('with a star marked fields are mandatory fields') ?></small></p>
	#endif
#}
</form>
