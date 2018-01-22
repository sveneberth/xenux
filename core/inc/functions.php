<?php
/**
 * escapemail
 * @param string $email: the to be escaped email
 * @return string: the escaped email
 */
function escapemail ($email, $props = array())
{
	if (empty($email))
	{
		return null;
	}
	else
	{
		$link = "<a" . (isset($props['class']) ? ' class="' . $props['class'] . '"' : '') . (isset($props['id']) ? ' id="' . $props['id'] . '"' : '') . " href=\"mailto:$email\">" . (isset($props['text']) ? $props['text'] : $email) . "</a>";

		$emailprops	= str_split($link, 1);

		$JS = "";

		foreach ($emailprops as $val)
		{
			$JS .= ((strlen($JS)==0) ? '' : ' + ') . "'$val'";
		}

		return
			"<script>document.write($JS);</script>".
			"<noscript>" . (isset($props['text']) ? "\"" . $props['text'] . "\" &lt;" . str_replace(array('@', '.'), array(' [at] ', ' [dot] '), $email) . "&gt;" : str_replace(array('@', '.'), array(' [at] ', ' [dot] '), $email)) . "</noscript>";
	}
}


/**
 * whitespace2nbsp
 * @param string $str: the haystack
 * @return string: the replaced value
 */
function whitespace2nbsp ($str)
{
	if (empty($str) || !isset($str))
		return false;

	return str_replace(" ", "&nbsp;", $str);
}


/**
 * pretty_date
 * conver a date like 1.1.1970 => x year ago
 * @param string $datestr: a date or time
 * @return string: the pretty date
 * @source: http://simbo.de/blog/2009/12/pretty-date-relative-zeitangaben-in-worten (adapted)
 */
function pretty_date ($datestr = '')
{
	$now = time();
	$date = strtotime($datestr);
	$d = $now - $date;
	if ($d < 60)
	{
		$d = round($d);
		return __('ago', ($d==1 ? __('one second') : $d.' '.__('seconds')));
	}

	$d = $d / 60;

	if ($d < 12.5)
	{
		$d = round($d);
		return __('ago', ($d==1 ? __('one minute') : $d.' '.__('minutes')));
	}

	switch (round($d / 15))
	{
		case 1:
			return __('ago', __('a quarter of an hour'));
		case 2:
			return __('ago', __('half an hour ago'));
		case 3:
			return __('ago', __('three-quarters of an hour ago'));
	}

	$d = $d / 60;

	if ($d < 6)
	{
		$d = round($d);
		return __('ago', ($d==1 ? __('one hour') : $d.' '.__('hours')));
	}

	if ($d < 36)
	{
		// a day starts at 5am
		$day_start = 5;

		if (date('j',($now-$day_start*3600)) == date('j',($date-$day_start*3600)))
			$r = __('today');
		elseif (date('j',($now-($day_start+24)*3600)) == date('j',($date-$day_start*3600)))
			$r = __('yesterday');
		else
			$r = __('two days ago');

		$hour_date = intval(date('G',$date)) + (intval(date('i',$date))/60);
		$hour_now = intval(date('G',$now)) + (intval(date('i',$now))/60);
		if ($hour_date>=22.5 || $hour_date<$day_start)
		{
			$r = $r==__('yesterday') ? __('last night') : $r.' '.__('night');
		}
		elseif ($hour_date>=$day_start && $hour_date<9)
			$r .= ' '.__('morning');
		elseif ($hour_date>=9 && $hour_date<11.5)
			$r .= ' '.__('before noon');
		elseif ($hour_date>=11.5 && $hour_date<13.5)
			$r .= ' '.__('noon');
		elseif ($hour_date>=13.5 && $hour_date<18)
			$r .= ' '.__('afternoon');
		elseif ($hour_date>=18 && $hour_date<22.5)
			$r .= ' '.__('evening');
		return $r;
	}

	$d = $d / 24;
	if ($d < 7)
	{
		$d = round($d);
		return __('ago', ($d==1 ? __('one day') : $d.' '.__('days')));
	}

	$d_weeks = $d / 7;
	if ($d_weeks<4)
	{
		$d = round($d_weeks);
		return __('ago', ($d==1 ? __('one week') : $d.' '.__('weeks')));
	}

	$d = $d / 30;
	if ($d<12)
	{
		$d = round($d);
		return __('ago', ($d==1 ? __('one month') : $d.' '.__('months')));
	}

	if ($d<18)
		return __('ago', __('one year'));

	if ($d<21)
		return __('ago', __('a year and a half'));

	$d = round($d / 12);
	return __('ago', $d.' '.__('years'));
}


