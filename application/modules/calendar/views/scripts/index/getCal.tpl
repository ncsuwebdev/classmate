<div id="calendarWrapper">
    <div id="calendar" title="{$calendar.month}_{$calendar.year}">
        <p class="calendarTitle">{$calendar.monthName} {$calendar.year}</p>
        <table class="calendarTable" cellpadding="3" cellspacing="0" border="1">
            <thead>
                <tr>
                    <th></th>
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
                    <td class="weekNum"> 
                        <p>{$r.weekNum}</p>
                    </td>
                    {foreach from=$r.days item=d}
                        <td class="calendarDay">
                            <span class="numEvents">
                                {if $d.numEvents}
                                    {$d.numEvents}
                                    {if $d.numEvents == 1}
                                        Event
                                    {else}
                                        Events
                                    {/if}
                                {/if}
                            </span>
                            <p class="num">{$d.num}</p>
                             
                            <div>
                            
                            {if $d.events}
                                {foreach from=$d.events item=e}
                                    {$e}<br />
                                {/foreach}
                            {/if}
                            </div>
                        </td>
                    {/foreach}    
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>