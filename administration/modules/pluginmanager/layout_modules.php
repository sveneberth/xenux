{{messages}}

<div class="grid-row">
	<section class="box-shadow grid-col">
		<p><?= __('upload a zip file to install a module') ?></p>

		{{upload_form}}
	</section>
</div>

<div class="grid-row">
	<section class="box-shadow grid-col">
		<h3><?= _('all installed modules') ?>:</h3>
		<?php
			#TODO: style me

			$installed_modules = json_decode($app->getOption('installed_modules'));
			foreach ((array) $installed_modules as $name)
			{
				echo $name . '<a style="float: right;" href="' . URL_ADMIN . '/pluginmanager/modules?removeModule=' . $name . '">X</a><br>';
			}
		?>
	</section>
</div>