/**
 * shortstr
 * @param string $str: the string to be shortened
 * @param string $size: the position on which the function starts with the search of the next whitespace
 * @param string $max: max length of the new string
 * @return string: the shortened string
 */
function shortstr ($str, $size=100, $max=200)
{
	if (strlen($str) > $size)
	{
		$spacePos = strpos($str, " ", $size);
		$spacePos = $spacePos==0 ? strlen($str) : $spacePos;
		$spacePos = $spacePos<=$max ? $spacePos : $max;
		return substr($str, 0, $spacePos) . "...";
	}
	else
	{
		return $str;
	}
}


/**
 * formatBytes
 * @param int $size: size in bytes
 * @param int $precision: amount of decimal places
 * @return string: converted value + unit
 * @source: http://stackoverflow.com/a/2510540/3749896
 */
function formatBytes ($size, $precision = 2)
{
	$base = log($size, 1024);
	$suffixes = array('', 'Ki', 'Mi', 'Gi', 'Ti', 'Pi', 'Ei', 'Zi', 'Yi');

	return ($size == 0 ? 0 : round(pow(1024, $base - floor($base)), $precision)) . ' ' . $suffixes[floor($base)] . 'B';
}


/**
 * generateRandomString
 * @param int $length: lenght of the random string
 * @param string $characters: the allowed characters
 * @return string: random string
 * @source: http://stackoverflow.com/a/2510540/3749896
 */
