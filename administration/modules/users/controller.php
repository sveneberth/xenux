<?php
#TODO: translation
class usersController extends AbstractController
{
	private $editUserID;
	private $messages = array();

	public function __construct($url)
	{
		parent::__construct($url);

		if (!isset($this->url[1]) || empty($this->url[1]))
			header("Location: ".URL_ADMIN.'/'.$this->modulename.'/home');
	}

	public function run()
	{
		global $XenuxDB, $app;

		// append translations
		translator::appendTranslations(PATH_ADMIN . '/modules/'.$this->modulename.'/translation/');

		if (@$this->url[1] == "home")
		{
			$this->userHome();
		}
		elseif (@$this->url[1] == "edit")
		{
			if (isset($this->url[2]) && is_numeric($this->url[2]) && !empty($this->url[2]))
			{
				$this->editUserID = $this->url[2];
				$this->userEdit();
			}
			else
			{
				#header("Location: ".URL_ADMIN.'/'.$this->modulename.'/home');
				throw new Exception(__('isWrong', 'users ID'));
			}
		}
		elseif (@$this->url[1] == "profile")
		{
			$this->editUserID = $app->user->userInfo->id;
			$this->userEdit();


			$this->page_name = __('profile');
		}
		elseif (@$this->url[1] == "new")
		{
			$this->userEdit(true);
		}
		else
		{
			throw new Exception("404 - $this->modulename template not found");
		}

		return true;
	}

