<div id="weekViewWrapper">
        
    <input type="button" style="float: right;" class="closeSticky" id="closeButton" value="Close" />
    <input type="button" id="prevWeekButton" value="&lsaquo; Previous Week" />
    <input type="button" id="nextWeekButton" value="Next Week &rsaquo;" />
    <img id="weekLoading" src="{$sitePrefix}/public/images/loading.gif" width="16" height="16" />
    
    <div id="weekViewData">
    
    <input type="hidden" id="weekViewNextWeekNum" value="{$nextWeekNum}" />
    <input type="hidden" id="weekViewNextYear" value="{$nextYear}" />
    
    <input type="hidden" id="weekViewPrevWeekNum" value="{$prevWeekNum}" />
    <input type="hidden" id="weekViewPrevYear" value="{$prevYear}" />
    
    <p class="weekViewTitle">{$calendar.0.date|date_format:$config.longDateFormat} - {$calendar.6.date|date_format:$config.longDateFormat}</p>
    <table class="weekViewTable" cellpadding="3" cellspacing="0" border="1" align="center">
        <thead>
            <tr class="weekDays">
                <th><span>Sunday</span><br />{$calendar.0.date|date_format:$config.medDateFormat}</th>
                <th><span>Monday</span><br />{$calendar.1.date|date_format:$config.medDateFormat}</th>
                <th><span>Tuesday</span><br />{$calendar.2.date|date_format:$config.medDateFormat}</th>
                <th><span>Wednesday</span><br />{$calendar.3.date|date_format:$config.medDateFormat}</th>
                <th><span>Thursday</span><br />{$calendar.4.date|date_format:$config.medDateFormat}</th>
                <th><span>Friday</span><br />{$calendar.5.date|date_format:$config.medDateFormat}</th>
                <th><span>Saturday</span><br />{$calendar.6.date|date_format:$config.medDateFormat}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                {foreach from=$calendar item=d}
                    <td class="weekViewDay" width="14%" height="99%" valign="top">                        
                        <div>
                        {if $d.events}
                            {foreach from=$d.events item=e}
                                {if $e.workshop}
                                    - <a href="{$sitePrefix}/workshop/index/details?workshopId={$e.workshop.workshopId}">{$e.workshop.title}</a><br />
                                {/if}
                            {/foreach}
                        {else}
                            No Events
                        {/if}
                        </div>
                    </td>
                {/foreach}    
            </tr>
        </tbody>
    </table>
    </div>
</div>