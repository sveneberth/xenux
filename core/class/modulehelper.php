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

		// create module
		$this->create_folder($this->name, $this->modulepath);
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
	* add_option:
	*/
	public function add_option($name, $value=null)
	{
		global $XenuxDB;

		$XenuxDB->insert('main', [
			'name' => $name,
			'value' => $value
		]);
	}

	private function create_folder($name, $path)
	{
		if (!file_exists($path . $name)) // create folder, if doesn't exists
			mkdir($path . $name);
	}


}
?>