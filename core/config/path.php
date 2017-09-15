<?php
$protocol	= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
$if_port	= ($_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '443') ? ':'.$_SERVER['SERVER_PORT'] : '';

$dir		= str_replace('core/config', '', str_replace('\\', '/', __DIR__));
$dir		= rtrim($dir, '/');
$doc_root	= str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);

$subdir		= str_replace($doc_root, '', $dir);

$url		= $protocol . '://' .$_SERVER['HTTP_HOST'] . $if_port . $subdir;

$_get_params = '';
foreach ($_GET as $key => $value)
{
	if ($key != 'url' && !is_array($value)) #FIXME: the is_array solution is only a hotfix
		$_get_params .= (!empty($_get_params) ? '&' : '') . $key.'='.$value;
}
$_folder_dir = str_replace(array($subdir, @$_GET['url']), '', str_replace('\\', '/', $_SERVER['REQUEST_URI']));
$_folder_dir = substr($_folder_dir, 0, stripos($_folder_dir, '?'));

define('MAIN_PATH',		$dir);
define('ADMIN_PATH',	$dir.'/administration');
define('MAIN_URL',		$url);
define('ADMIN_URL',		$url.'/administration');
define('REQUEST_URL',	$protocol . '://' .$_SERVER['HTTP_HOST'] . $if_port . substr($_SERVER['REQUEST_URI'], 0, stripos($_SERVER['REQUEST_URI'], '?')));
