<?php
class app
{
	public $template;
	private $site;
	public $siteurl;
	public $url;
	public $page_name;
	public $user;
	public $headlinePrefix;
	public $headlineSuffix;

	private $cssfiles = [];
	private $jsfiles = [];

	public $canonical_URL = null;
	public $prev_URL = null;
	public $next_URL = null;

	public function __construct($url)
	{
		$this->user		= new User;

		$this->setTemplate($this->getOption('template'));
		if (isset($_GET['useTemplate']) && full($_GET['useTemplate']) && DEBUG_MODE)
			$this->setTemplate($_GET['useTemplate']);

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

		if ($result !== false && $result !== null)
		{
			return $result->value;
		}

		return false;
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
			new file(@$this->url);
		}
		else
		{
			// append translations
			translator::appendTranslations(PATH_MAIN."/templates/".$this->template."/translation/");

			$template = new template(PATH_MAIN."/templates/".$this->template."/index.php");

			$template->setVar("page_content", $this->getPageContent());

			$template->setVar("SITE_PATH", $this->siteurl);
			$template->setVar("TEMPLATE_PATH", URL_MAIN.'/templates/'.$this->template);

			$template->setVar("CSS-FILES", $this->getCSS());
			$template->setVar("JS-FILES", $this->getJS());

			$template->setVar("meta_author", $this->getOption('meta_author'));
			$template->setVar("meta_desc", $this->getOption('meta_desc'));
			$template->setVar("meta_keys", $this->getOption('meta_keys'));

			$template->setVar("homepage_name", $this->getOption('hp_name'));
			$template->setVar("page_title", $this->page_name);

			$template->setVar("canonical_URL", $this->site=='home' ? URL_MAIN : (is_null($this->canonical_URL) ? $this->siteurl : $this->canonical_URL) );
			$template->setIfCondition("prev_URL", !is_null($this->prev_URL) && $this->site != 'home');
			$template->setVar("prev_URL", $this->prev_URL);
			$template->setIfCondition("next_URL", !is_null($this->next_URL) && $this->site != 'home');
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

			$template->setVar("SITE_PATH", URL_ADMIN.'/'.implode('/', $this->url));
			$template->setVar("TEMPLATE_PATH", URL_MAIN.'/administration/template');

			$template->setVar("meta_author", $this->getOption('meta_author'));
			$template->setVar("meta_desc", $this->getOption('meta_desc'));
			$template->setVar("meta_keys", $this->getOption('meta_keys'));

			$template->setVar("homepage_name", $this->getOption('hp_name'));
			$template->setVar("page_title", $this->page_name);
			$template->setVar("headline", $this->page_name);
			$template->setVar("headlinePrefix", $this->headlinePrefix);
			$template->setVar("headlineSuffix", $this->headlineSuffix);

			$template->setVar("CSS-FILES", $this->getCSS());
			$template->setVar("JS-FILES", $this->getJS());

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
				$this->headlinePrefix = $controller->headlinePrefix;
				$this->headlineSuffix = $controller->headlineSuffix;

				return $page_content;
			}
			else
			{
				throw new Exception("404 - controller \"$this->site\" not found");
			}
		}
		catch (Exception $e)
		{
			log::setPHPError($e);
			$this->page_name = "Error";
			return '<h1>Error</h1><p class="box-shadow info-message error">' . $e->getMessage() . '</p>';
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
			$css .= '<link rel="stylesheet" href="' . $file . '">' . "\n";
		}

		return $css;
	}

	public function addJS($file)
	{
		$this->jsfiles[] = $file;
	}

	public function getJS()
	{
		$js = '';
		foreach ($this->jsfiles as $file)
		{
			$js .= '<script src="' . $file . '"></script>' . "\n";
		}

		return $js;
	}
}
