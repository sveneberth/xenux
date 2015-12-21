<?php
$modulehelper->name('test');

$modulehelper->remove
([
	'#MODULEPATH'
]);

$modulehelper->remove_option('module_test_installed');

$XenuxDB->removeTable('test1');
?>