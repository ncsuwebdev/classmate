<div id="weekViewWrapper">
    <input type="button" class="closeSticky" id="closeButton" value="Close" />
    <p class="weekViewTitle">{$calendar.0.date|date_format:$config.longDateFormat} - {$calendar.6.date|date_format:$config.longDateFormat}</p>
    <table class="weekViewTable" cellpadding="3" cellspacing="0" border="1" width="99%" align="center">
        <thead>
            <tr>
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
                    <td class="weekViewDay" width="14%" height="99%">                        
                        <div>
                        {if $d.events}
                            {foreach from=$d.events item=e}
                                {if $e.workshop}
                                    - <a href="{$sitePrefix}/workshop/index/details?workshopId={$e.workshop.workshopId}">{$e.workshop.title}</a><br />
                                {/if}
                            {/foreach}
                        {/if}
                        </div>
                    </td>
                {/foreach}    
            </tr>
        </tbody>
    </table>
</div>