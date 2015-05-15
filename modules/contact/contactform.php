<h3>Kontaktformular</h3>
<?php
	$formFields = Array
	(
		'name' => Array
		(
			'type' => 'text',
			'value' => null,
			'required' => false,
			'label' => 'Name'
		),
		'email' => Array
		(
			'type' => 'email',
			'value' => null,
			'required' => true,
			'label' => 'E-Mail'
		),
		'message' => Array
		(
			'type' => 'textarea',
			'value' => null,
			'required' => true,
			'label' => 'Nachricht'
		),
		'submit' => Array
		(
			'type' => 'submit',
			'label' => 'Senden'
		)
	);

	$contactform = new form($formFields);

	if($contactform->isSend() && $contactform->isValid())
	{
		$data = $contactform->getInput();

		$header  = 'MIME-Version: 1.0' . "\r\n";
		$header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$header .= 'From: "'.$data['name'].'"<'.$data['email'].'>' . "\r\n";
		$mailtext = '<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Kontaktaufnahme über das Kontaktformular auf ihrer Homepage</title>
	</head>
	<body>
		Hallo!<br />
		Es hat ihnen jemand auf der Homepage <a href="'.URL_MAIN.'">'.URL_MAIN.'</a> eine Nachricht geschickt!<br /><br />
		<p>
			<b>Absender</b>
			<br />
			Name: $post->name
			<br />
			E-Mail: $post->email
		</p>
		<p>
			<b>Nachricht</b><br />
			'.nl2br($data['email']).'
		</p>
		<br /><br />
		<span style="font-family:Verdana;color:#808080;border-top: 1px #808080 solid;">Diese E-Mail wurde mit Xenux generiert und versendet.</span>
	</body>
</html>';
		mail
		(
			$app->getOption('admin_email'),
			"=?UTF-8?Q?" . quoted_printable_encode("Kontaktaufnahme über das Kontaktformular ihrer Homepage") . "?=", 
			$mailtext,
			$header
		)
		or
			die("<p>Die Nachricht konnte nicht versendet werden.</p>");

		echo "<p>Die Nachricht wurde erfolgreich versendet!</p>";
	}
	else
	{
		echo $contactform->getForm();
	}
?>