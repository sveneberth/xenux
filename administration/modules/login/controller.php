<?php
class LoginController extends AbstractController
{
	public function run()
	{
		global $XenuxDB, $app;

		// append translations
		translator::appendTranslations(ADMIN_PATH . '/modules/' . $this->modulename . '/translation/');

		$task = isset($_GET['task']) ? $_GET['task'] : '';

		$action = ($task == 'logout' || $task == 'login' || empty($task) ||
			!in_array($task, ['register', 'forgotusername', 'forgotpassword', 'resetpassword', 'setpassword', 'confirm'])
			) ? 'login' : $task;


		$template = new template(ADMIN_PATH . '/template/login.php', ['action'=>$action]);

		$template->setVar('TEMPLATE_URL', ADMIN_URL . '/template');
		$template->setVar('homepage_name', $app->getOption('hp_name'));
		$template->setVar('message', '');
		$template->setVar('form', '');

		switch ($action) {
			case 'register':
				$this->registerAction($template);
				$template->setVar('page_name', __('register'));
				break;
			case 'forgotusername':
				$this->forgotusernameAction($template);
				$template->setVar('page_name', __('forgotUsername'));
				break;
			case 'forgotpassword':
				$this->forgotpasswordAction($template);
				$template->setVar('page_name', __('forgotPassword'));
				break;
			case 'resetpassword':
				$this->resetpasswordAction($template);
				$template->setVar('page_name', __('resetpassword'));
				break;
			case 'setpassword':
				$this->setPasswordAction($template);
				$template->setVar('page_name', __('setpassword'));
				break;
			case 'confirm':
				$this->confirmAction($template);
				$template->setVar('page_name', __('confirm'));
				break;
			case 'login':
			default:
				$template->setVar('page_name', __('login'));
				$this->loginAction($template, $task);
				break;
		};


		echo $template->render();
		return true;
	}

