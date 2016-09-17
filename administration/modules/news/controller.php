<?php
class newsController extends AbstractController
{
	private $editNewsID;

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
			$this->newsHome();
		}
		elseif(@$this->url[1] == "edit")
		{
			if(isset($this->url[2]) && is_numeric($this->url[2]) && !empty($this->url[2]))
			{
				$this->editNewsID = $this->url[2];
				$this->newsEdit();
			}
			else
			{
				throw new Exception(__('isWrong', 'NEWS ID'));
			}
		}
		elseif(@$this->url[1] == "new")
		{
			$this->newsEdit(true);
		}
		else
		{
			throw new Exception("404 - $this->modulename template not found");
		}

		return true;
	}

	private function newsHome()
	{
		global $app, $XenuxDB;

		$template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout_home.php");
		$template->setVar("messages", '');

		if(isset($_GET['remove']) && is_numeric($_GET['remove']) && !empty($_GET['remove']))
		{
			$XenuxDB->delete('news', [
				'where' => [
					'id' => $_GET['remove']
				]
			]);
			$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('removedSuccessful').'</p>');
		}

		if (isset($_GET['action']) && in_array($_GET['action'], ['private', 'public', 'remove'])
			&& isset($_GET['item']) && is_array($_GET['item']))
		{
			foreach ($_GET['item'] as $item) {
				if (is_numeric($item)) {
					switch ($_GET['action']) {
						case 'private':
						case 'public':
							$XenuxDB->Update('news', [
								'public' => $_GET['action']=='public' ? true : false
							], [
									'id' => $item
							]);
							break;
						case 'remove':
							$XenuxDB->delete('news', [
								'where' => [
									'id' => $item
								]
							]);
							break;
					}
					$template->setVar('messages',
						'<p class="box-shadow info-message ok">' . __('batch processing successful') . '</p>');
				}
			}
		}

		$template->setVar("news", $this->getNewsTable());
		$template->setVar("amount", $XenuxDB->count('news'));

		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');
		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');

		echo $template->render();

		$this->page_name = __('home');
		$this->headlineSuffix = '<a class="btn-new" href="{{URL_ADMIN}}/news/new">' . __('new') . '</a>';
	}

	private function getNewsTable()
	{
		global $XenuxDB;

		$return = '';

		$news = $XenuxDB->getList('news', [
			'order' => 'create_date DESC'
		]);
		if($news)
		{
			foreach($news as $subnews)
			{
				$return .= '
<tr ' . ($subnews->public == false ? 'class="private"' : '') . '>
	<td class="column-select"><input type="checkbox" name="item[]" value="' . $subnews->id . '"></td>
	<td class="column-id">' . $subnews->id . '</td>
	<td class="column-title">
		<a class="edit" href="{{URL_ADMIN}}/news/edit/' . $subnews->id . '" title="' . __('click to edit news') . '">' . $subnews->title . '</a>
	</td>
	<td class="column-date">' . $subnews->create_date . '</td>
	<td class="column-text">' . shortstr(strip_tags($subnews->text), 50, 100) . '</td>
	<td class="column-actions">
		<a class="view-btn" target="_blank" href="{{URL_MAIN}}/news/view/' . getPreparedLink($subnews->id, $subnews->title) . '">' . __('view') . '</a>
		<a href="{{URL_ADMIN}}/news/home/?remove=' . $subnews->id . '" title="' . __('delete') . '" class="remove-btn"></a>
	</td>
</tr>';;
			}
		}

		return $return;
	}


	private function newsEdit($new=false)
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
			$news = $XenuxDB->getEntry('news', [
				'join' => [
					'[>]users' => ['news.author_id' => 'users.id']
				],
				'where' => [
					'news.id' => $this->editNewsID
				]
			]);

		if(!@$news && !$new)
			throw new Exception("error (news 404)");

		$formFields = array
		(
			'title' => array
			(
				'type' => 'text',
				'required' => true,
				'label' => __('title'),
				'value' => @$news->title,
				'class' => 'full_page'
			),
			'text' => array
			(
				'type' => 'textarea',
				'required' => true,
				'label' => __('newsContent'),
				'value' => htmlentities(@$news->text),
				'wysiwyg' => true,
				'showLabel' => false
			),
			'public' => array
			(
				'type' => 'checkbox',
				'label' => __('newsPublic'),
				'value' => 'true',
				'checked' => @$news->public
			),
			'author' => array
			(
				'type' => 'readonly',
				'label' => __('author'),
				'value' => isset($news) ? $news->username : $app->user->userInfo->username
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
			header('Location: '.URL_ADMIN.'/news/home');
			return false;
		}

		if($form->isSend() && $form->isValid())
		{
			$data = $form->getInput();

			$title = preg_replace('/[^a-zA-Z0-9_üÜäÄöÖ$€&#,.()\s]/' , '' , $data['title']);

			$text = strip_tags($data['text'], $_allowedTags);

			$public = parse_bool($data['public']);

			$author = $app->user->userInfo->id;

			if($new)
			{
				$news = $XenuxDB->Insert('news', [
					'title'				=> $title,
					'text'				=> $text,
					'public'			=> $public,
					'author_id'			=> $author,
					'create_date'		=> date('Y-m-d H:i:s'),
					'lastModified_date'	=> date('Y-m-d H:i:s')
				]);

				if($news)
				{
					$return = true;
					$this->editNewsID = $news;
				}
				else
				{
					$return = false;
				}
			}
			else
			{
				// update it
				$return = $XenuxDB->Update('news', [
					'title'				=> $title,
					'text'				=> $text,
					'public'			=> $public,
					'lastModified_date'	=> date('Y-m-d H:i:s')
				],
				[
					'id' => $this->editNewsID
				]);
			}

			if($return === true)
			{
				if ((defined('DEBUG') && DEBUG == true))
					log::writeLog('news saved successful');
				$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');

				if(isset($data['submit_close']))
				{
					header('Location: '.URL_ADMIN.'/news/home?savingSuccess=true');
					return false;
				}

				header('Location: '.URL_ADMIN.'/news/edit/'.$this->editNewsID.'?savingSuccess=true');
			}
			else
			{
				if ((defined('DEBUG') && DEBUG == true))
					log::writeLog('news saving failed');
				$template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');

				if(isset($data['submit_close']))
				{
					header('Location: '.URL_ADMIN.'/news/home?savingSuccess=false');
					return false;
				}

				header('Location: '.URL_ADMIN.'/news/edit/'.$this->editNewsID.'?savingSuccess=false');
			}
		}
		return $form->getForm();
	}
}
