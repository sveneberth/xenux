<?php
class cloudController extends AbstractController
{
	public function __construct($url)
	{
		$this->url = $url;
		$this->modulename = str_replace('Controller', '', get_class());

		if(!isset($this->url[1]) || empty($this->url[1]))
			header("Location: ".URL_ADMIN.'/'.$this->modulename.'/home');
	}
	
	public function run()
	{
		global $XenuxDB, $app;

		$template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout.php");
	
		$template->setVar("messages", '');
		
		echo $template->render();

		$this->page_name = "Xenux Cloud";

		return true;
	}

}
?>