<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="index, follow, noarchive" />


	<title>{{page_title}} | {{homepage_name}}</title>

	<meta name="description" content="{{meta_desc}}" />
	<meta name="keywords" content="{{meta_keys}}" />
	<meta name="auhor" content="{{meta_author}}" />
	<meta name="publisher" content="{{meta_author}}" />
	<meta name="copyright" content="{{meta_author}}" />


	<!-- http://xenux.bplaced.net -->
	<meta name="generator" content="Xenux v{{XENUX_VERSION}} - das kostenlose CMS" />
	
	<link rel="shortcut icon" href="{{TEMPLATE_PATH}}/img/logo.ico" /> "*" FIXME: use favicon.png (redesign favicon) "*"
	
	<!-- Bootstrap core CSS -->
	<link href="{{TEMPLATE_PATH}}/css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom styles for this template -->
	<link href="{{TEMPLATE_PATH}}/css/sticky-footer-navbar.css" rel="stylesheet">
	<link href="{{TEMPLATE_PATH}}/css/custom-style.css" rel="stylesheet">

	<!-- fancybox -->
	<link rel="stylesheet" type="text/css" href="{{TEMPLATE_PATH}}/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

	<!-- links -->
	<link rel="canonical" href="{{canonical_URL}}" />
	#if(prev_URL):<link rel="prev" href="{{prev_URL}}" />#endif
	#if(next_URL):<link rel="next" href="{{next_URL}}" />#endif

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>

<body>

	<!-- Fixed navbar -->
	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="{{URL_MAIN}}">{{homepage_name}}</a>
			</div>
			<div id="navbar" class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
<?php
				// get sites level 1 (only public sites)
				$sites = $XenuxDB->getList('sites', [
							'order' => 'sortindex ASC',
							'where' => [
								'AND' => [
									'parent_id' => 0,
									'public' => true
								]
							],
						]);
				if ($sites)
				{
					foreach ($sites as $site)
					{
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
							echo '<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">'.$site->title.' <span class="caret"></span></a>
									<ul class="dropdown-menu">
									<li><a href="'.getPageLink($site->id, $site->title).'">'.$site->title.'</a></li>
									<li role="separator" class="divider"></li>';
							foreach ($subsites as $subsite)
							{
								
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
									echo '<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">'.$subsite->title.' <span class="caret"></span></a>
									<ul class="dropdown-menu">';
									foreach ($subsubsites as $subsubsite)
									{
										echo "<li><a href=\"".getPageLink($subsubsite->id, $subsubsite->title)."\">".$subsubsite->title."</a></li>";
									}
									echo "</ul></li>";
								}
								else
								{
									echo "<li><a href=\"".getPageLink($subsite->id, $subsite->title)."\">".$subsite->title."</a></li>";
								}
							}
							echo "</ul></li>";
						}
						else
						{
							echo "<li><a href=\"".getPageLink($site->id, $site->title)."\">".$site->title."</a></li>";
						}
						
					}
				}
?>
"*"
					<li class="active"><a href="#">Home</a></li>
					<li><a href="#about">About</a></li>
					<li><a href="#contact">Contact</a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="#">Action</a></li>
							<li><a href="#">Another action</a></li>
							<li><a href="#">Something else here</a></li>
							<li role="separator" class="divider"></li>
							<li class="dropdown-header">Nav header</li>
							<li><a href="#">Separated link</a></li>
							<li><a href="#">One more separated link</a></li>
						</ul>
					</li>
