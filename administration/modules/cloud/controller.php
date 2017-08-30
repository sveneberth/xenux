<?php
class cloudController extends AbstractController
{
	#FIXME: remove if no other stuff needed
	public function __construct($url)
	{
		parent::__construct($url);
	}

	public function run()
	{
		global $XenuxDB, $app;

		// append translations
		translator::appendTranslations(PATH_ADMIN . '/modules/' . $this->modulename . '/translation/');
		$app->addJS(URL_ADMIN . '/modules/' . $this->modulename . '/cloud.js');
		$app->addCSS(URL_ADMIN . '/modules/' . $this->modulename . '/cloud.min.css');

		$template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout.php");
		$template->setVar("messages", '');
		echo $template->render();

		$this->page_name = __('cloud');

		return true;
	}
}
