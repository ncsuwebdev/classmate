<p>
    <input type="button" id="previousButton" value="Previous" /> 
    <input type="button" id="nextButton" value="Next" />
    <img id="loading" src="{$sitePrefix}/public/images/loading.gif" width="16" height="16" />
</p>

<textarea style="display: none;" id="popupDetails">
&lt;div class="infobox"&gt;
    &lt;div class="dis"&gt;
        &lt;h3 class="curve"&gt;
        &lt;div class="popupDetails"&gt;
          &lt;h2&gt;%title%&lt;/h2&gt;
            <!--&lt;img src="%thumbnail%" width="88" height="140" align="left"&gt;-->
          &lt;div style="float: left; width: 250px; overflow: auto;"&gt;
                &lt;p&gt;&lt;b&gt;&lt;a href="{$sitePrefix}/workshop/index/details?workshopId=%workshopId%"&gt;Click Here For More Info&lt;/a&gt;&lt;/b&gt;&lt;/p&gt;
                &lt;p&gt;&lt;b&gt;Description:&lt;/b&gt; %description%&lt;/p&gt;
            &lt;/div&gt;
        &lt;/div&gt;
        &lt;/h3&gt;
    &lt;div class="innerC"&gt;&lt;/div&gt;
    &lt;/div&gt;
    &lt;/div&gt;
</textarea>

<div id="calendarWrapper">
    <div id="calendar" title="{$calendar.month}_{$calendar.year}">
        <p class="calendarTitle">{$calendar.monthName} {$calendar.year}</p>
        <table class="calendarTable" cellpadding="3" cellspacing="0" border="1" bordercolor="#464548">
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