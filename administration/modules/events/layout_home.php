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
	width: 150px;
}
ul.data-table > li > .data-column.event-title {
	width: 20%;
	min-width: 200px;
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
	vertical-align: baseline;
	height: 25px;
	width: 25px;
	display: inline-block;
	vertical-align: top;
	margin: 0;
	line-height: 25px;
	right: 0;
	position: absolute;
}
</style>

{{messages}}

<section class="box-shadow floating one-column-box no-margin">
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
</section>