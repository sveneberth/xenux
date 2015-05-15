<?php
class sitemapController extends AbstractController
{
	private $searchString;
	
	public function __construct($url)
	{
		global $XenuxDB;
		
		$this->url = $url;
		$this->modulename = str_replace('Controller', '', get_class());
	}
	
	public function run()
	{
		$this->loadSitemap();
		// $this->buildXMLSitemap();

		$this->page_name = __("sitemap");		

		return true;
	}
	
	private function loadSitemap()
	{
		global $XenuxDB;
	
		echo "<h1 class=\"page-headline\">" . __("sitemap") . "</h1>";
		
		echo "<ul class=\"sitemap\">";

		$sites = $XenuxDB->getList('sites', [
			'order' => 'sortindex ASC',
			'where' => [
				'AND' => [
					'parent_id' => 0,
					'public' => true
				]
			],
		]);
		if($sites)
		{
			foreach($sites as $site)
			{
				echo "<li>\n\t<a href=\"".getPageLink($site->id, $site->title)."\">".$site->title."</a>\n";
				
				$subsites = $XenuxDB->getList('sites', [
					'order' => 'sortindex ASC',
					'where' => [
						'AND' => [
							'parent_id' => $site->id,
							'public' => true
						]
					],
				]);
				if($subsites)
				{
					echo "\t<ul>\n";
					foreach($subsites as $subsite)
					{
						echo "\t\t<li>\n\t\t\t<a href=\"".getPageLink($subsite->id, $subsite->title)."\">".$subsite->title."</a>\n";
						$subsubsites = $XenuxDB->getList('sites', [
							'order' => 'sortindex ASC',
							'where' => [
								'AND' => [
									'parent_id' => $subsite->id,
									'public' => true
								]
							],
						]);
						if($subsubsites)
						{
							echo "\t\t\t<ul>\n";
							foreach($subsubsites as $subsubsite)
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
		
		// news
		echo '<li><a href="{{URL_MAIN}}/news/list">' . __("news_Pl") . '</a>';
		$newsList = $XenuxDB->getList('news', [
			'columns' => [
				'id',
				'title'
			],
			'order' => 'create_date DESC'
		]);
		if ($newsList)
		{
			echo "<ul>";
			foreach ($newsList as $news)
			{
				echo '<li><a href="{{URL_MAIN}}/news/view/' . getPreparedLink($news->id, $news->title) . '">' . $news->title .'</a></li>';
			}
			echo "</ul>";
		}
		echo "</li>";

		// events
		echo '<li><a href="{{URL_MAIN}}/event/calendar">' . __("events") . '</a>';
		$eventsList = $XenuxDB->getList('events', [
			'columns' => [
				'id',
				'name'
			],
			'order' => 'create_date DESC'
		]);
		if ($eventsList)
		{
			echo "<ul>";
			foreach ($eventsList as $events)
			{
				echo '<li><a href="{{URL_MAIN}}/event/view/' . getPreparedLink($events->id, $events->title) . '">' . $events->title .'</a></li>';
			}
			echo "</ul>";
		}
		echo "</li>";

		// static sites
		echo '<li><a href="{{URL_MAIN}}/sitemap">' . __("sitemap") . '</a></li>';
		echo '<li><a href="{{URL_MAIN}}/search">' . __("search") . '</a></li>';
		echo '<li><a href="{{URL_MAIN}}/contact">' . __("contact") . '</a></li>';
		echo '<li><a href="{{URL_MAIN}}/imprint">' . __("imprint") . '</a></li>';

		echo "</ul>";
	}
	
	private function buildXMLSitemap($overwrite = false)
	{
		global $XenuxDB;
	
		ob_start();

			echo 
'<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
  xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" 
  xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">';
		
			$sites = $XenuxDB->getList('sites', ['orderBy' => 'sortindex', 'orderDir' => 'ASC', 'where' => "public = true"]);
			if($sites)
			{
				foreach($sites as $site)
				{
					echo '
	<url>
		<loc>'.getPageLink($site->id, $site->title).'</loc>
		<lastmod>'.$site->lastModified_date.'</lastmod>
	</url>';
				}
			}

			echo "\n</urlset>";	

		$buffer = ob_get_clean();

		$fileExists = file_exists(PATH_MAIN.'/'.'sitemap.xml');

		if(file_exists(PATH_MAIN.'/'.'sitemap.xml'))
		{
			// file exists
			$fileData = file_get_contents(PATH_MAIN.'/'.'sitemap.xml');

			if($fileData != $buffer || $overwrite)
			{
				$this->writeXMLFileData($buffer);
			}
		}
		else
		{
			$this->writeXMLFileData($buffer);
		}
	}

	private function writeXMLFileData($data, $filePath = PATH_MAIN.'/'.'sitemap.xml')
	{
		$file = fopen($filePath, "w")
			or die("something went wrong ...");
		fwrite($file, $data);
		fclose($file);

		return true;
	}
}
?>