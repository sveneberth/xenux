<?php
class mailer
{
	public $debugPath =  '/mails/';
	public $contentType = 'text/html';
	public $charset = 'UTF-8';
	public $lang = 'de';


	private $to = array();
	private $header = array();

	private $from = null;
	private $fromName = null;
	private $replyTo = null;
	private $replyToName = null;
	
	public $subject = null;
	public $body = null;
	private $mail = null;

	public function __construct()
	{
	}
	
	
	public function addAdress($adress, $name = null)
	{
		$this->to[] = [$adress, $this->escapeUTF8($name)];
	}
	
	private function getTo()
	{
		$tmp = array();

		foreach ($this->to as $toArr)
		{
			$tmp[] = 	(!is_null($toArr[0]) ? $toArr[0] : '') .
						(!is_null($toArr[1]) ?
							(is_null($toArr[1]) ? $toArr[0] : ' <' . $toArr[0] . '>') :
							'');
		}

		return implode(', ', $tmp);
	}

	public function setSender($adress, $name=null)
	{
		$this->from		= $adress;
		$this->fromName	= $this->escapeUTF8($name);
	}

	public function setReplyTo($adress, $name=null)
	{
		$this->replyTo		= $adress;
		$this->replyToName	= $this->escapeUTF8($name);
	}

	private function escapeUTF8($str)
	{
		return '=?UTF-8?B?' . base64_encode($str) . '?=';
	}

	public function push_header($value)
	{
		$this->header[] = $value;
	}

	private function buildHeader()
	{
		$this->push_header('MIME-Version: 1.0');
		$this->push_header('Content-type: ' . $this->contentType . '; charset=' . $this->charset);
		$this->push_header('From: ' . (!is_null($this->fromName) ? $this->fromName : '') . (!is_null($this->from) ? (is_null($this->fromName) ? $this->from : ' <' . $this->from . '>') : ''));
		$this->push_header('To: ' . $this->getTo());
		$this->push_header('Reply-To: ' . (!is_null($this->replyToName) ? $this->replyToName : '') . (!is_null($this->replyTo) ? (is_null($this->replyToName) ? $this->replyTo : ' <' . $this->replyTo . '>') : ''));
		$this->push_header('Subject: ' . $this->escapeUTF8($this->subject));
		$this->push_header('X-Mailer: PHP/' . phpversion());
	}

	public function setMessage($value)
	{
		$this->body = $value;
	}

	private function buildMail()
	{
		if(strpos($this->charset, 'UTF') ===  0 || strpos($this->charset, 'ISO') ===  0 )
		{
$this->mail = '<!DOCTYPE html>
<html lang="' . $this->lang . '">
	<head>
		<meta charset="' . $this->charset . '" />
		<title>' . $this->subject . '</title>
	</head>
	<body>
		' . $this->body . '
	</body>
</html>';
		}
		else
		{
			$this->mail = $this->body;
		}

	}

	public function send()
	{
		$this->buildHeader();
		$this->buildMail();

		$result = @mail
		(
			$this->getTo(),
			$this->escapeUTF8($this->subject),
			$this->mail,
			implode("\r\n", $this->header)
		);

		if(DEBUG == true)
		{
			$this->debugMail();
		}

		return $result;
	}

	private function debugMail()
	{
		if(!file_exists(PATH_MAIN . $this->debugPath))
			mkdir(PATH_MAIN . $this->debugPath);

		$txt = implode("\r\n", $this->header) . "\r\n" . $this->mail;

		$handle = fopen(PATH_MAIN . $this->debugPath . 'mail-' . date("Y-m-d-H-i-s") . '.txt', 'w');
		fwrite($handle, $txt);
		fclose($handle);
	}

}
?>