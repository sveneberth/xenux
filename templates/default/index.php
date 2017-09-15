<!DOCTYPE html>
<html lang="<?= translator::getLanguage() ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="index, follow, noarchive">

	<title>{{page_title}} &ndash; {{homepage_name}}</title>

	<meta name="description" content="{{meta_desc}}">
	<meta name="keywords" content="{{meta_keys}}">
	<meta name="auhor" content="{{meta_author}}">
	<meta name="publisher" content="{{meta_author}}">
	<meta name="copyright" content="{{meta_author}}">

	<!-- http://xenux.bplaced.net -->
	<meta name="generator" content="Xenux v{{XENUX_VERSION}} - das kostenlose CMS">

	<link rel="shortcut icon" href="{{TEMPLATE_URL}}/img/favicon_48.png">

	<!-- css -->
	<link rel="stylesheet" href="{{TEMPLATE_URL}}/css/style.css" media="all">

	<!-- jquery + plugins -->
	<script src="{{TEMPLATE_URL}}/js/jquery-2.1.1.min.js"></script>
	<script src="{{TEMPLATE_URL}}/js/jquery-migrate-1.2.1.min.js"></script>
	<script src="{{TEMPLATE_URL}}/js/jquery-ui.min.js"></script>
	<script src="{{TEMPLATE_URL}}/js/jquery.ui.touch-punch.min.js"></script>
	<script src="{{TEMPLATE_URL}}/js/jquery.mousewheel.js"></script>

	<!-- fancybox -->
	<script src="{{TEMPLATE_URL}}/fancybox/jquery.fancybox.pack.js?v=2.1.5"></script>
	<link rel="stylesheet" href="{{TEMPLATE_URL}}/fancybox/jquery.fancybox.css?v=2.1.5" media="screen">

	<!-- search -->
	<link rel="search" type="application/opensearchdescription+xml" title="Xenux Suche" href="{{MAIN_URL}}/search.xml.php">

	<!-- links -->
	<link rel="canonical" href="{{canonical_URL}}">
	#if(prev_URL):<link rel="prev" href="{{prev_URL}}">#endif
	#if(next_URL):<link rel="next" href="{{next_URL}}">#endif

	<noscript>
		<style>
			.sb-search {width: 100%;}
			.sb-search .sb-search-submit {z-index: 100;}
		</style>
	</noscript>
</head>
<body id="top">
	<a href="#top" class="toTop"></a>
	<div class="headWrapper">
		<header>
			<a href="javascript:openmobilemenu();" class="menu-icon"></a>
			<a class="logo" href="{{MAIN_URL}}">
				<?= embedSVG(TEMPLATE_PATH . '/img/xenux_logo.svg'); ?>
			</a>
			<ul class="topmenu mobilemenu">
				<li><a href="{{MAIN_URL}}/administration/login"><?= __('login') ?></a></li>
			</ul>
			<nobr>
				<ul class="topmenu mainmenu">
<?php
// get sites level 1 (only public sites)
$sites = $XenuxDB->getList('sites', [
			'order' => 'sortindex ASC',
			'where' => [
				#DEBUG 'AND' => [
					'parent_id' => 0,
					'public' => true
				#DEBUG],
			],
		]);
