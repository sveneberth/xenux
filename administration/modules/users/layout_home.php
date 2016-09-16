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
ul.data-table > li > .data-column.user-id {
	width: 50px;
}
ul.data-table > li > .data-column.user-username,
ul.data-table > li > .data-column.user-firstname,
ul.data-table > li > .data-column.user-lastname {
	width: 200px;
}
ul.data-table > li > .data-column.show {
	float: right;
}
ul.data-table > li > .data-column.user-create-date {
	width: 160px;
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
		<ul class="data-table">
			<li class="headline">
				<span class="data-column user-id"><?= __('ID') ?></span>
				<span class="data-column user-username"><?= __('username') ?></span>
				<span class="data-column user-firstname"><?= __('firstname') ?></span>
				<span class="data-column user-lastname"><?= __('lastname') ?></span>
				<!-- <span class="data-column user-create-date"><?= __('createDate') ?></span> -->
			</li>

			{{users}}
		</ul>

		{{amount}} <?= __('entries') ?>
	</section>
</div>
