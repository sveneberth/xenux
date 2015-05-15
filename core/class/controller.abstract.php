<?php
abstract class AbstractController {
	protected $db;
	protected $url;
	protected $modulename;
	protected $template;
	public $page_name;
	
	public function __construct()
	{
		$this->db = db::getConnection();
		
		/*
			FIXME:
			create modulename in abstract-class
			
			$this->modulename = str_replace('Controller', '', get_class());
		*/
	}
}