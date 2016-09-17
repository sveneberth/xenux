<!DOCTYPE html>
<html lang="<?= translator::getLanguage() ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<meta name="robots" content="noindex, nofollow, noarchive">

	<title>{{page_title}} | Xenux Backend</title>

	<link rel="baseurl" href="{{URL_MAIN}}">

	<!-- http://xenux.bplaced.net -->
	<meta name="generator" content="Xenux v{{XENUX_VERSION}} - das kostenlose CMS">

	<!-- css -->
	<link rel="stylesheet" type="text/css" href="{{TEMPLATE_PATH}}/css/style.min.css" media="all">
	{# <!-- <link rel="stylesheet" type="text/css" href="{{TEMPLATE_PATH}}/css/jquery-ui.css" media="all"> --> #}
	{{CSS-FILES}}

	<!-- jquery + plugins -->
	<script src="{{TEMPLATE_PATH}}/js/jquery-2.1.1.min.js"></script>
	<script src="{{TEMPLATE_PATH}}/js/jquery-migrate-1.2.1.min.js"></script>
	<script src="{{TEMPLATE_PATH}}/js/jquery-ui.min.js"></script>
	<script src="{{TEMPLATE_PATH}}/js/jquery.ui.touch-punch.min.js"></script>
	<script src="{{TEMPLATE_PATH}}/js/jquery.cookie.js"></script> <!-- need this here ?? -->
	<script src="{{TEMPLATE_PATH}}/js/jquery.mousewheel.js"></script>

	<!-- scripts -->
	<script src="{{TEMPLATE_PATH}}/js/functions.js"></script>
	<script src="{{TEMPLATE_PATH}}/js/script.js"></script>
	<script src="http://tablesorter.com/__jquery.tablesorter.min.js"></script>
	{{JS-FILES}}

	<script>
	$(document).ready(function() {
		$("#data-table").tablesorter();
	});
	</script>
</head>
<body id="top">
	<noscript>
		<div class="warning-noscript">
			<div>
				<?= __("noscript-message") ?>
			</div>
		</div>
		<style>
			header, menu, .wrapper {display: none;}
		</style>
	</noscript>

	<header>
		<a href="{{URL_MAIN}}" target="_blank" class="header-title"><?= $app->getOption('hp_name') ?></a>

		<div class="profile">
			<?= __('helloUser', $user->userInfo->username) ?>
			<div class="profile-sub">
				<img class="profile-image" src="{{TEMPLATE_PATH}}/images/profile.svg">

				<a class="profile-button" href="{{URL_ADMIN}}/users/profile">Profil</a>
				<a class="logout-button" href="{{URL_ADMIN}}/login?task=logout">Logout</a>
			</div>
		</div>

		<div class="languageSelector">
			<p>Change language:</p>
			<?php
				$langs = translator::getLanguages();
				if (count((array)$langs) > 1):
					?>
					<select onchange="window.location.href = '{{SITE_PATH}}?lang=' + $(this).val();" class="language-selector">
						<option disabled data-option-class="label" data-style="background-image:none;">Select Language</option>
						<?php
							foreach ($langs as $short => $options)
							{
								echo '<option value="' . $short . '" ' . ($short == translator::getLanguage() ? 'selected' : '') . ' >' . $options->label . '</option>';
							}
						?>
					</select>
					<?php
				endif;
			?>
		</div>
	</header>

	<menu class="main-menu-left">
		<ul class="menu">
			<li class="<?= $app->url[0] == 'dashboard' ? 'active open' : '' ?>">
				<a  class="<?= $app->url[0] == 'dashboard' ? 'active' : '' ?>" href="{{URL_ADMIN}}/dashboard"><?= __('dashboard') ?></a>
			</li>
			<?php
				$modules = $app->getAdminModule();
				foreach($modules as $module) // for module in modules
				{
					if (is_dir_empty(PATH_ADMIN."/modules/".$module) || $module == 'dashboard') // skip an empty folder and dashboard
						continue;


					if (file_exists(PATH_ADMIN.'/modules/'.$module.'/menu.json')) // if module-menu-file exist
					{
						// append translations
						translator::appendTranslations(PATH_ADMIN."/modules/".$module."/translation/");
						$filecontent = file_get_contents(PATH_ADMIN.'/modules/'.$module.'/menu.json');

						if ($json = is_json($filecontent, true)) // if file is a valid json-file
						{
							$headline = __($json->headline);

							echo '<li class="'.($app->url[0] == $module ? 'active open' : '').'">';
							echo '<a href="'.(isset($json->links) ? '#' : URL_ADMIN.'/'.$module).'">' . __($headline) . "</a>\n";

							if(isset($json->links))
							{
								echo '<ul style="'.($app->url[0] == $module ? '' : 'display:none')."\">\n";

								foreach($json->links as $label => $link)
								{
									echo "\t".'<li class="'.($app->url[0] == $module && $app->url[1]==str_replace('/','',$link) ? 'active' : '').'"><a href="'.URL_ADMIN.'/'.$module.$link.'">'.__($label).'</a></li>'."\n";
								}

								echo "</ul>\n";
							}

							echo "</li>\n";
						}
						else
						{
							// invalid json file (print nothing)
						}
					}
					else
					{
						echo "menufile doesn't exists";
					}
				}
			?>
		</ul>
	</menu>

	<div class="wrapper">
		<h1 class="page-headline box-shadow">{{headline}}{{headlineSuffix}}</h1>

		{{page_content}}

	</div>
</body>
</html>
