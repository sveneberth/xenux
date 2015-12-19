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

$XenuxDB->addTable('test1', [
	'id' => 'int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY',
	'column1' => 'varchar(150) NOT NULL',
	'column2' => 'text NOT NULL',
	'column3' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP'
]);

?>