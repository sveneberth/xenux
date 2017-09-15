<?php
class cloudController extends AbstractController
{
	public function run()
	{
		global $XenuxDB, $app;

		// append translations
		translator::appendTranslations(ADMIN_PATH . '/modules/' . $this->modulename . '/translation/');
		$app->addJS(ADMIN_URL . '/modules/' . $this->modulename . '/cloud.js');
		$app->addCSS(ADMIN_URL . '/modules/' . $this->modulename . '/cloud.min.css');

		$template = new template(ADMIN_PATH."/modules/".$this->modulename."/layout.php");
		$template->setVar("messages", '');
		echo $template->render();

		$this->page_name = __('cloud');

		return true;
	}
}
