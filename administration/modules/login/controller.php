<?php
#TODO: translation
class LoginController extends AbstractController
{
	public function __construct($url = null)
	{
		if(isset($url))
			$this->url = $url;
	}

	public function run()
	{
		global $XenuxDB, $app;

		$task = isset($_GET['task']) ? $_GET['task'] : '';

		$action = ($task == 'logout' || $task == 'login' || empty($task) ||
			!in_array($task, ['register', 'forgotusername', 'forgotpassword', 'resetpassword', 'setpassword', 'confirm'])
			) ? 'login' : $task;


		$template = new template(PATH_ADMIN."/template/login.php", ['action'=>$action]);

		$template->setVar("SITE_PATH",  URL_ADMIN.'/login');
		$template->setVar("TEMPLATE_PATH", URL_ADMIN.'/template');
		$template->setVar("homepage_name", $app->getOption('hp_name'));
		$template->setVar("message", '');
		$template->setVar("form", '');

		switch ($action) {
			case 'register':
				$this->registerAction($template);
				$template->setVar("page_name", __('register'));
				break;
			case 'forgotusername':
				$this->forgotusernameAction($template);
				$template->setVar("page_name", __('forgotusername'));
				break;
			case 'forgotpassword':
				$this->forgotpasswordAction($template);
				$template->setVar("page_name", __('forgotpassword'));
				break;
			case 'resetpassword':
				$this->resetpasswordAction($template);
				$template->setVar("page_name", __('resetpassword'));
				break;
			case 'setpassword':
				$this->setPasswordAction($template);
				$template->setVar("page_name", __('setpassword'));
				break;
			case 'confirm':
				$this->confirmAction($template);
				$template->setVar("page_name", __('confirm'));
				break;
			case 'login':
			default:
				$template->setVar("page_name", __('login'));
				$this->loginAction($template, $task);
				break;
		};


		echo $template->render();
		return true;
	}

	private function loginAction(&$template, $task)
	{
		global $app, $XenuxDB;

		if($task == 'logout')
		{
			$app->user->setLogout();
			$template->setIfCondition("logout", true);
		}

		$formFields = array
		(
			'username' => array
			(
				'type' => 'text',
				'required' => true,
				'label' => __('username')
			),
			'password' => array
			(
				'type' => 'password',
				'required' => true,
				'label' => __('password'),
				'min_length' => 0
			),
			'submit' => array
			(
				'type' => 'submit',
				'label' => __('login')
			)
		);

		$loginform = new form($formFields);
		$loginform->disableRequiredInfo();

		if($loginform->isSend() && $loginform->isValid())
		{
			$data = $loginform->getInput();

			$userFound = $app->user->getUserByUsername($data['username']);

			if($userFound)
			{
				if($app->user->checkPassword($data['password']))
				{
					$userInfo = $app->user->userInfo;

					if(parse_bool($userInfo->confirmed) !== true)
					{
						$template->setVar("message",  '<p>' . __('not confirmed') . '</p>');
						return false;
					}

					if(is_null($userInfo->lastlogin_date) && is_null($userInfo->lastlogin_ip) && is_null($userInfo->session_fingerprint))
					{
						// first login
						$token = generateRandomString();

						$XenuxDB->Update('users', [
							'verifykey' => $token,
						],
						[
							'id' => $userInfo->id
						]);

						header('Location: '.URL_ADMIN.'/login?task=firstLogin&id=' . $userInfo->id . '&token=' . $token . (isset($_GET['redirectTo']) ? '&redirectTo='.$_GET['redirectTo'] : ''));
						return false;
					}

					$app->user->setLogin();

					if(isset($_GET['redirectTo']) && !empty($_GET['redirectTo'])):
						header('Location: '.URL_ADMIN.'/'.$_GET['redirectTo']);
					else:
						header('Location: '.URL_ADMIN);
					endif;
				}
				else
				{
					$loginform->setErrorMsg('password wrong');
					$loginform->setFieldInvalid('password');
				}
			}
			else
			{
				$loginform->setErrorMsg('username wrong');
				$loginform->setFieldInvalid('username');
			}
		}

		$template->setVar("form",  $loginform->getForm());
	}

