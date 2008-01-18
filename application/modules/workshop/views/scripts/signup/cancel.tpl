<div id="detailsRight">
    <div class="rightTitle">
        Selected Class
    </div>

    <div class="rightContent">
    <table>
        <tbody>
            <tr>
                <td valign="top"><label>Date:</label></td>
                <td>{$event.date|date_format:$config.medDateFormat}</td>
            </tr> 
            <tr>
                <td valign="top"><label>Time:</label></td>
                <td>{$event.startTime|date_format:$config.timeFormat} - {$event.endTime|date_format:$config.timeFormat}</td>
            </tr>  
            <tr>
                <td valign="top"><label>Location:</label></td>
                <td>{$location.name}</td>
            </tr>    
            <tr>
                <td valign="top"><label>Instructor{if count($instructors) > 1}s{/if}:</label></td>
                <td>
                {foreach from=$instructors item=i}
                    {$i.firstName} {$i.lastName}<br />
                {foreachelse}
                None
                {/foreach}
                </td>
            </tr>                           
        </tbody>
    </table>    
    </div>
    <div class="rightTitle">
        Other Available Times
    </div>

    <div class="rightContent">
        {foreach from=$upcomingEvents item=e name=events}
            <div class="event" id="event_{$e.eventId}">
                <span class="date">{$e.date|date_format:$config.longDateFormat}</span>
                {$e.startTime|date_format:$config.timeFormat} - {$e.endTime|date_format:$config.timeFormat}
                <span class="status">
                {if $e.status == 'instructor'}
                    <a href="{$sitePrefix}/workshop/signup/instructor/?eventId={$e.eventId}"></a>Instructor Options
                {elseif $e.status == 'attending'}
                    <a href="{$sitePrefix}/workshop/signup/?eventId={$e.eventId}"></a>You are attending. Cancel...
                {elseif $e.status == 'waitlist'}
                    <a href="{$sitePrefix}/workshop/signup/?eventId={$e.eventId}"></a>You're on the waitlist.  Cancel...
                {elseif $e.status == 'restricted'}
                    <a href="{$sitePrefix}/workshop/signup/?eventId={$e.eventId}"></a><span class="restricted">Attendance Restricted</span>
                {else}
                    {if $e.roleSize < $e.maxSize}
                        <a href="{$sitePrefix}/workshop/signup/?eventId={$e.eventId}"></a>{math equation="x-y" x=$e.maxSize y=$e.roleSize} seats remaining. Register...
                    {else}
                        {if $e.waitlistSize != 0 && $e.waitlistSize > $e.waitlistTotal}
                            <a href="{$sitePrefix}/workshop/signup/?eventId={$e.eventId}"></a>Class is full.  Signup to waitlist...
                        {else}
                            This class is full.
                        {/if}                  
                    {/if}
                {/if}
                </span>
            </div>
        {foreachelse}
        No additional classes are scheduled at this time.
        {/foreach}  
    </div>      
</div>
<div id="detailsLeft">
    <div id="wsTitle">Cancel Reservation: <a href="{$sitePrefix}/workshop/index/details/?workshopId={$event.workshopId}">{$workshop.title|truncate:45}</a></div> 
    <form method="POST" action="">
        <input type="hidden" name="eventId" value="{$event.eventId}" />
        <div id="fullClass" class="banner">
           Are you sure you want to cancel?
        </div>        
        {if $status == 'attending'}
        Canceling your reservation will remove you from the class role and give your
        seat to the next person on the waitlist.
        {else}
        Removing yourself from the waitlist will mean you will not be automatically
        put into this class, should a spot become available.
        {/if}
        Are you sure
        you want to cancel?
        <br /><br />
        <input type="submit" value="Yes, Cancel my Reservation" />
        <input type="button" value="No, Go Back" onclick="history.go(-1);" />
    </form>
</div>