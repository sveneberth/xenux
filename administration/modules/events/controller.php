<?php
class eventsController extends AbstractController
{
	private $editID;

	public function __construct($url)
	{
		parent::__construct($url);

		if (!isset($this->url[1]) || empty($this->url[1]))
			header("Location: ".URL_ADMIN.'/'.$this->modulename.'/home');
	}

	public function run()
	{
		global $XenuxDB, $app;

		// append translations
		translator::appendTranslations(PATH_ADMIN . '/modules/'.$this->modulename.'/translation/');

		if (@$this->url[1] == "home")
		{
			$this->eventsHome();
		}
		elseif (@$this->url[1] == "edit")
		{
			if (isset($this->url[2]) && is_numeric($this->url[2]) && !empty($this->url[2]))
			{
				$this->editID = $this->url[2];
				$this->eventEdit();
			}
			else
			{
				throw new Exception(__('isWrong', 'EVENT ID'));
			}
		}
		elseif (@$this->url[1] == "new")
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

		if (isset($_GET['remove']) && is_numeric($_GET['remove']) && !empty($_GET['remove']))
		{
			$XenuxDB->delete('events', [
				'where' => [
					'id' => $_GET['remove']
				]
			]);
			$template->setVar('messages', '<p class="box-shadow info-message ok">'.__('removedSuccessful').'</p>');
		}

		if (isset($_GET['action']) && in_array($_GET['action'], ['private', 'public', 'remove'])
			&& isset($_GET['item']) && is_array($_GET['item']))
		{
			foreach ($_GET['item'] as $item) {
				if (is_numeric($item)) {
					switch ($_GET['action']) {
						case 'private':
						case 'public':
							$XenuxDB->Update('events', [
								'public' => $_GET['action']=='public' ? true : false
							], [
									'id' => $item
							]);
							break;
						case 'remove':
							$XenuxDB->delete('events', [
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


		$template->setVar("events", $this->getEventTable());
		$template->setVar("amount", $XenuxDB->count('events'));

		if (isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');
		if (isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');

		echo $template->render();

		$this->page_name = __('home');
		$this->headlineSuffix = '<a class="btn-new" href="{{URL_ADMIN}}/events/new">' . __('new') . '</a>';
	}

	private function getEventTable()
	{
		global $XenuxDB;

		$return = '';

		$events = $XenuxDB->getList('events', [
			'order' => 'start_date DESC'
		]);
		if ($events)
		{
			foreach($events as $event)
			{
				$return .= '
<tr ' . ($event->public == false ? 'class="private"' : '') . '>
	<td class="column-select"><input type="checkbox" name="item[]" value="' . $event->id . '"></td>
	<td class="column-id">' . $event->id . '</td>
	<td class="column-title">
		<a class="edit" href="{{URL_ADMIN}}/events/edit/' . $event->id . '" title="' . __('click to edit event') . '">' . $event->title . '</a>
	</td>
	<td class="column-date">' . $event->create_date . '</td>
	<td class="column-date">' . $event->start_date . '</td>
	<td class="column-date">' . $event->end_date . '</td>
	<td class="column-actions">
		<a class="view-btn" target="_blank" href="{{URL_MAIN}}/event/view/' . getPreparedLink($event->id, $event->title) . '">' . __('view') . '</a>
		<a href="{{URL_ADMIN}}/events/home/?remove=' . $event->id . '" title="' . __('delete') . '" class="remove-btn">
			<svg xmlns="http://www.w3.org/2000/svg" height="32px" version="1.1" viewBox="0 0 32 32" width="32px">
				 <path fill-rule="evenodd" d="M21.333 3.556h4.741V4.74H5.926V3.556h4.74V2.37c0-1.318 1.06-2.37 2.368-2.37h5.932a2.37 2.37 0 0 1 2.367 2.37v1.186zM5.926 5.926v22.517A3.55 3.55 0 0 0 9.482 32h13.036a3.556 3.556 0 0 0 3.556-3.557V5.926H5.926zm4.74 3.555v18.963h1.186V9.481h-1.185zm4.741 0v18.963h1.186V9.481h-1.186zm4.741 0v18.963h1.185V9.481h-1.185zm-7.107-8.296c-.657 0-1.19.526-1.19 1.185v1.186h8.297V2.37c0-.654-.519-1.185-1.189-1.185h-5.918z"/>
			</svg>
		</a>
	</td>
</tr>';
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

		if (isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');
		if (isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');

		echo $template->render();

		$this->page_name = $new ? __('new') : __('edit');
	}

	private function getEditForm(&$template, $new=false)
	{
		global $XenuxDB, $app;

		if (!$new)
			$event = $XenuxDB->getEntry('events', [
				'join' => [
					'[>]users' => ['events.author_id' => 'users.id']
				],
				'where' => [
					'events.id' => $this->editID
				]
			]);

		if (!@$event && !$new)
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
				'type' => 'wysiwyg',
				'label' => __('eventDesc'),
				'value' => @$event->text,
				'showLabel' => false
			),
			'public' => array
			(
				'type' => 'checkbox',
				'required' => true,
				'label' => __('eventPublic'),
				'value' => 'true',
				'checked' => @$event->public
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

		$form = new form($formFields);
		$form->disableRequiredInfo();

		if ($form->isSend() && isset($form->getInput()['cancel']))
		{
			header('Location: '.URL_ADMIN.'/evens/home');
			return false;
		}

		if ($form->isSend() && $form->isValid())
		{
			$data = $form->getInput();

			$title      = $data['title'];
			$text       = $data['text'];
			$start_date = $data['startDate']	. ' ' . $data['startTime'];
			$end_date   = $data['endDate']		. ' ' . $data['endTime'];
			$public     = parse_bool($data['public']);
			$author     = $app->user->userInfo->id;

			if ($new)
			{
				$event = $XenuxDB->Insert('events', [
					'title'				=> $title,
					'text'				=> $text,
					'public'			=> $public,
					'author_id'			=> $author,
					'create_date'		=> date('Y-m-d H:i:s'),
					'start_date'		=> $start_date,
					'end_date'			=> $end_date
				]);

				if ($event)
				{
					$return = true;
					$this->editID = $event;
				}
				else
				{
					$return = false;
				}
			}
			else
			{
				// update it
				$return = $XenuxDB->Update('events', [
					'title'				=> $title,
					'text'				=> $text,
					'public'			=> $public,
					'start_date'		=> $start_date,
					'end_date'			=> $end_date
				],
				[
					'id' => $this->editID
				]);
			}

			if ($return === true)
			{
				log::debug('event saved successful');
				$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');

				if (isset($data['submit_close']))
				{
					header('Location: '.URL_ADMIN.'/events/home?savingSuccess=true');
					return false;
				}

				header('Location: '.URL_ADMIN.'/events/edit/'.$this->editID.'?savingSuccess=true');
			}
			else
			{
				log::debug('event saving failed');
				$template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');

				if (isset($data['submit_close']) || $new)
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
