<div class="box one-column-box#if(hasThumbnail): hasThumbnail#endif">
	#if(hasThumbnail):
		<a href="{{MAIN_URL}}/posts/view/{{post_ID}}-{{post_title_url}}">
			<img class="thumbnail-img" src="{{post_imageURL}}" title="{{post_imageTitle}}" alt="{{post_imageTitle}}">
		</a>
	#endif
	<div class="post-list-body">
		<a href="{{MAIN_URL}}/posts/view/{{post_ID}}-{{post_title_url}}">
			<div class="headline">{{post_title}}</div>
		</a>
		<div class="meta-info">{{post_createDate}}, {{post_author}}</div> {# #TODO: link #}

		{{post_content}}

		<br>
		<a class="read-more" href="{{MAIN_URL}}/posts/view/{{post_ID}}-{{post_title_url}}">&raquo;<?= __("readPost") ?></a>
	</div>
</div>
