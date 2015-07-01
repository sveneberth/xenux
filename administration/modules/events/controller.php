<?php
class eventsController extends AbstractController
{
	private $editID;

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
			$this->eventsHome();
		}
		elseif(@$this->url[1] == "edit")
		{
			if(isset($this->url[2]) && is_numeric($this->url[2]) && !empty($this->url[2]))
			{
				$this->editID = $this->url[2];
				$this->eventEdit();
			}
			else
			{
				throw new Exception(__('isWrong', 'EVENT ID'));
			}
		}
		elseif(@$this->url[1] == "new")
		{
			$this->eventEdit(true);
		}
		else
		{
			throw new Exception("404 - $this->modulename template not found");
		}

		return true;
	}

	private function eventsHome()
	{
		global $app, $XenuxDB;

		$template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout_home.php");
	
		$template->setVar("messages", '');

		if(isset($_GET['remove']) && is_numeric($_GET['remove']) && !empty($_GET['remove']))
		{
			$XenuxDB->delete('events', [
				'where' => [
					'id' => $_GET['remove']
				]
			]);
			$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('removedSuccessful').'</p>');
		}
		
		$template->setVar("events", $this->getEventTable());

		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');
		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');
		
		echo $template->render();

		$this->page_name = __('home');
	}

	private function getEventTable()
	{
		global $XenuxDB;

		$return = '';

		$events = $XenuxDB->getList('events', [
			'order' => 'start_date DESC'
		]);
		if($events)
		{
			foreach($events as $event)
			{
				$return .= '
<li '.($event->public == false ? 'class="non-public"' : '').'>
	<span class="data-column event-id">'.$event->id.'</span>
	<a class="data-column event-title edit" href="{{URL_ADMIN}}/events/edit/'.$event->id.'" title="'.__('click to edit events').'">'.$event->title.'</a>
	<span class="data-column event-create-date">'.$event->create_date.'</span>
	<span class="data-column event-start-date">'.$event->start_date.'</span>
	<span class="data-column event-end-date">'.$event->end_date.'</span>
	<a class="data-column show" target="_blank" href="{{URL_MAIN}}/event/view/'.getPreparedLink($event->id, $event->title).'">'.__('show').'</a>
	<a href="{{URL_ADMIN}}/events/home/?remove='.$event->id.'" title="'.__('deleteEvent').'" class="remove remove-icon clickable"></a>
</li>';
			}
		}

		return $return;
	}


	private function eventEdit($new=false)
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
			$event = $XenuxDB->getEntry('events', [
				'join' => [
					'[>]users' => ['events.author_id' => 'users.id']
				],
				'where' => [
					'events.id' => $this->editID
				]
			]);

		if(!@$event && !$new)
			throw new Exception("error (evnts 404)");
			
		$formFields = array
		(
			'title' => array
			(
				'type' => 'text',
				'required' => true,
				'label' => __('title'),
				'value' => @$event->title,
				'class' => 'full_page'
			),
			'startDate' => array
			(
				'type' => 'date',
				'required' => true,
				'label' => __('startDate'),
				'value' => mysql2date('Y-m-d', @$event->start_date),
				'class' => ''
			),
			'startTime' => array
			(
				'type' => 'time',
				'required' => true,
				'label' => __('startTime'),
				'value' => mysql2date('H:i:s', @$event->start_date),
				'class' => ''
			),
			'endDate' => array
			(
				'type' => 'date',
				'required' => true,
				'label' => __('endDate'),
				'value' => mysql2date('Y-m-d', @$event->end_date),
				'class' => ''
			),
			'endTime' => array
			(
				'type' => 'time',
				'required' => true,
				'label' => __('endTime'),
				'value' => mysql2date('H:i:s', @$event->end_date),
				'class' => ''
			),
			'text' => array
			(
				'type' => 'textarea',
				'required' => true,
				'label' => __('eventDesc'),
				'value' => htmlentities(@$event->text),
				'wysiwyg' => true,
				'showLabel' => false
			),
			'public' => array
			(
				'type' => 'bool_radio',
				'required' => true,
				'label' => __('eventPublic'),
				'value' => @$event->public
			),
			'author' => array
			(
				'type' => 'readonly',
				'label' => __('author'),
				'value' => isset($event) ? $event->username : $app->user->userInfo->username
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

			$author = $app->user->userID;

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
					$this->editID = $news;
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
					'id' => $this->editID
				]);
			}

			if($return === true)
			{
				if ((defined('DEBUG') && DEBUG == true))
					log::writeLog('event saved successful');
				$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');

				if(isset($data['submit_close']))
				{
					header('Location: '.URL_ADMIN.'/events/home?savingSuccess=true');
					return false;
				}

				header('Location: '.URL_ADMIN.'/events/edit/'.$this->editID.'?savingSuccess=true');
			}
			else
			{
				if ((defined('DEBUG') && DEBUG == true))
					log::writeLog('event saving failed');
				$template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');

				if(isset($data['submit_close']))
				{
					header('Location: '.URL_ADMIN.'/events/home?savingSuccess=false');
					return false;
				}
				
				header('Location: '.URL_ADMIN.'/events/edit/'.$this->editID.'?savingSuccess=false');
			}			
		}
		return $form->getForm();
	}
}
?>