ClassMate is your one-stop-shop for registering for classes<br /><br />
<div id="events">
	<h3><span>Your Classes</span></h3>
	{foreach from=$attendeeEvents item=e}
	<div class="event">
	    <div class="utility">
	       <div class="status">{$e.status|capitalize}</div>
                {if $e.status == 'instructor'}
                {elseif $e.status == 'attending'}
                    <div class="reservation"><a href="">Reservation</a></div>
                    <div class="evaluation"><a href="">Evaluate</a></div>
                {elseif $e.status == 'waitlist'}
                {/if}
	    
	    </div>
	    <div class="workshopName"><a href="{$sitePrefix}/workshop/index/details/?workshopId={$e.workshopId}">{$e.workshop.title}</a></div>
	    <div class="eventDateTime">
	        <span class="date">{$e.date|date_format:$config.longDateFormat}</span>
	        <span class="time">{$e.startTime|date_format:$config.timeFormat} - {$e.endTime|date_format:$config.timeFormat}</span>
	    </div>
	</div>
	{foreachelse}
	You are not signed up for any classes right now.
	{/foreach}
</div>
{if count($relatedWorkshops) != 0}
<div class="workshops">
	<h3><span>Other Workshops You May Be Interested In</span></h3>
	{foreach from=$relatedWorkshops item=w}
	<div class="workshop">
	    <div class="name"><a href="{$sitePrefix}/workshop/index/details/?workshopId={$w.workshopId}">{$w.title}</a></div>
	    <div class="description">{$w.description|strip_tags|truncate:150}</div>  
	</div>
	{/foreach}
</div>
{/if}