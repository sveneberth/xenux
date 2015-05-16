<?php
class usersController extends AbstractController
{
	private $editUserID;
	private $messages = array();

	public function __construct($url)
	{
		$this->url = $url;
		$this->modulename = str_replace('Controller', '', get_class());

		if(!isset($this->url[1]) || empty($this->url[1]))
			header("Location: ".URL_ADMIN.'/'.$this->modulename.'/home');
	}
	
	public function run()
	{
		#FIXME: build profile
		global $XenuxDB, $app;

		if(@$this->url[1] == "home")
		{
			$this->userHome();
		}
		elseif(@$this->url[1] == "edit")
		{
			if(isset($this->url[2]) && is_numeric($this->url[2]) && !empty($this->url[2]))
			{
				$this->editUserID = $this->url[2];
				$this->userEdit();
			}
			else
			{
				throw new Exception(__('isWrong', 'users ID'));
			}
		}
		elseif(@$this->url[1] == "new")
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
		
		
		if(isset($_GET['remove']) && is_numeric($_GET['remove']) && !empty($_GET['remove']))
		{
			$XenuxDB->delete('users', [
				'where' => [
					'id' => $_GET['remove']
				]
			]);
			$this->messages[] = '<p class="box-shadow info-message ok">'.__('removedSuccessful').'</p>';
		}
		
		$template->setVar("users", $this->getUserTable());

		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$this->messages[] = '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>';
		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$this->messages[] = '<p class="box-shadow info-message error">'.__('savingFailed').'</p>';
		
		
		$template->setVar("messages", implode("\n", $this->messages));

		echo $template->render();

		$this->page_name = "users:home";
	}

	private function getUserTable()
	{
		global $XenuxDB;

		$return = '';

		$users = $XenuxDB->getList('users', [
			'order' => 'username ASC'
		]);
		if($users)
		{
			foreach($users as $user)
			{
				$return .= '
<li>
	<span class="data-column user-id">'.$user->id.'</span>
	<a class="data-column user-username edit" href="{{URL_ADMIN}}/users/edit/'.$user->id.'" title="'.__('click to edit user').'">'.$user->username.'</a>
	<span class="data-column user-firstname">'.$user->firstname.'</span>
	<span class="data-column user-lastname">'.$user->lastname.'</span>
	<!--<a class="data-column show" target="_blank" href="{{URL_MAIN}}/user/view/'.getPreparedLink($user->id, $user->username).'">'.__('show').'</a>-->
	<a href="{{URL_ADMIN}}/users/home/?remove='.$user->id.'" title="'.__('deleteusers').'" class="remove remove-icon clickable"></a>
</li>';
			}
		}

		return $return;
	}


	private function userEdit($new=false)
	{
		$template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout_edit.php");
	
		$template->setVar("form", $this->getEditForm($template, $new));

		$template->setIfCondition("new", $new);

		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$this->messages[] = '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>';
		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$this->messages[] = '<p class="box-shadow info-message error">'.__('savingFailed').'</p>';
		
		$template->setVar("messages", implode("\n", $this->messages));

		echo $template->render();

		$this->page_name = "users:".($new ? 'new' : 'edit');
	}

