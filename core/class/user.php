<?php
class user
{
	#FIXME: remove userID - its bullshit

	private $session;
	public $userInfo;
	public $userID;
	private $userIsLoggedIn;
	
	public function __construct()
	{
		$this->session = @$_SESSION['_LOGIN'];

		if($this->isLogin()) {
			$this->userInfo	= $this->getUserInfo($this->session['userID']);
			$this->userID	= $this->userInfo->id;
		}
	}
	
	public function isLogin()
	{
		if(isset($this->session['userID']) && is_numeric($this->session['userID']))
		{
			$userID	= $this->session['userID'];
			if($this->getSessionFingerprint() == $this->getUserSessionFingerprint($userID))
			{
				return true;
			}
			else
			{
				if (!(defined('DEBUG') && DEBUG == true))
					log::writeLog("Session stolen ???");
				$this->setLogout();
				return false;
			}
		}
		return false;
	}
	
	public function checkPassword($password)
	{
		$userInfo = $this->getUserInfo($this->userID);

		$stored = $userInfo->password;
		$username = $userInfo->username;

	    $string = hash_hmac ( "whirlpool", str_pad ( $password, strlen ( $password ) * 4, sha1 ( $username ), STR_PAD_BOTH ), SALT, true );
	    return crypt ( $string, substr ( $stored, 0, 30 ) ) == $stored;
	}
	
		
	public function createPasswordHash($username, $password, $rounds='10')
	{
		$string = hash_hmac ( "whirlpool", str_pad ( $password, strlen ( $password ) * 4, sha1 ( $username ), STR_PAD_BOTH ), SALT, true );
		$salt = substr ( str_shuffle ( './0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ) , 0, 22 );
		return crypt ( $string, '$2a$' . $rounds . '$' . $salt );
	}
	
	
	public function getUserInfo($userID)
	{
		global $XenuxDB;

		$user = $XenuxDB->getEntry('users', [
			'where' => [
				'id' => $userID
			]
		]);

		if($user)
		{
			$this->userID = $user->id;
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

		if($user)
		{
			$this->userID = $user->id;
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

		if($user)
		{
			$this->userID = $user->id;
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

		if($user)
		{
			return $user->session_fingerprint;
		}

		return false;
	}
	
	public function setLogin()
	{
		global $XenuxDB;

		$_SESSION['_LOGIN']['userID'] = $this->userID;

		$XenuxDB->Update('users', [
			'lastlogin_ip'		=> $_SERVER['REMOTE_ADDR'],
			'lastlogin_date'	=> date('Y-m-d H:i:s'),
		],
		[
			'id' => $this->userID
		]);

		$this->setSessionFingerprint($this->userID);
	
		return true;
	}
	
	public function setLogout()
	{
		$_SESSION['_LOGIN'] = '';
		$this->session = @$_SESSION['_LOGIN'];

		$this->clearSessionFingerprint($this->userID);
		return true;
	}
	
	private function getSessionFingerprint()
	{
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
?>