function generateRandomString ($length = 10,  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


/**
 * mysql2date
 * @param string $format: the request format
 * @param string $date: the date
 * @return string: formatted date
 */
function mysql2date ($format, $date)
{
	if (empty($format) || empty($date))
		return false;

	$unixtime = strtotime($date);

	return date($format, $unixtime);
}


/**
 * date2mysql
 * @param string $date: date or unixtime
 * @param $isUnixtime: is @param $date unixtime (optional)
 * @return string: formatted date
 */
function date2mysql ($date, $isUnixtime=false)
{
	if (!$isUnixtime)
		$date = strtotime($date);

	return date('Y-m-d H:i:s', $date);
}


function getPreparedLink($id, $title='')
{
	return $id . (!empty($title) ? "/".urlencode($title) : '');
}
function getPageLink($id, $title='')
{
	return MAIN_URL."/page/" . getPreparedLink($id, $title);
}


/**
 * inludeExists
 * @param string $file: path of file
 * @return bool: file included ?
 */
function inludeExists ($file)
{
	if (file_exists($file))
	{
		include_once($file);
		return true;
	}

	return false;
}


/**
 * is_json
 * @param string $string: the to be checed (json-)string
 * @param string $return_data: if json, return the checked json string ?
 * @return bool: file included ?
 */
function is_json ($string, $return_data = false)
{
	$data = json_decode($string);
	return (json_last_error() == JSON_ERROR_NONE) ? ($return_data ? $data : true) : false;
}


/**
 * getMenuBarMultiSites
 * this function build the Navigation for eg. search result which are dividet in several sites.
 * @param int $absolutenumber: the amount of entrys
 * @param int $start: the number of the entry of the actually site
 * @param int $amount: the amount of entries per site
 * @return string: the navigationbar as html
 */
function getMenuBarMultiSites ($absolutenumber, $start, $amount)
{
	$return = '';

	if ($absolutenumber > $amount)
	{
		$return .= "<div class=\"sitenavi\">\n";
		$b = ceil($absolutenumber/$amount);
		for ($a = ceil($absolutenumber/$amount); $a > 0; $a--)
		{
			$thissite = $b - $a + 1;
			$limit = $amount * ($b - $a);
			$return .= "\t<a class=\"sitenavi";
			if ($limit == $start)
				$return .= " active";
			$return .= "\" title=\"".__('page')." $thissite\" href=\"{{REQUEST_URL}}?";
			foreach ($_GET as $key => $value)
			{
				if ($key != 'url' && $key != 'start' && $key != 'amount')
					$return .= $key . '=' . $value . '&';
			}
			$return .= "start=$limit&amount=$amount\">$thissite</a>\n";
		}
		$return .= "</div>\n";
	}

	return $return;
}


/**
 * parse_bool
 * @param string $value: the to be parsed string
 * @return bool: true ("1", "true", "on" and "yes") or false ("0", "false", "off" and "no") else null
 */
function parse_bool ($value)
{
	return filter_var($value, FILTER_VALIDATE_BOOLEAN);
}


/**
 * function full
 * opposite of PHP's default function `empty`
 * but better support for objects
 * @param mixed $str: value to check
 * @return bool: is not empty ?
 */
function full($str)
{
	if (is_object($str))
		$str = (array) $str;

	return !empty($str);
}


/**
 * function full_copy
 * copy a folder and subfolders
 * @param string $source: path of the source
 * @param string $target: path of the target
 * @return --
 */
function full_copy ($source, $target)
{
	if (is_dir($source))
	{
		create_folder($target);

		$d = dir($source);

		while (false !== ($entry = $d->read()))
		{
			if ($entry == '.' || $entry == '..')
				continue;

			$Entry = $source . '/' . $entry;
			if (is_dir($Entry))
			{
				full_copy ($Entry, $target . '/' . $entry);
				continue;
			}
			copy ($Entry, $target . '/' . $entry);
		}

		$d->close();
	}
	else
	{
		copy( $source, $target );
	}
}


/**
 * turn_array
 * @param array $m: result of preg_match_all
 * @return array: the turned array
 * @source: http://php.net/manual/de/function.preg-match-all.php#102520
 */
function turn_array ($m)
{
    for ($z = 0;$z < count($m);$z++)
    {
        for ($x = 0;$x < count($m[$z]);$x++)
        {
            $rt[$x][$z] = $m[$z][$x];
        }
    }

    return $rt;
}


/**
 * rrmmdir
 * this fuction delete recursive a dir
 * @param string $dir: path of the to be deleted dir
 * @return bool: successful ?
 */
function rrmdir ($dir)
{
	if (!file_exists($dir))
		return true;

	if (!is_dir($dir))
		return unlink($dir);

	foreach (scandir($dir) as $item)
	{
		if ($item == '.' || $item == '..')
			continue;

		if (!rrmdir($dir . DIRECTORY_SEPARATOR . $item))
			return false;
	}

	return rmdir($dir);
}


/**
 * remove_array_value
 * remove an key by the value
 * @param array $array: the array
 * @param mixed $value: the to be removed value
 */
function remove_array_value(array &$array, &$value)
{
	if (($key = array_search($value, $array)) !== false)
	{
		unset($array[$key]);
		return true;
	}

	return false;
}


/**
 * is_dir_empty
 * check if a folder is empty
 * @param string $dir: path of the folder
 * @return bool: is empty ?
 */
function is_dir_empty($dir)
{
	if (!is_readable($dir))
		return NULL;

	return (count(scandir($dir)) == 2);
}


/**
 * embedSVG
 * embed a SVG for inline useage
 * @param string $file: path to svg file
 * @return string: the svg
 */
function embedSVG($file)
{
	if (!is_readable($file))
	{
		log::debug('Cannot find ' . $file);
		return NULL;
	}

	return file_get_contents($file);
}

/**
 * create_folder
 * @param string $path: path of the new folder
 * @return bool: worked or failed?
 */
function create_folder($path)
{
	if (!file_exists($path)) // create folder, if doesn't exists
		return mkdir($path, 0777, true);
}
