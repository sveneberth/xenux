<?php
class newsController extends AbstractController
{
	public function __construct($url)
	{
		$this->url = $url;
		$this->modulename = str_replace('Controller', '', get_class());
	}

	public function run()
	{
		if (@$this->url[1] == "list")
		{
			$this->newsList();
		}
		elseif (@$this->url[1] == "view")
		{
			$this->newsView();
		}
		else
		{
			header('HTTP/1.1 404 Not Found');
			throw new Exception(__('error404msg'));
			//throw new Exception("404 - $this->modulename template not found");
		}
		return true;
	}

	private function newsList()
	{
		global $XenuxDB;

		echo '<div class="page-header"><h1>' . __('news_Pl') . '</h1></div>';

		$start			= is_numeric(@$_GET['start']) ? floor($_GET['start']) : 0;
		$amount			= (is_numeric(@$_GET['amount']) && floor(@$_GET['amount']) != 0) ? floor($_GET['amount']) : 10;
		$absolutenumber	= $XenuxDB->count('news');

		$newsList = $XenuxDB->getList('news', [
			'order' => 'create_date DESC',
			'limit' => [$start, $amount]
		]);

		if ($newsList)
		{
			foreach($newsList as $news)
			{
				$template = new template(PATH_MAIN."/modules/".$this->modulename."/layout_list.php");

				$template->setVar("news_content", shortstr(strip_tags($news->text), 200, 300));
				$template->setVar("news_title", $news->title);
				$template->setVar("news_title_url", urlencode($news->title));
				$template->setVar("news_createDate", pretty_date($news->create_date));
				$template->setVar("news_ID", $news->id);

				echo $template->render();
			}

			echo getMenuBarMultiSites($absolutenumber, $start, $amount);

			$this->page_name = __('news_Pl');
		}
		else
		{
			echo "<p style=\"margin:5px 0;\">" . __('noNews') . "!</p>";
		}
	}

	private function newsView()
	{
		global $app, $XenuxDB;

		$newsID = explode('-', @$this->url[2])[0];
		$newsID = preg_replace("/[^0-9]/", '', $newsID);

		$news = $XenuxDB->getEntry('news', [
			'where' => [
				'id' => $newsID
			]
		]);

		if ($news)
		{
			$template = new template(PATH_MAIN."/modules/".$this->modulename."/layout_view.php");

			$template->setVar("news_content", $news->text);
			$template->setVar("news_title", $news->title);
			$template->setVar("news_createDate", mysql2date("d.m.Y H:i", $news->create_date));

			echo $template->render();

			$this->page_name = $news->title;
			$app->canonical_URL = URL_MAIN . '/' . getPreparedLink($news->id, $news->title);
		}
		else
		{
			header('HTTP/1.1 404 Not Found');
			throw new Exception(__('error404msg'));
		}
	}
}
