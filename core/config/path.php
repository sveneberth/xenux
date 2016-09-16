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
	if($key != 'url' && !is_array($value)) #FIXME: the is_array solution is only a hotfix
		$_get_params .= (!empty($_get_params) ? '&' : '') . $key.'='.$value;
}
$_folder_dir = str_replace(array($subdir, @$_GET['url']), '', str_replace('\\', '/', $_SERVER['REQUEST_URI']));
$_folder_dir = substr($_folder_dir, 0, stripos($_folder_dir, '?'));

define('PATH_MAIN',		$dir);
define('PATH_ADMIN',	$dir.'/administration');
define('URL_MAIN',		$url);
define('URL_ADMIN',		$url.'/administration');
define('URL_REQUEST',	$protocol . '://' .$_SERVER['HTTP_HOST'] . $if_port . substr($_SERVER['REQUEST_URI'], 0, stripos($_SERVER['REQUEST_URI'], '?')));
