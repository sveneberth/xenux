<?php
include_once(MAIN_PATH."/modules/page/controller.php");

class contactController extends pageController
{
	public function run()
	{
		global $app;

		$this->pageView($app->getOption('ContactPage_ID'), true);

		echo $this->getContactForm();

		return true;
	}

	#TODO: move contactform in this controller?!
	private function getContactForm()
	{
		$template = new template(MAIN_PATH."/modules/contact/contactform.php");

		return $template->render();
	}
}
