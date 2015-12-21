<?php
class modulemanagerController extends AbstractController
{
	public function __construct($url)
	{
		$this->url = $url;
		$this->modulename = str_replace('Controller', '', get_class());

		if(!isset($this->url[1]) || empty($this->url[1]))
			header("Location: ".URL_ADMIN.'/'.$this->modulename.'/modules');
	}
	
	public function run()
	{
		global $XenuxDB, $app;
		
		// append translations
		translator::appendTranslations(PATH_ADMIN . '/modules/'.$this->modulename.'/translation/');

		$template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout.php");
		$template->setVar("messages", '');
		$template->setVar("upload_form", $this->getUploadForm($template));

		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');
		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');
		
		
		echo $template->render();

		$this->page_name = __('modules');

		return true;
	}

	private function getUploadForm(&$template)
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
				//	echo '<p>uploading successful</p>';

					$zip = new ZipArchive;
					if ($zip->open($uploadfile)) // unzip the file
					{
						$zip->extractTo($uploaddir . '/module/');
						$zip->close();

						echo '<p>unzipping successeful</p>';

						$modulehelper = new modulehelper;
						include_once($uploaddir . '/module/install.php'); // run installer


						deleteDirectory(PATH_MAIN . '/tmp/'); //remove temp-folder
					}
					else // something went wrong
					{
						echo '<p>something went wrong :/</p>';
					}
					
				}
				else
				{
					echo '<p>upload failed</p>';
				}
			}
			else
			{
				// not a zip-file
				echo '<p>Please upload a zip-file.</p>';
			}
		}

		return $form->getForm();
	}
}
?>