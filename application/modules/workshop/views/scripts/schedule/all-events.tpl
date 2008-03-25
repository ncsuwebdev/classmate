These are all the events going on within ClassMate.<br /><Br />

<select id="instructorSelect">
    <option value="">All Instructors</option>   
    {foreach from=$instructorDropDown item=i}
        <option value="{$i.userId}">{$i.firstName|capitalize} {$i.lastName|capitalize}</option>
    {/foreach}
</select>
<input type="button" id="instructorSelectButton" value="Filter Events By Instructor" />
<br /><br />

{foreach from=$events item=e name=events}
{assign var=wc value=$e.workshop.workshopCategoryId}
<div class="event">
{if $smarty.foreach.events.index < count($events) && $smarty.foreach.events.index != 0}
<div class="eventSpacer"></div><br />
{/if}
    <div class="status">  
    {if $e.roleSize < $e.maxSize}
    <div class="seatAvailable"> {math equation="x - y" x=$e.maxSize y=$e.roleSize} of {$e.maxSize} seats available.</div>
    {else}
         {if $e.waitlistSize != 0}
              {if $e.waitlistTotal < $e.waitlistSize}
              <div class="waitlistAvailable"> {math equation="x-y" x=$e.waitlistSize y=$e.waitlistTotal} waitlist spots available.</div>
              {else}
              <div class="fullClass"> Class and waitlist are full.</div>
              {/if} 
         {else}
         <div class="fullClass"> All {$e.maxSize} seats are taken.  </div>  
         {/if}
    {/if}
    {if $e.instructor}
    <a href="{$sitePrefix}/workshop/instructor/?eventId={$e.eventId}">Instructor Tools</a>
    {/if}
    
    {if $e.signupable}
    <a href="{$sitePrefix}/workshop/signup/?eventId={$e.eventId}">Sign-up For Class</a>
    {/if}
    
    {if $e.evaluatable}
    <a href="{$sitePrefix}/workshop/evaluate/?eventId={$e.eventId}">Evaluate Class</a>
    {/if}
    
    {if $e.cancelable}
    <a href="{$sitePrefix}/workshop/signup/cancel/?eventId={$e.eventId}">Cancel Reservation...</a>
    {/if}
    </div> 
    <div class="workshopName" style="background-image:url({$sitePrefix}/index/image/?imageId={$categories.$wc.smallIconImageId});"><a href="{$sitePrefix}/workshop/index/details/?workshopId={$e.workshopId}">{$e.workshop.title}</a></div>
    <div class="dateTime">
        <span class="date">{$e.date|date_format:$config.longDateFormat}</span>
        <span class="time">{$e.startTime|date_format:$config.timeFormat} - {$e.endTime|date_format:$config.timeFormat}</span>
        <span class="location"><b>Location:</b><a href="{$sitePrefix}/workshop/location/details/?locationId={$e.location.locationId}">{$e.location.name}</a></span>
        <span class="instructorList"><b>Instructor{if count($e.instructors) > 1}s{/if}: </b>
            {foreach from=$e.instructors item=i name=instructor}
                {if $smarty.foreach.instructor.index != 0}, {/if}
                <span class="instructor" title="{$i.profile.userId}">{$i.profile.firstName|capitalize} {$i.profile.lastName|capitalize}</span>                
            {foreachelse}
                No instructor assigned yet
            {/foreach}   
        </span>
    </div>           
</div>
{foreachelse}
No Events Scheduled
{/foreach}