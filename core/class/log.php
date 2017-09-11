<?php
class log
{
	public static function setPHPWarning($e) {
		$msg = sprintf("[PHP WARNING] %s in %s:%d", $e->getMessage(), $e->getFile(), $e->getLine());
		self::writeLog($msg);
	}

	public static function setPHPError($e) {
		$msg = sprintf("[PHP ERROR] %s in %s:%d", $e->getMessage(), $e->getFile(), $e->getLine());
		self::writeLog($msg);
	}

	public static function setDBError($e) {
		$msg = sprintf("[DB ERROR] %s in %s:%d", $e->getMessage(), $e->getFile(), $e->getLine());
		self::writeLog($msg);
	}

	public static function debug($str) {
		if (!(defined('DEBUG') && DEBUG == true)) // debug only in debug mode
			return false;

		$msg = sprintf("[DEBUG] %s", $str);
		self::writeLog($msg);
	}

	public static function writeLog($msg)
	{
		// if message an array, convert them into a JSON-String
		if (is_array($msg) || is_object($msg))
		{
			$msg = json_encode($msg);
		}

		$msg = date('[d-M-Y H:i:s]') . ' ' . $msg . "\r\n";

		create_folder(PATH_MAIN . '/logs/');

		$handle = fopen(PATH_MAIN . '/logs/' . date('Y-m-d') . '.log', 'a');
		fwrite($handle, $msg);
		fclose($handle);
	}
}
