    <b>Attendees:</b> {count source=$attendeeList} of {$event.maxSize} seats taken<br /><br />
    
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
        <span class="name">{$a.firstName} {$a.lastName}</span>
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
            <div class="name">{$a.firstName} {$a.lastName}</div>
            <div class="signup">Signed up on {$a.timestamp|date_format:$config.dateTimeFormat}</div>
            </div>    
        {foreachelse}
        No Attendees.
        {/foreach} 
    {/if}