<?php
class postsController extends AbstractController
{
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

		$this->page_name = __('posts');

		$start			= is_numeric(@$_GET['start']) ? floor($_GET['start']) : 0;
		$amount			= (is_numeric(@$_GET['amount']) && floor(@$_GET['amount']) != 0) ? floor($_GET['amount']) : 10;
		$absolutenumber	= $XenuxDB->count('posts');

		$posts = $XenuxDB->getList('posts', [
			'columns' => [
				'posts.id(post_id)',
				'posts.text',
				'posts.title',
				'posts.create_date',
				'files.id(file_id)',
				'files.filename',
				'users.username'
			],
			'order' => 'create_date DESC',
			'limit' => [$start, $amount],
			'where' => [
				'status' => 'publish'
			],
			'join' => [
				'[>]files' => ['posts.thumbnail_id' => 'files.id'],
				'[>]users' => ['posts.author_id' => 'users.id']
			]
		]);

		if ($posts)
		{
			foreach ($posts as $post)
			{
				$template = new template(PATH_MAIN . '/modules/' . $this->modulename . '/layout_list.php');

				$template->setVar('post_content', shortstr(strip_tags($post->text), 400, 500));
				$template->setVar('post_author', $post->username);
				$template->setVar('post_title', $post->title);
				$template->setVar('post_title_url', urlencode($post->title));
				$template->setVar('post_createDate', pretty_date($post->create_date));
				$template->setVar('post_ID', $post->post_id);
				$template->setVar('post_imageURL', URL_MAIN . "/file/{$post->file_id}-" . urlencode($post->filename));
				$template->setVar('post_imageTitle', $post->filename);

				$template->setIfCondition('hasThumbnail', !is_null($post->file_id));

				echo $template->render();
			}

			echo getMenuBarMultiSites($absolutenumber, $start, $amount);

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
			'columns' => [
				'posts.id(post_id)',
				'posts.text',
				'posts.title',
				'posts.create_date',
				'files.id(file_id)',
				'files.filename',
				'users.username'
			],
			'where' => [
				'AND' => [
					'posts.id' => $postID,
					'status' => 'publish'
				]
			],
			'join' => [
				'[>]files' => ['posts.thumbnail_id' => 'files.id'],
				'[>]users' => ['posts.author_id' => 'users.id']
			]
		]);

		if ($post)
		{
			$template = new template(PATH_MAIN . '/modules/' . $this->modulename . '/layout_view.php',
			[
				'author' => $post->username,
				'date'   => mysql2date('d.m.Y', $post->create_date),
				'time'   => mysql2date('H.i', $post->create_date)
			]);

			$template->setVar('post_content', $post->text);
			$template->setVar('post_imageURL', URL_MAIN . "/file/{$post->file_id}-" . urlencode($post->filename));
			$template->setVar('post_imageTitle', $post->filename);

			$template->setIfCondition("show_meta_info", parse_bool($app->getOption('sites_show_meta_info')));

			echo $template->render();

			$this->page_name = $post->title;
			$app->canonical_URL = URL_MAIN . '/' . getPreparedLink($post->post_id, $post->title);
		}
		else
		{
			header('HTTP/1.1 404 Not Found');
			throw new Exception(__('error404msg'));
		}
	}
}
