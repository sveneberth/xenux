<?php
class ErrorPage
{
	public static function view($statuscode = 404, $msg = null, $redirect = null)
	{
		echo self::get($statuscode, $msg, $redirect);
		exit;
	}

	public static function get($statuscode = 404, $msg = null, $redirect = null)
	{
		$template = new template(PATH_MAIN . "/core/template/error.php");
		$template->setVar('errorcode', $statuscode);

		switch ($statuscode)
		{
			case 301:
				header('Location: ' . $redirect);
				// Because we have no output here we dont need to set template vars
				break;
			case 401:
				$template->setVar('status',	"Unauthorized");
				$template->setVar('message', "You don't have permission to access this file. Please login.");
				break;
			case 403:
				$template->setVar('status',	"Forbidden");
				$template->setVar('message', "You don't have permission to access this file.");
				break;
			case 404:
				$template->setVar('status',	"Not Found");
				$template->setVar('message', "The requested resource doesn't exist on this server.");
				break;
			case 405:
				$template->setVar('status',	"Method Not Allowed");
				$template->setVar('message', "The requested was not allowed.");
				break;
			case 418:
				$template->setVar('status',	"I'm a teapot");
				$template->setVar('message', "Any attempt to brew coffee with a teapot should result in the error code \"418 I'm a teapot\". The resulting entity body MAY be short and stout.");
				break;
			case 500:
				$template->setVar('status',	"Internal Server Error");
				$template->setVar('message', "The server encountered an unexpected condition that prevented it from fulfilling the request.");
				break;
			case 501:
				$template->setVar('status',	"Not Implemented");
				$template->setVar('message', "The server does not support the functionality required to fulfill the request.");
				break;
			case 503:
				header("Retry-After: 60");
				$template->setVar('status',	"Service Unavailable");
				$template->setVar('message', "The server is currently unable to handle the request due to a temporary overload or scheduled maintenance, which will likely be alleviated after some delay.");
				break;
		}

		if (!is_null($msg))
		{
			$template->setVar('message', $msg);
		}

		header("HTTP/1.1 {$statuscode} " . $template->getVar('status'));

		return $template->render();
	}
}
