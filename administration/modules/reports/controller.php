<?php
/*
#FIMXE:
can select logs, mails
this is only allowed in debug mode.
*/
class reportsController extends AbstractController
{
	public function __construct($url)
	{
		$this->url = $url;
		$this->modulename = str_replace('Controller', '', get_class());

		if(!isset($this->url[1]) || empty($this->url[1]))
			header("Location: ".URL_ADMIN.'/'.$this->modulename.'/home');
	}
	
	public function run()
	{
		global $XenuxDB, $app;

		// append translations
		translator::appendTranslations(PATH_ADMIN . '/modules/'.$this->modulename.'/translation/');

		if($app->user->userInfo->role < 2)
		{
			throw new Exception(__("not allowed - missing permissions"));	
		}

		$template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout.php");

		if(@$this->url[1] == "logs")
		{
			$template->setVar("log", htmlentities($this->_getLog('logs')));
			$template->setVar("logFiles", $this->_getLogFileOptions('logs'));
			$this->page_name = __('reports:logs');
		}
		elseif(@$this->url[1] == "mails")
		{
			$template->setVar("log", htmlentities($this->_getLog('mails')));
			$template->setVar("logFiles", $this->_getLogFileOptions('mails'));
			$this->page_name = __('reports:mails');
		}
	
		echo $template->render();


		return true;
	}


	private function _getLogFileOptions($kind)
	{
		$Arr = array();

		foreach ($this->_getLogFiles($kind) as $filename)
		{
			$Arr[] = '<option value="'.$filename.'" '.($filename==@$_GET['file'] ? 'selected' : '').'>'.$filename.'</option>';
		}

		return implode('', $Arr);
	}

	private function _getLogFiles($kind)
	{
		$Arr = array();

		$path = PATH_MAIN . '/'.$kind.'/';

		if ($handle = opendir($path))
		{
			while (false !== ($file = readdir($handle)))
			{
				if (is_dir($path . $file)) 
					continue;

				$filename = explode('.', $file);
				$filename = str_replace('.' . end($filename), '', $file);

				$Arr[] = $filename;
			}

			closedir($handle);
		}

		return $Arr;
	}

	private function _getLog($kind)
	{
		if(isset($_GET['file']))
		{
			if(in_array($_GET['file'], $this->_getLogFiles($kind)))
			{
				$file = $_GET['file'];
			}
			else
			{
				$file = date("Y-m-d");
			}
		}
		else
		{
			$file = date("Y-m-d");
		}

		return @file_get_contents(PATH_MAIN.'/'.$kind.'/'.$file.'.'.($kind=='mails'?'txt':'log'), 'r');
	}

}
?>