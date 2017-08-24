<?php
class cloudController extends AbstractController
{
	public function __construct($url)
	{
		parent::__construct($url);

		if(!isset($this->url[1]) || empty($this->url[1]))
			header('Location: ' . URL_ADMIN . '/' . $this->modulename . '/cloud');
	}

	public function run()
	{
		global $XenuxDB, $app;

		// append translations
		translator::appendTranslations(PATH_ADMIN . '/modules/'.$this->modulename.'/translation/');
		$app->addJS(URL_ADMIN . '/template/js/cloud.js');

		$template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout.php");
		$template->setVar("messages", '');
		echo $template->render();

		$this->page_name = __('cloud');

		return true;
	}
}
