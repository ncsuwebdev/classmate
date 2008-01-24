<div id="detailsRight">
    <div class="rightTitle">
        User Information
    </div>

    <div class="rightContent">
    {if $acl.edit}
        <a href="{$sitePrefix}/profile/index/edit/"><div class="edit">Edit Profile</div></a>
    {/if}
    <table width="100%">
        <tbody>
        {if $profile.picImageId != 0}
        <tr>
            <td colspan="2" align="center"><img src="{$sitePrefix}/index/image/?imageId={$profile.picImageId}" border="0" /></td>
        </tr>        
        {/if}
	    <tr>
	        <td width="80"><label>User ID:</label></td>
	        <td>{$displayUserId}</td>
	    </tr>
	    <tr>
	        <td><label>Login Type:</label></td>
	        <td>{$adapter.name}</td>
	    </tr>
	    <tr>
	        <td>
	        <label>Name:</label></td>
	        <td>{$profile.firstName} {$profile.lastName}</td>
	    </tr>
	    <tr>
	        <td>	        
	        <label>Email:</label></td>
	        <td>{$email|empty_alt:"None"}</td>
	    </tr>    
	    <tr>
	        <td>	        
	        <label>User Type:</label></td>
	        <td>
	        {assign var=type value=$profile.type}
	        {$types.$type|empty_alt:$profile.type}</td>
	    </tr>                             
        </tbody>
    </table>    
    </div>
</div>
<div id="detailsLeft">    
    <b>My ClassMate</b> is your one-stop-shop for all the classes you are involved
    with.  Make sure to keep your user information up-to-date so that you get all
    the benefits of being a ClassMate member.
    <div id="toggler">
	    <div class="sectionTitle toggler">
	        <p class="left"></p>
	        <p class="right"></p>
	        <p class="content">Current Reservations</p>
	    </div>
	    <div id="events" class="element">
		    {foreach from=$attendeeEvents item=e}
		    <div class="event">	        
	            <div class="dateTime">
	                <span class="date">{$e.date|date_format:$config.longDateFormat}</span>
	                <span class="time">{$e.startTime|date_format:$config.timeFormat} - {$e.endTime|date_format:$config.timeFormat}</span>
	            </div>	        
		        <div class="workshopName">{$e.workshop.title}</div>
		        {if $e.status == 'instructor'}
		            <div class="instructor">Instructor Tools</div>
	            {elseif $e.status == 'attending'}
	                You are attending.  <a href="{$sitePrefix}/workshop/signup/reservation/?eventId={$e.eventId}&amp;userId={$profile.userId}">Click for reservation...</a>
	            {elseif $e.status == 'waitlist'}
	                You are on the waitlist.
	            {/if}	        
		    </div>
		    {foreachelse}
		    You are not signed up for any classes right now.
		    {/foreach}
		</div>
	    <div class="sectionTitle toggler">
	        <p class="left"></p>
	        <p class="right"></p>
	        <p class="content">Other Workshops You May Be Interested In</p>
	    </div>	
		<div class="workshops element">
		    Based on the other workshops you have attended, you may be interested
		    in these other workshops that we offer.<br />
		    {foreach from=$relatedWorkshops item=w}
		    <div class="workshop">
		        <div class="name"><a href="{$sitePrefix}/workshop/index/details/?workshopId={$w.workshopId}">{$w.title}</a></div>
		        <div class="description">{$w.description|strip_tags|truncate:150}</div>  
		    </div>
		    {/foreach}
		</div>
	 
	    <div class="sectionTitle toggler">
	        <p class="left"></p>
	        <p class="right"></p>
	        <p class="content">Reservation History</p>
	    </div>
	    <div id="events" class="element">
	        {foreach from=$pastEvents item=e name=past}
	           {if $smarty.foreach.past.index < count($pastEvents) && $smarty.foreach.past.index != 0}
	           <div class="eventSpacer"></div>
	           {/if}
	        <div class="event">         
	            <div class="dateTime">
	                <span class="date">{$e.date|date_format:$config.longDateFormat}</span>
	                <span class="time">{$e.startTime|date_format:$config.timeFormat} - {$e.endTime|date_format:$config.timeFormat}</span>
	            </div>          
	            <div class="workshopName"><a href="{$sitePrefix}/workshop/index/details/?workshopId={$e.workshopId}">{$e.workshop.title}</a></div>
	            {if $e.status == 'instructor'}
	                <div class="instructor">Instructor Tools</div>
	            {elseif $e.status == 'attending'}
	                You attended this class.
	            {elseif $e.status == 'waitlist'}
	                You were on the waitlist, but didn't attend.
	            {/if}           
	        </div>
	        {foreachelse}
	        You are not signed up for any classes right now.
	        {/foreach}
	    </div> 	
	</div>
</div>