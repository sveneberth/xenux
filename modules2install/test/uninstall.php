<?php
$modulehelper->remove
([
	'#MODULEPATH',
	'#MODULEADMINPATH'
]);

$modulehelper->remove_option('module_test_installed');

$XenuxDB->removeTable('test1');
?>