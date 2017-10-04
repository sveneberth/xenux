<?php
abstract class AbstractController {
	protected $url;
	protected $modulename;
	protected $template;
	public $page_name;
	public $headlinePrefix;
	public $headlineSuffix;

	public function __construct($url)
	{
		$this->modulename = strtolower(str_replace('Controller', '', get_called_class()));
		$this->url = $url;
	}
}
