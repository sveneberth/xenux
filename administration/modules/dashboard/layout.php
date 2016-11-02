<div class="grid-row">
	<section class="box-shadow grid-col grid-col-half">
		<h3><?= __('user statistics') ?></h3>
		<p><?= __('user registered') ?>: {{count_users}}</p>
		<p><?= __('active today') ?>: {{count_active_users}}</p>
	</section>

	<section class="box-shadow grid-col grid-col-half">
		<h3><?= __('site statistics') ?></h3>
		<p><?= __('amount sites') ?>: {{count_pages}}</p>
		<p><?= __('amount public') ?>: {{count_public_pages}}</p>
	</section>
</div>

<div class="grid-row">
	<section class="box-shadow grid-col grid-col-half">
		<h3><?= __('post statistics') ?></h3>
		<p><?= __('amount posts') ?>: {{count_posts}}</p>
		<p><?= __('amount public') ?>: {{count_public_posts}}</p>
		<p><?= __('amount last week') ?>: {{count_posts_lastWeek}}</p>
	</section>

	<section class="box-shadow grid-col grid-col-half">
		<h3><?= __('cloud statistics') ?></h3>
		<p><?= __('amount files') ?>: {{count_cloud_files}}</p>
		<p><?= __('amount pictures') ?>: {{count_cloud_images}}</p>
		<p><?= __('amount other files') ?>: {{count_cloud_others}}</p>
		<p><?= __('total size of all files') ?>: {{total_size_files}}</p>
	</section>
</div>
