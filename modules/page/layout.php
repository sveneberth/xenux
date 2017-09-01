<div class="page-header">
	<h1>{{page_title}}</h1>
</div>
{{page_content}}

#if(show_meta_info):
<span class="meta-info meta-info-footer"><?= __('writtenInfo', $author, $date, $time) ?></span>
#endif

{{_PREV_NEXT}}
