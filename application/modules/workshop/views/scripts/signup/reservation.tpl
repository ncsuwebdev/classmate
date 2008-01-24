{if $status == 'attending'}
<h3 id="signup">You are signed up for this class.</h3>
{elseif $status == 'waitlist'}
<h3 id="signup">You are on the waitlist for this class.</h3>
{/if}

<table class="form">
    <tbody>
        <tr>
            <td><label>Workshop:</label></td>
            <td><a href="{$sitePrefix}/workshop/index/details/?workshopId={$workshop.workshopId}">{$workshop.title}</a></td>
        </tr>
        <tr>
            <td><label>Date:</label></td>
            <td>{$event.date|date_format:$config.longDateFormat}</td>
        </tr>
        <tr>
            <td><label>Time:</label></td>
            <td>{$event.startTime|date_format:$config.timeFormat} - {$event.endTime|date_format:$config.timeFormat}</td>
        </tr>
        <tr>
            <td><label>Instructors:</label></td>
            <td>
            {foreach from=$instructors item=i}
            {$i.firstName} {$i.lastName} &lt;<a href="mailto:{$i.emailAddress}">{$i.emailAddress}</a><br />
            {foreachelse}
            No Instructors have been assigned yet
            {/foreach}
            </td>
        </tr> 
        <tr>
            <td><label>Location:</label></td>
            <td>{$location.name}</td>
        </tr>
    </tbody>
</table>
<input type="button" value="Cancel My Reservation" onclick="location.href='{$sitePrefix}/workshop/signup/cancel/?eventId={$event.eventId}'">