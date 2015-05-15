<?php
class translator
{	
	public static $translations = array();
	public static $putBasicTranslations = false;


	public static function translate($str)
	{
		if(self::$putBasicTranslations === false)
		{
			self::$translations = self::getTranslations(self::getLanguage());
			self::$putBasicTranslations = true;
		}
		
		$translations = self::$translations;	
		#var_dump($translations);

		if(!isset($translations[$str]))
			return $str;

		$args = func_get_args(); // get arguments
		unset($args[0]); // unset varname
		
		$translationStr = $translations[$str]; // set string
		
		foreach($args as $key => $val)
		{
			$translationStr = str_replace("%".$key, $val, $translationStr); // replace vars
		}
		return $translationStr;
	}

	public static function getLanguage()
	{
		global $app;
		return isset($_SESSION['language']) ? $_SESSION['language'] : $app->getOption('default_language');
	}
	
	public function setLanguage($lang)
	{
		if(self::translationExists($lang))
		{
			$_SESSION['language'] = $lang;
		}
	}

	private static function getTranslations($lang, $path=PATH_MAIN."/translation/")
	{
		return
			(array) json_decode(file_get_contents($path . $lang . '.json'));
	}

	public static function appendTranslations($path)
	{
		if(self::$putBasicTranslations === false)
		{
			self::$translations = self::getTranslations(self::getLanguage());
			self::$putBasicTranslations = true;
		}

		$lang = self::getLanguage();
		if(self::translationExists($lang, $path))
		{
			self::$translations = array_merge(self::$translations, self::getTranslations($lang, $path));

			return true;
		}
		
		return false;
	}

	private function translationExists($lang, $path=PATH_MAIN.'/translation/')
	{
		return
			file_exists($path . $lang . '.json') && 
			is_json(file_get_contents($path . $lang . '.json')) && 
			isset(self::getLanguages()->{$lang});
	}

	public function getLanguages()
	{
		$content = file_get_contents(PATH_MAIN."/translation/index.json");
		if($json = is_json($content, true))
		{
			return $json->languages;
		}

		return false;
	}
}

function __() // an alias for translator::translate()
{
	return forward_static_call_array(array('translator', 'translate'), func_get_args());
}
?>