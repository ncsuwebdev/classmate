<div id="tagList">
	<b>Tags:</b>
	{foreach from=$tags item=t}
	<a href="{$sitePrefix}/index/search/?search={$t.name}">{$t.name}</a> &nbsp; 
	{foreachelse}
	None
	{/foreach}
</div>

<div id="tabContainer">
    <ul class="mootabs_title">
        <li title="tabDescription">Workshop Description</li>
        <li title="tabSchedule">Upcoming Schedule</li>        
        <li title="tabPrereqs">Pre-Requisites</li>
        <li title="tabDocuments">Handouts</li>
    </ul>
	
	<div class="mootabs_panel" id="tabDescription">
	    {if $acl.edit}
	    <img src="{$sitePrefix}/public/images/edit.png" width="16" height="16" style="float:right" /><br />
	    {/if}
		<span id="descriptionContent">{$workshop.description}</span>
    </div>
    
    <div class="mootabs_panel" id="tabDocuments">
        Click on the document to download it.<br /><br />
		<div id="documents">
		    {foreach from=$documents item=d}
		    <div class="document" title="{$d.description}">
		        <span class="doctype">
		        <a href="{$sitePrefix}/{$d.path}"><img src="{$sitePrefix}/public/images/doctype/{$d.type}.png" width="32" height="32" /></a></span>
		        <span class="docname"><a href="{$sitePrefix}/{$d.path}">{$d.name}</a></span>
		    </div>
		    {/foreach}
		</div>
	</div>
	
	<div class="mootabs_panel" id="tabPrereqs">
        {$workshop.prerequisites}
    </div>
    
    <div class="mootabs_panel" id="tabSchedule">
        These are the times when this workshop will be offered.  If you would like
        to request a special time for this workshop to be taught, you can 
        <a href="{$sitePrefix}/workshop/request/">Request a Custom Workshop</a>.<br /><br />
		<table class="list">
		    {foreach from=$events item=e name=events}
		        {assign var=locationId value=$e.locationId}
		        {if $smarty.foreach.events.index % $config.headerRowRepeat == 0}
		        <tr>
		            <th width="200">Date</th>
		            <th width="150">Time</th>
		            <th width="200">Location</th>
		            <th width="200">Status</th>
		        </tr>
		        {/if}
		        <tr class="{cycle values="row1,row2"}">
		            <td><a href="{$sitePrefix}/workshop/event/details/?eventId={$e.eventId}">{$e.date|date_format:$config.longDateFormat}</a></td>
		            <td style="text-align:center">{$e.startTime|date_format:$config.timeFormat} - {$e.endTime|date_format:$config.timeFormat}</td>
		            <td style="text-align:center">{$locations.$locationId.name}</td>
		            <td style="text-align:center">{$e.maxSize} spots open</td>
		        </tr>
		    {foreachelse}
		    <tr>
		        <td class="noResults">No events are scheduled at this time.</td>
		    </tr>
		    {/foreach}
		</table>
    </div>
</div>