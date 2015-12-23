<?php
class modulemanagerController extends AbstractController
{
	public function __construct($url)
	{
		$this->url = $url;
		$this->template;
		$this->modulename = str_replace('Controller', '', get_class());

		if(!isset($this->url[1]) || empty($this->url[1]))
			header("Location: ".URL_ADMIN.'/'.$this->modulename.'/modules');
	}
	
	public function run()
	{
		global $XenuxDB, $app;
		
		// append translations
		translator::appendTranslations(PATH_ADMIN . '/modules/'.$this->modulename.'/translation/');

		$this->template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout.php");
		$this->template->setVar("messages", '');
		$this->template->setVar("upload_form", $this->getUploadForm());

		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$this->template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');
		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$this->template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');
		
		$this->_checkRemove();
		
		echo $this->template->render();

		$this->page_name = __('modules');

		return true;
	}

	private function getUploadForm()
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
		//	echo "send";

			if (!file_exists(PATH_MAIN."/tmp/")) // create folder, if doesn't exists
				mkdir(PATH_MAIN."/tmp/");

			$uploaddir	= PATH_MAIN . '/tmp/'; // uploaddir
			$uploadfile	= $uploaddir . basename($_FILES['file']['name']); // uploadfile
			$ext		= end((explode(".",  $_FILES["file"]["name"]))); // extension

			if($ext == 'zip')
			{
				// OK
				if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) // upload file
				{
					// uploading successful

					$zip = new ZipArchive;
					if ($zip->open($uploadfile)) // unzip the file
					{
						$zip->extractTo($uploaddir . '/module/');
						$zip->close();


						$modulehelper = new modulehelper;
						$moduleInfo = $modulehelper->getModuleInfo($uploaddir . '/module/'); // get the module info
						$modulehelper->name($moduleInfo->name);

						if ($modulehelper->install()) // install module
						{
							// run module-installer
							include_once($uploaddir . '/module/install.php'); // run installer
							$this->template->setVar("messages", '<p class="box-shadow info-message ok">'.__('module installed successful').'</p>');
						}
						else
						{
							// module already installed: show an error-message
							$this->template->setVar("messages", '<p class="box-shadow info-message error">'.__('module already installed').'</p>');
						}


						rrmdir(PATH_MAIN . '/tmp/'); // remove temp-folder
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
		}

		return $form->getForm();
	}

	private function _checkRemove()
	{
		global $app, $XenuxDB;

		if(isset($_GET['removeModule']) && full(@$_GET['removeModule'])) // if get parameter set
		{
			if(isset($_GET['confirmed']) && @$_GET['confirmed'] == true) // was the remove-process confimed ??
			{
				$modules = json_decode($app->getOption('installed_modules'));
				if(in_array($_GET['removeModule'], $modules)) // check if module installed
				{
					// uninstall the module

					$modulehelper = new modulehelper;
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
				$this->template->setVar("messages", '<p class="box-shadow info-message warning">'.__('shure to remove?').'<br /><a class="btn" href="' . URL_ADMIN . '/modulemanager/modules?removeModule=' . $_GET['removeModule'] . '&confirmed=true">' . __('yes') . '</a></p>');
			}
		}
	}
}
?>