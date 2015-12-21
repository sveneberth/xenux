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
		if(isset($_GET['removeModule']) && full(@$_GET['removeModule']))
		{
			if(isset($_GET['confirmed']) && true == @$_GET['confirmed'])
			{
				$modules = json_decode($app->getOption('installed_modules'));
				if(in_array($_GET['removeModule'], $modules))
				{
					// uninstall

					$modulehelper = new modulehelper;
					include_once(PATH_MAIN . '/modules/' . $_GET['removeModule'] . '/uninstall.php'); // run uninstaller
				}
				else
				{
					echo 'err: module not installed';
				}
			}
			else
			{
				echo '<p>' . __('shure to remove?') . '</p>';
				echo '<a href="' . URL_ADMIN . '/modulemanager/modules?removeModule=' . $_GET['removeModule'] . '&confirmed=true">' . __('yes') . '</a>';
			}
		}

		$installed_modules = json_decode($app->getOption('installed_modules'));
		foreach ($installed_modules as $name)
		{
			echo $name . '<a href="' . URL_ADMIN . '/modulemanager/modules?removeModule=' . $name . '">X</a><br />';
		}
	?>
</section>