<?php
class optionsController extends AbstractController
{
	public function __construct($url)
	{
		$this->url = $url;
		$this->modulename = str_replace('Controller', '', get_class());

		if(!isset($this->url[1]) || empty($this->url[1]))
			header("Location: ".URL_ADMIN.'/'.$this->modulename.'/basic');
	}
	
	public function run()
	{
		global $XenuxDB, $app;
		
		// append translations
		translator::appendTranslations(PATH_ADMIN . '/modules/'.$this->modulename.'/translation/');

		$template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout.php");
	
		$template->setVar("messages", '');
		$template->setVar("form", $this->getForm($template));

		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');
		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');
		
		
		echo $template->render();

		$this->page_name = __('options');

		return true;
	}

	#FIXME: split options (basic, pages, etc.)

	private function getForm(&$template)
	{
		global $XenuxDB, $app;

		$languagesOption = array();
		foreach (translator::getLanguages() as $lang => $meta)
		{
			$languagesOption[] = [
				'value' => $lang,
				'label' => $meta->label
			]; 
		}

		$templateOption = array();
		foreach ($app->getTemplates() as $templateFolder)
		{
			$templateOption[] = [
				'value' => $templateFolder,
				'label' => $templateFolder
			]; 
		}

		$formFields = array
		(
			'meta_author' => array
			(
				'type' => 'text',
				'required' => true,
				'label' => __('meta_author'),
				'value' => $app->getOption('meta_author')
			),
			'hp_name' => array
			(
				'type' => 'text',
				'required' => true,
				'label' => __('hp_name'),
				'value' => $app->getOption('hp_name')
			),
			'meta_desc' => array
			(
				'type' => 'textarea',
				'label' => __('meta_desc'),
				'value' => $app->getOption('meta_desc'),
				'info' => __('description of the homepage for meta tags')
			),
			'meta_keys' => array
			(
				'type' => 'textarea',
				'required' => true,
				'label' => __('meta_keys'),
				'value' => $app->getOption('meta_keys'),
				'info' => __('keywords of the homepage for meta tags')
			),
			'admin_email' => array
			(
				'type' => 'email',
				'required' => true,
				'label' => __('admin_email'),
				'value' => $app->getOption('admin_email')
			),
			'users_can_register' => array
			(
				'type' => 'bool_radio',
				'required' => true,
				'label' => __('users_can_register'),
				'value' => parse_bool($app->getOption('users_can_register')),
			),
			'homepage_offline' => array
			(
				'type' => 'bool_radio',
				'required' => true,
				'label' => __('homepage_offline'),
				'value' => parse_bool($app->getOption('homepage_offline')),
			),
			'template' => array
			(
				'type' => 'select',
				'required' => true,
				'label' => __('template'),
				'value' => $app->getOption('template'),
				'options' => $templateOption
			),
			'default_language' => array
			(
				'type' => 'select',
				'required' => true,
				'label' => __('default_language'),
				'value' => $app->getOption('default_language'),
				'options' => $languagesOption
			),
			'submit' => array
			(
				'type' => 'submit',
				'label' => __('save')
			)
		);

		$form = new form($formFields);
		$form->disableRequiredInfo();

		if($form->isSend() && $form->isValid())
		{
			$data = $form->getInput();

			$success = true;
			foreach ($formFields as $name => $props)
			{
				$return = $XenuxDB->Update('main', [
					'value' => $data[$name]
				],
				[
					'name' => $name
				]);
				if($return === false)
					$success = false;
			}

			if($success)
			{
				header('Location: '.URL_ADMIN.'/options/basic/?savingSuccess=true');
			}
			else
			{
				header('Location: '.URL_ADMIN.'/options/basic/?savingSuccess=false');
			}
			
		}
		return $form->getForm();
	}
}
?>