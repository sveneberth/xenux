<?php
class searchController extends AbstractController
{
	private $searchString;
	
	public function __construct($url)
	{
		global $XenuxDB;
		
		$this->url = $url;
		$this->modulename = str_replace('Controller', '', get_class());
		$this->searchString = $XenuxDB->escapeString(@$_GET['q']);
	}
	
	public function run()
	{
		if(!empty($this->searchString))
		{
			$this->search();
		}
		else
		{
			echo "empty searchstring"; 
		}
		
		$this->page_name = __("search");

		return true;
	}
	
	#FIXME: search in events, news too
	private function search()
	{
		global $XenuxDB;
	
		echo "<h1 class=\"page-headline\">" . __("search") . "</h1>";
	
		$start			= is_numeric(@$_GET['start']) ? floor($_GET['start']) : 0;
		$amount			= (is_numeric(@$_GET['amount']) && floor(@$_GET['amount']) != 0) ? floor($_GET['amount']) : 10;
		$absolutenumber = $XenuxDB->count('sites', [
			'where' => [
				'AND' => [
					'OR' => [
						'title[~]' => $this->searchString,
						'text[~]' => $this->searchString
					],
					'public' => true,
				]
			]
		]);

		$matches = $XenuxDB->getList('sites', [
			'where' => [
				'AND' => [
					'OR' => [
						'title[~]' => $this->searchString,
						'text[~]' => $this->searchString
					],
					'public' => true,
				]
			],
			'limit' => [$start, $amount]
		]);
/*
		#FIXME!!!
		$result = $XenuxDB->query("SELECT * FROM `XENUX_sites` WHERE (
										`title` LIKE '%".$this->searchString."%'
										OR 	`text` LIKE '%".$this->searchString."%'
									)
									AND public = true
									UNION ALL
									SELECT * FROM `XENUX_news` WHERE (
										`title` LIKE '%".$this->searchString."%'
										OR 	`text` LIKE '%".$this->searchString."%'
									)
									AND public = true;
									");
		var_dump($result);	

		log::writeLog($XenuxDB->getLastQuery());
*/

		if($matches)
		{
			foreach($matches as $match)
			{
				$template = new template(PATH_MAIN."/modules/".$this->modulename."/layout.php");
		
				$template->setVar("page_content", shortstr(str_replace("&nbsp;", "", strip_tags($match->text)), 300));
				$template->setVar("page_title", $match->title);
				$template->setVar("page_URL", getPageLink($match->id, $match->title));

				echo $template->render();

			}

			echo getMenuBarMultiSites($absolutenumber, $start, $amount);
		}
		else
		{
			echo "<p>" . __("noSearchResult", $this->searchString) . "</p>";
		}	
	}
}
?>