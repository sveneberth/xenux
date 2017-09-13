<div class="profile layout2">
#if(realname_show_profile):
	<div class="row">
		<span class="title"><?= __('realname') ?></span>
		<span class="value">{{realname}}</span>
	</div>
#endif
#if(email_show_profile):
	<div class="row">
		<span class="title"><?= __('email') ?></span>
		<span class="value"><a href="mailto:{{email}}" target="_blank">{{email}}</a></span>
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
#if(social_media_not_empty):
	<div class="row">
		<span class="title"><?= __('social_media') ?></span>
		<span class="value">
			<?php
			preg_match_all('/(.*?)\:\s?(.*?)$/m', $socialmedia_links, $matches);
			foreach (turn_array($matches) as $match)
			{
				?>
				<a href="<?= $match[2] ?>" target="_blank"><?= $match[1] ?></a><br>
				<?php
			}
			?>
		</span>
	</div>
#endif
	<div class="row">
		<span class="title"><?= __('amount of postings') ?></span>
		<span class="value">{{amountPostings}}</span>
	</div>
</div>

<style>
{# #FIXME: move #}
.profile {
}
.profile > .row {
	margin: 5px 0 0;
	clear: both;
	display: block;
}
.profile.layout1 > .row > span {
	float: left;
}
.profile > .row > span.title {
	font-weight: bold;
	width: 200px;
}
.profile.layout2 > .row > span.value {
	display: block;
    margin-left: 40px;
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
