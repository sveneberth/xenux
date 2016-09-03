<script>
	var url = window.location.href;
	var title = document.title;
	var newUrl = url.substring(0, url.indexOf('?')) + window.location.hash;
	// replace new url
	if(window.history.replaceState) {
		window.history.replaceState(null, null, newUrl);
	}
</script>

{{messages}}

<section class="box-shadow floating one-column-box">
	<p><?= __('upload a zip file to install a template') ?></p>

	{{upload_form}}
</section>

<section class="box-shadow floating one-column-box">
	<h3><?= _('all installed templates') ?>:</h3>

	<?php
		#FIXME: style me

		$installed_templates = json_decode($app->getOption('installed_templates'));
		foreach ((array) $installed_templates as $name)
		{
			echo $name . '<a style="float: right;" href="' . URL_ADMIN . '/pluginmanager/templates?removeTemplate=' . $name . '">X</a><br>';
		}
	?>
</section>
