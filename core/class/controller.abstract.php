<?php
abstract class AbstractController {
	protected $db;
	protected $url;
	protected $modulename;
	protected $template;
	public $page_name;
	public $headlineSuffix;

	public function __construct($url)
	{
		$this->modulename = str_replace('Controller', '', get_called_class());
		$this->url = $url;
	}
}
