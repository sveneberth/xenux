<?php
/**
 * @package    Xenux
 *
 * @link       http://www.xenux.bplaced.net
 * @version    2.0
 * @author     Sven Eberth <mail@sven-eberth.de.hm>
 * @copyright  Copyright (c) 2013 - 2017, Sven Eberth.
 * @license    GNU General Public License version 3, see LICENSE.txt
 */

// include Xenux-Loader
include_once(__DIR__ . "/core/xenux-load.php");

if(parse_bool($app->getOption('homepage_offline')) === true && !preg_match('/file/', @$_GET['url']))
{
	die("<p>Homepage is in maintenance. Please visit later again.</p>");
}

$app->buildPage();

// close the connection to the database
$XenuxDB->closeConnection();