	private function registerAction(&$template)
	{
		global $app, $XenuxDB;

		if (!parse_bool($app->getOption('users_can_register')))
		{
			$template->setVar("message", '<p class="info">Es ist dir nicht erlaubt, dich zu regstieren. Bitte wende dich an den Administrator</p>');
			return false;
		}

		$formFields = array
		(
			'firstname' => array
			(
				'type' => 'text',
				'required' => false,
				'label' => __('firstname')
			),
			'lastname' => array
			(
				'type' => 'text',
				'required' => false,
				'label' => __('lastname')
			),
			'email' => array
			(
				'type' => 'email',
				'required' => true,
				'label' => __('email')
			),
			'username' => array
			(
				'type' => 'text',
				'required' => true,
				'label' => __('username')
			),
			'password' => array
			(
				'type' => 'password',
				'required' => true,
				'label' => __('password'),
				'min_length' => 6
			),
			'passwordAgain' => array
			(
				'type' => 'password',
				'required' => true,
				'label' => __('passwordAgain'),
				'min_length' => 6
			),
			'submit' => array
			(
				'type' => 'submit',
				'label' => __('register')
			)
		);

		$registerform = new form($formFields);
		$registerform->disableRequiredInfo();

		if($registerform->isSend() && $registerform->isValid())
		{
			$data = $registerform->getInput();

			$userFoundByUsername	= $app->user->getUserByUsername($data['username']);
			$userFoundByEmail		= $app->user->getUserByEmail($data['email']);

			if(!$userFoundByUsername) // check if user exits
			{
				if(!$userFoundByEmail) // check if email used
				{
					if($data['password'] == $data['passwordAgain'])
					{
						$token = generateRandomString();

						$return = $XenuxDB->Insert('users', [
							'firstname' => $data['firstname'],
							'lastname' => $data['lastname'],
							'username' => $data['username'],
							'email' => $data['email'],
							'password' => $app->user->createPasswordHash($data['username'], $data['password']),
							'verifykey' => $token
						]);
						if($return !== false)
						{
							// user added successfull
							$confirmlink = URL_ADMIN . '/login/?task=confirm&id=' . $return . '&token=' . $token;

							$mail = new mailer;
							$mail->setSender(XENUX_MAIL);
							$mail->setReplyTo($app->getOption('admin_email'));
							$mail->addAdress($data['email'], $data['username']);
							$mail->subject = 'Registrierung auf "' . $app->getOption('hp_name') . '" bestätigen';
							$mail->body =
'Hallo ' . $data['username'] . '<br>
um deine Registrierung auf ' . $_SERVER['SERVER_NAME'] . ' abzuschließen klicke bitte auf den folgenden Link oder kopiere ihn in die Adressleiste deines Browsers:
<a href="'.$confirmlink.'">'.$confirmlink.'</a><br>
<p>Solltest Du Dich nicht auf ' . $_SERVER['SERVER_NAME'] . ' registriert haben, ignoriere diese Mail bitte.</p>';

							if(!$mail->send())
							{
								$template->setVar("message", '<p>Die Nachricht konnte nicht versendet werden.</p>');
							}
							else
							{
								$template->setVar("message", '<p>Bitte bestätige nun deine Registrierung über den Link in der dir soeben zugesendeten E-Mail.</p>');
							}

							return false;
						}
						else
						{
							$template->setVar("message", 'something went wrong -.-');
						}

					}
					else
					{
						$registerform->setErrorMsg('passwords not equal');
						$registerform->setFieldInvalid('passwordAgain');
					}
				}
				else
				{
					$registerform->setErrorMsg('email exists');
					$registerform->setFieldInvalid('email');
				}
			}
			else
			{

				$registerform->setErrorMsg('username exists');
				$registerform->setFieldInvalid('username');
			}
		}

		$template->setVar("form",  $registerform->getForm());
	}

	private function forgotusernameAction(&$template)
	{
		global $app, $XenuxDB;

		$formFields = array
		(
			'email' => array
			(
				'type' => 'email',
				'required' => true,
				'label' => __('email')
			),
			'submit' => array
			(
				'type' => 'submit',
				'label' => __('sendUsername')
			)
		);

		$forgotusernameform = new form($formFields);
		$forgotusernameform->disableRequiredInfo();

		if($forgotusernameform->isSend() && $forgotusernameform->isValid())
		{
			$data = $forgotusernameform->getInput();

			$userFoundByEmail		= $app->user->getUserByEmail($data['email']);

			if($userFoundByEmail) // check if user exists
			{
				$userinfo = $app->user->userInfo;

				$mail = new mailer;
				$mail->setSender(XENUX_MAIL);
				$mail->setReplyTo($app->getOption('admin_email'));
				$mail->addAdress($userinfo->email, $userinfo->username);
				$mail->subject = 'Benutzername vergessen';
				$mail->body =
'Hallo!<br>
Dein Benutzername für <a href="' . URL_MAIN . '">' . URL_MAIN . '</a> lautet: ' . $userinfo->username . '
<p>Solltest Du die Zusendung des Benuzernamens nicht angefordert haben, ignoriere diese Mail bitte.</p>';

				if(!$mail->send())
				{
					$template->setVar("message", '<p>Die Nachricht konnte nicht versendet werden.</p>');
				}
				else
				{
					$template->setVar("message",  '<p>Dein Benutzername wurde dir soeben per E-Mail zugeschickt!</p>');
				}
			}
			else
			{
				$template->setVar("message",  '<p>Es konnte keinem Account die E-Mail-Adresse <i>'.$data['email'].'</i> zugeordnet werden!</p>');
			}
		}

		$template->setVar("form",  $forgotusernameform->getForm());
	}

