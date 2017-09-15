<?php
class translator
{
	public static $translations = array();
	public static $putBasicTranslations = false;


	public static function translate($str)
	{
		if (self::$putBasicTranslations === false) // check if the basic translations are already included
		{
			self::$translations = self::getTranslations(self::getLanguage());
			self::$putBasicTranslations = true;
		}

		$translations = self::$translations;

		if (!isset($translations[$str]))
			return $str;

		$args = func_get_args(); // get arguments
		unset($args[0]); // unset varname

		$translationStr = $translations[$str]; // set string

		foreach ($args as $key => $val)
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
		if (self::translationExists($lang))
		{
			$_SESSION['language'] = $lang;
		}
	}

	public static function getTranslationFile($lang, $path=MAIN_PATH."/translation/")
	{
		return
			json_decode(file_get_contents($path . $lang . '.json'));
	}

	private static function getTranslations($lang, $path=MAIN_PATH."/translation/")
	{
		return
			(array) self::getTranslationFile($lang, $path)->translations;
	}

	public static function appendTranslations($path)
	{
		if (self::$putBasicTranslations === false)
		{
			self::$translations = self::getTranslations(self::getLanguage());
			self::$putBasicTranslations = true;
		}

		$lang = self::getLanguage();

		// check if currently language available
		if (self::translationExists($lang, $path))
		{
			self::$translations = array_merge(self::$translations, self::getTranslations($lang, $path));

			return true;
		}

		return false;
	}

	private function translationExists($lang, $path=MAIN_PATH.'/translation/')
	{
		return
			file_exists($path . $lang . '.json') &&
			is_json(file_get_contents($path . $lang . '.json')) &&
			isset(self::getLanguages()[$lang]);
	}

	public function getLanguages($path=MAIN_PATH.'/translation/')
	{
		$languages = array();

		if ($handle = opendir($path))
		{
			while (false !== ($file = readdir($handle)))
			{
				if (is_dir($path . $file))
					continue;

				$content = file_get_contents($path . $file);
				if ($json = is_json($content, true))
				{
					$filename = explode('.', $file);
					$language = str_replace('.'. end($filename), '', $file);
					$languages[$language] = $json->info;
				}
			}

			closedir($handle);
		}

		return $languages;
	}
}

function __() // an alias for translator::translate()
{
	return forward_static_call_array(array('translator', 'translate'), func_get_args());
}
