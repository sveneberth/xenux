<?php
class eventController extends AbstractController
{
	public function __construct($url)
	{
		parent::__construct($url);
	}

	public function run()
	{
		// append translations
		translator::appendTranslations(MAIN_PATH . '/modules/event/translation/');

		if (@$this->url[1] == "calendar")
		{
			$this->eventCalendar();
		}
		elseif (@$this->url[1] == "view")
		{
			$this->eventView();
		}
		else
		{
			header('HTTP/1.1 404 Not Found');
			throw new Exception(__('error404msg'));
			//throw new Exception("404 - $this->modulename template not found");
		}
		return true;
	}

	private function eventCalendar()
	{
		global $XenuxDB;

		include_once(MAIN_PATH."/modules/".$this->modulename.'/calendar.php');

		$calendar = new Calendar();
		$calendar->render();

		$this->page_name = __('calendar');
	}

	private function eventView()
	{
		global $app, $XenuxDB;

		$eventID = explode('-', @$this->url[2])[0];
		$eventID = preg_replace("/[^0-9]/", '', $eventID);

		$event = $XenuxDB->getEntry('events', [
			'where' => [
				'id' => $eventID
			]
		]);

		if ($event)
		{
			$template = new template(MAIN_PATH."/modules/".$this->modulename."/layout_view.php");

			$template->setVar("event_content", $event->text);
			$template->setVar("event_start", mysql2date("d.m.Y H:i", $event->start_date));
			$template->setVar("event_end", mysql2date("d.m.Y H:i", $event->end_date));

			echo $template->render();

			$this->page_name = $event->title;
			$app->canonical_URL = MAIN_URL . '/' . getPreparedLink($event->id, $event->title);
		}
		else
		{
			header('HTTP/1.1 404 Not Found');
			throw new Exception(__('error404msg'));
		}
	}
}