	private function forgotpasswordAction(&$template)
	{
		global $app, $XenuxDB;

		$formFields = array
		(
			'username' => array
			(
				'type' => 'text',
				'required' => true,
				'label' => __('username')
			),
			'submit' => array
			(
				'type' => 'submit',
				'label' => __('resetPassword')
			)
		);

		$forgotpasswordform = new form($formFields);
		$forgotpasswordform->disableRequiredInfo();

		if($forgotpasswordform->isSend() && $forgotpasswordform->isValid())
		{
			$data = $forgotpasswordform->getInput();

			$userFoundByUsername = $app->user->getUserByUsername($data['username']);

			if($userFoundByUsername) // check if user exists
			{
				$userinfo = $app->user->userInfo;
				if (empty($userinfo->password)) // user has not set his password
				{
					$template->setVar("message", 'please set your first password');
					return false;
				}
				$token = generateRandomString();

				$result = $XenuxDB->Update("users", [
					'verifykey' => $token
				],
				[
					'id' => $userinfo->id
				]);
				if(!$result)
				{
					$template->setVar("message", 'something went wrong -.-');
					return false;
				}

				$url = URL_ADMIN . '/login?task=resetpassword&amp;id=' . $userinfo->id . '&amp;token=' . $token;

				$mail = new mailer;
				$mail->setSender(XENUX_MAIL);
				$mail->setReplyTo($app->getOption('admin_email'));
				$mail->addAdress($userinfo->email, $userinfo->username);
				$mail->subject = 'Passwort vergessen';
				$mail->body =
'Hallo ' . $userinfo->username . '!<br>
<p>Du hast am ' . date("d.m.Y") . ' um ' . date("H:i") . ' von der IP-Adresse ' . $_SERVER['REMOTE_ADDR'] . ' eine Passwortrücksetzung angefordert. Das Passwort kann unter der URL<br>
<a href="' . $url . '">' . $url . '</a><br>
		zurückgesetzt werden.</p>
<p>Solltest Du die Zurücksetzung des Passworts nicht angefordert haben, ignoriere diese Mail bitte.</p>';

				if(!$mail->send())
				{
					$template->setVar("message", '<p>Die Nachricht konnte nicht versendet werden.</p>');
				}
				else
				{
					$template->setVar("message",  '<p>Bitte öffne nun in der dir soeben zu gesendeten E-Mail den Link, um das Passwort zurückzusetzen!</p>');
				}

				$template->setVar("message",  '<p>Bitte öffne nun in der dir soeben zu gesendeten E-Mail den Link, um das Passwort zurückzusetzen!</p>');
			}
			else
			{
				$template->setVar("message", '<p>Es konnte keinem Account der Benutzername <i>' . $data['username'] . '</i> zugeordnet werden!</p>');
			}
		}

		$template->setVar("form",  $forgotpasswordform->getForm());
	}

	private function resetpasswordAction(&$template)
	{
		global $app, $XenuxDB;

		if(!isset($_GET['id']) || !isset($_GET['token']))
		{
			$template->setVar("message", "<p>Es trat ein Fehler auf... Stellen sie sicher, das der Link stimmt und aktuell ist!<p>");
			return false;
		}

		$userfound = $XenuxDB->getEntry('users', [
			'columns'=> [
				'id'
			],
			'where'=> [
				'AND' => [
					'id' => $_GET['id'],
					'verifykey' => $_GET['token']
				]
			]
		]);
		if($userfound)
		{
			$userinfo = $app->user->getUserInfo($userfound->id);

			$formFields = array
			(
				'password' => array
				(
					'type' => 'password',
					'required' => true,
					'label' => __('password'),
					'min_length' => 6
				),
				'passwordAgain' => array
				(
					'type' => 'password',
					'required' => true,
					'label' => __('passwordAgain'),
					'min_length' => 6
				),
				'submit' => array
				(
					'type' => 'submit',
					'label' => __('resetPassword')
				)
			);

			$forgotpasswordform = new form($formFields);
			$forgotpasswordform->disableRequiredInfo();

			if($forgotpasswordform->isSend() && $forgotpasswordform->isValid())
			{
				$data = $forgotpasswordform->getInput();

				if($data['password'] == $data['passwordAgain'])
				{
					$return = $XenuxDB->Update('users', [
						'verifykey' => NULL,
						'password' => $app->user->createPasswordHash($userinfo->username, $data['password'])
					],
					[
						'id' => $userinfo->id
					]);

					if($return)
					{
						$template->setVar("message", "<p>Das Passwort wurde erfolgreich zurückgesetzt!</p>");
						$template->setVar("form",  '');
						return false;
					}
					else
					{
						$template->setVar("message", "<p>something went wrong -.-</p>");
					}
				}
				else
				{
					$template->setVar("message", "<p>Die eingegeben Passwörter sind nicht identisch!<p>");
				}
			}

			$template->setVar("form",  $forgotpasswordform->getForm());
		}
		else
		{
			$template->setVar("message", "<p>Es trat ein Fehler auf... Stellen sie sicher, das der Link stimmt und aktuell ist!<p>");
		}
	}

