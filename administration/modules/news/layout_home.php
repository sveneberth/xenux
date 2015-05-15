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
ul.data-table > li > .data-column.news-create-date {
	width: 160px;
}
ul.data-table > li > .data-column.news-title {
	width: 25%;
	min-width: 250px;
	max-width: 500px;
}
ul.data-table > li > .data-column.news-id {
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
				<span class="data-column news-id"><?= __('ID') ?></span>
				<span class="data-column news-title"><?= __('title') ?></span>
				<span class="data-column news-create-date"><?= __('createDate') ?></span>
				<span class="data-column news-text"><?= __('newsPreview') ?></span>
			</li>

			{{news}}
		</ul>
	</div>
</section>