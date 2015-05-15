<?php
/**
 * @package    Xenux
 *
 * @link       http://www.xenux.bplaced.net
 * @version    1.5
 * @author     Sven Eberth <mail@sven-eberth.de.hm>
 * @copyright  Copyright (c) 2013 - 2015, Sven Eberth.
 * @license    GNU General Public License version 3, see LICENSE.txt
 */

// include Xenux-Loader
include_once(dirname(__DIR__) . "/core/xenux-load.php");

$app->buildAdminPage();

// close the connection to the database
$XenuxDB->closeConnection();
?>