	private function setPasswordAction(&$template)
	{
		global $app, $XenuxDB;

		if(!isset($_GET['id']) || !isset($_GET['token']))
		{
			$template->setVar("message", "<p>Es trat ein Fehler auf... Stellen sie sicher, das der Link stimmt und aktuell ist!<p>");
			return false;
		}

		$userfound = $XenuxDB->getEntry('users', [
			'columns'=> [
				'id'
			],
			'where'=> [
				'AND' => [
					'id' => $_GET['id'],
					'verifykey' => $_GET['token']
				]
			]
		]);
		if($userfound)
		{
			$userinfo = $app->user->getUserInfo($userfound->id);

			$formFields = array
			(
				'password' => array
				(
					'type' => 'password',
					'required' => true,
					'label' => __('password'),
					'min_length' => 6
				),
				'passwordAgain' => array
				(
					'type' => 'password',
					'required' => true,
					'label' => __('passwordAgain'),
					'min_length' => 6
				),
				'submit' => array
				(
					'type' => 'submit',
					'label' => __('resetPassword')
				)
			);

			$forgotpasswordform = new form($formFields);
			$forgotpasswordform->disableRequiredInfo();

			if($forgotpasswordform->isSend() && $forgotpasswordform->isValid())
			{
				$data = $forgotpasswordform->getInput();

				if($data['password'] == $data['passwordAgain'])
				{
					$return = $XenuxDB->Update('users', [
						'verifykey' => NULL,
						'password' => $app->user->createPasswordHash($userinfo->username, $data['password'])
					],
					[
						'id' => $userinfo->id
					]);

					if($return)
					{
						$mail = new mailer;
						$mail->setSender(XENUX_MAIL);
						$mail->setReplyTo($app->getOption('admin_email'));
						$mail->addAdress($userinfo->email, $userinfo->firstname . $userinfo->lastname);
						$mail->setSubject('Passort gespeichert');
						$mail->setMessage('Hallo' . $username . '!<br>
	<p>Dein Passwort für deinen Benutzeraccount auf <a href="' . URL_MAIN . '">' . URL_MAIN . '</a> wurde erogreich gespeichert.</p>');
						$mail->send();

						$template->setVar("message", "<p>Das Passwort wurde erfolgreich gespeichert!</p>");
						$app->user->setLogin();

						header('Location: ' . URL_ADMIN . (isset($_GET['redirectTo']) ? $_GET['redirectTo'] : ''));
					}
					else
					{
						$template->setVar("message", "<p>something went wrong -.-</p>");
					}
				}
				else
				{
					$template->setVar("message", "<p>Die eingegeben Passwörter sind nicht identisch!<p>");
				}
			}

			$template->setVar("form",  $forgotpasswordform->getForm());
		}
		else
		{
			$template->setVar("message", "<p>Es trat ein Fehler auf... Stellen sie sicher, das der Link stimmt und aktuell ist!<p>");
		}
	}

	private function confirmAction(&$template)
	{
		global $app, $XenuxDB;

		if(!isset($_GET['id']) || !isset($_GET['token']))
		{
			$template->setVar("message", "<p>Es trat ein Fehler auf... Stellen sie sicher, das der Link stimmt und aktuell ist!<p>");
			return false;
		}

		$user = $XenuxDB->getEntry('users', [
			'columns'=> [
				'id'
			],
			'where'=> [
				'AND' => [
					'id' => $_GET['id'],
					'verifykey' => $_GET['token']
				]
			]
		]);
		if($user)
		{
			$app->user->userInfo->id = $user->id;

			$return = $XenuxDB->Update('users', [
				'verifykey' => NULL,
				'confirmed' => true
			],
			[
				'id' => $user->id
			]);

			if($return)
			{
				$template->setIfCondition("confirmSucessful", true);
				$app->user->setLogin();
				header('Refresh:5; url=' . URL_ADMIN, true, 303);
			}
		}
		else
		{
			$template->setVar("message", "<p>Es trat ein Fehler auf... Stellen sie sicher, das der Link stimmt und aktuell ist!<p>");
		}
	}
}
