<?php
#TODO: default templates in self
class Calendar {

	private $dayLabels       = array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");

	private $preMonth        = 0;
	private $preYear         = 0;
	private $daysInPreMonth  = 0;

	private $currentYear     = 0;
	private $currentMonth    = 0;
	private $currentDay      = 0;
	private $currentDate     = null;

	private $nextMonth       = 0;
	private $nextYear        = 0;
	private $daysInNextMonth = 0;

	private $daysInMonth     = 0;


	public function __construct()
	{
	}

	public function render()
	{
		global $app;

		$year	= (isset($_GET['year'])  && preg_match("/[0-9]/", $_GET['year']))  ? $_GET['year']  : date("Y");
		$month	= (isset($_GET['month']) && preg_match("/[0-9]/", $_GET['month'])) ? $_GET['month'] : date("m");

		$this->currentDate = strtotime($year . '-' . $month . '-1'); // get date as unixtime

		$this->currentYear  = date("Y", $this->currentDate); // use to get year in 4 digits
		$this->currentMonth = date("m", $this->currentDate);

		$this->nextMonth = $this->currentMonth == 12	? 1 : intval($this->currentMonth)+1;
		$this->nextYear  = $this->currentMonth == 12	? intval($this->currentYear)+1 : $this->currentYear;
		$this->preMonth  = $this->currentMonth == 1	? 12 : intval($this->currentMonth)-1;
		$this->preYear   = $this->currentMonth == 1	? intval($this->currentYear)-1 : $this->currentYear;

		$this->daysInPreMonth  = $this->_daysInMonth($this->preMonth, $this->preYear);
		$this->daysInMonth     = $this->_daysInMonth($this->currentMonth, $this->currentYear);
		$this->daysInNextMonth = $this->_daysInMonth($this->nextMonth, $this->nextYear);


		$weeksInMonth = $this->_weeksInMonth($month, $year);

		$weeks = null;
			for ($week = 0; $week < $weeksInMonth; $week++)
			{

				$daysOfWeek = '';
				for ($day = 1; $day <= 7; $day++)
				{
					$daysOfWeek .= $this->_showDay($week * 7 + $day, $day);
				}

				$template = new template(MAIN_PATH."/templates/".$app->template."/_calendar_week.php");
				$template->setVar("daysOfWeek", $daysOfWeek);
				$weeks .= $template->render();
			}


		$template = new template(MAIN_PATH."/templates/".$app->template."/_calendar_layout.php");

		$template->setVar("prevUrlParam", "month=" . sprintf('%02d', $this->preMonth) . "&year=" . $this->preYear);
		$template->setVar("nextUrlParam", "month=" . sprintf('%02d', $this->nextMonth) . "&year=" . $this->nextYear);
		$template->setVar("year", $this->currentYear);
		$template->setVar("month", __(date('M', $this->currentDate)));

		$template->setVar("labels", $this->_createLabels());
		$template->setVar("weeks", $weeks);

		echo $template->render();

		return true;
	}

