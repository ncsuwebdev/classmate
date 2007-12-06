<p>
    <input type="button" id="previousButton" value="Previous" /> 
    <input type="button" id="nextButton" value="Next" />
    <img id="loading" src="{$sitePrefix}/public/images/loading.gif" width="16" height="16" />
</p>
<div id="eventsWrapper"></div>
<div id="calendarWrapper">
    <div id="calendar" title="{$calendar.month}_{$calendar.year}">
        <p class="calendarTitle">{$calendar.monthName} {$calendar.year}</p>
        <table class="calendarTable" cellpadding="3" cellspacing="0" border="1" bordercolor="#464548">
            <thead>
                <tr>
                    <th>Sunday</th>
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednesday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                    <th>Saturday</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$calendar.rows item=r}
                <tr>
                    {foreach from=$r.days item=d}
                        <td class="calendarDay">
                            <p class="num">{$d.num}</p>
                            {if $d.events}
                                {foreach from=$d.events item=e}
                                    {$e}<br />
                                {/foreach}
                            {/if}
                        </td>
                    {/foreach}    
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>