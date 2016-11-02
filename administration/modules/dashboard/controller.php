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

		// append translations
		translator::appendTranslations(PATH_ADMIN . '/modules/'.$this->modulename.'/translation/');

		$template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout.php");

		$template->setVar("count_users", $this->_getCountUsers());
		$template->setVar("count_active_users", $this->_getCountActiveUsers());

		$template->setVar("count_pages", $this->_getCountPages());
		$template->setVar("count_public_pages", $this->_getCountPublicPages());

		$template->setVar("count_posts", $this->_getCountPosts());
		$template->setVar("count_public_posts", $this->_getCountPublicPosts());
		$template->setVar("count_posts_lastWeek", $this->_getCountPostsLastWeek());

		$template->setVar("count_cloud_files", $this->_getCountCloudFiles());
		$template->setVar("count_cloud_images", $this->_getCountCloudImages());
		$template->setVar("count_cloud_others", $this->_getCountCloudOthers());
		$template->setVar("total_size_files", formatBytes($this->_getTotalSizeCloudFiles()));

		echo $template->render();

		$this->page_name = __("dashboard");

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


	private function _getCountPosts()
	{
		global $XenuxDB, $app;
		return $XenuxDB->Count('posts');
	}

	private function _getCountPublicPosts()
	{
		global $XenuxDB, $app;
		return $XenuxDB->Count('posts', [
			'where' => [
				'status' => 'publish'
			]
		]);
	}

	private function _getCountPostsLastWeek()
	{
		global $XenuxDB, $app;
		return $XenuxDB->Count('posts', [
			'where' => [
				'AND' => [
					'create_date[>=]' => date2mysql("-1 week +1 day"),
					'status' => 'publish'
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

	private function _getTotalSizeCloudFiles()
	{
		global $XenuxDB, $app;
		return $XenuxDB->getEntry('files', [
			'columns' => [
				'#SUM(size)(totalsize)'
			]
		])->totalsize;
	}
}
?>
