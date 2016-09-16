<script>
	var url = window.location.href;
	var title = document.title;
	var newUrl = url.substring(0, url.indexOf('?')) + window.location.hash;
	// replace new url
	if(window.history.replaceState) {
		window.history.replaceState(null, null, newUrl);
	}
</script>
<style>
ul.data-table > li.non-public {
	opacity: .7;
}
ul.data-table > li > .data-column.event-create-date,
ul.data-table > li > .data-column.event-start-date,
ul.data-table > li > .data-column.event-end-date {
	width: 160px;
}
ul.data-table > li > .data-column.event-title {
	width: 25%;
	min-width: 250px;
	max-width: 500px;
}
ul.data-table > li > .data-column.event-id {
	width: 50px;
}
ul.data-table > li > .data-column.show {
	right: 40px;
	position: absolute;
}
ul.data-table > li > .remove-icon {
	background-image: url('{{TEMPLATE_PATH}}/images/remove.png');
	background-size: 100%;
	background-repeat: no-repeat;
	background-position: center;
	display: block;
	height: 25px;
	width: 25px;
	position: absolute;
	right: 0;
	top: 50%;
	transform: translateY(-50%);
}
</style>

{{messages}}

<div class="grid-row">
	<section class="box-shadow grid-col">
		<div class="menu_order">
			<ul class="data-table">
				<li class="headline">
					<span class="data-column event-id"><?= __('ID') ?></span>
					<span class="data-column event-title"><?= __('title') ?></span>
					<span class="data-column event-create-date"><?= __('createDate') ?></span>
					<span class="data-column event-start-date"><?= __('startDate') ?></span>
					<span class="data-column event-end-date"><?= __('endDate') ?></span>
				</li>

				{{events}}
			</ul>
		</div>

		{{amount}} <?= __('entries') ?>
	</section>
</div>
