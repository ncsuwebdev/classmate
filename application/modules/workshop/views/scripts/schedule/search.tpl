<input type="hidden" id="week" value="{$week}" />
<input type="hidden" id="year" value="{$year}" />

<input type="hidden" id="nextWeek" value="{$nextWeekNum}" />
<input type="hidden" id="nextYear" value="{$nextYear}" />

<input type="hidden" id="prevWeek" value="{$prevWeekNum}" />
<input type="hidden" id="prevYear" value="{$prevYear}" />

<input style="float: left;" id="previousWeekButton" type="button" value="&lsaquo; Previous Week">
<input style="float: right;" id="nextWeekButton" type="button" value="Next Week &rsaquo;">
<p class="weekViewTitle">{$calendar.0.date|date_format:$config.longDateFormat} - {$calendar.6.date|date_format:$config.longDateFormat}</p>
<div style="clear: both; height: 7px;"></div>
<table class="weekViewDayHeader">
    <tbody>
        <tr class="weekDays">
            <th class="time"></th>
            {foreach from=$calendar item=c}
                {assign var=df value=$c.date|date_format:'%D'}
                <th class="day{if $df == $today} today{/if}">{$c.date|date_format:'%a'} {$c.date|date_format:$config.dayFormat|ordinal}</th>
            {/foreach}
        </tr>
    </tbody>
</table>
<div id="weekViewWrapper">
<table class="weekViewTable">
    <tbody>
        <tr>
            <td class="time">
                <div class="timeSlotPad"></div>
                {section name=times start=$displayStartTime loop=$endTime step=1800}
                    <div class="timeSlot">{$smarty.section.times.index|date_format:$config.timeFormat}</div>
                {/section}
                <div class="timeSlot">{$endTime|date_format:$config.timeFormat}</div>
            </td>
            {foreach from=$calendar item=d}
                {assign var='currTime' value=$startTime}
                {assign var=df value=$d.date|date_format:'%D'}
                <td title="{$d.date|date_format:$config.dateFormat}" class="eventColumn{if $df == $today} today{/if}" width="100px;">
                {foreach name=events from=$d.events item=e}
                    {if $currTime < $e.startTime|timestamp}
                        <div class="emptyTime" style="height: {math equation="((x - y) / z)" x=$e.startTime|timestamp y=$currTime z=60}px">&nbsp;</div>
                        {assign var='currTime' value=$e.endTime|timestamp}
                    {/if}

                    <div class="event" id="{$e.eventId}" style="height: {$e.numMinutes}px;">
                        <div style="height: {$e.numMinutes}px;">
                        <input type="hidden" id="event{$e.eventId}startTime" value="{$e.startTime|timestamp}" />
                        <input type="hidden" id="event{$e.eventId}endTime" value="{$e.endTime|timestamp}" />
                        <p class="eventHeader">
                            {if $acl.delete}
                            <a class="delete" href="#" onclick="deleteEvent({$e.eventId});">
                                <img src="{$sitePrefix}/public/images/delete.png" height="16" width="16" alt="Delete Event" />
                            </a>
                            {/if}
                            {$e.startTime|date_format:$config.timeFormat}
                        </p>
                        <p class="eventDetails">
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
                        </p>
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
</div>                           
