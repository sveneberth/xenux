<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="robots" content="noindex, nofollow, noarchive" />

	<title>{{page_title}} | Xenux Backend</title>
	
	<link rel="baseurl" href="{{URL_MAIN}}" />
	<script>var baseurl = '{{URL_MAIN}}';</script>

	<!-- http://xenux.bplaced.net -->
	<meta name="generator" content="Xenux v{{XENUX_VERSION}} - das kostenlose CMS" />
	
	<!-- css -->
	<link rel="stylesheet" type="text/css" href="{{TEMPLATE_PATH}}/css/style.css" media="all"/>
	<!-- <link rel="stylesheet" type="text/css" href="{{TEMPLATE_PATH}}/css/jquery-ui.css" media="all"/> -->
	
	<!-- jquery + plugins -->
	<script src="{{TEMPLATE_PATH}}/js/jquery-2.1.1.min.js"></script>
	<script src="{{TEMPLATE_PATH}}/js/jquery-migrate-1.2.1.min.js"></script>
	<script src="{{TEMPLATE_PATH}}/js/jquery-ui.min.js"></script>
	<script src="{{TEMPLATE_PATH}}/js/jquery.ui.touch-punch.min.js"></script>
	<script src="{{TEMPLATE_PATH}}/js/jquery.cookie.js"></script> <!-- need this here ?? -->
	<script src="{{TEMPLATE_PATH}}/js/jquery.mousewheel.js"></script>
	
	<!-- scripts -->
	<script src="{{TEMPLATE_PATH}}/js/functions.js"></script>
	<script>var default_active_menu_left = {{num_active_module}};</script>
	<script src="{{TEMPLATE_PATH}}/js/script.js"></script>
</head>
<body id="top">
	<header>

		<a href="{{URL_MAIN}}" target="_blank" class="header-title"><?= $app->getOption('hp_name') ?></a>

		<div class="profile">
			<?= __('helloUser', $user->userInfo->username) ?>
			<div class="profile-sub">
				<img class="profile-image" src="{{TEMPLATE_PATH}}/images/profile.svg" />
				
				<a class="profile-button" href="{{URL_ADMIN}}/users/profile">Profil</a>
				<a class="logout-button" href="{{URL_ADMIN}}/login?task=logout">Logout</a>
			</div>
		</div>
	</header>
	
	<menu class="main-menu-left">
		<h2><?= __('dashboard') ?></h2>
		<ul>
			<li class="<?= $app->url[0]=='dashboard'?'active':'' ?>"><a href="https://localhost/xenux_dev/administration/dashboard"><?= __('dashboard') ?></a></li>
		</ul>
		<?php
			$modules = $app->getAdminModule();
			foreach($modules as $module) // for module in modules
			{
				if ($module == 'dashboard') // skip dashboard
					continue;

				if (file_exists(PATH_ADMIN.'/modules/'.$module.'/menu.json')) // if module-menu-file exist
				{
					$filecontent = file_get_contents(PATH_ADMIN.'/modules/'.$module.'/menu.json');

					if ($json = is_json($filecontent, true)) // if file is a valid json-file
					{
						$headline = __($json->headline);

						echo "<h2>$headline</h2>\n";
						echo "<ul>\n";

						foreach($json->links as $label => $link)
						{
							echo "\t".'<li class="'.($app->url[0] == $module && $app->url[1]==str_replace('/','',$link)?'active':'').'"><a href="'.URL_ADMIN.'/'.$module.$link.'">'.$label.'</a></li>'."\n";
						}

						echo "</ul>\n";
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
	</menu>
	
	<div class="wrapper">
		<h1 class="page-headline box-shadow">{{headline}}</h1>

		{{page_content}}

	</div>
</body>
</html>