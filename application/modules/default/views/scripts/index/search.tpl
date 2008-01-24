{foreach from=$workshops item=w}
    <div class="searchResult">
	    <div class="title"><a href="{$sitePrefix}/workshop/index/details/?workshopId={$w->workshopId}">{$w->title}</a></div>
	    <div class="cescription">{$w->description|strip_tags|truncate:250}</div>
	</div>
{/foreach}