	private function loginAction(&$template, $task)
	{
		global $app, $XenuxDB;

		if ($app->user->isLogin() && $task != 'logout')
		{
			header('Location: ' . ADMIN_URL, true, 303);
		}

		if ($task == 'logout')
		{
			$app->user->setLogout();
			$template->setIfCondition('logout', true);
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

		if ($loginform->isSend() && $loginform->isValid())
		{
			$data = $loginform->getInput();

			$userFound = $app->user->getUserByUsername($data['username']);

			if ($userFound)
			{
				if ($app->user->checkPassword($data['password']))
				{
					$userInfo = $app->user->userInfo;

					if (parse_bool($userInfo->confirmed) !== true)
					{
						$template->setVar('message', '<p>' . __('not confirmed') . '</p>');
						return false;
					}

					if (is_null($userInfo->lastlogin_date) && is_null($userInfo->lastlogin_ip) && is_null($userInfo->session_fingerprint))
					{
						// first login
						$token = generateRandomString();

						$XenuxDB->Update('users', [
							'verifykey' => $token,
						],
						[
							'id' => $userInfo->id
						]);

						header('Location: ' . ADMIN_URL . '/login?task=firstLogin&id=' . $userInfo->id . '&token=' . $token . (isset($_GET['redirectTo']) ? '&redirectTo=' . $_GET['redirectTo'] : ''));
						return false;
					}

					$app->user->setLogin();

					if (isset($_GET['redirectTo']) && !empty($_GET['redirectTo'])):
						header('Location: ' . ADMIN_URL . '/' . $_GET['redirectTo']);
					else:
						header('Location: ' . ADMIN_URL);
					endif;
				}
				else
				{
					$loginform->setErrorMsg(__('password wrong'));
					$loginform->setFieldInvalid('password');
				}
			}
			else
			{
				$loginform->setErrorMsg(__('username wrong'));
				$loginform->setFieldInvalid('username');
			}
		}

		$template->setVar('form',  $loginform->getForm());
	}

	private function registerAction(&$template)
	{
		global $app, $XenuxDB;

		if (!parse_bool($app->getOption('users_can_register')))
		{
			$template->setVar('message', '<p class="info">' . __('registrationClosed') . '</p>');
			return false;
		}

		$formFields = array
		(
			'firstname' => array
			(
				'type'     => 'text',
				'required' => false,
				'label'    => __('firstname')
			),
			'lastname' => array
			(
				'type'     => 'text',
				'required' => false,
				'label'    => __('lastname')
			),
			'email' => array
			(
				'type'     => 'email',
				'required' => true,
				'label'    => __('email')
			),
			'username' => array
			(
				'type'     => 'text',
				'required' => true,
				'label'    => __('username')
			),
			'password' => array
			(
				'type'     => 'password',
				'required' => true,
				'label'    => __('password')
			),
			'passwordAgain' => array
			(
				'type'     => 'password',
				'required' => true,
				'label'    => __('passwordAgain')
			),
			'submit' => array
			(
				'type'  => 'submit',
				'label' => __('register')
			)
		);

		$registerform = new form($formFields);
		$registerform->disableRequiredInfo();

		if ($registerform->isSend() && $registerform->isValid())
		{
			$data = $registerform->getInput();

			$userFoundByUsername	= $app->user->getUserByUsername($data['username']);
			$userFoundByEmail		= $app->user->getUserByEmail($data['email']);

			if (!$userFoundByUsername) // check if user exits
			{
				if (!$userFoundByEmail) // check if email used
				{
					if ($data['password'] == $data['passwordAgain'])
					{
						$token = generateRandomString();

						$return = $XenuxDB->Insert('users', [
							'firstname' => $data['firstname'],
							'lastname'  => $data['lastname'],
							'username'  => $data['username'],
							'email'     => $data['email'],
							'password'  => $app->user->createPasswordHash($data['password']),
							'verifykey' => $token
						]);
						if ($return !== false)
						{
							// user added successfull
							$confirmlink = ADMIN_URL . '/login/?task=confirm&amp;id=' . $return . '&amp;token=' . $token;

							$mail = new mailer;
							$mail->setSender(XENUX_MAIL);
							$mail->setReplyTo($app->getOption('admin_email'));
							$mail->addAdress($data['email'], $data['username']);
							$mail->setSubject(__('confirm registration on', $app->getOption('hp_name')));
							$mail->setMessage(
								'<p>' . __('helloUser', $data['username']) . '!</p>' .
								'<p>' . __('open link to confirm registration', MAIN_URL) . '<br>' .
								'<a href="' . $confirmlink . '">' . $confirmlink . '</a></p>' .
								'<p>' . __('not registered by self', MAIN_URL) . '</p>'
							);

							if (!$mail->send())
							{
								$template->setVar('message', '<p>' . __('message couldnt sent') . '</p>');
							}
							else
							{
								$template->setVar('message', '<p>' . __('please confirm registration') . '</p>');
							}

							return false;
						}
						else
						{
							log::setPHPError('something went wrong -.-');
							ErrorPage::view(500);
						}

					}
					else
					{
						$registerform->setErrorMsg(__('passwords not equal'));
						$registerform->setFieldInvalid('passwordAgain');
					}
				}
				else
				{
					$registerform->setErrorMsg(__('email exists'));
					$registerform->setFieldInvalid('email');
				}
			}
			else
			{

				$registerform->setErrorMsg(__('username exists'));
				$registerform->setFieldInvalid('username');
			}
		}

		$template->setVar('form',  $registerform->getForm());
	}

	private function forgotusernameAction(&$template)
	{
		global $app, $XenuxDB;

		$formFields = array
		(
			'email' => array
			(
				'type'     => 'email',
				'required' => true,
				'label'    => __('email')
			),
			'submit' => array
			(
				'type'  => 'submit',
				'label' => __('sendUsername')
			)
		);

		$forgotusernameform = new form($formFields);
		$forgotusernameform->disableRequiredInfo();

		if ($forgotusernameform->isSend() && $forgotusernameform->isValid())
		{
			$data = $forgotusernameform->getInput();

			$userFoundByEmail = $app->user->getUserByEmail($data['email']);

			if ($userFoundByEmail) // check if user exists
			{
				$userinfo = $app->user->userInfo;

				$mail = new mailer;
				$mail->setSender(XENUX_MAIL);
				$mail->setReplyTo($app->getOption('admin_email'));
				$mail->addAdress($userinfo->email, $userinfo->username);
				$mail->setSubject(__('forgotUsername'));
				$mail->setMessage(
					'<p>' . __('hello!') . '</p>' .
					'<p>' . __('username for is', MAIN_URL, $userinfo->username) . '</p>' .
					'<p>' . __('ignore forgotUsername mail') . '</p>'
				);

				if (!$mail->send())
				{
					$template->setVar('message', '<p>' . __('message couldnt sent') . '</p>');
				}
				else
				{
					$template->setVar('message', '<p>' . __('username sent to email') . '</p>');
				}
			}
			else
			{
				$template->setVar('message', '<p>' . __('clouldnt match account with email', $data['email']) . '</p>');
			}
		}

		$template->setVar('form',  $forgotusernameform->getForm());
	}

	private function forgotpasswordAction(&$template)
	{
		global $app, $XenuxDB;

		$formFields = array
		(
			'username' => array
			(
				'type'     => 'text',
				'required' => true,
				'label'    => __('username')
			),
			'submit' => array
			(
				'type'  => 'submit',
				'label' => __('resetPassword')
			)
		);

		$forgotpasswordform = new form($formFields);
		$forgotpasswordform->disableRequiredInfo();

		if ($forgotpasswordform->isSend() && $forgotpasswordform->isValid())
		{
			$data = $forgotpasswordform->getInput();

			$userFoundByUsername = $app->user->getUserByUsername($data['username']);

			if ($userFoundByUsername) // check if user exists
			{
				$userinfo = $app->user->userInfo;
				if (empty($userinfo->password)) // user has not set his password
				{
					$template->setVar('message', __('please set your first password'));
					return false;
				}
				$token = generateRandomString();

				$result = $XenuxDB->Update('users', [
					'verifykey' => $token
				],
				[
					'id' => $userinfo->id
				]);
				if (!$result)
				{
					log::setPHPError('something went wrong -.-');
					ErrorPage::view(500);
					return false;
				}

				$url = ADMIN_URL . '/login?task=resetpassword&amp;id=' . $userinfo->id . '&amp;token=' . $token;

				$mail = new mailer;
				$mail->setSender(XENUX_MAIL);
				$mail->setReplyTo($app->getOption('admin_email'));
				$mail->addAdress($userinfo->email, $userinfo->username);
				$mail->setSubject(__('forgotPassword'));
				$mail->setMessage(
					'<p>' . __('helloUser', $userinfo->username) . '</p>' .
					'<p>' . __('your requested forgotPassword', date('d.m.Y'), date('H:i'), $_SERVER['REMOTE_ADDR']) .
					__('password reset url', $url) . '</p>
					<p>' . __('ignore forgotPassword mail') . '</p>'
				);

				if (!$mail->send())
				{
					$template->setVar('message', '<p>' . __('message couldnt sent') . '</p>');
				}
				else
				{
					$template->setVar('message', '<p>' . __('password reset sent to mail') . '</p>');
				}
			}
			else
			{
				$template->setVar('message', '<p>' . __('clouldnt match account with username', $data['username']) . '</p>');
			}
		}

		$template->setVar('form',  $forgotpasswordform->getForm());
	}

	private function resetpasswordAction(&$template)
	{
		global $app, $XenuxDB;

		if (!isset($_GET['id']) || !isset($_GET['token']))
		{
			ErrorPage::view(405, __('error occurred, please review validity of link'));
			return false;
		}

		$userfound = $XenuxDB->getEntry('users', [
			'columns'=> [
				'id'
			],
			'where'=> [
				'id' => $_GET['id'],
				'verifykey' => $_GET['token']
			]
		]);
		if ($userfound)
		{
			$userinfo = $app->user->getUserInfo($userfound->id);

			$formFields = array
			(
				'password' => array
				(
					'type'       => 'password',
					'required'   => true,
					'label'      => __('password')
				),
				'passwordAgain' => array
				(
					'type'       => 'password',
					'required'   => true,
					'label'      => __('passwordAgain')
				),
				'submit' => array
				(
					'type'  => 'submit',
					'label' => __('resetPassword')
				)
			);

			$forgotpasswordform = new form($formFields);
			$forgotpasswordform->disableRequiredInfo();

			if ($forgotpasswordform->isSend() && $forgotpasswordform->isValid())
			{
				$data = $forgotpasswordform->getInput();

				if ($data['password'] == $data['passwordAgain'])
				{
					$return = $XenuxDB->Update('users', [
						'verifykey' => NULL,
						'password' => $app->user->createPasswordHash($data['password'])
					],
					[
						'id' => $userinfo->id
					]);

					if ($return)
					{
						$template->setVar('message', '<p>' . __('passsword reset successful') . '</p>');
						$template->setVar('form', '');
						return false;
					}
					else
					{
						log::setPHPError('something went wrong -.-');
						ErrorPage::view(500);
					}
				}
				else
				{
					$template->setVar('message', '<p>' . __('entered passwords not equal') . '<p>');
				}
			}

			$template->setVar('form',  $forgotpasswordform->getForm());
		}
		else
		{
			ErrorPage::view(405, __('error occurred, please review validity of link'));
		}
	}

	private function setPasswordAction(&$template)
	{
		global $app, $XenuxDB;

		if (!isset($_GET['id']) || !isset($_GET['token']))
		{
			ErrorPage::view(405, __('error occurred, please review validity of link'));
			return false;
		}

		$userfound = $XenuxDB->getEntry('users', [
			'columns'=> [
				'id'
			],
			'where'=> [
				'id' => $_GET['id'],
				'verifykey' => $_GET['token']
			]
		]);
		if ($userfound)
		{
			$userinfo = $app->user->getUserInfo($userfound->id);

			$formFields = array
			(
				'password' => array
				(
					'type'     => 'password',
					'required' => true,
					'label'    => __('password'),
				),
				'passwordAgain' => array
				(
					'type'     => 'password',
					'required' => true,
					'label'    => __('passwordAgain'),
				),
				'submit' => array
				(
					'type'  => 'submit',
					'label' => __('resetPassword')
				)
			);

			$forgotpasswordform = new form($formFields);
			$forgotpasswordform->disableRequiredInfo();

			if ($forgotpasswordform->isSend() && $forgotpasswordform->isValid())
			{
				$data = $forgotpasswordform->getInput();

				if ($data['password'] == $data['passwordAgain'])
				{
					$return = $XenuxDB->Update('users', [
						'verifykey' => NULL,
						'password' => $app->user->createPasswordHash($data['password'])
					],
					[
						'id' => $userinfo->id
					]);

					if ($return)
					{
						$mail = new mailer;
						$mail->setSender(XENUX_MAIL);
						$mail->setReplyTo($app->getOption('admin_email'));
						$mail->addAdress($userinfo->email, $userinfo->firstname . $userinfo->lastname);
						$mail->setSubject(__('saved password'));
						$mail->setMessage(
							'<p>' . __('helloUser', $username) . '!</p>' .
							'<p>' . __('saved password msg mail', MAIN_URL) . '</p>');
						$mail->send();

						$template->setVar('message', '<p>' . __('saved password msg') . '</p>');
						$app->user->setLogin();

						header('Location: ' . ADMIN_URL . (isset($_GET['redirectTo']) ? $_GET['redirectTo'] : ''));
					}
					else
					{
						log::setPHPError('something went wrong -.-');
						ErrorPage::view(500);
					}
				}
				else
				{
					$template->setVar('message', '<p>' . __('entered passwords not equal') . '<p>');
				}
			}

			$template->setVar('form',  $forgotpasswordform->getForm());
		}
		else
		{
			ErrorPage::view(405, __('error occurred, please review validity of link'));
		}
	}

	private function confirmAction(&$template)
	{
		global $app, $XenuxDB;

		if (!isset($_GET['id']) || !isset($_GET['token']))
		{
			ErrorPage::view(405, __('error occurred, please review validity of link'));
			return false;
		}

		$user = $XenuxDB->getEntry('users', [
			'columns'=> [
				'id'
			],
			'where'=> [
				'id' => $_GET['id'],
				'verifykey' => $_GET['token']
			]
		]);
		if ($user)
		{
			@$app->user->userInfo->id = $user->id;

			$return = $XenuxDB->Update('users', [
				'verifykey' => NULL,
				'confirmed' => true
			],
			[
				'id' => $user->id
			]);

			if ($return)
			{
				$template->setIfCondition('confirmSucessful', true);
				$app->user->setLogin();
				header('Refresh:5; url=' . ADMIN_URL, true, 303);
			}
		}
		else
		{
			ErrorPage::view(405, __('error occurred, please review validity of link'));
		}
	}
}
