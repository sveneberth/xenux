<?php

class ressource
{
	private $file;
	private $type;
	private $mimetype;

	public function __construct($url = null, $type = null)
	{
		if (@$url[1] != '')
		{
			$this->file = $url;
			$this->file[0] = MAIN_PATH; // replace module name with MAIN_PATH
			$this->file = implode('/', $this->file);
			$this->type = $type;

			if ($this->type == 'js')
			{
				$this->mimetype = 'application/javascript';
				$this->render();
			}
			elseif ($this->type == 'css')
			{
				$this->mimetype = 'text/css';
				$this->render();
			}
			else
			{
				log::debug('500 - wrong type');
				ErrorPage::view(500);
			}
		}
		else
		{
			// missing id ...
			log::debug('405 - missing url');
			ErrorPage::view(405);
		}
	}

	private function render()
	{
		global $app, $XenuxDB;

		if (strpos('..', $this->file) !== false)
		{
			// Directory Traversal Attack
			log::debug('405 - Directory Traversal Attacks <{$this->file}>');
			ErrorPage::view(405);
			return false;
		}

		// append translations in self directionary
		translator::appendTranslations(dirname($this->file) . '/translation/');

		if (file_exists($this->file))
		{
			if (pathinfo($this->file, PATHINFO_EXTENSION) == $this->type)
			{
				$template = new template($this->file);

				echo $template->render();

				$filesize  = filesize($this->file);
				$filemtime = date('D, d M Y H:i:s', filemtime($this->file));

				header("Last-Modified: {$filemtime} GMT");
				header("Content-Length: {$filesize}");
				header("Content-type: {$this->mimetype}");
			}
			else
			{
				log::debug("405 - requested filetype of item <{$this->file}> not allowed");
				ErrorPage::view(405);
			}
		}
		else
		{
			// item not found ...
			log::debug('404 - requested ressource <"'.$this->file.'"> not found');
			ErrorPage::view(404);
		}
	}
}
