<?php
class dashboardController extends AbstractController
{
	public function __construct($url)
	{
		$this->url = $url;
		$this->modulename = str_replace('Controller', '', get_class());

		if(!isset($this->url[1]) || empty($this->url[1]))
			header("Location: ".URL_ADMIN.'/'.$this->modulename.'/home');
	}
	
	public function run()
	{
		global $XenuxDB, $app;

		$template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout.php");
	
		$template->setVar("count_users", $this->_getCountUsers());
		$template->setVar("count_active_users", $this->_getCountActiveUsers());

		$template->setVar("count_pages", $this->_getCountPages());
		$template->setVar("count_public_pages", $this->_getCountPublicPages());

		$template->setVar("count_news", $this->_getCountNews());
		$template->setVar("count_public_news", $this->_getCountPublicNews());
		$template->setVar("count_news_lastWeek", $this->_getCountNewsLastWeek());
		
		$template->setVar("count_cloud_files", $this->_getCountCloudFiles());
		$template->setVar("count_cloud_images", $this->_getCountCloudImages());
		$template->setVar("count_cloud_others", $this->_getCountCloudOthers());
		
		echo $template->render();

		$this->page_name = "Dashboard";

		return true;
	}


	private function _getCountPages()
	{
		global $XenuxDB, $app;
		return $XenuxDB->Count('sites');
	}

	private function _getCountPublicPages()
	{
		global $XenuxDB, $app;
		return $XenuxDB->Count('sites', [
			'where' => [
				'public' => true
			]
		]);
	}


	private function _getCountUsers()
	{
		global $XenuxDB, $app;
		return $XenuxDB->Count('users');
	}

	private function _getCountActiveUsers()
	{
		global $XenuxDB, $app;
		return $XenuxDB->Count('users', [
			'where' => [
				'##lastlogin_date[>=]' => 'CURDATE()'
			]
		]);
	}


	private function _getCountNews()
	{
		global $XenuxDB, $app;
		return $XenuxDB->Count('news');
	}

	private function _getCountPublicNews()
	{
		global $XenuxDB, $app;
		return $XenuxDB->Count('news', [
			'where' => [
				'public' => true
			]
		]);
	}

	private function _getCountNewsLastWeek()
	{
		global $XenuxDB, $app;
		return $XenuxDB->Count('news', [
			'where' => [
				'AND' => [
					'create_date[>=]' => date2mysql(strtotime("-1 week +1 day"), true),
					'public' => true
				]
			]
		]);
	}
	

	private function _getCountCloudFiles()
	{
		global $XenuxDB, $app;
		return $XenuxDB->Count('files', [
			'where' => [
				'type' => 'file'
			]
		]);
	}

	private function _getCountCloudImages()
	{
		global $XenuxDB, $app;
		return $XenuxDB->Count('files', [
			'where' => [
				'mime_type[~]' => 'image/',
			]
		]);
	}

	private function _getCountCloudOthers()
	{
		return $this->_getCountCloudFiles() - $this->_getCountCloudImages();
	}
}
?>