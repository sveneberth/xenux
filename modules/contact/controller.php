<?php
include_once(PATH_MAIN."/modules/page/controller.php");

class contactController extends pageController
{
	public function __construct($url)
	{
		parent::__construct($url);
	}

	public function run()
	{
		global $app;

		$this->pageView($app->getOption('ContactPage_ID'), true);

		echo $this->getContactForm();

		return true;
	}

	private function getContactForm()
	{
		$template = new template(PATH_MAIN."/modules/contact/contactform.php");

		return $template->render();
	}
}
