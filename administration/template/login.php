{# #TODO: translation #}
<!DOCTYPE html>
<html lang="<?= translator::getLanguage() ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex, follow, noarchive">

	<title>Login>{{page_name}} | {{homepage_name}} Backend</title>

	<!-- http://xenux.bplaced.net -->
	<meta name="generator" content="Xenux v{{XENUX_VERSION}} - das kostenlose CMS">

	<!-- <link rel="shortcut icon" href="{{TEMPLATE_PATH}}/img/favicon.png"> -->

	<!-- css -->
	<link rel="stylesheet" href="{{TEMPLATE_PATH}}/css/login.css" media="all">
</head>
<body id="top">
	#if(logout):
		<p class="logout-message box-shadow">Du hast dich erfolgreich ausgeloggt!</p>
	#endif

	<div class="login-wrapper">

		<?php if($action == 'login'): ?>
			<div class="login-box box-shadow">
				<h2>{{page_name}}</h2>

				{{message}}
				{{form}}

			</div>
			<p class="center forgot">
				<a class="forgotusername" href="{{URL_ADMIN}}/login?task=forgotusername"><?= __("forgotUsername") ?>?</a> |
				<a class="forgotpassword" href="{{URL_ADMIN}}/login?task=forgotpassword"><?= __("forgotPassword") ?>?</a>
			</p>
			<?php if(parse_bool($app->getOption('users_can_register'))): ?>
				<a class="center register" href="{{URL_ADMIN}}/login?task=register"><?= __('register') ?></a>
			<?php endif; ?>
		<?php endif; ?>


		<?php if($action == 'register'): ?>
			<div class="login-box box-shadow">
				<h2>{{page_name}}</h2>

				{{message}}
				{{form}}
			</div>
			<a class="center login" href="{{URL_ADMIN}}/login"><?= __('login') ?></a>
		<?php endif; ?>


		<?php if($action == 'forgotusername'): ?>
			<div class="login-box box-shadow">
				<h2>{{page_name}}</h2>

				<p class="info">Falls du deinen Benutzernamen vergessen kannst, kannst du ihn hier an die Registrierte E-Mail-Adresse schicken.</p>
				{{message}}
				{{form}}
			</div>
			<a class="center login" href="{{URL_ADMIN}}/login"><?= __('login') ?></a>
		<?php endif; ?>


		<?php if($action == 'forgotpassword'): ?>
			<div class="login-box box-shadow">
				<h2>{{page_name}}</h2>

				<p class="info">Falls du deinen Passwort vergessen hast, kannst du es über einen Link aus der gesendeten E-Mail zurücksetzten.</p>

				{{message}}
				{{form}}
			</div>
			<a class="center login" href="{{URL_ADMIN}}/login"><?= __('login') ?></a>
		<?php endif; ?>


		<?php if($action == 'resetpassword'): ?>
			<div class="login-box box-shadow">
				<h2>{{page_name}}</h2>

				<p class="info">hier passwort zurücksetzten.</p>

				{{message}}
				{{form}}
			</div>
			<a class="center login" href="{{URL_ADMIN}}/login"><?= __('login') ?></a>
		<?php endif; ?>

		<?php if($action == 'setpassword'): ?>
			<div class="login-box box-shadow">
				<h2>{{page_name}}</h2>

				<p class="info">Du musst zu erst dein Passwort festlegen!</p>

				{{message}}
				{{form}}
			</div>
			<a class="center login" href="{{URL_ADMIN}}/login"><?= __('login') ?></a>
		<?php endif; ?>

		<?php if($action == 'confirm'): ?>
			<div class="login-box box-shadow">
				<h2>{{page_name}}</h2>

				#if(confirmSucessful):
					<p class="info">Dein Account wurde erfolgreich bestätigt. Du wirst nun automatisch weitergeleitet.</p>
				#else:
					<p class="info">failed...</p>
				#endif
				{{message}}
				{{form}}
			</div>
		<?php endif; ?>

	</div>
</body>
</html>
