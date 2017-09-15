<?php
class pluginhelper
{
	private $type;
	private $name;
	public $tmppath;
	private $modulepath = MAIN_PATH . '/modules/';
	private $moduleadminpath = ADMIN_PATH . '/modules/';
	private $templatepath = MAIN_PATH . '/templates/';
	private $hp_offline;


	public function __construct($type, $needTMP = true)
	{
		global $XenuxDB, $app;

		$this->type = $type;

		$this->tmppath = MAIN_PATH . '/tmp/' . generateRandomString(6) . '/';

		if ($needTMP)
			create_folder($this->tmppath);

		$this->hp_offline = $app->getOption('homepage_offline');

		if ($this->hp_offline == false)
		{
			$XenuxDB->Update('main', [ // set homepage in maintenance
				'value' => true
			],
			[
				'name' => 'homepage_offline'
			]);
		}
	}


	/**
	* name:
	* set the name of the plugin in the pluginhelper-object, to work with this
	* @param string $name: name of the plugin
	*/
	public function name($name)
	{
		$this->name = $name;
	}


	/**
	* install:
	* @return bool: everthing okay?
	*/
	public function install()
	{
		if ($this->installed())	// avoid double installation
			return false;		// returned false, if already installed

		if ($this->type == 'module')
		{
			// create module
			create_folder($this->modulepath . $this->name);
			create_folder($this->moduleadminpath . $this->name);

			// register module in options

			$installed_modules   = json_decode($this->get_option('installed_modules'));
			$installed_modules[] = $this->name;
			$this->update_option('installed_modules', json_encode($installed_modules));
		}
		elseif ($this->type == 'template')
		{
			full_copy($this->tmppath, $this->templatepath . $this->name);

			// create template
			create_folder($this->templatepath . $this->name);

			// register template in options
			$installed_templates   = json_decode($this->get_option('installed_templates'));
			$installed_templates[] = $this->name;
			$this->update_option('installed_templates', json_encode($installed_templates));
		}
		else
			return null;


		return true;
	}


	/**
	* uninstall:
	* @return bool: everthing okay?
	*/
	public function uninstall()
	{
		if (!$this->installed()) // not installed
			return false;

		if ($this->type == 'module')
		{
			$installed_modules = json_decode($this->get_option('installed_modules'));
			remove_array_value($installed_modules, $this->name);

			// write all the rest modules in options
			$this->update_option('installed_modules', json_encode($installed_modules));
		}
		elseif ($this->type == 'template')
		{
			rrmdir($this->templatepath . $this->name);

			$installed_templates = json_decode($this->get_option('installed_templates'));
			remove_array_value($installed_templates, $this->name);

			// write all the rest templates in options
			$this->update_option('installed_templates', json_encode($installed_templates));
		}
		else
			return null;

		return true;
	}

	/**
	* remove:
	* @param array $arr: array of element to be moved
	* @return ---
	*/
	public function move(array $arr)
	{
		foreach ($arr as $old => $new)
		{
			full_copy($this->tmppath . $old, $this->replace_paths($new));
		}
	}


	/**
	* remove:
	* @param array $arr: array of element to be moved
	* @return ---
	*/
	public function remove(array $arr)
	{
		foreach ($arr as $object)
		{
			$object = $this->replace_paths($object); // replace the constants

			rrmdir($object); // remove file or folder
		}
	}


	/**
	* get_option:
	* @param string $name: name of the option
	* @return string: value of the requested option
	*/
	public function get_option($name)
	{
		global $app;

		return $app->getOption($name);
	}


	/**
	* add_option:
	* @param string $name: name of the option to be added
	* @param string|int|bool $value: value of the option to be added
	* @return bool: result of the XenuxDB query
	*/
	public function add_option($name, $value=null)
	{
		global $XenuxDB;

		if ($this->get_option($name) === false)
		{
			return $XenuxDB->Insert('main', [
				'name' => $name,
				'value' => $value
			]);
		}

		return false;
	}


	/**
	* update_option:
	* @param string $name: name of the option to be updated
	* @param string|int|bool $value: value of the option to be updated
	* @return bool: result of the XenuxDB query
	*/
	public function update_option($name, $value=null)
	{
		global $XenuxDB;

		return $XenuxDB->Update('main', [
			'value' => $value
		],
		[
			'name' => $name
		]);

	}


	/**
	* remove_option:
	* @param string $name: name of the option to be removed
	* @return bool: result of the XenuxDB query
	*/
	public function remove_option($name)
	{
		global $XenuxDB;

		return $XenuxDB->Delete('main', [
			'where' => [
				'name' => $name
			]
		]);

	}


	/**
	* replace_paths
	* @param string $string: path of a file
	* @return string: path with replaced constants
	*/
	private function replace_paths($string)
	{
		return
			str_replace([
				'#MODULEPATH',
				'#MODULEADMINPATH'
			], [
				$this->modulepath . $this->name . '/',
				$this->moduleadminpath . $this->name . '/'
			], $string);
	}


	/**
	* installed
	* @param string $name: name of a plugin
	* @return bool: if plugin installed
	*/
	public function installed($name = null)
	{
		if ($this->type == 'module')
		{
			// get installed modules
			$installed = json_decode($this->get_option('installed_modules'));
		}
		elseif ($this->type == 'template')
		{
			// get installed templates
			$installed = json_decode($this->get_option('installed_templates'));
		}
		else
			return null;

		return
			in_array (
				is_null($name) ? $this->name : $name,
				(array) $installed
			);
	}


	/**
	* getInfo
	* @param string $path: path of the file in which is the info.json file
	* @return object info: file as object
	*/
	public function getInfo($path = null)
	{
		$path = is_null($path) ? $this->tmppath : $path;

		return
			json_decode(file_get_contents($path . '/info.json'));
	}


	/**
	* close
	*/
	public function close()
	{
		global $XenuxDB;

		rrmdir($this->tmppath);

		if (is_dir_empty(dirname($this->tmppath)))
			rrmdir(dirname($this->tmppath));

		if ($this->hp_offline == false)
		{
			$XenuxDB->Update('main', [ // set homepage out of maintenance
				'value' => false
			],
			[
				'name' => 'homepage_offline'
			]);
		}
	}

}
