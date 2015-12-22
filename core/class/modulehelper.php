<?php
class modulehelper
{
	private $name;
	private $tmppath = PATH_MAIN . '/tmp/';
	private $modulepath = PATH_MAIN . '/modules/';

	public function __construct()
	{
		
	}


	/**
	* name:
	*/
	public function name($name)
	{
		$this->name = $name;
	}


	public function install()
	{
		if ($this->module_installed())	// avoid double installation
			return false;				// returned false, if module already installed

		// create module
		$this->create_folder($this->name, $this->modulepath);

		// register module in options
		$installed_modules[] = $this->name;
		$this->update_option('installed_modules', json_encode($installed_modules));

		return true;
	}

	public function uninstall()
	{
		if (!$this->module_installed())
		{
			echo '<p>module not installed</p>';
			return false;
		}

		$installed_modules = json_decode($this->get_option('installed_modules'));
		remove_array_value($installed_modules, $this->name);

		// write all the rest modules in options
		$this->update_option('installed_modules', json_encode($installed_modules));

		return true;
	}

	/**
	* move:
	*/
	public function move(array $arr)
	{
		foreach($arr as $old => $new)
		{
			full_copy($this->tmppath . 'module/' . $old, str_replace('#MODULEPATH', $this->modulepath . $this->name . '/', $new));
		}
	}
	
	/**
	* remove:
	*/
	public function remove(array $arr)
	{
		foreach($arr as $object)
		{
			if (empty($this->name))
			{
				// error

				// avoid to remove the full `/modules` folder
				// #FIXME: solve it prettier ...

				continue;
			}

			$object = str_replace('#MODULEPATH', $this->modulepath . $this->name . '/', $object);

			if (is_dir($object))
			{
				rrmdir($object);
			}
			else
			{
				unlink($object);
			}
		}
	}

	/**
	* get_option:
	*/
	public function get_option($name)
	{
		global $app;

		return $app->getOption($name);
	}

	/**
	* add_option:
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
	*/
	public function update_option($name, $value=null)
	{
		global $XenuxDB;

		$XenuxDB->Update('main', [
			'value' => $value
		],
		[
			'name' => $name
		]);

	}
	
	/**
	* remove_option:
	*/
	public function remove_option($name)
	{
		global $XenuxDB;

		$XenuxDB->Delete('main', [
			'where' => [
				'name' => $name
			]
		]);

	}

	private function create_folder($name, $path)
	{
		if (!file_exists($path . $name)) // create folder, if doesn't exists
			mkdir($path . $name);
	}


	public function module_installed($name = null)
	{
		// get installed_modules
		$installed_modules = json_decode($this->get_option('installed_modules'));

		return
			in_array (
				is_null($name) ? $this->name : $name,
				(array) $installed_modules
			);
	}

	public function getModuleInfo($path)
	{
		return
			json_decode(file_get_contents($path . '/info.json'));
	}

}
?>