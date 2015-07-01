<?php
class cloudController extends AbstractController
{
	public function __construct($url)
	{
		$this->url = $url;
		$this->modulename = str_replace('Controller', '', get_class());

		if(!isset($this->url[1]) || empty($this->url[1]))
			header("Location: ".URL_ADMIN.'/'.$this->modulename.'/cloud');
	}
	
	public function run()
	{
		global $XenuxDB, $app;

		// append translations
		translator::appendTranslations(PATH_ADMIN . '/modules/'.$this->modulename.'/translation/');

		$app->addCSS(URL_ADMIN . '/modules/'.$this->modulename.'/cloud.css');

		$template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout.php");
	
		$template->setVar("messages", '');
		
		echo $template->render();

		$this->page_name = __('cloud');

		return true;
	}

}
?>