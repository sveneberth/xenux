<?php
class pluginmanagerController extends AbstractController
{
	public function __construct($url)
	{
		$this->url = $url;
		$this->template;
		$this->modulename = str_replace('Controller', '', get_class());

		if (!isset($this->url[1]) || empty($this->url[1]))
			header("Location: ".URL_ADMIN.'/'.$this->modulename.'/modules');
	}
	
	public function run()
	{
		global $XenuxDB, $app;
		
		// append translations
		translator::appendTranslations(PATH_ADMIN . '/modules/'.$this->modulename.'/translation/');

		if (@$this->url[1] == "modules")
		{
			$this->modules();
		}
		elseif (@$this->url[1] == "templates")
		{
			$this->templates();
		}
		else
		{
			throw new Exception("404 - $this->modulename template not found");
		}

		return true;
	}

	private function modules()
	{
		global $app, $XenuxDB;

		$this->template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout_modules.php");
		$this->template->setVar("messages", '');
		$this->template->setVar("upload_form", $this->getModulesUploadForm());

		if (isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$this->template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');
		if (isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$this->template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');
		
		$this->_checkModuleRemove();
		
		echo $this->template->render();

		$this->page_name = __('modules');
	}		

	private function getModulesUploadForm()
	{
		global $app, $XenuxDB;
		
		$formFields = array
		(
			'file' => array
			(
				'type' => 'file',
				'required' => true,
				'multiple' => false,
				'label' => __('upload file')
			),
			'submit' => array
			(
				'type' => 'submit',
				'label' => __('upload')
			)
		);
	
		$form = new form($formFields);
		$form->disableRequiredInfo();


		if ($form->isSend() && $form->isValid())
		{
			$modulehelper = new pluginhelper('module');

			$uploadfile	= $modulehelper->tmppath . basename($_FILES['file']['name']); // uploadfile
			$ext		= end((explode(".",  $_FILES["file"]["name"]))); // extension

			if ($ext == 'zip')
			{
				// OK

				$hp_offline = $app->getOption('homepage_offline');
				if ($hp_offline == false)
					$XenuxDB->Update('main', [ // set homepage in maintenance
						'value' => true
					],
					[
						'name' => 'homepage_offline'
					]);

				if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) // upload file
				{
					// uploading successful

					$zip = new ZipArchive;
					if ($zip->open($uploadfile)) // unzip the file
					{
						$zip->extractTo($modulehelper->tmppath);
						$zip->close();

						$moduleInfo = $modulehelper->getInfo(); // get the module info
						$modulehelper->name($moduleInfo->name);

						if ($modulehelper->install()) // install module
						{
							// run module-installer
							include_once($modulehelper->tmppath . '/install.php'); // run installer
							$this->template->setVar("messages", '<p class="box-shadow info-message ok">'.__('module installed successful').'</p>');
						}
						else
						{
							// module already installed: show an error-message
							$this->template->setVar("messages", '<p class="box-shadow info-message error">'.__('module already installed').'</p>');
						}
					}
					else // something went wrong
					{
						$this->template->setVar("messages", '<p class="box-shadow info-message error">'.__('something went wrong :/').'</p>');
					}
					
				}
				else
				{
					$this->template->setVar("messages", '<p class="box-shadow info-message error">'.__('upload failed').'</p>');
				}
				
				if ($hp_offline == false)
					$XenuxDB->Update('main', [ // set homepage out of maintenance
						'value' => false
					],
					[
						'name' => 'homepage_offline'
					]);
			}
			else
			{
				// not a zip-file
				$this->template->setVar("messages", '<p class="box-shadow info-message warning">'.__('Please upload a zip-file.').'</p>');
			}

			$modulehelper->close();
		}

		return $form->getForm();
	}

	private function _checkModuleRemove()
	{
		global $app, $XenuxDB;

		if (isset($_GET['removeModule']) && full(@$_GET['removeModule'])) // if get parameter set
		{
			if (isset($_GET['confirmed']) && @$_GET['confirmed'] == true) // was the remove-process confirmed ??
			{
				$modules = json_decode($app->getOption('installed_modules'));
				if (in_array($_GET['removeModule'], $modules)) // check if module installed
				{
					// uninstall the module

					$modulehelper = new pluginhelper('module', false);
					$modulehelper->name($_GET['removeModule']);

					if ($modulehelper->uninstall())
					{
						include_once(PATH_MAIN . '/modules/' . $_GET['removeModule'] . '/uninstall.php'); // run uninstaller
						$this->template->setVar("messages", '<p class="box-shadow info-message ok">'.__('removedSuccessful').'</p>');
					}
					else
					{
						$this->template->setVar("messages", '<p class="box-shadow info-message error">'.__('removing failed, module not installed').'</p>');
					}

					$modulehelper->close();
				}
				else
				{
					// error: module not installed
					$this->template->setVar("messages", '<p class="box-shadow info-message error">'.__('removing failed, module not installed').'</p>');
				}
			}
			else
			{
				// show confirmation
				$this->template->setVar("messages", '<p class="box-shadow info-message warning">'.__('shure to remove?').'<br /><a class="btn" href="' . URL_ADMIN . '/pluginmanager/modules?removeModule=' . $_GET['removeModule'] . '&confirmed=true">' . __('yes') . '</a></p>');
			}
		}
	}


	private function templates()
	{
		global $app, $XenuxDB;

		$this->template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout_templates.php");
		$this->template->setVar("messages", '');
		$this->template->setVar("upload_form", $this->getTemplatesUploadForm());

		if (isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$this->template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');
		if (isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$this->template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');
		
		$this->_checkTemplateRemove();
		
		echo $this->template->render();

		$this->page_name = __('templates');
	}		

	private function getTemplatesUploadForm()
	{
		global $app, $XenuxDB;
		
		$formFields = array
		(
			'file' => array
			(
				'type' => 'file',
				'required' => true,
				'multiple' => false,
				'label' => __('upload file')
			),
			'submit' => array
			(
				'type' => 'submit',
				'label' => __('upload')
			)
		);
	
		$form = new form($formFields);
		$form->disableRequiredInfo();


		if ($form->isSend() && $form->isValid())
		{
			$templatehelper = new pluginhelper('template');

			$uploadfile	= $templatehelper->tmppath . basename($_FILES['file']['name']); // uploadfile
			$ext		= end((explode(".",  $_FILES["file"]["name"]))); // extension

			if ($ext == 'zip')
			{
				// OK
				if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) // upload file
				{
					// uploading successful

					$zip = new ZipArchive;
					if ($zip->open($uploadfile)) // unzip the file
					{
						$zip->extractTo($templatehelper->tmppath);
						$zip->close();

						
						$templateInfo = $templatehelper->getInfo($templatehelper->tmppath); // get the template info
						$templatehelper->name($templateInfo->name);

						if ($templatehelper->install()) // install template
						{
							$this->template->setVar("messages", '<p class="box-shadow info-message ok">'.__('template installed successful').'</p>');
						}
						else
						{
							// template already installed: show an error-message
							$this->template->setVar("messages", '<p class="box-shadow info-message error">'.__('template already installed').'</p>');
						}
					}
					else // something went wrong
					{
						$this->template->setVar("messages", '<p class="box-shadow info-message error">'.__('something went wrong :/').'</p>');
					}
					
				}
				else
				{
					$this->template->setVar("messages", '<p class="box-shadow info-message error">'.__('upload failed').'</p>');
				}
			}
			else
			{
				// not a zip-file
				$this->template->setVar("messages", '<p class="box-shadow info-message warning">'.__('Please upload a zip-file.').'</p>');
			}

			$templatehelper->close();
		}

		return $form->getForm();
	}

	private function _checkTemplateRemove()
	{
		global $app, $XenuxDB;

		if (isset($_GET['removeTemplate']) && full(@$_GET['removeTemplate'])) // if get parameter set
		{
			if (isset($_GET['confirmed']) && @$_GET['confirmed'] == true) // was the remove-process confirmed ??
			{
				$templates = json_decode($app->getOption('installed_templates'));
			
				if (in_array($_GET['removeTemplate'], $templates)) // check if template installed
				{
					// uninstall the template

					$templatehelper = new pluginhelper('template', false);
					$templatehelper->name($_GET['removeTemplate']);

					if ($templatehelper->uninstall())
					{
						$this->template->setVar("messages", '<p class="box-shadow info-message ok">'.__('removedSuccessful').'</p>');
					}
					else
					{
						$this->template->setVar("messages", '<p class="box-shadow info-message error">'.__('removing failed, template not installed').'</p>');
					}

					$templatehelper->close();
				}
				else
				{
					// error: template not installed
					$this->template->setVar("messages", '<p class="box-shadow info-message error">'.__('removing failed, template not installed').'</p>');
				}
			}
			else
			{
				// show confirmation
				$this->template->setVar("messages", '<p class="box-shadow info-message warning">'.__('shure to remove?').'<br /><a class="btn" href="' . URL_ADMIN . '/pluginmanager/templates?removeTemplate=' . $_GET['removeTemplate'] . '&confirmed=true">' . __('yes') . '</a></p>');
			}
		}
	}
}
?>