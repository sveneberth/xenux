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

		// #TODO: merge action and remove in every module/list
		if (isset($_GET['apply-action']) && isset($_GET['action']) && in_array($_GET['action'], ['publish', 'draft', 'trash'])
			&& isset($_GET['item']) && is_array($_GET['item']))
		{
			foreach ($_GET['item'] as $item) {
				if (is_numeric($item)) {
					switch ($_GET['action']) {
						case 'publish':
						case 'draft':
							$XenuxDB->Update('events', [
								'status' => $_GET['action']
							], [
									'id' => $item
							]);
							break;
						case 'trash':
							if (@$_GET['filter'] == 'trash')
							{ // delete in trash
								$XenuxDB->delete('events', [
									'where' => [
										'id' => $item
									]
								]);
							}
							else
							{ // move in trash
								$XenuxDB->Update('events', [
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

		$amount        = $XenuxDB->Count('events', ['where' => ['status' => $filter]]);
		$amountPublish = $XenuxDB->Count('events', ['where' => ['status' => 'publish']]);
		$amountDraft   = $XenuxDB->Count('events', ['where' => ['status' => 'draft']]);
		$amountTrash   = $XenuxDB->Count('events', ['where' => ['status' => 'trash']]);

		$template->setVar('events', $this->getEventTable($filter));
		$template->setVar('amount', $amount);
		$template->setVar('amountPublish', $amountPublish);
		$template->setVar('amountDraft', $amountDraft);
		$template->setVar('amountTrash', $amountTrash);

		if (isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');
		if (isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');

		echo $template->render();

		$app->addJS(URL_TEMPLATE . '/js/jquery.tablesorter.min.js');
		$app->addJS(URL_ADMIN . '/modules/' . $this->modulename . '/script.js');
		$this->page_name = __('home');
		$this->headlineSuffix = '<a class="btn-new" href="{{URL_ADMIN}}/events/new">' . __('new') . '</a>';
	}

	private function getEventTable($filter)
	{
		global $XenuxDB;

		$return = '';

		$events = $XenuxDB->getList('events', [
			'order' => 'start_date DESC',
			'where' => [
				'status' => $filter
			]
		]);
		if ($events)
		{
			foreach ($events as $event)
			{
				$return .= '
<tr class="is-' . $event->status . '">
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
			' . embedSVG(PATH_ADMIN . '/template/images/trash.svg') . '
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
				'type'     => 'text',
				'required' => true,
				'label'    => __('title'),
				'value'    => @$event->title,
				'class'    => 'full_page'
			),
			'startDate' => array
				(
				'type'     => 'date',
				'required' => true,
				'label'    => __('startDate'),
				'value'    => mysql2date('Y-m-d', @$event->start_date),
				'class'    => ''
			),
			'startTime' => array
			(
				'type'     => 'time',
				'required' => true,
				'label'    => __('startTime'),
				'value'    => mysql2date('H:i:s', @$event->start_date),
				'class'    => ''
			),
			'endDate' => array
			(
				'type'     => 'date',
				'required' => true,
				'label'    => __('endDate'),
				'value'    => mysql2date('Y-m-d', @$event->end_date),
				'class'    => ''
			),
			'endTime' => array
			(
				'type'     => 'time',
				'required' => true,
				'label'    => __('endTime'),
				'value'    => mysql2date('H:i:s', @$event->end_date),
				'class'    => ''
			),
			'text' => array
			(
				'type'      => 'wysiwyg',
				'label'     => __('eventDesc'),
				'value'     => @$event->text,
				'showLabel' => false
			),
			'status' => array
			(
				'type'     => 'select',
				'required' => true,
				'label'    => __('status'),
				'value'    => @$events->status,
				'options'  => [
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
				'type'  => 'readonly',
				'label' => __('author'),
				'value' => isset($event) ? $event->username : $app->user->userInfo->username
			),
			'submit_stay' => array
			(
				'type'  => 'submit',
				'label' => __('save&stay'),
				'class' => 'floating'
			),
			'submit_close' => array
			(
				'type'  => 'submit',
				'label' => __('save&close'),
				'class' => 'floating space-left'
			),
			'cancel' => array
			(
				'type'  => 'submit',
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
			$start_date = $data['startDate'] . ' ' . $data['startTime'];
			$end_date   = $data['endDate']   . ' ' . $data['endTime'];
			$status     = in_array($data['status'], ['publish', 'draft', 'trash']) ? $data['status'] : 'draft';
			$author     = $app->user->userInfo->id;

			if ($new)
			{
				$event = $XenuxDB->Insert('events', [
					'title'       => $title,
					'text'        => $text,
					'status'      => $status,
					'author_id'   => $author,
					'create_date' => date('Y-m-d H:i:s'),
					'start_date'  => $start_date,
					'end_date'    => $end_date
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
					'title'      => $title,
					'text'       => $text,
					'status'     => $status,
					'start_date' => $start_date,
					'end_date'   => $end_date
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

				header('Location: '.URL_ADMIN.'/events/edit/' . $this->editID . '?savingSuccess=true');
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

				header('Location: '.URL_ADMIN.'/events/edit/' . $this->editID . '?savingSuccess=false');
			}
		}
		return $form->getForm();
	}
}
