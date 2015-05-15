<?php
class contactpersonController extends AbstractController
{
	public function __construct($url)
	{
		$this->url = $url;
		$this->modulename = str_replace('Controller', '', get_class());
	}
	
	public function run()
	{
		if (@$this->url[1] == "list")
		{
			$this->contactpersonList();
		}
		elseif (@$this->url[1] == "view")
		{
			$this->contactpersonView();
		}
		else
		{
			throw new Exception("404 - $this->modulename template not found");
		}
		return true;
	}
	
	private function contactpersonList()
	{
		global $XenuxDB;
		
		echo "<h1 class=\"page-headline\">" . __('contactpersons') . "</h1>";
		
		$start			= is_numeric(@$_GET['start']) ? floor($_GET['start']) : 0;
		$amount			= (is_numeric(@$_GET['amount']) && floor(@$_GET['amount']) != 0) ? floor($_GET['amount']) : 20;
		$absolutenumber	= $XenuxDB->count('contactpersons');

		$contactpersonList = $XenuxDB->getList('contactpersons', [
			'order' => 'name ASC',
			'limit' => [$start, $amount]
		]);
		
		if ($contactpersonList)
		{
			echo "<div>";
			foreach($contactpersonList as $contactperson)
			{
				$template = new template(PATH_MAIN."/modules/".$this->modulename."/layout_list.php");
		
				$template->setVar("desc", shortstr(strip_tags($contactperson->text)));
				$template->setVar("position", $contactperson->position);
				$template->setVar("name", $contactperson->name);
				$template->setVar("name_url", urlencode($contactperson->name));
				$template->setVar("ID", $contactperson->id);
				$template->setVar("email", escapemail($contactperson->email));

				echo $template->render();
			}
			echo "</div>";

			echo getMenuBarMultiSites($absolutenumber, $start, $amount);

		}
		else
		{
			echo "<p style=\"margin:5px 0;\">" . __('nocontactpersons') . "!</p>";
		}

		$this->page_name = __('contactpersons');
	}
	
	private function contactpersonView()
	{
		global $XenuxDB;
	
		$contactpersonID = explode('-', @$this->url[2])[0];
		$contactpersonID = preg_replace("/[^0-9]/", '', $contactpersonID);

		$contactperson = $XenuxDB->getEntry('contactpersons', [
			'where' => [
				'id' => $contactpersonID
			]
		]);

		if ($contactperson)
		{
			$template = new template(PATH_MAIN."/modules/".$this->modulename."/layout_view.php");
	
			$template->setVar("desc", $contactperson->text);
			$template->setVar("name", $contactperson->name);
			$template->setVar("position", $contactperson->position);
			$template->setVar("email", escapemail($contactperson->email));
			
			echo $template->render();

			$this->page_name = "contactperson:$contactperson->name";
		}
		else
		{
			echo "404 - contactperson not found";			
			$this->page_name = "contactperson:404";
		}
	}
}
?>