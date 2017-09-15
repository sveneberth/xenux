<?php
include_once(MAIN_PATH."/modules/page/controller.php");


class imprintController extends pageController
{
	public function run()
	{
		global $app;

		$this->pageView($app->getOption('ImprintPage_ID'), true);

		return true;
	}
}
