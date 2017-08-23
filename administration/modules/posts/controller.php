<?php
class postsController extends AbstractController
{
	private $editPostID;

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

		if(@$this->url[1] == "home")
		{
			$this->postsHome();
		}
		elseif(@$this->url[1] == "edit")
		{
			if(isset($this->url[2]) && is_numeric($this->url[2]) && !empty($this->url[2]))
			{
				$this->editPostID = $this->url[2];
				$this->postEdit();
			}
			else
			{
				throw new Exception(__('isWrong', 'POST ID'));
			}
		}
		elseif(@$this->url[1] == "new")
		{
			$this->postEdit(true);
		}
		else
		{
			throw new Exception("404 - $this->modulename template not found");
		}

		return true;
	}

	private function postsHome()
	{
		global $app, $XenuxDB;

		$template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout_home.php");
		$template->setVar("messages", '');


		// #TODO: merge action and remove in every module/list
		if (isset($_GET['apply-action']) && isset($_GET['action']) && in_array($_GET['action'], ['publish', 'draft', 'trash'])
			&& isset($_GET['item']) && is_array($_GET['item']))
		{
			foreach ($_GET['item'] as $item) {
				if (is_numeric($item)) {
					switch ($_GET['action']) {
						case 'publish':
						case 'draft':
							$XenuxDB->Update('posts', [
								'status' => $_GET['action']
							], [
									'id' => $item
							]);
							break;
						case 'trash':
							if(@$_GET['filter'] == 'trash')
							{ // delete in trash
								$XenuxDB->delete('posts', [
									'where' => [
										'id' => $item
									]
								]);
							}
							else
							{ // move in trash
								$XenuxDB->Update('posts', [
									'status' => 'trash'
								], [
									'id' => $item
								]);
							}
							break;
					}
					$template->setVar('messages',
						'<p class="box-shadow info-message ok">' . __('batch processing successful') . '</p>');
				}
			}
		}

		$filter = 'publish'; // default filter
		if (isset($_GET['apply-filter']) && isset($_GET['filter']) && in_array($_GET['filter'], ['publish', 'draft', 'trash']))
		{
			$filter = $_GET['filter'];
		}

		$amount = $XenuxDB->Count('posts', ['where' => ['status' => $filter]]);
		$amountPublish = $XenuxDB->Count('posts', ['where' => ['status' => 'publish']]);
		$amountDraft = $XenuxDB->Count('posts', ['where' => ['status' => 'draft']]);
		$amountTrash = $XenuxDB->Count('posts', ['where' => ['status' => 'trash']]);

		$template->setVar('posts', $this->getPostTable($filter));
		$template->setVar('amount', $amount);
		$template->setVar('amountPublish', $amountPublish);
		$template->setVar('amountDraft', $amountDraft);
		$template->setVar('amountTrash', $amountTrash);

		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');
		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');

		echo $template->render();

		$this->page_name = __('home');
		$this->headlineSuffix = '<a class="btn-new" href="{{URL_ADMIN}}/' . $this->modulename . '/new">' . __('new') . '</a>';
	}

	private function getPostTable($filter)
	{
		global $XenuxDB;

		$return = '';

		$posts = $XenuxDB->getList('posts', [
			'columns'=> [
				'posts.id(post_id)',
				'posts.title',
				'posts.status',
				'posts.create_date',
				'users.username',
			],
			'join' => [
				'[>]users' => ['posts.author_id' => 'users.id']
			],
			'order' => 'title DESC',
			'where' => [
				'status' => $filter
			]
		]);
		if($posts)
		{
			foreach($posts as $post)
			{
				$return .= '
<tr>
	<td class="column-select"><input type="checkbox" name="item[]" value="' . $post->post_id . '"></td>
	<td class="column-id">' . $post->post_id . '</td>
	<td class="column-title">
		<a class="edit" href="{{URL_ADMIN}}/' . $this->modulename . '/edit/' . $post->post_id . '" title="' . __('click to edit') . '">' . $post->title . ($post->status == 'draft' ? ' <span class="draft-hint">(' . __('draft') . ')</span>' : '') . '</a>
	</td>
	<td class="column-date">' . $post->create_date . '</td>
	<td class="column-author">' . $post->username . '</td>
	<td class="column-actions">
		<a class="view-btn" target="_blank" href="{{URL_MAIN}}/' . $this->modulename . '/view/' . getPreparedLink($post->post_id, $post->title) . '">' . __('view') . '</a>
		<a href="{{URL_ADMIN}}/' . $this->modulename . '/home/?apply-filter&filter=' . $filter . '&apply-action&action=trash&item[]=' . $post->post_id . '" title="' . __('delete') . '" class="remove-btn"></a>
	</td>
</tr>';;
			}
		}

		return $return;
	}


	private function postEdit($new=false)
	{
		$template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout_edit.php");

		$template->setVar("messages", '');
		$template->setVar("form", $this->getEditForm($template, $new));

		$template->setIfCondition("new", $new);

		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');
		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');

		echo $template->render();

		$this->page_name = $new ? __('new') : __('edit');
	}

	private function getEditForm(&$template, $new=false)
	{
		global $XenuxDB, $app;

		if(!$new)
			$post = $XenuxDB->getEntry('posts', [
				'join' => [
					'[>]users' => ['posts.author_id' => 'users.id']
				],
				'where' => [
					'posts.id' => $this->editPostID
				]
			]);

		if(!@$post && !$new)
			throw new Exception("error (post 404)");

		$formFields = array
		(
			'title' => array
			(
				'type' => 'text',
				'required' => true,
				'label' => __('title'),
				'value' => @$post->title,
				'class' => 'full_page'
			),
			'text' => array
			(
				'type' => 'textarea',
				'required' => true,
				'label' => __('desc'),
				'value' => htmlentities(@$post->text),
				'wysiwyg' => true,
				'showLabel' => false
			),
			'status' => array
			(
				'type' => 'select',
				'required' => true,
				'label' => __('status'),
				'value' => @$post->status,
				'options' => [
					[
						'value' => 'publish',
						'label' => __('publish')
					],
					[
						'value' => 'draft',
						'label' => __('draft')
					]
				]
			),
			'author' => array
			(
				'type' => 'readonly',
				'label' => __('author'),
				'value' => isset($news) ? $post->username : $app->user->userInfo->username
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

		$_allowedTags = "<font><b><strong><a><i><em><u><span><div><p><img><ol><ul><li><h1><h2><h3><h4><h5><h6><table><tr><td><th><br><hr><code><pre><del><ins><blockquote><sub><sup><address><q><cite><var><samp><kbd><tt><small><big><s><caption><tbody><thead><tfoot><param>";

		$form = new form($formFields);
		$form->disableRequiredInfo();

		if($form->isSend() && isset($form->getInput()['cancel']))
		{
			header('Location: '.URL_ADMIN.'/posts/home');
			return false;
		}

		if($form->isSend() && $form->isValid())
		{
			$data = $form->getInput();

			$title  = preg_replace('/[^a-zA-Z0-9_üÜäÄöÖ$€&#,.()\s]/' , '' , $data['title']);
			$text   = strip_tags($data['text'], $_allowedTags);
			$status = in_array($data['status'], ['publish', 'draft', 'trash']) ? $data['status'] : 'draft';
			$author = $app->user->userInfo->id;

			if($new)
			{
				#TODO: add thumbnail
				$post = $XenuxDB->Insert('posts', [
					'title'             => $title,
					'text'              => $text,
					'status'            => $status,
					'author_id'         => $author,
					'create_date'       => date('Y-m-d H:i:s'),
					'lastModified_date' => date('Y-m-d H:i:s')
				]);

				if($post)
				{
					$return = true;
					$this->editPostID = $post;
				}
				else
				{
					$return = false;
				}
			}
			else
			{
				// update it
				$return = $XenuxDB->Update('posts', [
					'title'             => $title,
					'text'              => $text,
					'status'            => $status,
					'lastModified_date' => date('Y-m-d H:i:s')
				],
				[
					'id' => $this->editPostID
				]);
			}

			if($return === true)
			{
				log::debug('post saved successful');
				$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');

				if(isset($data['submit_close']))
				{
					header('Location: '.URL_ADMIN.'/posts/home?savingSuccess=true');
					return false;
				}

				header('Location: '.URL_ADMIN.'/posts/edit/'.$this->editPostID.'?savingSuccess=true');
			}
			else
			{
				log::debug('post saving failed');
				$template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');

				if(isset($data['submit_close']) || $new)
				{
					header('Location: '.URL_ADMIN.'/posts/home?savingSuccess=false');
					return false;
				}

				header('Location: '.URL_ADMIN.'/posts/edit/'.$this->editPostID.'?savingSuccess=false');
			}
		}
		return $form->getForm();
	}
}
