{foreach from=$workshops item=w}
    <span class="searchTitle"><a href="{$sitePrefix}/workshop/index/details/?workshopId={$w.workshopId}">{$w.title}:</a></span><br />
    {$w.description|strip_tags|truncate:250}<br /><br />
{/foreach}