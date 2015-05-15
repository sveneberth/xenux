<?php
class ErrorPage
{
	public function __construct($statuscode = 404)
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
				$template->setVar('message', "The request file doesn't exist on this server.");
				break;
			case 500:
				$template->setVar('status',	"Internal server error");
				$template->setVar('message', "The request failed.");
				break;
		}

		header("HTTP/1.0 {$statuscode} ".$template->getVar('status'));

		return $template->render();
	}
}
?>