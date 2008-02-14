<input type="hidden" id="eventId" value="{$event.eventId}" />
<div id="detailsRight">
   
    <div class="rightTitle">
        Instructor Toolbox
    </div>
    
    <div class="rightContent">
        <div class="action attendanceAction"><a href="{$sitePrefix}/workshop/instructor/?eventId={$event.eventId}">Take Roll / View Attendees</a></div>
        <div class="action contactAction"><a href="{$sitePrefix}/workshop/instructor/contact/?eventId={$event.eventId}">Contact All Attendees</a></div> 
        <div class="action evaluationAction"><a href="{$sitePrefix}/workshop/instructor/evaluationResults/?eventId={$event.eventId}">View Evaluation Results</a></div>   
    </div>
    
    <div class="rightTitle">
        Event Details
    </div>
    
    <div class="rightContent">
        <table width="100%">
            <tbody>
                <tr>
                    <td valign="top"><label>Instructors:</label></td>
                    <td>
                    {foreach from=$instructors item=i}
                    {$i.firstName} {$i.lastName}<br />
                    {foreachelse}
                    No Instructors
                    {/foreach}
                    </td>
                </tr>
                <tr>
                    <td valign="top"><label>Min #:</label></td>
                    <td>{$event.minSize} students</td>
                </tr>
                <tr>
                    <td valign="top"><label>Max #:</label></td>
                    <td>{$event.maxSize} students</td>
                </tr>
                <tr>
                    <td valign="top"><label>Waitlist #:</label></td>
                    <td>{$event.waitlistSize} students</td>
                </tr> 
                {if $globalAcl.editEvent}
                <tr>
                    <td colspan="2" align="center">
                    <br />
                    <input type="button" value="Edit Event Details" onclick="javascript:location.href='{$sitePrefix}/workshop/schedule/index/?eventId={$event.eventId}'" />
                    </td>
                </tr>
                {/if}                                               
            </tbody>
        </table>    
    </div>
</div>
<div id="detailsLeft">   
    <div id="workshopTitleContainer">
        <img src="{$sitePrefix}/index/image/?imageId={$category.largeIconImageId}" alt="{$category.name}" />
        <div id="wsTitle">Instructor Tools for <a href="{$sitePrefix}/workshop/index/details/?workshopId={$workshop.workshopId}">{$workshop.title}</a></div>       
    </div>   
        <div class="event" id="event_{$e.eventId}">
            <span class="date">{$event.date|date_format:$config.longDateCompactFormat}</span> |
            <span class="time">{$event.startTime|date_format:$config.timeFormat} - {$event.endTime|date_format:$config.timeFormat}</span> |
            <span class="location"><a href="{$sitePrefix}/workshop/location/details/?locationId={$e.location.locationId}">{$location.name}</a></span>
        </div>     
     {$toolTemplate}
</div>