<script>
	var url = window.location.href;
	var title = document.title;
	var newUrl = url.substring(0, url.indexOf('?')) + window.location.hash;
	// replace new url
	if(window.history.replaceState) {
		window.history.replaceState(null, null, newUrl);
	}
</script>

{{messages}}

<section class="box-shadow floating one-column-box no-margin">
	#if(new):
		<p><?= __('here can you add a new user') ?></p>
	#else:
		<p><?= __('here can you edit the user') ?></p>
	#endif

	<div class="form">
		{{form}}
	</div>
</section>