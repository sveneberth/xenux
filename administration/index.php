<?php
/**
 * @package    Xenux
 *
 * @link       http://www.xenux.bplaced.net
 * @version    2.0
 * @author     Sven Eberth <mail@sveneberth.de>
 * @copyright  Copyright (c) 2013 - 2018, Sven Eberth.
 * @license    GNU General Public License version 3, see LICENSE
 */

// include Xenux-Loader
include_once(dirname(__DIR__) . "/core/xenux-load.php");

$app->buildAdminPage();

// close the connection to the database
$XenuxDB->closeConnection();
