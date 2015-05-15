<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="robots" content="index, follow, noarchive" />

	<title>{{page_title}} | {{homepage_name}}</title>
	
	<meta name="description" content="{{meta_desc}}" />
	<meta name="keywords" content="{{meta_keys}}" />
	<meta name="auhor" content="{{meta_author}}" />
	<meta name="publisher" content="{{meta_author}}" />
	<meta name="copyright" content="{{meta_author}}" />
	
	<!-- http://xenux.bplaced.net -->
	<meta name="generator" content="Xenux - das kostenlose CMS" />
	
</head>
<body>

	<?php
	echo "<ul>";
	$sites = $XenuxDB->getList('sites', Array('orderBy' => 'sortindex', 'orderDir' => 'ASC'));
	if($sites)
	{
		foreach($sites as $site)
		{
			echo "<li>".$site->title;
			
			$subsites = $XenuxDB->getList('sites', Array('orderBy' => 'sortindex', 'orderDir' => 'ASC', 'where' => "parent_id = $site->id"));
			if($subsites)
			{
				echo "<ul>";
				foreach($subsites as $subsite)
				{
					echo "<li>".$subsite->title;
					$subsubsites = $XenuxDB->getList('sites', Array('orderBy' => 'sortindex', 'orderDir' => 'ASC', 'where' => "parent_id = $subsite->id"));
					if($subsubsites)
					{
						echo "<ul>";
						foreach($subsubsites as $subsubsite)
						{
							echo "<li>".$subsubsite->title."</li>";
						}
						echo "</ul>";
					}
					echo "</li>";
				}
				echo "</ul>";
			}
			echo "</li>";
		}
	}
	echo "</ul>";
	echo "\n\n\n";
	?>
	<p>TEMPLATE_PATH: {{TEMPLATE_PATH}}</p>
	<p>URL_MAIN: {{URL_MAIN}}</p>

	{{_contactPersons}}
	{{_contactPersons}}
	{{page_content}}
	<a href="{{URL_MAIN}}/sitemap">sitemap</a>
</body>
</html>