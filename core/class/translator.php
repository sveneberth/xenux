<?php
class translator
{	
	public static function translate($str)
	{
		$translations = self::getTranslations(self::getLanguage());

		if(!isset($translations->$str))
			return $str;

		$args = func_get_args(); // get arguments
		unset($args[0]); // unset varname
		
		$translationStr = $translations->$str; // set string
		
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

	private static function getTranslations($lang)
	{
		return
			json_decode(file_get_contents(PATH_MAIN."/translation/{$lang}.json"));
	}

	private function translationExists($lang)
	{
		return
			file_exists(PATH_MAIN.'/translation/'.$lang.'.json') && 
			is_json(file_get_contents(PATH_MAIN.'/translation/'.$lang.'.json')) && 
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