	private function getEditForm(&$template, $new=false)
	{
		global $XenuxDB, $app;

		if(!$new)
			$user = $XenuxDB->getEntry('users', [
				'where' => [
					'id' => $this->editUserID
				]
			]);

		if(!@$user && !$new)
			throw new Exception("error (user 404)");
			
		$formFields = array
		(
			'username' => array
			(
				'type' => $new ? 'text' : 'readonly',
				'required' => true,
				'label' => __('username'),
				'value' =>  !$new ? $user->username : '',
				'info' => !$new ? __('usernames cannot be changed') : '',
			),
			'firstname' => array
			(
				'type' => 'text',
				'required' => true,
				'editable' => false,
				'label' => __('firstname'),
				'value' => @$user->firstname,
			),
			'lastname' => array
			(
				'type' => 'text',
				'required' => true,
				'editable' => false,
				'label' => __('lastname'),
				'value' => @$user->lastname,
			),
			'email' => array
			(
				'type' => 'email',
				'required' => true,
				'editable' => false,
				'label' => __('email'),
				'value' => @$user->email,
			),
			'homepage' => array
			(
				'type' => 'text',
				'required' => false,
				'label' => __('homepage'),
				'value' => @$user->homepage,
			),
			'password' => array
			(
				'type' => 'password',
				'required' => $new ? true : false,
				'label' => __('password'),
				'min_length' => 6,
				'info' => !$new ? __('If you don\'t want to change the password, leave the fields blank') : '',
			),
			'passwordAgain' => array
			(
				'type' => 'password',
				'required' => $new ? true : false,
				'label' => __('passwordAgain'),
				'min_length' => 6
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
			)
		);

		$form = new form($formFields);
		$form->disableRequiredInfo();

		if($form->isSend() && isset($form->getInput()['cancel']))
		{
			header('Location: '.URL_ADMIN.'/users/home');
			return false;
		}

		if($form->isSend() && $form->isValid())
		{
			$data = $form->getInput();
			log::writeLog(print_r($data, true));

			$username		= preg_replace('/[^a-zA-Z0-9_\-\.]/' , '' , $data['username']);
			$homepage		= (preg_match('/^([a-zA-Z]*)\:\/\//', $data['homepage']) && !empty($data['homepage'])) ? $data['homepage'] : 'http://'.$data['homepage'];

			$success = true;

			if($new)
			{
				$userFoundByUsername	= $app->user->getUserByUsername($data['username']);
				$userFoundByEmail		= $app->user->getUserByEmail($data['email']);
				$passwordsEqual			= $data['password'] == $data['passwordAgain'];

				if($userFoundByUsername)
				{
					$this->messages[] = '<p class="box-shadow info-message warning">'.__('an user with this username exist already').'</p>';
				}
				if($userFoundByEmail)
				{
					$this->messages[] = '<p class="box-shadow info-message warning">'.__('an user with this email exist already').'</p>';
				}
				if(!$passwordsEqual)
				{
					$this->messages[] = '<p class="box-shadow info-message warning">'.__('the passwords are not equal').'</p>';
				}

				if($userFoundByEmail || $userFoundByUsername || !$passwordsEqual)
				{
					return $form->getForm();
				}

				$user = $XenuxDB->Insert('users', [
					'username'			=> $username,
					'firstname'			=> $data['firstname'],
					'lastname'			=> $data['lastname'],
					'email'				=> $data['email'],
					'password'			=> $app->user->createPasswordHash($username, $data['password']),
					'homepage'			=> $homepage,
					'bio'				=> $data['bio'],
					'confirmed'			=> true
				]);

				if($user !== false)
				{
					$this->editUserID = $user;
				}
				else
				{
					$success = false;
				}
			}
			else
			{
				if((isset($data['password']) && !empty($data['password'])) || (isset($data['passwordAgain']) && !empty($data['passwordAgain'])))
				{
					// password change
					if($data['password'] == $data['passwordAgain'])
					{
						$return = $XenuxDB->Update('users', [
							'password' => $app->user->createPasswordHash($data['username'], $data['password']),
						],
						[
							'id' => $this->editUserID
						]);

						if($return === false)
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
					'firstname'			=> $data['firstname'],
					'lastname'			=> $data['lastname'],
					'email'				=> $data['email'],
					'homepage'			=> $homepage,
					'bio'				=> $data['bio'],
				],
				[
					'id' => $this->editUserID
				]);

				if($return === false)
					$success = false;
			}

			if($success === true)
			{
				log::writeLog('saved successfull');
				$this->messages[] = '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>';

				if(isset($data['submit_close']))
				{
					header('Location: '.URL_ADMIN.'/users/home?savingSuccess=true');
					return false;
				}

				header('Location: '.URL_ADMIN.'/users/edit/'.$this->editUserID.'?savingSuccess=true');
			}
			else
			{
				$this->messages[] = '<p class="box-shadow info-message error">'.__('savingFailed').'</p>';
				log::writeLog('saving failed');

				if(isset($data['submit_close']))
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
?>