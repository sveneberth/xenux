<div class="box one-column-box">
	<div class="headline">{{news_title}}</div>
	<div class="meta-info">{{news_createDate}}</div>
	
	{{news_content}}

	<br />
	<a style="display:inline-block;margin-top:10px;" href="{{URL_MAIN}}/news/view/{{news_ID}}-{{news_title_url}}">&raquo;<?= __("readNews") ?></a>
</div>
