<?php
include_once(PATH_MAIN."/modules/page/controller.php");


class homeController extends pageController
{
	public function __construct($url)
	{
		parent::__construct($url);
	}

	public function run()
	{
		global $app;

		$this->pageView($app->getOption('HomePage_ID'), true);

		return true;
	}
}
