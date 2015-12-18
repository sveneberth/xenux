<div class="page-header">
	<h1>{{username}}</h1>
</div>

<div class="profile">
#if(realname_show_profile):
	<div class="row">
		<span class="title"><?= __('realname') ?></span>
		<span class="value">{{realname}}</span>
	</div>
#endif
#if(email_show_profile):
	<div class="row">
		<span class="title"><?= __('email') ?></span>
		<span class="value">{{email}}</span>
	</div>
#endif
	<div class="row">
		<span class="title"><?= __('bio') ?></span>
		<span class="value">{{bio}}</span>
	</div>
	<div class="row">
		<span class="title"><?= __('homepage') ?></span>
		<span class="value"><a href="{{homepage}}" target="_blank">{{homepage}}</a></span>
	</div>
</div>

<style>
.profile {
}
.profile > .row {
	margin: 5px 0 0;
	clear: both;
	display: block;
}
.profile > .row > span {
	float: left;
}
.profile > .row > span.title {
	font-weight: bold;
	width: 150px;
}
.profile > .row:before,
.profile > .row:after {
	display: none;
	content: none;
}
.profile:after,
.profile > .row:after {
	clear: both;
	content: "";
	display: block;
}
</style>