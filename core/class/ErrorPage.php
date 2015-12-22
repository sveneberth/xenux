<?php
class ErrorPage
{
	public static function view($statuscode = 404)
	{
		echo self::get($statuscode);
		exit;
	}

	public static function get($statuscode = 404)
	{
		$template = new template(PATH_MAIN."/core/template/error.php");
		$template->setVar('errorcode', $statuscode);

		switch ($statuscode)
		{
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
				$template->setVar('message', "The requested file doesn't exist on this server.");
				break;
			case 405:
				$template->setVar('status',	"Method Not Allowed");
				$template->setVar('message', "The requested was not allowed.");
				break;
			case 418:
				$template->setVar('status',	"I'm a teapot");
				$template->setVar('message', "I'm a teapot.");
				break;
			case 500:
				$template->setVar('status',	"Internal server error");
				$template->setVar('message', "The request failed.");
				break;
		}

		header("HTTP/1.1 {$statuscode} ".$template->getVar('status'));

		return $template->render();
	}
}
?>