{{messages}}

<div class="grid-row">
	<section class="box-shadow grid-col">
		<p><?= __('upload a zip file to install a template') ?></p>

		{{upload_form}}
	</section>
</div>

<div class="grid-row">
	<section class="box-shadow grid-col">
		<h3><?= _('all installed templates') ?>:</h3>
		<?php
			#TODO: style me

			$installed_templates = json_decode($app->getOption('installed_templates'));
			foreach ((array) $installed_templates as $name)
			{
				echo $name . '<a style="float: right;" href="' .
					ADMIN_URL . '/pluginmanager/templates?removeTemplate=' . $name . '">' .
					embedSVG(ADMIN_PATH . '/template/images/trash.svg') . '</a><br>';
			}
		?>
	</section>
</div>
