<?php
$protocol    = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
$port        = ($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 403) ? ':'.$_SERVER['SERVER_PORT'] : '';

$dir         = str_replace('core/config', '', str_replace('\\', '/', __DIR__));
$dir         = rtrim($dir, '/');
$doc_root    = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$doc_root    = rtrim($doc_root, '/');
$subdir      = trim(str_replace($doc_root, '', $dir), '/');

$url         = trim($protocol . '://' . $_SERVER['HTTP_HOST'] . $port . '/' . $subdir, '/');
$request_url = trim($protocol . '://' . $_SERVER['HTTP_HOST'] . $port . '/' . trim((
	stripos($_SERVER['REQUEST_URI'], '?') > -1 ? // have get params?
		substr($_SERVER['REQUEST_URI'], 0, stripos($_SERVER['REQUEST_URI'], '?')) : // use string before ?
		$_SERVER['REQUEST_URI'] // use whole string
	), '/'), '/');

$_get_params = '';
foreach ($_GET as $key => $value)
{
	if ($key != 'url')
	{
		if (is_array($value))
		{
			foreach ($value as $k => $v)
			{
				$_get_params .= (!empty($_get_params) ? '&' : '') . sprintf('%s[%s]=%s', $key, $k, $v);
			}
		}
		else
		{
			$_get_params .= (!empty($_get_params) ? '&' : '') . sprintf('%s=%s', $key, $value);
		}
	}
}

$_folder_dir = str_replace(array($subdir, @$_GET['url']), '', str_replace('\\', '/', $_SERVER['REQUEST_URI']));
$_folder_dir = substr($_folder_dir, 0, stripos($_folder_dir, '?'));
$_folder_dir = trim($_folder_dir, '/');

define('MAIN_PATH',   $dir);
define('ADMIN_PATH',  $dir . '/administration');
define('MAIN_URL',    $url);
define('ADMIN_URL',   $url . '/administration');
define('REQUEST_URL', $request_url);

unset($protocol, $port, $dir, $doc_root, $subdir, $url, $request_url, $value, $key);
