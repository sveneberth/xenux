#if(show_meta_info):
<span class="meta-info"><?= __('writtenInfo', $author, $date, $time) ?></span>
#endif
<img class="thumbnail-img" src="{{post_imageURL}}" title="{{post_imageTitle}}" alt="{{post_imageTitle}}">
{{post_content}}

<br>
<br>
<a href="{{MAIN_URL}}/posts/list">&raquo;<?= __('gotoPostList') ?></a>
