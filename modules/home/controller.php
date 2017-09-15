<?php
include_once(MAIN_PATH."/modules/page/controller.php");


class homeController extends pageController
{
	public function run()
	{
		global $app;

		$this->pageView($app->getOption('HomePage_ID'), true);

		return true;
	}
}
