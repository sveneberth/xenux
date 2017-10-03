<div class="calendar">
	<div class="calendar_header">
		<a class="calendar-menu-button prev" href="{{REQUEST_URL}}?{{prevUrlParam}}"><?= __('prevMonth') ?></a>

		<span class="calendar_title">{{year}} {{month}}</span>

		<a class="calendar-menu-button next" href="{{REQUEST_URL}}?{{nextUrlParam}}"><?= __('nextMonth') ?></a>
	</div>

	<div class="calendar_content">
		<ul class="calendar_label">
			{{labels}} {# _calendar_label.php #}
		</ul>

		<div class="clear"></div>

		<ul class="calendar_dates">
			{{weeks}} {# _calendar_weeks.php #}
		</ul>

		<div class="clear"></div>
	</div>
</div>
