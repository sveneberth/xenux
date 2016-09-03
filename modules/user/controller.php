<?php
class userController extends AbstractController
{
	protected $pageID;

	public function __construct($url = null)
	{
		if(isset($url))
			$this->url = $url;

		$this->modulename = str_replace('Controller', '', get_class());
	}

	public function run()
	{
		// append translations
		translator::appendTranslations(PATH_MAIN . '/modules/user/translation/');

		if (@$this->url[1] == "view")
		{
			if (isset($this->url[2]) && full($this->url[2]))
			{
				$this->user = @$this->url[2];
				$this->userView();
			}
		}
		else
		{
			header('HTTP/1.1 404 Not Found');
			throw new Exception(__('error404msg'));
			//throw new Exception("404 - $this->modulename template not found");
		}
		return true;
	}

	protected function userView()
	{
		global $app, $XenuxDB;

		$user = $XenuxDB->getEntry('users', [
			'where' => [
				'username' => $this->user
			]
		]);
		if($user)
		{
			$template = new template(PATH_MAIN."/modules/".$this->modulename."/layout.php",
			[
				'socialmedia_links' => $user->social_media
			]);
			$template->setVar("username", $user->username);
			$template->setVar("realname", $user->firstname . ' ' . $user->lastname);
			$template->setIfCondition("realname_show_profile", $user->realname_show_profile);
			$template->setVar("email", $user->email);
			$template->setIfCondition("email_show_profile", $user->email_show_profile);
			$template->setVar("bio", $user->bio);
			$template->setVar("homepage", $user->homepage);
			$template->setIfCondition("social_media_not_empty", full($user->social_media));
			$template->setVar("amountPostings", $XenuxDB->count('sites', [
				'where' => [
					'author_id' => $user->id
				]
			]) + $XenuxDB->count('events', [
				'where' => [
					'author_id' => $user->id
				]
			]) + $XenuxDB->count('news', [
				'where' => [
					'author_id' => $user->id
				]
			]));

			echo $template->render();

			$this->page_name = "$this->user";
		}
		else
		{
			header('HTTP/1.1 404 Not Found');
			throw new Exception(__('error404msg'));
		}
	}
}
