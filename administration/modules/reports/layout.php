<section class="box-shadow floating one-column-box">
	<p><?= __('choose a logfile') ?></p>
	<select name="logFile" size="1" onchange="window.location='?file='+this.value">
		{{logFiles}}
	</select>

	<textarea readonly class="full_page" style="height: 300px; resize: vertical;">{{log}}</textarea>
</section>