	private function userHome()
	{
		global $app, $XenuxDB;

		$template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout_home.php");


		if (isset($_GET['remove']) && is_numeric($_GET['remove']) && !empty($_GET['remove']))
		{
			$XenuxDB->delete('users', [
				'where' => [
					'id' => $_GET['remove']
				]
			]);
			$this->messages[] = '<p class="box-shadow info-message ok">'.__('removedSuccessful').'</p>';
		}

		if (isset($_GET['action']) && in_array($_GET['action'], ['remove'])
			&& isset($_GET['item']) && is_array($_GET['item']))
		{
			foreach ($_GET['item'] as $item) {
				if (is_numeric($item)) {
					switch ($_GET['action']) {
						case 'remove':
							$XenuxDB->delete('users', [
								'where' => [
									'id' => $item
								]
							]);
							break;
					}
					$template->setVar('messages',
						'<p class="box-shadow info-message ok">' . __('batch processing successful') . '</p>');
				}
			}
		}

		$template->setVar("users", $this->getUserTable());
		$template->setVar("amount", $XenuxDB->count('users'));

		if (isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$this->messages[] = '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>';
		if (isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$this->messages[] = '<p class="box-shadow info-message error">'.__('savingFailed').'</p>';


		$template->setVar("messages", implode("\n", $this->messages));

		echo $template->render();

		$this->page_name = __('home');
		$this->headlineSuffix = '<a class="btn-new" href="{{URL_ADMIN}}/users/new">' . __('new') . '</a>';
	}

	private function getUserTable()
	{
		global $XenuxDB;

		$return = '';

		$users = $XenuxDB->getList('users', [
			'order' => 'username ASC'
		]);
		if ($users)
		{
			foreach($users as $user)
			{
				$return .= '
<tr>
	<td class="column-select"><input type="checkbox" name="item[]" value="' . $user->id . '"></td>
	<td class="column-id">' . $user->id . '</td>
	<td class="column-text">
		<a class="edit" href="{{URL_ADMIN}}/users/edit/' . $user->id . '" title="' . __('click to edit user') . '">' . $user->username . '</a>
	</td>
	<td class="column-text">' . $user->firstname . '</td>
	<td class="column-text">' . $user->lastname . '</td>
	<td class="column-actions">
		<a class="view-btn" target="_blank" href="{{URL_MAIN}}/user/view/' . urlencode($user->username) . '">' . __('view') . '</a>
		<a href="{{URL_ADMIN}}/users/home/?remove=' . $user->id . '" title="' . __('delete') . '" class="remove-btn">
			<svg xmlns="http://www.w3.org/2000/svg" height="32px" version="1.1" viewBox="0 0 32 32" width="32px">
				 <path fill-rule="evenodd" d="M21.333 3.556h4.741V4.74H5.926V3.556h4.74V2.37c0-1.318 1.06-2.37 2.368-2.37h5.932a2.37 2.37 0 0 1 2.367 2.37v1.186zM5.926 5.926v22.517A3.55 3.55 0 0 0 9.482 32h13.036a3.556 3.556 0 0 0 3.556-3.557V5.926H5.926zm4.74 3.555v18.963h1.186V9.481h-1.185zm4.741 0v18.963h1.186V9.481h-1.186zm4.741 0v18.963h1.185V9.481h-1.185zm-7.107-8.296c-.657 0-1.19.526-1.19 1.185v1.186h8.297V2.37c0-.654-.519-1.185-1.189-1.185h-5.918z"/>
			</svg>
		</a>
	</td>
</tr>';
			}
		}

		return $return;
	}


	private function userEdit($new=false)
	{
		$template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout_edit.php", [
			"profileEdit" => @$this->url[1] == "profile"
		]);

		$template->setVar("form", $this->getEditForm($template, $new));

		$template->setIfCondition("new", $new);
		$template->setIfCondition("profileEdit", @$this->url[1] == "profile");

		if (isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$this->messages[] = '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>';
		if (isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$this->messages[] = '<p class="box-shadow info-message error">'.__('savingFailed').'</p>';

		$template->setVar("messages", implode("\n", $this->messages));

		echo $template->render();

		$this->page_name = $new ? __('new') : __('edit');
	}

	private function getEditForm(&$template, $new=false)
	{
		global $XenuxDB, $app;

		if (!$new)
			$user = $XenuxDB->getEntry('users', [
				'where' => [
					'id' => $this->editUserID
				]
			]);

		if (!@$user && !$new)
			throw new Exception("error (user 404)");

		$formFields = array
		(
			'username' => array
			(
				'type' => 'text',
				'required' => true,
				'label' => __('username'),
				'value' =>  !$new ? $user->username : '',
			),
			'firstname' => array
			(
				'type' => 'text',
				'required' => false,
				'label' => __('firstname'),
				'value' => @$user->firstname,
			),
			'lastname' => array
			(
				'type' => 'text',
				'required' => false,
				'label' => __('lastname'),
				'value' => @$user->lastname,
			),
			'realname_show_profile' => array
			(
				'type' => 'checkbox',
				'label' => __('realname_show_profile'),
				'value' => 'true',
				'checked' => @$user->realname_show_profile
			),
			'email' => array
			(
				'type' => 'email',
				'required' => true,
				'label' => __('email'),
				'value' => @$user->email,
			),
			'email_show_profile' => array
			(
				'type' => 'checkbox',
				'label' => __('email_show_profile'),
				'value' => 'true',
				'checked' => @$user->email_show_profile
			),
			'homepage' => array
			(
				'type' => 'text',
				'required' => false,
				'label' => __('homepage'),
				'value' => @$user->homepage,
			),
			'social_media' => array
			(
				'type' => 'textarea',
				'required' => false,
				'label' => __('social_media'),
				'value' => @$user->social_media,
				'info' => __('social media introduction'),
			),
			'password' => array
			(
				'type' => 'password',
				'label' => __('password'),
				'info' => !$new ?
					__('If you dont want to change the password, leave the fields blank') :
					__('leave the fields blank and the user can set the password himself'),
			),
			'passwordAgain' => array
			(
				'type' => 'password',
				'label' => __('passwordAgain'),
				'info' => !$new ?
					__('If you dont want to change the password, leave the fields blank') :
					__('leave the fields blank and the user can set the password himself'),
			),
			'bio' => array
			(
				'type' => 'textarea',
				'required' => false,
				'label' => __('bio'),
				'value' => @$user->bio,
			),
			'submit_stay' => array
			(
				'type' => 'submit',
				'label' => __('save&stay'),
				'class' => 'floating'
			),
			'submit_close' => array
			(
				'type' => 'submit',
				'label' => __('save&close'),
				'class' => 'floating space-left'
			),
			'cancel' => array
			(
				'type' => 'submit',
				'label' => __('cancel'),
				'style' => 'background-color:red',
				'class' => 'floating space-left'
			),
			'clearfix' => array
			(
				'type' => 'html',
				'value' => '<div class="clear"></div>'
			)
		);

		if ($new) {
			// unset, not needed -> user get email
			unset($formFields['password']);
			unset($formFields['passwordAgain']);
		}

		$form = new form($formFields);
	//	$form->disableRequiredInfo();

		if ($form->isSend() && isset($form->getInput()['cancel']))
		{
			header('Location: '.URL_ADMIN.'/users/home');
			return false;
		}

		if ($form->isSend() && $form->isValid())
		{
			$data = $form->getInput();

			$username		= preg_replace('/[^a-zA-Z0-9_\-\.]/' , '' , $data['username']);
			$homepage		= full($data['homepage']) ? (preg_match('/^([a-zA-Z]*)\:\/\//', $data['homepage']) ? $data['homepage'] : 'http://'.$data['homepage']) : '';
			$social_media =  trim(preg_replace('/(.*?)\:\s?(\w*?):(.*?)$/m', '', $data['social_media']));

			$social_media_ok		= ($social_media == '');
			$userFoundByUsername	= $app->user->getUserByUsername($username) && $username != @$user->username;
			$userFoundByEmail		= $app->user->getUserByEmail($data['email']) && $data['email'] != @$user->email;

			if (!$social_media_ok)
			{
				$this->messages[] = '<p class="box-shadow info-message warning">'.__('the social media links are inacceptable').'</p>';
			}

			if ($userFoundByUsername)
			{
				$this->messages[] = '<p class="box-shadow info-message warning">'.__('an user with this username exist already').'</p>';
			}

			if ($userFoundByEmail)
			{
				$this->messages[] = '<p class="box-shadow info-message warning">'.__('an user with this email exist already').'</p>';
			}

			if ($userFoundByEmail || $userFoundByUsername || !$social_media_ok)
			{
				return $form->getForm();
			}

			$success = true;

			if ($new)
			{
				$token = generateRandomString();

				$user = $XenuxDB->Insert('users', [
					'username'				=> $username,
					'firstname'				=> $data['firstname'],
					'lastname'				=> $data['lastname'],
					'realname_show_profile'	=> parse_bool(@$data['realname_show_profile']),
					'email'					=> $data['email'],
					'email_show_profile'	=> parse_bool(@$data['email_show_profile']),
					'password'				=> '',
					'verifykey'					=> $token,
					'homepage'				=> $homepage,
					'bio'					=> $data['bio'],
					'social_media'			=> $data['social_media'],
					'confirmed'				=> true
				]);


				if (is_numeric($user) && $user != 0)
				{
					$url = URL_ADMIN . '/login?task=setpassword&amp;id=' . $user . '&amp;token=' . $token;

					$mail = new mailer;
					$mail->setSender(XENUX_MAIL);
					$mail->setReplyTo($app->getOption('admin_email'));
					$mail->addAdress($data['email'], $data['firstname'] . $data['lastname']);
					$mail->setSubject('Benutzeraccount erstellt');
					$mail->setMessage('Hallo!<br>
<p>Es wurde für dich auf <a href="' . URL_MAIN . '">' . URL_MAIN . '</a> ein Benutzeraccount angelegt.</p>
<p>Benutzername: ' .  $username . '</p>
<p>Unter der folgenden Adresse kannst du dein Passwort festlegen:<br>
<a href="' . $url . '">' . $url . '</a></p>');

					if(!$mail->send())
					{
						$this->messages[] = '<p class="box-shadow info-message warning">Die Nachricht konnte nicht versendet werden.</p>';
						$template->setVar("message", '<p>Die Nachricht konnte nicht versendet werden.</p>');
						$success = false;
					}
					else
					{
						$this->messages[] = '<p class="box-shadow info-message ok">Mail an den Nutzer erfolgreich versand.</p>';
						$this->editUserID = $user;
					}
				}
				else
				{
					$success = false;
				}
			}
			else
			{
				if ((isset($data['password']) && !empty($data['password'])) || (isset($data['passwordAgain']) && !empty($data['passwordAgain'])))
				{
					// password change
					if ($data['password'] == $data['passwordAgain'])
					{
						$return = $XenuxDB->Update('users', [
							'password' => $app->user->createPasswordHash($data['password']),
						],
						[
							'id' => $this->editUserID
						]);

						if ($return === false)
							$success = false;
					}
					else
					{
						$this->messages[] = '<p class="box-shadow info-message warning">'.__('the passwords are not equal').'</p>';
						return $form->getForm();
					}
				}

				// update it
				$return = $XenuxDB->Update('users', [
					'username'				=> $data['username'],
					'firstname'				=> $data['firstname'],
					'lastname'				=> $data['lastname'],
					'realname_show_profile'	=> parse_bool($data['realname_show_profile']),
					'email'					=> $data['email'],
					'email_show_profile'	=> parse_bool($data['email_show_profile']),
					'homepage'				=> $homepage,
					'bio'					=> $data['bio'],
					'social_media'			=> $data['social_media']
				],
				[
					'id' => $this->editUserID
				]);

				if ($return === false)
					$success = false;
			}

			if ($success === true)
			{
				log::debug('user saved successfull');
				$this->messages[] = '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>';

				if (isset($data['submit_close']))
				{
					header('Location: '.URL_ADMIN.'/users/home?savingSuccess=true');
					return false;
				}

				header('Location: '.URL_ADMIN.'/users/edit/'.$this->editUserID.'?savingSuccess=true');
			}
			else
			{
				log::debug('user saving failed');
				$this->messages[] = '<p class="box-shadow info-message error">'.__('savingFailed').'</p>';

				if (isset($data['submit_close']) || $new)
				{
					header('Location: '.URL_ADMIN.'/users/home?savingSuccess=false');
					return false;
				}

				header('Location: '.URL_ADMIN.'/users/edit/'.$this->editUserID.'?savingSuccess=false');
			}
		}
		return $form->getForm();
	}
}
