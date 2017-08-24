<?php
class testController extends AbstractController
{
	private $searchString;

	public function __construct($url)
	{
		// append translations
		translator::appendTranslations(PATH_MAIN . '/modules/test/translation/');

		global $XenuxDB;

		$this->url = $url;
		$this->modulename = str_replace('Controller', '', get_class());
	}

	public function run()
	{
		$this->helloWorld();

		$this->page_name = __("helloWorld");

		return true;
	}

	private function helloWorld()
	{
		global $XenuxDB;

		echo "<h1 class=\"page-headline\">" . __('helloWorld') . "</h1>";
	}
}
