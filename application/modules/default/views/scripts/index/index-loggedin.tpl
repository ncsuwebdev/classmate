<h3><span>Your Classes</span></h3>
{foreach from=$attendeeEvents item=e}
<div class="event">
    <div class="workshopName"><a href="{$sitePrefix}/workshop/index/details/?workshopId={$e.workshopId}">{$e.workshop.title}</a></div>
    <div class="eventDateTime">
        <span class="date">{$e.date|date_format:$config.longDateFormat}</span>
        <span class="time">{$e.startTime|date_format:$config.timeFormat} - {$e.endTime|date_format:$config.timeFormat}</span>
    </div>
</div>
{/foreach}
<h3><span>Other Workshops</span></h3>