<?php
class pageController extends AbstractController
{
	protected $pageID;
	
	public function __construct($url = null)
	{
		if(isset($url))
			$this->url = $url;
		
		$this->modulename = "page"; // use static, because works as abstract too
	}
	
	public function run()
	{
		$this->pageID = preg_replace("/[^0-9]/", '', @$this->url[1]);
		
		if(!empty($this->pageID))
		{
			$this->pageView($this->pageID);
		}
		else
		{
			throw new Exception("404 - page not found");
		}
		return true;
	}
	
	protected function pageView($id, $showIsPrivate=false)
	{
		global $app, $XenuxDB;

		$page = $XenuxDB->getEntry('sites', [
			'where' => [
				'sites.id' => $id
			],
			'columns' => [
				'sites.text',
				'sites.title',
				'sites.sortindex',
				'sites.parent_id',
				'sites.create_date',
				'sites.public',
				'sites.id(site_id)',
				'users.username'
			],
			'join' => [
				'[>]users' => ['sites.author_id' => 'users.id']
			]
		]);
		if($page)
		{
			if(($page->public || $showIsPrivate) || $app->user->isLogin())
			{
				$template = new template(PATH_MAIN."/modules/".$this->modulename."/layout.php",
				[
					'author'	=> $page->username,
					'date'		=> mysql2date('d.m.Y', $page->create_date),
					'time'		=> mysql2date('H.i', $page->create_date)
				]);

				$template->setVar("page_content", $page->text);
				$template->setVar("page_title", $page->title);

				$template->setIfCondition("show_meta_info", parse_bool($app->getOption('sites_show_meta_info')));

				$template->setVar("_PREV_NEXT", $this->getPrevNext($page));
				$template->setVar("_contactPersons", $this->getContactPersons($id));

				/* next and prev url */
				$app->canonical_URL = getPageLink($page->site_id, $page->title);
				if($prev = $this->_getPrevSite($page->parent_id, $page->sortindex))
				{
					$app->prev_URL = getPageLink($prev->id, $prev->title);
				}
				if($next = $this->_getNextSite($page->parent_id, $page->sortindex))
				{
					$app->next_URL = getPageLink($next->id, $next->title);
				}
				
				echo $template->render();

				$this->page_name = "Page:$page->title";
			}
			else
			{
				throw new Exception("Page not public");
			}
		}
		else
		{
			echo "404 - page not found";
			$this->page_name = "Page:404";
		}
	}

	
	protected function getContactPersons($id)
	{
		global $app, $XenuxDB;
		
		$return = array();

		$contactpersons = $XenuxDB->getList('site_contactperson', [
			'columns' => [
				'contactpersons.id',
				'name',
				'position',
				'email'
			],
			'where' => [
				'site_id' => $id
			],
			'join' => [
				'[>]sites' => ['site_contactperson.site_id' => 'sites.id'],
				'[>]contactpersons' => ['site_contactperson.contactperson_id' => 'contactpersons.id']
			]
		]);
		if($contactpersons)
		{
			$list = null;
			foreach($contactpersons as $contactperson)
			{
				$template = new template(PATH_MAIN."/templates/".$app->template."/_contactPersons_li.php");
		
				$template->setVar("name", $contactperson->name);
				$template->setVar("name_url", urldecode($contactperson->name));
				$template->setVar("position", $contactperson->position);
				$template->setVar("email", escapemail($contactperson->email));
				$template->setVar("ID", $contactperson->id);

				$list .= $template->render();
			}
			
			$template = new template(PATH_MAIN."/templates/".$app->template."/_contactPersons_ul.php");
			$template->setVar("list", $list);
			$return[] = $template->render();
		}
		
		
		return implode('', $return);
	}

	protected function getPrevNext($page)
	{
		global $app, $XenuxDB;

		if($page->parent_id == 0)
			return false;

		$prev = $this->_getPrevSite($page->parent_id, $page->sortindex);
		$next = $this->_getNextSite($page->parent_id, $page->sortindex);

		if($prev || $next)
		{
			$template = new template(PATH_MAIN."/templates/".$app->template."/_prevNextNavi.php");
	
			$template->setIfCondition("prev",	$prev);
			if($prev)
			{
				$template->setVar("prev|title",	$prev->title);
				$template->setVar("prev|url",	getPageLink($prev->id, $prev->title));
			}

			$template->setIfCondition("next",	$next);
			if($next)
			{
				$template->setVar("next|title",	$next->title);
				$template->setVar("next|url",	getPageLink($next->id, $next->title));
			}
	
			return $template->render();
		}
		return false;	
	}

	protected function _getPrevSite($parent_id, $sortindex)
	{
		global $XenuxDB;
		
		$prev = $XenuxDB->getEntry('sites', [
			'columns' => [
				'id',
				'title'
			],
			'order' => 'sortindex DESC',
			'where' => [
				'AND' => [
					'sortindex[<]'	=> $sortindex,
					'parent_id'		=> $parent_id,
					'public'		=> true,
				]
			]
		]);
		
		return $prev;
	}

	protected function _getNextSite($parent_id, $sortindex)
	{
		global $XenuxDB;
		
		$next = $XenuxDB->getEntry('sites', [
			'columns' => [
				'id',
				'title'
			],
			'order' => 'sortindex ASC',
			'where' => [
				'AND' => [
					'sortindex[>]'	=> $sortindex,
					'parent_id'		=> $parent_id,
					'public'		=> true,
				]
			]
		]);
		
		return $next;
	}
}
?>