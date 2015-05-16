<?php
function contains($var) {
	$array = func_get_args();
	unset($array[0]);
	return in_array($var, $array); 
}


function escapemail($email, $Arr = array()) {
	if (empty($email)) {
		return false;
	} else {
		$link = "<a " . (isset($Arr['class']) ? 'class="' . $Arr['class'] . '"' : '') . " " . (isset($Arr['id']) ? 'id="' . $Arr['id'] . '"' : '') . " href=\"mailto:$email\">" . (isset($Arr['text']) ? $Arr['text'] : $email) . "</a>";
		
		$emailArr	= str_split($link, 1);
		
		$JS = "";
		
		foreach($emailArr as $val) {
			$JS .= ((strlen($JS)==0) ? '' : ' + ') . "'$val'";
		}
		
		return
			"<script>document.write($JS);</script>".
			"<noscript>" . (isset($Arr['text']) ? "\"" . $Arr['text'] . "\" &lt;" . str_replace(array('@', '.'), array(' [at] ', ' [dot] '), $email) . "&gt;" : str_replace(array('@', '.'), array(' [at] ', ' [dot] '), $email)) . "</noscript>";
	}
}

function request_failed() {
	echo "Bei der Anfrage trat ein Fehler auf, möglicherweise haben sie auf einen fehlerhaften Link geklickt...";
	return false;
}

function whitespace2nbsp($str) {
	if(empty($str) || !isset($str))
		return false;
		
	return str_replace(" ", "&nbsp;", $str);
}

#FIXME: translation
/* function from http://simbo.de/blog/2009/12/pretty-date-relative-zeitangaben-in-worten/ */
function pretty_date( $datestr='' ) {
	$now = time();
	$date = strtotime($datestr);
	$d = $now-$date;
	if( $d < 60 ) {
		$d = round($d);
		return 'vor '.($d==1?'einer Sekunde':$d.' Sekunden');
	}
	$d = $d/60;
	if( $d < 12.5 ) {
		$d = round($d);
		return 'vor '.($d==1?'einer Minute':$d.' Minuten');
	}
	switch( round($d/15) ) {
		case 1:
			return 'vor einer viertel Stunde';
		case 2:
			return 'vor einer halben Stunde';
		case 3:
			return 'vor einer dreiviertel Stunde';
	}
	$d = $d/60;
	if( $d < 6 ) {
		$d = round($d);
		return 'vor '.($d==1?'einer Stunde':$d.' Stunden');
	}
	if( $d < 36 ) {
		// ein Tag beginnt um 5 Uhr morgens
		$day_start = 5;
		if( date('j',($now-$day_start*3600)) == date('j',($date-$day_start*3600)) )
			$r = 'heute';
		elseif( date('j',($now-($day_start+24)*3600)) == date('j',($date-$day_start*3600)) )
			$r = 'gestern';
		else
			$r = 'vorgestern';
		$hour_date = intval(date('G',$date)) + (intval(date('i',$date))/60);
		$hour_now = intval(date('G',$now)) + (intval(date('i',$now))/60);
		if( $hour_date>=22.5 || $hour_date<$day_start ) {
			$r = $r=='gestern' ? 'letzte Nacht' : $r.' Nacht';
		}
		elseif( $hour_date>=$day_start && $hour_date<9 )
			$r .= ' Morgen';
		elseif( $hour_date>=9 && $hour_date<11.5 )
			$r .= ' Vormittag';
		elseif( $hour_date>=11.5 && $hour_date<13.5 )
			$r .= ' Mittag';
		elseif( $hour_date>=13.5 && $hour_date<18 )
			$r .= ' Nachmittag';
		elseif( $hour_date>=18 && $hour_date<22.5 )
			$r .= ' Abend';
		return $r;
	}
	$d = $d/24;
	if( $d < 7 ) {
		$d = round($d);
		return 'vor '.($d==1?'einem Tag':$d.' Tagen');
	}
	$d_weeks = $d/7;
	if( $d_weeks<4 ) {
		$d = round($d_weeks);
		return 'vor '.($d==1?'einer Woche':$d.' Wochen');
	}
	$d = $d/30;
	if( $d<12 ) {
		$d = round($d);
		return 'vor '.($d==1?'einem Monat':$d.' Monaten');
	}
	if( $d<18 )
		return 'vor einem Jahr';
	if( $d<21 )
		return 'vor eineinhalb Jahren';
	$d = round($d/12);
	return 'vor '.$d.' Jahren';
}

function shortstr($str, $size=100, $max=200)
{
	if(strlen($str) > $size)
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

function FileSizeConvert($bytes) {
    $bytes = floatval($bytes);
	if($bytes == 0)
		return "0 B";
	$arBytes = array(
		0 => array(
			"UNIT" => "TiB",
			"VALUE" => pow(1024, 4)
		),
		1 => array(
			"UNIT" => "GiB",
			"VALUE" => pow(1024, 3)
		),
		2 => array(
			"UNIT" => "MiB",
			"VALUE" => pow(1024, 2)
		),
		3 => array(
			"UNIT" => "KiB",
			"VALUE" => 1024
		),
		4 => array(
			"UNIT" => "B",
			"VALUE" => 1
		),
	);

    foreach($arBytes as $arItem) {
        if($bytes >= $arItem["VALUE"]) {
            $result = $bytes / $arItem["VALUE"];
            $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
            break;
        }
    }
    return $result;
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function mysql2date($format, $date)
{
	if(empty($format) || empty($date))
		return false;

	$unixtime = strtotime($date);

	/*
	needet??? standardly in function date integrated
	if('U' == $format)
	return $unixtime;
	*/

	return date($format, $unixtime);
}

function date2mysql($date, $isUnixtime=false)
{
	if(!$isUnixtime)
		$date = strtotime($date);

	return date('Y-m-d H:i:s', $date);
}


function getPreparedLink($id, $title='')
{
	return $id . (!empty($title) ? "-".urlencode($title) : '');
}
function getPageLink($id, $title='')
{
	return URL_MAIN."/page/" . getPreparedLink($id, $title);
}

function inludeExists($file)
{
	if(file_exists($file))
	{
		include_once($file);
		return true;
	}

	return false;
}

function is_json($string, $return_data = false)
{
	$data = json_decode($string);
	return (json_last_error() == JSON_ERROR_NONE) ? ($return_data ? $data : TRUE) : FALSE;
}

function getMenuBarMultiSites($absolutenumber, $start, $amount)
{
	$return = '';

	if($absolutenumber > $amount)
	{
		$return .= "<div class=\"sitenavi\">\n";
		$b = ceil($absolutenumber/$amount);
		for($a = ceil($absolutenumber/$amount); $a > 0; $a--)
		{
			$thissite = $b - $a + 1;
			$limit = $amount * ($b - $a);
			$return .= "\t<a class=\"sitenavi";
			if($limit == $start)
				$return .= " active";
			$return .= "\" href=\"{{SITE_PATH}}?";
			foreach ($_GET as $key => $value)
			{
				if($key != 'url' && $key != 'start' && $key != 'amount')					
					$return .= $key . '=' . $value . '&';
			}
			$return .= "start=$limit&amount=$amount\">$thissite</a>\n";
		}
		$return .= "</div>\n";
	}

	return $return;
}

function parse_bool($value)
{
	return filter_var($value, FILTER_VALIDATE_BOOLEAN);
}

/**
* function full
* @param str: value to check
* opposite of PHP's default function `empty`
* but better support for objects
*/
function full($str)
{
	if(is_object($str))
		$str = (array) $str;

	return !empty($str);
}
?>