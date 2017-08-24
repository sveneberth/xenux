<?php
class postsController extends AbstractController
{
	public function __construct($url)
	{
		parent::__construct($url);
	}

	public function run()
	{
		if (@$this->url[1] == 'list')
		{
			$this->postList();
		}
		elseif (@$this->url[1] == 'view')
		{
			$this->postView();
		}
		else
		{
			header('HTTP/1.1 404 Not Found');
			throw new Exception(__('error404msg'));
			//throw new Exception('404 - $this->modulename template not found');
		}
		return true;
	}

	private function postList()
	{
		global $XenuxDB;

		echo '<div class="page-header"><h1>' . __('posts') . '</h1></div>';

		$start			= is_numeric(@$_GET['start']) ? floor($_GET['start']) : 0;
		$amount			= (is_numeric(@$_GET['amount']) && floor(@$_GET['amount']) != 0) ? floor($_GET['amount']) : 10;
		$absolutenumber	= $XenuxDB->count('posts');

		$posts = $XenuxDB->getList('posts', [
			'order' => 'create_date DESC',
			'limit' => [$start, $amount],
			'where' => [
				'status' => 'publish'
			]
		]);

		if ($posts)
		{
			foreach($posts as $post)
			{
				$template = new template(PATH_MAIN . '/modules/' . $this->modulename . '/layout_list.php');

				$template->setVar('post_content', shortstr(strip_tags($post->text), 200, 300));
				$template->setVar('post_title', $post->title);
				$template->setVar('post_title_url', urlencode($post->title));
				$template->setVar('post_createDate', pretty_date($post->create_date));
				$template->setVar('post_ID', $post->id);

				echo $template->render();
			}

			echo getMenuBarMultiSites($absolutenumber, $start, $amount);

			$this->page_name = __('posts');
		}
		else
		{
			echo '<p style="margin:5px 0;">' . __('noPosts') . '!</p>';
		}
	}

	private function postView()
	{
		global $app, $XenuxDB;

		$postID = explode('-', @$this->url[2])[0];
		$postID = preg_replace("/[^0-9]/", '', $postID);

		$post = $XenuxDB->getEntry('posts', [
			'where' => [
			'AND' => [
					'id' => $postID,
					'status' => 'publish'
				]
			]
		]);

		if ($post)
		{
			$template = new template(PATH_MAIN . '/modules/' . $this->modulename . '/layout_view.php');

			$template->setVar('post_content', $post->text);
			$template->setVar('post_title', $post->title);
			$template->setVar('post_createDate', mysql2date('d.m.Y H:i', $post->create_date));

			echo $template->render();

			$this->page_name = $post->title;
			$app->canonical_URL = URL_MAIN . '/' . getPreparedLink($post->id, $post->title);
		}
		else
		{
			header('HTTP/1.1 404 Not Found');
			throw new Exception(__('error404msg'));
		}
	}
}
