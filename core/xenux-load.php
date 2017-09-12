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
if (!file_exists(PATH_MAIN."/xenux-conf.php"))
{
	header("Location: ".URL_MAIN."/install/");
}

/** include Config of Xenux */
include_once(PATH_MAIN.'/xenux-conf.php');

/** include vars */
include_once(PATH_MAIN.'/core/config/vars.php');

/** include helper functions **/
include_once(PATH_MAIN.'/core/inc/functions.php'); // include functions

/** include classes */
include_once(PATH_MAIN.'/core/class/log.php');
include_once(PATH_MAIN.'/core/class/db.php');
include_once(PATH_MAIN.'/core/class/XenuxDB.php');
include_once(PATH_MAIN.'/core/class/app.php');
include_once(PATH_MAIN.'/core/class/translator.php');
include_once(PATH_MAIN.'/core/class/template.php');
include_once(PATH_MAIN.'/core/class/controller.abstract.php');
include_once(PATH_MAIN.'/core/class/user.php');
include_once(PATH_MAIN.'/core/class/form.php');
include_once(PATH_MAIN.'/core/class/mailer.php');
include_once(PATH_MAIN.'/core/class/ErrorPage.php');
include_once(PATH_MAIN.'/core/class/pluginhelper.php');
include_once(PATH_MAIN.'/core/class/file.php');

/** divider for a new page call */
//if (defined('DEBUG') && DEBUG == true) #FIXME if final
	log::writeLog("\r\n\r\n");

/** Build a Database and App Handler */
$XenuxDB	= new XenuxDB;
$app		= new app(isset($_GET['url']) ? $_GET['url'] : '');
