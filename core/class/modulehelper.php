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

		// get installed_modules
		$installed_modules = json_decode($this->get_option('installed_modules'));

		if(in_array($this->name, $installed_modules))
		{
			echo '<p>module already installed</p>';
			return false;
		}

		// create module
		$this->create_folder($this->name, $this->modulepath);

		// register module in options
		$installed_modules[] = $this->name;
		$this->update_option('installed_modules', json_encode($installed_modules));

#		return true;
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

		if($this->get_option($name) === false)
		{
			// option already exists
			return false;
		}

		$XenuxDB->insert('main', [
			'name' => $name,
			'value' => $value
		]);
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

	private function create_folder($name, $path)
	{
		if (!file_exists($path . $name)) // create folder, if doesn't exists
			mkdir($path . $name);
	}


}
?>