if ($sites)
{
	foreach ($sites as $site)
	{
		echo "<li>\n\t<a href=\"".getPageLink($site->id, $site->title)."\">".$site->title."</a>\n";

		// get sites level 2 (only public sites)
		$subsites = $XenuxDB->getList('sites', [
			'order' => 'sortindex ASC',
			'where' => [
				'AND' => [
					'parent_id' => $site->id,
					'public' => true
				]
			],
		]);
		if ($subsites)
		{
			echo "\t<ul>\n";
			foreach ($subsites as $subsite)
			{
				echo "\t\t<li>\n\t\t\t<a href=\"".getPageLink($subsite->id, $subsite->title)."\">".$subsite->title."</a>\n";

				// get sites level 3 (only public sites)
				$subsubsites = $XenuxDB->getList('sites', [
					'order' => 'sortindex ASC',
					'where' => [
						'AND' => [
							'parent_id' => $subsite->id,
							'public' => true
						]
					],
				]);
				if ($subsubsites)
				{
					echo "\t\t\t<ul>\n";
					foreach ($subsubsites as $subsubsite)
					{
						echo "\t\t\t\t<li>\n\t\t\t\t\t<a href=\"".getPageLink($subsubsite->id, $subsubsite->title)."\">".$subsubsite->title."</a>\n\t\t\t\t</li>\n";
					}
					echo "\t\t\t</ul>\n";
				}
				echo "\t\t</li>\n";
			}
			echo "\t</ul>\n";
		}

		echo "</li>\n";
	}
}
?>
					<li class="search">
						<div id="sb-search" class="sb-search">
							<form action="{{MAIN_URL}}/search/" method="GET">
								<input onkeyup="$('.sb-search-submit').css('z-index', ($(this).val() == '' ? 11 : 99));" type="search" class="sb-search-input" name="q" placeholder="<?= __('search') ?>" value="">
								<input type="submit" class="sb-search-submit" value="">
								<span onclick="$('div#sb-search').toggleClass('sb-search-open');$('.sb-search-input').focus();" class="sb-icon-search"></span>
							</form>
						</div>
					</li>
					<li class="mobilemenu"><a href="{{MAIN_URL}}/news/list"><?= __('posts') ?></a></li>
					<li class="mobilemenu"><a href="{{MAIN_URL}}/event/calendar"><?= __('events') ?></a></li>
					<li class="mobilemenu"><a href="{{MAIN_URL}}/search"><?= __('search') ?></a></li>
				</ul>
			</nobr>
		</header>
	</div>

	<div class="wrapper">
		<noscript>
			<div class="warning-noscript">
				<div>
					<?= __('noscript-message') ?>
				</div>
			</div>
		</noscript>
		<div class="leftboxes">

			<ul class="posts">
				<h3><?= __('newestPosts') ?>:</h3>
				<?php
					// get posts
					$posts = $XenuxDB->getList('posts', [
						'limit' => 3,
						'order' => 'create_date DESC',
						'where' => [
							'status' => 'publish'
						]
					]);
					if ($posts)
					{
						foreach ($posts as $post)
						{
							echo '<li>
										<span class="title">' . $post->title . '</span>
										<span class="date">' . pretty_date($post->create_date) . '</span>' .
										shortstr(strip_tags($post->text), 50) . '<br>
										<a href="{{MAIN_URL}}/posts/view/' . getPreparedLink($post->id, $post->title) . '">&raquo;' . __('showPost') . '</a>
									</li>';
						}
					}
					else
					{
						echo '<p style="margin:5px 0;">' . __('noPosts') . '</p>';
					}
				?>
				<a href="{{MAIN_URL}}/posts/list"><?= __('showAllPosts') ?></a>
			</ul>


			<ul class="events">
				<h3><?= __('events') ?>:</h3>
				<?php
					// get events
					$eventList = $XenuxDB->getList('events', [
						'limit' => 3,
						'order' => 'start_date DESC',
						'where' => [
							'##start_date[>=]' => 'CURDATE()'
						]
					]);
					if ($eventList)
					{
						foreach ($eventList as $event)
						{
							echo '<li>
										<span class="title">' . $event->title . '</span>
										<span class="date">' . mysql2date('d.m.Y H:i', $event->start_date) . '</span>' .
										shortstr(strip_tags($event->text), 50) . '<br>
										<a href="{{MAIN_URL}}/event/view/' . getPreparedLink($event->id, $event->title) . '">&raquo;' . __('showEvent') . '</a>
									</li>';
						}
					}
					else
					{
						echo '<p style="margin:5px 0;">' . __('noEvents') . '</p>';
					}
					?>
					<a href="{{MAIN_URL}}/event/calendar"><?= __('gotoEventCalendar') ?></a>
			</ul>


			<div>
				<h3><?= __('login') ?>:</h3>
<?php
				if (!$user->isLogin())
				{
?>
				<form action="{{ADMIN_URL}}/login" method="POST">
					<input type="text" name="username" placeholder="<?= __('username') ?>">
					<input type="password" name="password" placeholder="<?= __('password') ?>">
					<input style="margin: 5px 0;" type="submit" value="<?= __('login') ?>">

					<a href="{{ADMIN_URL}}/login?task=forgotpassword"><?= __('forgotPassword') ?>?</a><br>
					<a href="{{ADMIN_URL}}/login?task=forgotusername"><?= __('forgotUsername') ?>?</a><br>
					<?php if (parse_bool($app->getOption('users_can_register'))): ?>
						<a href="{{ADMIN_URL}}/login?task=register"><?= __('register') ?></a>
					<?php endif; ?>
				</form>
<?php
				}
				else
				{
?>
				<?= __('successLogin', $user->userInfo->firstname) ?>!<br>
				<a href="{{ADMIN_URL}}">&raquo;<?= __('go to administration') ?></a><br>
				<a href="{{ADMIN_URL}}/login?task=logout"><?= __('logout') ?></a>
<?php
				}
?>
			</div>


			<div>
				<p style="margin: 5px 0;">Change language:</p>
				<?php
					$langs = translator::getLanguages();
					if (count((array)$langs) > 1):
						?>
						<select onchange="window.location.href = '{{SITE_URL}}?lang=' + $(this).val();" class="language-selector">
							<option disabled="disabled" data-option-class="label" data-style="background-image:none;">Select Language</option>
							<?php
								foreach ($langs as $short => $options)
								{
									echo '<option value="' . $short . '" data-style="background-image: url(\''.MAIN_URL. $options->flag.'\');" ' . ($short == translator::getLanguage() ? 'selected' : '') . ' >' . $options->label . '</option>';
								}
							?>
						</select>
						<?php
					endif;
				?>
			</div>

		</div>

		<main>
			<div class="page-header">
				<h1 class="page-headline">{{headlinePrefix}}{{headline}}{{headlineSuffix}}</h1>
			</div>
			{{page_content}}
		</main>

		<footer>
			powered by <a target="_blank" href="<?= XENUX_URL_HP ?>">Xenux</a>

			<div class="links">
				<a href="{{MAIN_URL}}/sitemap"><?= __('sitemap') ?></a>
				<a href="{{MAIN_URL}}/administration"><?= __('administration') ?></a>

				<a href="{{MAIN_URL}}/contact"><?= __('contact') ?></a>
				<a href="{{MAIN_URL}}/imprint"><?= __('imprint') ?></a>
			</div>
		</footer>

	</div>

	<!-- scripts -->
	<script src="{{MAIN_URL}}/core/static/js/xenux.min.js"></script>
	<script src="{{MAIN_URL}}/js/templates/default/js/script.js"></script>
</body>
</html>
