<?php

$modulehelper->name('test');

$modulehelper->move
([
	'controller.php' => '#MODULPATH/controller.php',
	'translation' => '#MODULPATH/translation',
	'test.txt' => '#MODULPATH/test.txt'
]);

$modulehelper->add_option('modul_test_installed', 'no-value');

#$modulehelper->databasehelper->add_table 	// maybe over $XenuxDB or about a helper


?>