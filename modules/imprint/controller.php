<?php
include_once(PATH_MAIN."/modules/page/controller.php");


class imprintController extends pageController
{
	public function run()
	{
		global $app;

		$this->pageView($app->getOption('ImprintPage_ID'), true);

		return true;
	}
}
