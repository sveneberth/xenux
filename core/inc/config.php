<?php
session_start(); // start php session

define('BASEDIR', substr(__file__,0,-19));

include_once(BASEDIR.'core/inc/arrays.php'); // include arrays
include_once(BASEDIR.'core/inc/functions.php'); // include functions
include_once(BASEDIR.'core/inc/table_definitions.php'); // include table_definitions
include_once(BASEDIR.'mysql.conf'); // include Config for MySQL

if($_SERVER['HTTP_HOST'] == 'localhost') {
	error_reporting(E_ALL);
} else {
	error_reporting(0);
}

$db = new MySQLi(MYSQL_HOST, MYSQL_USER, MYSQL_PW, MYSQL_DB); // connect with database

if(mysqli_connect_errno()) { // if conntection failed
	printf("Can't connect to MySQL Server. Please tell the <a href=\"mailto:developers@xenux\">Devolopers</a> this Errorcode:<br />\"%s\"", mysqli_connect_error());
	exit;
}

$db->query("SET NAMES 'utf8'"); // define database as utf-8

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