{if $acl.addAttendee}
<div id="createEventPopup" style="position: absolute; left: -10000px;">
<form method="POST" action="{$sitePrefix}/workshop/instructor/addAttendee/" id="addAttendeeForm">
<input type="hidden" name="eventId" value="{$event.eventId}">
<table width="100%" class="form">
    <tbody>     
         <tr>
            <td colspan="2">
                <label for="attendees">Add Attendee:</label>
            </td>
         </tr>
         <tr>
            <td id="attendees" width="250">None Added</td>
            <td align="center">
                {html_options size=10 multiple=true id=attendeeList name=attendeeList options=$users}
                <br />
                <input type="button" id="attendeeAddButton" value="&lsaquo; Add Selected User" />              
            </td>
         </tr>
         <tr>
            <td colspan="2"><label>Add Users To:</label></td>
         <tr>
         <tr>
            <td colspan="2">
            <input type="radio" name="type" value="firstAvailable" checked="checked" /> Next Available Spot<br />
            {if $event.roleSize < $event.maxSize}
            <input type="radio" name="type" value="attending" /> Attendee Roster<br />
            {else}
                <input type="radio" name="type" value="attending" /> Attendee Roster (Override Max Size)<br />
                {if $event.waitlistSize != 0}
                    {if $event.waitlistTotal < $event.waitlistSize}
                    <input type="radio" name="type" value="waitlist" /> Waitlist<br />
                    {else}
                    <input type="radio" name="type" value="waitlist" /> Waitlist (Override Waitlist Size)<br />   
                    {/if}
                 {/if}
             {/if}         
            </td>
         </tr>         
     </tbody>
</table>
</form>
</div>
{/if}
    {if $acl.addAttendee}
    <div class="add" id="addAttendee">Add Attendee</div>   
    {/if}
    <b>Attendees:</b> {count source=$attendeeList} of {$event.maxSize} seats taken<br /><br />
    
    <form style="float: right;" method="post" action="{$sitePrefix}/workshop/instructor/markAllAsAttended">
        <input type="hidden" value="{$event.eventId}" name="eventId" />
        <input type="submit" value="Mark all attendees as attended" />
    </form>
    <br />
    <br />
    {foreach from=$attendeeList item=a}
       <div class="attendee taken">
        {if $a.picImageId == 0}
        <img src="{$sitePrefix}/public/images/system-users.png" border="0" />
        {else}
        <img src="{$sitePrefix}/index/image/?imageId={$a.picImageId}" border="0" />
        {/if}
        
        <span class="attendance" id="att_{$a.userId}">    
            <span class="update" id="update_{$a.userId}">&nbsp;</span>  
            <div class="present {if $a.attended == 0}off{else}on{/if}"><span>Attended Class</span></div>
            <div class="absent {if $a.attended == 0}on{else}off{/if}"><span>Absent from Class</span></div>      
        </span>    
        <span class="name">       
        <span>{$a.firstName} {$a.lastName}</span>
        {if $acl.deleteAttendee}
        <img id="del_{$a.userId}" src="{$sitePrefix}/public/images/delete.png" alt="remove {$a.firstName} {$a.lastName}" class="removeAttendee" />
        {/if}         
        </span>
        <div class="signup">Signed up on {$a.timestamp|date_format:$config.dateTimeFormat}</div>
        </div>    
    {foreachelse}
    No Attendees.
    {/foreach}
        {if count($waitlist) != 0}
        <br /><br />
        <b>Waitlist: </b> {count source=$waitlist} of {$event.waitlistSize} seats taken<br /><br />
        {foreach from=$waitlist item=a}
           <div class="attendee taken">
            {if $a.picImageId == 0}
            <img src="{$sitePrefix}/public/images/system-users.png" border="0" />
            {else}
            <img src="{$sitePrefix}/index/image/?imageId={$a.picImageId}" border="0" />
            {/if}
            
            <div class="name">        
            <span>{$a.firstName} {$a.lastName}</span>
	        {if $acl.deleteAttendee}
	        <img id="del_{$a.userId}" src="{$sitePrefix}/public/images/delete.png" alt="remove {$a.firstName} {$a.lastName}" class="removeAttendee" />
	        {/if} 
	        </div>
            <div class="signup">Signed up on {$a.timestamp|date_format:$config.dateTimeFormat}</div>
            </div>    
        {foreachelse}
        No Attendees.
        {/foreach} 
    {/if}