<?php
/**
* Xenux-Loader
* Load all needed components to run the Xenux-System
*/

/** start the PHP Session */
session_start();

/** Set the Header */
header('Content-Type: text/html, charset=utf-8');

/** include path builder */
include_once(__DIR__  .'/config/path.php');

/** enable Error-Reporting, if Debugging Mode enabled. Else use server default settings */
if (defined('DEBUG') && DEBUG == true && $_SERVER['HTTP_HOST'] == 'localhost')
{
	error_reporting(E_ALL);
}

/** go to the Xenux-Installer, if config-file doesn't exists */
if (!file_exists(MAIN_PATH."/xenux-conf.php"))
{
	header("Location: ".MAIN_URL."/install/");
}

/** include Config of Xenux */
include_once(MAIN_PATH.'/xenux-conf.php');

/** include vars */
include_once(MAIN_PATH.'/core/config/vars.php');

/** include helper functions **/
include_once(MAIN_PATH.'/core/inc/functions.php'); // include functions

/** include classes */
include_once(MAIN_PATH.'/core/class/log.php');
include_once(MAIN_PATH.'/core/class/db.php');
include_once(MAIN_PATH.'/core/class/XenuxDB.php');
include_once(MAIN_PATH.'/core/class/app.php');
include_once(MAIN_PATH.'/core/class/translator.php');
include_once(MAIN_PATH.'/core/class/template.php');
include_once(MAIN_PATH.'/core/class/controller.abstract.php');
include_once(MAIN_PATH.'/core/class/user.php');
include_once(MAIN_PATH.'/core/class/form.php');
include_once(MAIN_PATH.'/core/class/mailer.php');
include_once(MAIN_PATH.'/core/class/ErrorPage.php');
include_once(MAIN_PATH.'/core/class/pluginhelper.php');
include_once(MAIN_PATH.'/core/class/file.php');

/** divider for a new page call */
//if (defined('DEBUG') && DEBUG == true) #FIXME if final
	log::writeLog("\r\n\r\n");

/** Build a Database and App Handler */
$XenuxDB	= new XenuxDB;
$app		= new app(isset($_GET['url']) ? $_GET['url'] : '');
