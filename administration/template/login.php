<!DOCTYPE html>
<html lang="<?= translator::getLanguage() ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex, follow, noarchive">

	<title>{{page_name}} &ndash; Xenux Backend</title>

	<!-- http://xenux.bplaced.net -->
	<meta name="generator" content="Xenux v{{XENUX_VERSION}} - das kostenlose CMS">
	<link rel="shortcut icon" href="{{TEMPLATE_URL}}/images/favicon_48.png">

	<!-- css -->
	<link rel="stylesheet" href="{{TEMPLATE_URL}}/css/login.css" media="all">
</head>
<body id="top">
	#if(logout):
		<p class="logout-message box-shadow"><?= __('logout successful') ?></p>
	#endif

	<div class="login-wrapper">

		<?php if($action == 'login'): ?>
			<div class="login-box box-shadow">
				<h2>{{page_name}}</h2>

				{{message}}
				{{form}}

			</div>
			<p class="center forgot">
				<a class="forgotusername" href="{{ADMIN_URL}}/login?task=forgotusername"><?= __('forgotUsername') ?>?</a> |
				<a class="forgotpassword" href="{{ADMIN_URL}}/login?task=forgotpassword"><?= __('forgotPassword') ?>?</a>
			</p>
			<?php if(parse_bool($app->getOption('users_can_register'))): ?>
				<a class="center register" href="{{ADMIN_URL}}/login?task=register"><?= __('register') ?></a>
			<?php endif; ?>
		<?php endif; ?>


		<?php if($action == 'register'): ?>
			<div class="login-box box-shadow">
				<h2>{{page_name}}</h2>

				{{message}}
				{{form}}
			</div>
			<a class="center login" href="{{ADMIN_URL}}/login"><?= __('login') ?></a>
		<?php endif; ?>


		<?php if($action == 'forgotusername'): ?>
			<div class="login-box box-shadow">
				<h2>{{page_name}}</h2>

				<p class="info"><?= __('forgotUsername info') ?></p>
				{{message}}
				{{form}}
			</div>
			<a class="center login" href="{{ADMIN_URL}}/login"><?= __('login') ?></a>
		<?php endif; ?>


		<?php if($action == 'forgotpassword'): ?>
			<div class="login-box box-shadow">
				<h2>{{page_name}}</h2>

				<p class="info"><?= __('forgotPassword info') ?></p>

				{{message}}
				{{form}}
			</div>
			<a class="center login" href="{{ADMIN_URL}}/login"><?= __('login') ?></a>
		<?php endif; ?>


		<?php if($action == 'resetpassword'): ?>
			<div class="login-box box-shadow">
				<h2>{{page_name}}</h2>

				<p class="info"><?= __('forgotPassword action info') ?></p>

				{{message}}
				{{form}}
			</div>
			<a class="center login" href="{{ADMIN_URL}}/login"><?= __('login') ?></a>
		<?php endif; ?>

		<?php if($action == 'setpassword'): ?>
			<div class="login-box box-shadow">
				<h2>{{page_name}}</h2>

				<p class="info"><?= __('setPassword info') ?></p>

				{{message}}
				{{form}}
			</div>
			<a class="center login" href="{{ADMIN_URL}}/login"><?= __('login') ?></a>
		<?php endif; ?>

		<?php if($action == 'confirm'): ?>
			<div class="login-box box-shadow">
				<h2>{{page_name}}</h2>

				#if(confirmSucessful):
					<p class="info"><?= __('account confirmation successful') ?></p>
				#else:
					<p class="info"><?= __('account confirmation failed') ?></p>
				#endif
				{{message}}
				{{form}}
			</div>
		<?php endif; ?>

	</div>
</body>
</html>
