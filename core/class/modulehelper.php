<?php
class modulehelper
{
	private $name;
	private $tmppath = PATH_MAIN . '/tmp/';
	private $modulepath = PATH_MAIN . '/modules/';
	private $moduleadminpath = PATH_ADMIN . '/modules/';

	
	public function __construct()
	{
	}

	
	/**
	* name:
	* set the name of the module in the modulehelper-object, to work with this
	* @param string $name: name of the module 
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
		if ($this->module_installed())	// avoid double installation
			return false;				// returned false, if module already installed

		// create module
		$this->create_folder($this->name, $this->modulepath);
		$this->create_folder($this->name, $this->moduleadminpath);

		// register module in options
		$installed_modules[] = $this->name;
		$this->update_option('installed_modules', json_encode($installed_modules));

		return true;
	}


	/**
	* uninstall:
	* @return bool: everthing okay? 
	*/
	public function uninstall()
	{
		if (!$this->module_installed()) // module not installed
			return false;
		
		$installed_modules = json_decode($this->get_option('installed_modules'));
		remove_array_value($installed_modules, $this->name);

		// write all the rest modules in options
		$this->update_option('installed_modules', json_encode($installed_modules));

		return true;
	}

	/**
	* remove:
	* @param array $arr: array of element to be moved 
	* @return ---
	*/
	public function move(array $arr)
	{
		foreach($arr as $old => $new)
		{
			full_copy($this->tmppath . 'module/' . $old, $this->replace_paths($new));
		}
	}
	

	/**
	* remove:
	* @param array $arr: array of element to be moved 
	* @return ---
	*/
	public function remove(array $arr)
	{
		foreach($arr as $object)
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
	* create_folder
	* @param string $name: name of the to be created folder
	* @param string $path: path of parent folder
	* @return string: path with replaced constants
	*/
	private function create_folder($name, $path)
	{
		if (!file_exists($path . $name)) // create folder, if doesn't exists
			return mkdir($path . $name);
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
	* module_installed
	* @param string $name: name of a module
	* @return bool: if module installed
	*/
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


	/**
	* getModuleInfo
	* @param string $path: path of the file in which is the info.json file
	* @return object info: file as object
	*/
	public function getModuleInfo($path)
	{
		return
			json_decode(file_get_contents($path . '/info.json'));
	}

}
?>