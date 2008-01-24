<div id="right">
    <div class="sectionTitle">
        <p class="left"></p>
        <p class="right"></p>
        <p class="content">ClassMate Classes</p>
    </div>
    <div id="myTabs">
        <ul class="mootabs_title">
            <li title="events">Coming Soon</li>
            <li title="popular">Most Popular</li>
            <li title="searches">Recent Searches</li>
        </ul>
	    <div id="events" class="mootabs_panel">
	        {foreach from=$upcoming item=e}
	        <div class="event">
	           <span class="date">{$e.date|date_format:$config.medDateFormat}</span>
	           <div class="workshopName"><a href="{$sitePrefix}/workshop/index/details/?workshopId={$e.workshopId}">{$e.workshop.title|truncate:40}</a></div> 
	        </div>
	        {foreachelse}
	        You are not signed up for any classes right now.
	        {/foreach}
	    </div>
        <div id="popular" class="mootabs_panel">
        Coming Soon
        </div>
        <div id="searches" class="mootabs_panel">
        This, too, is Coming Soon.
        </div>
    </div>    
</div>
<div id="left">
ClassMate is your one-stop-shop for registering for classes
</div>