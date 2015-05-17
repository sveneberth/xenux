<?php
class app
{	
	public $template;
	private $site;
	public $siteurl;
	public $url;
	public $page_name;
	public $user;
	
	private $cssfiles = array();

	public $canonical_URL = null;
	public $prev_URL = null;
	public $next_URL = null;

	public function __construct($url)
	{
		$this->user		= new User;

		$this->setTemplate($this->getOption('template'));

		$url			= strtolower($url);
		$this->siteurl	= URL_MAIN.'/'.$url;
		$this->url		= explode('/', $url);

		if (isset($_GET['lang']))
		{
			translator::setLanguage($_GET['lang']);
			header("Location: " . URL_REQUEST);
		}
	}

	public function getOption($option)
	{
		global $app, $XenuxDB;

		$result = $XenuxDB->getEntry('main', [
			'where' => [
				'name' => $option
			]
		]);

		if ($result !== false)
		{
			return $result->value;
		}
		
		return false;
	}

	public function getTemplates()
	{
		$folders = array();

		if ($handle = opendir(PATH_MAIN . '/templates/'))
		{
			while (false !== ($entry = readdir($handle)))
			{
				if ($entry == '.' || $entry == '..') 
					continue;

				$folders[] = $entry;
			}

			closedir($handle);
		}

		return $folders;
	}

	public function setTemplate($template)
	{
		$this->template = $template;
	}
	
	public function buildPage()
	{
		$this->site = empty($this->url[0]) ? 'home' : $this->url[0];
	
		if ($this->site == 'file')
		{
			echo $this->getFile(@$this->url[1]);
		}
		else
		{
			// append translations
			translator::appendTranslations(PATH_MAIN."/templates/".$this->template."/translation/");

			$template = new template(PATH_MAIN."/templates/".$this->template."/index.php");
			
			$template->setVar("page_content", $this->getPageContent());

			$template->setVar("SITE_PATH", $this->siteurl);
			$template->setVar("TEMPLATE_PATH", URL_MAIN.'/templates/'.$this->template);

			$template->setVar("meta_author", $this->getOption('meta_author'));
			$template->setVar("meta_desc", $this->getOption('meta_desc'));
			$template->setVar("meta_keys", $this->getOption('meta_keys'));

			$template->setVar("homepage_name", $this->getOption('hp_name'));
			$template->setVar("page_title", $this->page_name);

			$template->setVar("canonical_URL", is_null($this->canonical_URL) ? $this->siteurl : $this->canonical_URL);
			$template->setIfCondition("prev_URL", !is_null($this->prev_URL));
			$template->setVar("prev_URL", $this->prev_URL);
			$template->setIfCondition("next_URL", !is_null($this->next_URL));
			$template->setVar("next_URL", $this->next_URL);

			echo $template->render();
		}
	}

	public function buildAdminPage()
	{
		$this->site = $this->url[0];
		
		// append translations
		translator::appendTranslations(PATH_ADMIN . '/translation/');

		if ($this->site == 'login' || $this->site == 'logout')
		{
			if (inludeExists(PATH_ADMIN."/modules/login/controller.php"))
			{
				$controller = new loginController($this->url);
				
				$controller->run();
			}
			else
			{
				throw new Exception("404 - controller not found");
				
				return "404 - controller not found";
			}
		}
		elseif ($this->user->isLogin())
		{
			if (empty($this->site))
			{
				header('Location: '.URL_ADMIN.'/dashboard/home');
				return false;
			}

			$template = new template(PATH_ADMIN."/template/index.php");
			
			$template->setVar("page_content", $this->getPageContent(true));

			$template->setVar("SITE_PATH", $this->siteurl);
			$template->setVar("TEMPLATE_PATH", URL_MAIN.'/administration/template');
			
			$template->setVar("meta_author", $this->getOption('meta_author'));
			$template->setVar("meta_desc", $this->getOption('meta_desc'));
			$template->setVar("meta_keys", $this->getOption('meta_keys'));

			$template->setVar("homepage_name", $this->getOption('hp_name'));
			$template->setVar("page_title", $this->page_name);
			$template->setVar("headline", $this->page_name);

			$num_active_module = array_search($this->site, $this->getAdminModule());
			$template->setVar("num_active_module", $num_active_module !== false ? $num_active_module+1 : 0);

			$template->setVar("CSS-FILES", $this->getCSS());
			
			echo $template->render();	
		}
		else
		{
			global $_get_params;			
			header('Location: '.URL_MAIN.'/administration/login'.(!empty($this->url[0]) ? '?redirectTo='.implode('/', $this->url) : '').'?'.$_get_params);
		}
	}

