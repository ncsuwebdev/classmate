<!--<input type="hidden" id="startTime" value="{$startTime}" />
<input type="hidden" id="endTime" value="{$endTime}" />-->

<input type="hidden" id="week" value="{$week}" />
<input type="hidden" id="year" value="{$year}" />
<input type="hidden" id="month" value="{$month}" />

<input type="hidden" id="nextWeek" value="{$nextWeekNum}" />
<input type="hidden" id="nextYear" value="{$nextYear}" />

<input type="hidden" id="prevWeek" value="{$prevWeekNum}" />
<input type="hidden" id="prevYear" value="{$prevYear}" />

<!-- <input style="float: left;" id="previousWeekButton" type="button" value="&lsaquo; Previous Week">
<input style="float: right;" id="nextWeekButton" type="button" value="Next Week &rsaquo;">-->
<p class="weekViewTitle">{$calendar.0.date|date_format:$config.longDateFormat} - {$calendar.6.date|date_format:$config.longDateFormat}</p>
<div style="clear: both; height: 7px;"></div>
<table class="weekViewTable" cellpadding="3" cellspacing="0" border="1" align="center">
    <thead>
        <tr class="weekDays">
            <th>Times</th>
            <th>Sun {$calendar.0.date|date_format:$config.dateFormat}</th>
            <th>Mon {$calendar.1.date|date_format:$config.dateFormat}</th>
            <th>Tue {$calendar.2.date|date_format:$config.dateFormat}</th>
            <th>Wed {$calendar.3.date|date_format:$config.dateFormat}</th>
            <th>Thu {$calendar.4.date|date_format:$config.dateFormat}</th>
            <th>Fri {$calendar.5.date|date_format:$config.dateFormat}</th>
            <th>Sat {$calendar.6.date|date_format:$config.dateFormat}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                {section name=times start=$startTime loop=$endTime step=1800}
                    <div class="timeSlot">{$smarty.section.times.index|date_format:$config.timeFormat}</div>
                {/section}
                <div class="timeSlot">{$endTime|date_format:$config.timeFormat}</div>
            </td>
            
            {foreach from=$calendar item=d}
                {assign var='currTime' value=$startTime}
                <td title="{$d.date|date_format:$config.dateFormat}" class="eventColumn" width="100px;">
                {foreach name=events from=$d.events item=e}
                    {if $currTime < $e.startTime|timestamp}
                        <div class="emptyTime" style="height: {math equation="((x - y) / z)" x=$e.startTime|timestamp y=$currTime z=60}px">&nbsp;</div>
                        {assign var='currTime' value=$e.endTime|timestamp}
                    {/if}

                    <div class="event" id="{$e.eventId}" style="height: {$e.numMinutes}px;">
                        <div style="height: {$e.numMinutes}px;">
                        {if $acl.delete}
                        <a style="float: right;" href="{$sitePrefix}/schedule/index/delete?eventId={$e.eventId}" onclick="return confirm('Are you sure you want to remove this event?');"><img src="{$sitePrefix}/public/images/delete.png" height="16" width="16" alt="Delete Event" /></a>
                        {/if}
                        <input type="hidden" id="event{$e.eventId}startTime" value="{$e.startTime|timestamp}" />
                        <input type="hidden" id="event{$e.eventId}endTime" value="{$e.endTime|timestamp}" />
                        <p class="eventHeader">{$e.startTime|date_format:$config.timeFormat}</p>
                        {if $e.workshop}
                            {if $acl.details}
                            <a href="{$sitePrefix}/schedule/index/details?eventId={$e.eventId}">
                            {/if}
                            {$e.workshop.title|truncate:30}
                            {if $acl.details}
                            </a>
                            {/if}
                        {else}                           
                            {$e.workshop.title}
                        {/if}
                        <br />
                        {* {$e.startTime|date_format:$config.timeFormat} - {$e.endTime|date_format:$config.timeFormat} *}
                        {assign var='currTime' value=$e.endTime|timestamp}
                        </div>
                    </div>
                {/foreach}
                {if $currTime < $endTime}
                    <div class="bottomEmptyTime" style="height: {math equation="(((x - y) / z)+30)" x=$endTime y=$currTime z=60}px">&nbsp;</div>
                {/if}
                </td>
            {/foreach}    
        </tr>
    </tbody>
</table>                               
