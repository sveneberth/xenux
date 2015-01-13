<?php
session_start(); // start php session

if($_SERVER['HTTP_HOST'] == 'localhost') {
	error_reporting(E_ALL);
}

$url = 'http';
if (@$_SERVER["HTTPS"] == "on") $url .= "s";
$url .= "://" . $_SERVER["SERVER_NAME"];
if ($_SERVER["SERVER_PORT"] != "80") {
	$url .= ":" . $_SERVER["SERVER_PORT"];
}
$url .= $_SERVER["SCRIPT_NAME"] != '/' ? ( !isset($index) ? dirname($_SERVER["SCRIPT_NAME"]) : ( @$index == 'frontend' ? dirname($_SERVER["SCRIPT_NAME"]) : dirname(dirname($_SERVER["SCRIPT_NAME"])))) : '';


define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
define('XENUX_URL', $url);

include_once(BASEDIR.'core/inc/arrays.php'); // include arrays
include_once(BASEDIR.'core/inc/functions.php'); // include functions
include_once(BASEDIR.'mysql.conf'); // include Config for MySQL


$db = new MySQLi(MYSQL_HOST, MYSQL_USER, MYSQL_PW, MYSQL_DB); // connect with database

if(mysqli_connect_errno()) { // if conntection failed
	printf("Can't connect to MySQL Server. Please tell the administrator this Errorcode:<br />\"%s\"", mysqli_connect_error());
	exit;
}

$db->query("SET NAMES 'utf8';"); // define database as utf-8

if(isset($_SESSION['login_xenux'])) {
	if($_SESSION['login_xenux'] == 1) {
		$result = $db->query("SELECT * FROM XENUX_users WHERE id = {$_SESSION['userid_xenux']};");
		$login = $result->fetch_object(); // set login with userdata
	}
}

$get = new stdClass();
foreach($_GET as $key => $val) {
	if(!is_array($val)) {
		$get->$key = $db->real_escape_string($val);
	}
}
$post = new stdClass();
foreach($_POST as $key => $val) {
	if(!is_array($val)) {
		$post->$key = $db->real_escape_string($val);
	}
}

$main = new stdClass;
$result = $db->query("SELECT * FROM XENUX_main;");
while($row = $result->fetch_object()) {
	$main->{$row->name} = $row->value;
}
?>