	private function _showDay($cellNumber, $dayInWeek)
	{
		global $app, $XenuxDB;

		$additionalClass = '';

		$today_day  = date("d");
		$today_mon  = date("m");
		$today_year = date("Y");

		// first day of this month in this week (1-7)
		$firstDayOfTheWeek = date('N', strtotime($this->currentYear . '-' . $this->currentMonth . '-01'));

		if ($this->currentDay == 0 && $cellNumber == $firstDayOfTheWeek)
		{
			$this->currentDay = 1;
		}

		if ($this->currentDay != 0 && $this->currentDay <= $this->daysInMonth)
		{	// cellNumber in month
			$cellContent = $this->currentDay;
			$this->currentDay++;

			if ($cellContent == $today_day && $this->currentMonth == $today_mon && $this->currentYear == $today_year)
				$additionalClass = "today";

			$month = $this->currentMonth;
		}
		else
		{	// cellNumber before or after month
			if ($this->currentDay == 0)
			{	// before
				$cellContent = sprintf("%02d", $this->daysInPreMonth + $dayInWeek + 1 - $firstDayOfTheWeek);
				$additionalClass = "before";

				$month = $this->preMonth;
			}
			else
			{	// after
				$cellContent = sprintf("%02d", $cellNumber - $this->daysInMonth - $firstDayOfTheWeek + 1);
				$additionalClass = "after";

				$month = $this->nextMonth;
			}
		}

		$dates = null;

		$events = $XenuxDB->getList('events', [
			'where' => [
				'AND' => [
					'start_date[<=]' => "{$this->currentYear}-{$month}-{$cellContent} 23:59:59",
					'end_date[>=]'   => "{$this->currentYear}-{$month}-{$cellContent} 00:00:00",
				]
			],
			'order' => 'start_date ASC'
		]);
		if ($events)
		{
			foreach ($events as $event)
			{
				$template = new template(MAIN_PATH."/templates/".$app->template."/_calendar_day_dates.php");

				if (mysql2date("Y-m-d", $event->start_date) == mysql2date("Y-m-d", $event->end_date)) // event only one day long
				{
					$template->setVar("time", mysql2date("H:i", $event->start_date));
				}
				elseif (mysql2date("Y-m-d", $event->start_date) == mysql2date("Y-m-d", "{$this->currentYear}-{$month}-{$cellContent}")) // start day
				{
					$template->setVar("time", "&larr;<span style=\"width:5px;display: inline-block;\"></span>" . mysql2date("H:i", $event->start_date));
				}
				elseif (mysql2date("Y-m-d", $event->start_date) < mysql2date("Y-m-d", "{$this->currentYear}-{$month}-{$cellContent}") && mysql2date("Y-m-d", $event->end_date) > mysql2date("Y-m-d", "{$this->currentYear}-{$month}-{$cellContent}")) // middle day
				{
					$template->setVar("time", "&harr;");
				}
				elseif (mysql2date("Y-m-d", $event->end_date) == mysql2date("Y-m-d", "{$this->currentYear}-{$month}-{$cellContent}")) // end day
				{
					$template->setVar("time", "&rarr;<span style=\"width:5px;display: inline-block;\"></span>" . mysql2date("H:i", $event->end_date));
				}

				$template->setVar("ID", $event->id);
				$template->setVar("name_url", urlencode($event->title));
				$template->setVar("name", $event->title);

				$dates .= $template->render();
			}
		}


		$template = new template(MAIN_PATH."/templates/".$app->template."/_calendar_day.php");
		$template->setVar("day", $cellContent);
		$template->setVar("additionalClass", $additionalClass);
		$template->setVar("dates", $dates);

		return $template->render();
	}

	private function _createLabels()
	{
		global $app;

		$labels = null;

		foreach ($this->dayLabels as $index => $label)
		{
			$template = new template(MAIN_PATH."/templates/".$app->template."/_calendar_label.php");
			$template->setVar("label", __($label));
			$labels .= $template->render();
		}

		return $labels;
	}

	private function _weeksInMonth($month=null, $year=null)
	{
		if ($year == null)
			$year = $this->currentYear;

		if ($month == null)
			$month = $this->currentMonth;

		$daysInMonths = $this->_daysInMonth($month, $year);

		$numOfweeks = ($daysInMonths %7 == 0 ? 0 : 1) + intval($daysInMonths / 7);

		$monthStartingDay = date('N', strtotime($year . '-' . $month . '-01'));
		$monthEndingDay   = date('N', strtotime($year . '-' . $month . '-' . $daysInMonths));

		if ($monthEndingDay < $monthStartingDay)
			$numOfweeks++;

		return $numOfweeks;
	}

	private function _daysInMonth($month=null, $year=null)
	{
		if ($year == null)
			$year = $this->currentYear;

		if ($month == null)
			$month = $this->currentMonth;

		return date('t', strtotime($year . '-' . $month . '-01'));
	}
}
