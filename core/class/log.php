<?php
class log
{
	public static function setPHPWarning($e) {
		$msg = sprintf("PHP Warning: %s in %s:%d", $e->getMessage(), $e->getFile(), $e->getLine());
		self::writeLog($msg);
	}

	public static function setPHPError($e) {
		$msg = sprintf("PHP Error: %s in %s:%d", $e->getMessage(), $e->getFile(), $e->getLine());
		self::writeLog($msg);
	}

	public static function setDBError($e) {
		$msg = sprintf("DB Error: %s in %s:%d", $e->getMessage(), $e->getFile(), $e->getLine());
		self::writeLog($msg);
	}

	public static function writeLog($msg)
	{
		// if message an array, convert them into a JSON-String
		if(is_array($msg))
		{
			$msg = json_encode($msg);
		}

		$msg = date("[d-M-Y H:i:s]")." ".$msg."\r\n";

		if(!file_exists(PATH_MAIN."/logs/"))
			mkdir(PATH_MAIN."/logs/");

		$handle = fopen(PATH_MAIN."/logs/".date("Y-m-d").".log", "a");
		fwrite($handle, $msg);
		fclose($handle);
	}
}
