<h3>Kontaktformular</h3>

<?php
#TODO: translation

$formFields = Array
(
	'name' => Array
	(
		'type'     => 'text',
		'value'    => null,
		'required' => false,
		'label'    => 'Name'
	),
	'email' => Array
	(
		'type'     => 'email',
		'value'    => null,
		'required' => true,
		'label'    => 'E-Mail'
	),
	'message' => Array
	(
		'type'     => 'textarea',
		'value'    => null,
		'required' => true,
		'label'    => 'Nachricht'
	),
	'submit' => Array
	(
		'type'  => 'submit',
		'label' => 'Senden',
		'style' => 'margin-top:1em;display:block;'
	)
);

$contactform = new form($formFields);

if($contactform->isSend() && $contactform->isValid())
{
	$data = $contactform->getInput();

	$contactmailer = new mailer;

	$contactmailer->addAdress( $app->getOption('admin_email') );
	$contactmailer->setSender( $data['email'], $data['name'] );
	$contactmailer->setSubject( 'Kontaktformular von ' . $app->getOption('hp_name') );
	$contactmailer->setMessage( 'Hallo!<br>
Es hat ihnen jemand auf der Homepage <a href="'.URL_MAIN.'">'.URL_MAIN.'</a> eine Nachricht geschickt!<br><br>
<p>
<b>Absender</b><br>
Name: $post->name<br>
E-Mail: $post->email
</p>
<p>
<b>Nachricht</b><br>
' . nl2br($data['message'], false) . '
</p>
<br><br>
<span style="font-family:Verdana;color:#808080;border-top: 1px #808080 solid;">Diese E-Mail wurde mit Xenux generiert und versendet.</span>' );

	if ($contactmailer->send())
		echo '<p>Die Nachricht wurde erfolgreich versendet!</p>';
	else
		echo '<p>Die Nachricht konnte nicht versendet werden.</p>';
}
else
{
	echo $contactform->getForm();
}
