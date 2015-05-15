<?php
/*
#FIMXE:
can select logs, mails
this is only allowed in debug mode.
*/
class reportsController extends AbstractController
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
	
		$template->setVar("log", htmlentities($this->_getLog()));
		
		echo $template->render();

		$this->page_name = __('reports');

		return true;
	}


	private function _getLog()
	{
		return @file_get_contents(PATH_MAIN."/logs/".date("Y-m-d").".log", "r");
	}

}
?>