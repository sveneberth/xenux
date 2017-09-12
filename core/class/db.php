<?php
class db
{
	private static $db = null;

	public static function getConnection()
	{
		if (self::$db == null)
		{
			try {
				self::$db = new MySQLi(MYSQL_HOST, MYSQL_USER, MYSQL_PW, MYSQL_DB); // connect with database

				if (self::$db->connect_errno) { // if connection failed
					throw new Exception("Failed to connect to MySQL: (" . self::$db->connect_errno . ") " . self::$db->connect_error);
				}

				self::$db->query("SET NAMES 'utf8';"); // define database as utf-8

				log::debug("### connection to database successful ###");
			}
			catch (Exception $e)
			{
				log::setDBError($e);
				die($e->getMessage());
			}
		}

		return self::$db;
	}
}
