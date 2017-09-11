<?php
require_once(PATH_MAIN . '/core/libs/html2text/Html2Text.php');
require_once(PATH_MAIN . '/core/libs/html2text/Html2TextException.php');

#TDOD: add function addAttachment

class mailer
{
	public $debugPath =  '/mails/';
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

	private $boundary = null;

	public function __construct()
	{
		$this->from = 'xenux@' . $_SERVER['SERVER_NAME'];
		$this->fromName = 'XENUX';
		$this->boundary = uniqid('np');
	}


	public function addAdress($adress, $name = null)
	{
		$this->to[] = [$adress, $this->escapeUTF8($name)];
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

	public function setMessage($value)
	{
		$this->body = $value;
	}

	public function setSubject($value)
	{
		$this->subject = trim(str_replace(["\r", "\n"], '', $value));
	}

	public function push_header($value)
	{
		$this->header[] = $value;
	}

	private function getTo()
	{
		$tmp = array();

		foreach ($this->to as $toArr)
		{
			$tmp[] = 	(!$this->is_null($toArr[1]) ? $toArr[1] : '') . // name if set
						(!$this->is_null($toArr[0]) ? // email not empty
							($this->is_null($toArr[1]) ? $toArr[0] : ' <' . $toArr[0] . '>') :
							'');
		}

		return implode(', ', $tmp);
	}

	private function buildHeader()
	{
		$this->push_header('MIME-Version: 1.0');
		$this->push_header('From: ' . (!$this->is_null($this->fromName) ? $this->fromName : '') . (!$this->is_null($this->from) ? ($this->is_null($this->fromName) ? $this->from : ' <' . $this->from . '>') : ''));
		$this->push_header('Reply-To: ' . (!$this->is_null($this->replyToName) ? $this->replyToName : '') . (!$this->is_null($this->replyTo) ? ($this->is_null($this->replyToName) ? $this->replyTo : ' <' . $this->replyTo . '>') : ''));
		$this->push_header('X-Mailer: XENUX ' . XENUX_VERSION . ' MAILER');
		$this->push_header('Content-Type: multipart/alternative;boundary="' . $this->boundary . '"');
	}

	private function buildMail()
	{
		$mailPlain = Html2Text\Html2Text::convert($this->body);
		$mailHTML = '<!DOCTYPE html>
<html lang="' . $this->lang . '">
	<head>
		<meta charset="' . $this->charset . '">
		<title>' . $this->subject . '</title>
	</head>
	<body>
		' . $this->body . '
	</body>
</html>';

		$mail  = "\n\n--" . $this->boundary . "\n";
		$mail .= 'Content-type: text/plain;charset=' . $this->charset . "\n";
		$mail .= "Content-Transfer-Encoding: quoted-printable\n\n";
		$mail .= $mailPlain;
		$mail .= "\n\n--" . $this->boundary . "\n";
		$mail .= 'Content-type: text/html;charset=' . $this->charset . "\n";
		$mail .= "Content-Transfer-Encoding: quoted-printable\n\n";
		$mail .= $mailHTML;
		$mail .= "\n\n--" . $this->boundary . '--';

		$mail = str_replace(["\r\n", "\r", "\n"], "\n", $mail); // Line Endings
		$mail = chunk_split($mail, 76); // Use 76 characters wide lines

		$this->mail = $mail;
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
			implode("\n", $this->header)
		);

		if (defined('DEBUG') && DEBUG == true)
			$this->debugMail();

		return $result;
	}

	private function debugMail()
	{
		create_folder(PATH_MAIN . $this->debugPath);

		$this->push_header('To: ' . $this->getTo());
		$this->push_header('Subject: ' . $this->escapeUTF8($this->subject));

		$txt = implode("\n", $this->header) . "\n" . $this->mail;

		$handle = fopen(PATH_MAIN . $this->debugPath . 'mail-' . date("Y-m-d-H-i-s") . '.txt', 'w');
		fwrite($handle, $txt);
		fclose($handle);
	}

	private function escapeUTF8($str)
	{
		return '=?UTF-8?B?' . base64_encode($str) . '?=';
	}

	private function is_null($str)
	{
		return ($str == $this->escapeUTF8('') || is_null($str));
	}
}
