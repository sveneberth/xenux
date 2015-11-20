<?php

if ($modulehelper->name('test') === false)
	exit; // module already installed, exit installation

$modulehelper->move
([
	'controller.php' => '#MODULEPATH/controller.php',
	'translation' => '#MODULEPATH/translation',
	'test.txt' => '#MODULEPATH/test.txt'
]);

$modulehelper->add_option('modul_test_installed', 'no-value');

#$modulehelper->databasehelper->add_table 	// maybe over $XenuxDB or about a helper


?>