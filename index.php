<?php
/**
 * @package    Xenux
 *
 * @link       http://www.xenux.bplaced.net
 * @version    2.0
 * @author     Sven Eberth <se@firemail.cc>
 * @copyright  Copyright (c) 2013 - 2017, Sven Eberth.
 * @license    GNU General Public License version 3, see LICENSE
 */

// include Xenux-Loader
include_once(__DIR__ . '/core/xenux-load.php');


if (parse_bool($app->getOption('homepage_offline')) === true && substr(@$_GET['url'], 0, 5) !== 'file/')
{
	ErrorPage::view(503, 'Homepage is in maintenance. Please visit later again.');
}

$app->buildPage();

// close the connection to the database
$XenuxDB->closeConnection();
