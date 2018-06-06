<?php
class user
{
	private $session;
	public $userInfo;
	private $userIsLoggedIn;

	public function __construct()
	{
		$this->session = @$_SESSION['_LOGIN'];

		if ($this->isLogin())
		{
			$this->userInfo	= $this->getUserInfo($this->session['userID']);
		}
	}

	public function isLogin()
	{
		if (isset($this->session['userID']) && is_numeric($this->session['userID']))
		{
			$userID	= $this->session['userID'];
			if ($this->getSessionFingerprint() == $this->getUserSessionFingerprint($userID))
			{
				return true;
			}
			else
			{
				log::debug('Session stolen ???');
				$this->setLogout();
				return false;
			}
		}
		return false;
	}

	public function checkPassword($password)
	{
		$stored = $this->userInfo->password;

		return password_verify($password, $stored);
	}

	public function createPasswordHash($password)
	{
		return password_hash($password, PASSWORD_DEFAULT, [
			'cost' => COST
		]);
	}

	public function getUserInfo($userID)
	{
		global $XenuxDB;

		$user = $XenuxDB->getEntry('users', [
			'where' => [
				'id' => $userID
			]
		]);

		if ($user)
		{
			$this->userInfo = $user;
			return $user;
		}
		else
		{
			return false;
		}
	}

	public function getUserByUsername($username)
	{
		global $XenuxDB;

		$user = $XenuxDB->getEntry('users', [
			'columns' => 'id',
			'where' => [
				'username' => $username
			]
		]);

		if ($user)
		{
			$this->userInfo = $this->getUserInfo($user->id);
			return $user->id;
		}
		else
		{
			return false;
		}
	}

	public function getUserByEmail($email)
	{
		global $XenuxDB;

		$user = $XenuxDB->getEntry('users', [
			'columns' => 'id',
			'where' => [
				'email' => $email
			]
		]);

		if ($user)
		{
			$this->userInfo = $this->getUserInfo($user->id);
			return $user->id;
		}
		else
		{
			return false;
		}
	}

	public function getUserSessionFingerprint($userID)
	{
		global $XenuxDB;

		$user = $XenuxDB->getEntry('users', [
			'columns' => 'session_fingerprint',
			'where' => [
				'id' => $userID
			]
		]);

		if ($user)
		{
			return $user->session_fingerprint;
		}

		return false;
	}

	public function setLogin()
	{
		global $XenuxDB;

		$_SESSION['_LOGIN']['userID'] = $this->userInfo->id;

		$XenuxDB->Update('users', [
			'lastlogin_ip'		=> $_SERVER['REMOTE_ADDR'],
			'lastlogin_date'	=> date('Y-m-d H:i:s'),
		],
		[
			'id' => $this->userInfo->id
		]);

		$this->setSessionFingerprint($this->userInfo->id);

		return true;
	}

	public function setLogout()
	{
		$_SESSION['_LOGIN'] = '';
		$this->session = @$_SESSION['_LOGIN'];

		if ($this->isLogin())
			$this->clearSessionFingerprint($this->userInfo->id);

		return true;
	}

	private function getSessionFingerprint()
	{
		#TODO: use salt
		return SHA1($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
	}

	public function setSessionFingerprint($userID)
	{
		global $XenuxDB;

		$fingerprint = $this->getSessionFingerprint();

		$XenuxDB->Update('users', [
			'session_fingerprint' => $fingerprint,
		],
		[
			'id' => $userID
		]);

		return true;
	}

	private function clearSessionFingerprint($userID)
	{
		global $XenuxDB;

		$XenuxDB->Update('users', [
			'session_fingerprint' => NULL,
		],
		[
			'id' => $userID
		]);

		return true;
	}
}