	public function getAdminModule($all=false)
	{
		$return = Array();

		$modules = array_filter(glob(PATH_ADMIN.'/modules/*'), 'is_dir');
		foreach ($modules as $module)
		{
			$module = str_replace(PATH_ADMIN.'/modules/', '', $module);

			if (($module == 'login' || $module == 'dashboard') && $all == false)
				continue;

			$return[] = $module;
		}

		return $return;
	}
	
	private function getPageContent($administration=false)
	{
		try
		{
			if (inludeExists(($administration ? PATH_ADMIN : PATH_MAIN)."/modules/".$this->site."/controller.php"))
			{
				$classname = $this->site."Controller";
				$controller = new $classname($this->url);
				
				ob_start();
					$controller->run();
				$page_content = ob_get_clean();
				
				$this->page_name = $controller->page_name;
				
				return $page_content;
			}
			else
			{
				throw new Exception("404 - controller \"$this->site\" not found");
			}
		}
		catch (Exception $e)
		{
			if (defined('DEBUG') && DEBUG == true)
				log::setPHPError($e);
			$this->page_name = "Error";
			return '<p class="box-shadow info-message error">' . $e->getMessage() . '</p>';
		}
	}

	public function addCSS($file)
	{
		$this->cssfiles[] = $file;
	}
	
	public function getCSS()
	{
		$css = '';
		foreach ($this->cssfiles as $file)
		{
			$css .= '<link rel="stylesheet" href="' . $file . '" />';
		}

		return $css;
	}


	/**
	* function getFile (a part of the Xenux-Cloud)
	* request: PROJECT-PATH/file/{SHA1-HASH-OF-THE-FILE-ID}{flags}
	* flags:
	* -s(int)	: set the width for an image
	* -c		: get a square images
	* -d		: get the file as download
	*/
	private function getFile($param)
	{
		global $app, $XenuxDB;

		$hashID = $XenuxDB->escapeString(explode('-', $param)[0]);
		
		preg_match_all('/-([a-z]?)([0-9]*)/', $param, $optionMatches, PREG_SET_ORDER);

		$options = array();
		foreach ($optionMatches as $match)
		{
			$options[$match[1]] = $match[2];
		}

		$file = $XenuxDB->getEntry('files', [
					'where' => [
						'AND' => [
							'type' => 'file',
							'SHA1(id)' => $hashID
						]
					]
				]);
		if ($file)
		{
			$lastModified = mysql2date('D, d M Y H:i:s', $file->lastModified);
			$typeCategory = substr($file->mime_type, 0, strpos($file->mime_type, "/"));

			header("Content-Disposition: ".(isset($options['d']) ? 'attachment' : 'inline')."; filename=\"{$file->filename}\"");
			header("Cache-Control: public, max-age=3600");
			header("Last-Modified: {$lastModified} GMT");

			if ($typeCategory == 'image' && $file->mime_type != "image/svg+xml" && (isset($options['c']) || isset($options['r']) || isset($options['s'])))
			{
				$image = imagecreatefromstring($file->data);

				if (isset($options['r']) && is_numeric($options['r']))
					$image = imagerotate($image, $options['r'],  imagecolorallocate($image, 255, 255, 255));

				$x = imagesx($image);
				$y = imagesy($image);

				if (isset($options['s']))
					$options['s'] = $options['s'] > $x ? $x : $options['s'];
				
				if (isset($options['c']))
				{
					$desired_height	= $desired_width = isset($options['s']) && is_numeric($options['s']) ? $options['s'] : $y;
				}
				else
				{
					$desired_width = (isset($options['s']) && is_numeric($options['s'])) ? $options['s'] : $x;
					$desired_height = $y / $x * $desired_width;
				}

				$new = imagecreatetruecolor($desired_width, $desired_height);
				imagealphablending($new, FALSE);
				imagesavealpha($new, TRUE);
				imagecopyresampled($new, $image, 0, 0, 0, 0, $desired_width, $desired_height, $x, $y);
				imagedestroy($image);
				
				if ($file->mime_type == "image/jpeg")
				{
					header("Content-type: image/jpeg");
					imagejpeg($new);
				}
				elseif ($file->mime_type == "image/gif")
				{
					header("Content-type: image/gif");
					imagegif ($new);
				}
				else
				{
					header("Content-type: image/png");
					imagepng($new);
				}
			}
			else
			{
				header("Content-type: {$file->mime_type}");
				echo $file->data;
			}
		}
		else
		{
			ErrorPage::view(404);
			if (defined('DEBUG') && DEBUG == true)
				log::writeLog('404 - Request file "'.$param.'" not found');
		}
	}
}
?>