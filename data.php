<?php
/**
 * @package    Xenux
 *
 * @link       http://www.xenux.bplaced.net
 * @version    1.4.9
 * @author     Sven Eberth <mail@sven-eberth.de.hm>
 * @copyright  Copyright (c) 2013 - 2015, Sven Eberth.
 * @license    GNU General Public License version 3, see LICENSE.txt
 */

session_start(); // start php session

// activate error_reporting for developement on localhost 
if($_SERVER['HTTP_HOST'] == 'localhost')
{
	error_reporting(E_ALL);
}

// check, if config-file is exists
if(!file_exists("mysql.conf.php"))
{
	header("Location: ./install/");
}


header('Content-Type: text/html, charset=utf-8');

// include include-file
include(__DIR__ . "/core/inc.php");


$XenuxDB = new XenuxDB;

for ($i=0; $i < 10; $i++)
{ 
	switch ($_REQUEST['token'])
	{
		case 'sites':
			$XenuxDB->Insert('sites', ['title'=>"w$%&(fnkqwf dw f{$i}", 'text'=>"page content lorem ipsum !§$%&/()bq mqwf köqf q<p>wqfnkwqf</p>wqkf qwfkqw\n\nasflqw \nfqwfjl qwfl qwf lqwf {$i}", 'parent_id'=>118]);
			break;
		case 'event':
			$XenuxDB->Insert('dates', ['name'=>"event (qqwf) {$i}", 'text'=>"page content lorem ipsum !§$%&/()bq mqwf köqf q<p>wqfnkwqf</p>wqkf qwfkqw\n\nasflqw \nfqwfjl qwfl qwf lqwf {$i}", 'date'=>date('Y-m-d H:i:s', strtotime('+3 month'))]);
			break;
	}
}

/*

INSERT INTO XENUX_sites ( `title`,`text`,`parent_id` ) VALUES ( 'w$%&(fnkqwf dw f4','page content lorem ipsum !Â§$%&/()bq mqwf kÃ¶qf q<p>wqfnkwqf</p>wqkf qwfkqw asflqw fqwfjl qwfl qwf lqwf 4', (SELECT id FROM (SELECT * FROM XENUX_sites) as X ORDER BY RAND() LIMIT 1) )

*/


$XenuxDB->closeConnection();
?>