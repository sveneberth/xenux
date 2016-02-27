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
	<p><?= __('upload a zip file to install a module') ?></p>

	{{upload_form}}
</section>

<section class="box-shadow floating one-column-box">
	<h3><?= _('all installed modules') ?>:</h3>

	<?php
		#FIXME: style me

		$installed_modules = json_decode($app->getOption('installed_modules'));
		foreach ((array) $installed_modules as $name)
		{
			echo $name . '<a style="float: right;" href="' . URL_ADMIN . '/pluginmanager/modules?removeModule=' . $name . '">X</a><br />';
		}
	?>
</section>