"*"
				</ul>
			</div><!--/.nav-collapse -->
		</div>
	</nav>

	<!-- Begin page content -->
	<div class="container">
		<div class="row">
			<div class="col-md-10">{{page_content}}</div>
			<div class="col-md-2 sitebar">
				<aside class="search">
					<h5><?= __('search') ?></h5>
					<form action="{{URL_MAIN}}/search/" method="GET">
						<input type="search" class="search-input" name="q" placeholder="<?= __("search") ?>" value="" />
						<input type="submit" class="search-submit" value="<?= __("search") ?>" />
					</form>
				</aside>
				<?php
					// newest sites (only public sites)
					$newestSitesList = $XenuxDB->getList('sites', [
						'limit' => 5,
						'order' => 'create_date DESC',
						'where' => [
							'public' => true
						]
					]);

					if ($newestSitesList)
					{
						echo '<aside class="newest-sites">';
						echo '<h5>'. __('newestSites') . '</h5>';
						echo '<ul>';
						
						foreach ($newestSitesList as $site)
						{
							echo '<li><a href="'.getPageLink($site->id, $site->title).'">'.$site->title.'</a></li>';
						}
						
						echo '</ul>';
						echo '</aside>';
					}
				?>

				<aside class="news">
					<h5><?= __('news_Pl') ?></h5>
					<ul>
						<?php
							// get news (only public news)
							$newsList = $XenuxDB->getList('news', [
								'limit' => 3,
								'order' => 'create_date DESC',
								'where' => [
									'public' => true
								]
							]);
							if ($newsList)
							{
								foreach ($newsList as $news)
								{
									echo '<li><a href="{{URL_MAIN}}/news/view/'.getPreparedLink($news->id, $news->title).'">'.$news->title.'</a></li>';
								}
							}
							else
							{
								echo "<p style=\"margin:5px 0;\">" . __("noNews") . "</p>";
							}
						?>
					</ul>
					<a style="display:inline-block;margin-top:5px;" href="{{URL_MAIN}}/news/list"><?= __("showAllNews") ?></a>
				</aside>

				<aside class="events">
					<h5><?= __('events') ?></h5>
					<ul>
						<?php
							// get events (only public events)
							$eventList = $XenuxDB->getList('events', [
								'limit' => 3,
								'order' => 'start_date DESC',
								'where' => [
									'AND' => [
										'##start_date[>=]' => 'CURDATE()',
										'public' => true
									]
								]
							]);
							if ($eventList)
							{
								foreach ($eventList as $event)
								{
									echo '<li><a href="{{URL_MAIN}}/event/view/'.getPreparedLink($event->id, $event->title).'">'.$event->title.'</a></li>';
								}
							}
							else
							{
								echo "<p style=\"margin:5px 0;\">" . __("noEvents") . "</p>";
							}
						?>
					</ul>
					<a style="display:inline-block;margin-top:5px;" href="{{URL_MAIN}}/event/calendar"><?= __("gotoEventCalendar") ?></a>
				</aside>
				<aside>
					<h5>Change language</h5>
					<?php
						$langs = translator::getLanguages();
						if (count((array)$langs) > 1):
							?>
							<select onchange="window.location.href = '{{SITE_PATH}}?lang=' + $(this).val();" class="language-selector">
								<option disabled="disabled" data-option-class="label" data-style="background-image:none;">Select Language</option>
								<?php
									foreach ($langs as $short => $options)
									{
										echo '<option value="' . $short . '" data-style="background-image: url(\''.URL_MAIN. $options->flag.'\');" ' . ($short == translator::getLanguage() ? 'selected' : '') . ' >' . $options->label . '</option>';
									}
								?>
							</select>
							<?php
						endif;
					?>
				</aside>
			</div>
		</div>
	</div>

	<footer class="footer">
		<div class="container">
			<div class="text-muted">
				powered by <a target="_blank" href="<?= XENUX_URL_HP ?>">Xenux</a>

				<div class="links">
					<a href="{{URL_MAIN}}/sitemap"><?= __('sitemap') ?></a>
					<a href="{{URL_MAIN}}/administration"><?= __('administration') ?></a>
					
					<a href="{{URL_MAIN}}/contact"><?= __('contact') ?></a>
					<a href="{{URL_MAIN}}/imprint"><?= __('imprint') ?></a>
				</div>
			</div>

		</div>
	</footer>


	<!-- JavaScript
	================================================== -->

	<!-- jquery + plugins -->
	<script src="{{TEMPLATE_PATH}}/js/jquery-2.1.1.min.js"></script>
	<script src="{{TEMPLATE_PATH}}/js/jquery-migrate-1.2.1.min.js"></script>
	<script src="{{TEMPLATE_PATH}}/js/jquery-ui.min.js"></script>
	<script src="{{TEMPLATE_PATH}}/js/jquery.ui.touch-punch.min.js"></script>
	<script src="{{TEMPLATE_PATH}}/js/jquery.cookie.js"></script>
	<script src="{{TEMPLATE_PATH}}/js/jquery.mousewheel.js"></script>

	<!-- Bootstrap core JavaScript -->
	<script src="{{TEMPLATE_PATH}}/js/bootstrap.min.js"></script>

	<!-- fancybox -->
	<script src="{{TEMPLATE_PATH}}/fancybox/jquery.fancybox.pack.js?v=2.1.5"></script>

	<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
	<script src="{{TEMPLATE_PATH}}/js/ie10-viewport-bug-workaround.js"></script>

	<!-- scripts -->
	<script>
		var XENUX = {
			translation: {
				pictureXofY: '<?= __('picture x of y') ?>'
			},
			path: {
				baseurl: '{{URL_MAIN}}',
				sitepath: '{{SITE_PATH}}'
			}
		}
	</script>
	<script src="{{TEMPLATE_PATH}}/js/functions.js"></script>
	<script src="{{TEMPLATE_PATH}}/js/script.js"></script>
</body>
</html>