<?php
function contains($var) {
	$array = func_get_args();
	unset($array[0]);
	return in_array($var, $array); 
}

function maxlines($str, $num=10) {
    $lines = explode("\n", $str);
    $firsts = array_slice($lines, 0, $num);
    return implode("\n", $firsts);
}

function escapemail($wert) {
	$text = "";
	if (empty($wert)) {
		return;
	} else {
		$lenght = strlen($wert);
		for ($i = 0; $i < $lenght; $i++) {
				$num = "000";
				$char = substr($wert,$i, 1);
				if ($char == "A") {
					$num = "065";
				}
				if ($char == "a") {
					$num = "097";
				}
				if ($char == "B") {
					$num = "066";
				}
				if ($char == "b") {
					$num = "098";
				}
				if ($char == "C") {
					$num = "067";
				}
				if ($char == "c") {
					$num = "099";
				}
				if ($char == "D") {
					$num = "068";
				}
				if ($char == "d") {
					$num = "100";
				}
				if ($char == "E") {
					$num = "069";
				}
				if ($char == "e") {
					$num = "101";
				}
				if ($char == "F") {
					$num = "070";
				}
				if ($char == "f") {
					$num = "102";
				}
				if ($char == "G") {
					$num = "071";
				}
				if ($char == "g") {
					$num = "103";
				}
				if ($char == "H") {
					$num = "072";
				}
				if ($char == "h") {
					$num = "104";
				}
				if ($char == "I") {
					$num = "073";
				}
				if ($char == "i") {
					$num = "105";
				}
				if ($char == "J") {
					$num = "074";
				}
				if ($char == "j") {
					$num = "106";
				}
				if ($char == "K") {
					$num = "075";
				}
				if ($char == "k") {
					$num = "107";
				}
				if ($char == "L") {
					$num = "076";
				}
				if ($char == "l") {
					$num = "108";
				}
				if ($char == "M") {
					$num = "077";
				}
				if ($char == "m") {
					$num = "109";
				}
				if ($char == "N") {
					$num = "078";
				}
				if ($char == "n") {
					$num = "110";
				}
				if ($char == "O") {
					$num = "079";
				}
				if ($char == "o") {
					$num = "111";
				}
				if ($char == "P") {
					$num = "080";
				}
				if ($char == "p") {
					$num = "112";
				}
				if ($char == "Q") {
					$num = "081";
				}
				if ($char == "q") {
					$num = "113";
				}
				if ($char == "R") {
					$num = "082";
				}
				if ($char == "r") {
					$num = "114";
				}
				if ($char == "S") {
					$num = "083";
				}
				if ($char == "s") {
					$num = "115";
				}
				if ($char == "T") {
					$num = "084";
				}
				if ($char == "t") {
					$num = "116";
				}
				if ($char == "U") {
					$num = "085";
				}
				if ($char == "u") {
					$num = "117";
				}
				if ($char == "V") {
					$num = "086";
				}
				if ($char == "v") {
					$num = "118";
				}
				if ($char == "W") {
					$num = "087";
				}
				if ($char == "w") {
					$num = "119";
				}
				if ($char == "X") {
					$num = "088";
				}
				if ($char == "x") {
					$num = "120";
				}
				if ($char == "Y") {
					$num = "089";
				}
				if ($char == "y") {
					$num = "121";
				}
				if ($char == "Z") {
					$num = "090";
				}
				if ($char == "z") {
					$num = "122";
				}
				if ($char == "0") {
					$num = "048";
				}
				if ($char == "1") {
					$num = "049";
				}
				if ($char == "2") {
					$num = "050";
				}
				if ($char == "3") {
					$num = "051";
				}
				if ($char == "4") {
					$num = "052";
				}
				if ($char == "5") {
					$num = "053";
				}
				if ($char == "6") {
					$num = "054";
				}
				if ($char == "7") {
					$num = "055";
				}
				if ($char == "8") {
					$num = "056";
				}
				if ($char == "9") {
					$num = "057";
				}
				if ($char == "&") {
					$num = "038";
				}
				if ($char == " ") {
					$num = "032";
				}
				if ($char == "_") {
					$num = "095";
				}
				if ($char == "-") {
					$num = "045";
				}
				if ($char == "@") {
					$num = "064";
				}
				if ($char == ".") {
					$num = "046";
				}
				if ($char == ":") {
					$num = "058";
				}
				if($num == "000") {
					$text .= $char;
				} else {
					$text .= "&#".$num.";";
				}
			}
		return '<a href="&#109;&#097;&#105;&#108;&#116;&#111;&#058;'.$text.'">'.$text.'</a>';
	}
}

function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
	$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
	$rgbArray = array();
	if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
		$colorVal = hexdec($hexStr);
		$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
		$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
		$rgbArray['blue'] = 0xFF & $colorVal;
	} elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
		$rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
		$rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
		$rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
	} else {
		return false; //Invalid hex color code
	}
	return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
}

function lighter($red = 0, $green = 0, $blue = 0) {
	if(255-$red > 50) {
		$red = $red+50;
	}
	if(255-$green > 50) {
		$green = $green+50;
	}
	if(255-$blue > 50) {
		$blue = $blue+50;
	}
	return "rgb($red,$green,$blue)";
}

function logger($value) {
	echo "<script>console.log('".$value."');</script>";
};

function request_failed() {
	echo "Bei der Anfrage trat ein Fehler auf, möglicherweise haben sie auf einen fehlerhaften Link geklickt...";
	return false;
}

function nbsp($str) {
	if(empty($str) || !isset($str))
		return false;
		
	return str_replace(" ", "&nbsp;", $str);
}

/* fucntion from http://simbo.de/blog/2009/12/pretty-date-relative-zeitangaben-in-worten/ */
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

function shortstr($str, $size = 100) {
	if(strlen($str) > $size) {
		return substr($str, 0, strpos($str, " ", $size))." ...";
	} else {
		return $str;
	}
}

?>