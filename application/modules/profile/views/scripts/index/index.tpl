<div id="detailsRight">
    <div class="rightTitle">
        {if $acl.edit}
            <a href="{$sitePrefix}/profile/index/edit/?userId={$profile.userId}"><div class="edit">&nbsp;</div></a>
        {/if}      
        User Information      
    </div>

    <div class="rightContent">
    <table width="100%">
        <tbody>
        <tr>
            <td>
            <label>Name:</label></td>
            <td>{$profile.firstName} {$profile.lastName}</td>
        </tr>        
	    <tr>
	        <td width="65"><label>User ID:</label></td>
	        <td>{$displayUserId}</td>
	    </tr>
	    <tr>
	        <td><label>Login Via:</label></td>
	        <td>{$adapter.name}</td>
	    </tr>
	    <tr>
	        <td>	        
	        <label>Email:</label></td>
	        <td>{$profile.emailAddress|empty_alt:"None"}</td>
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
    <div id="titleContainer">
        {if $profile.picImageId == 0}
        <img src="{$sitePrefix}/public/images/system-users.png" border="0" />
        {else}
        <img src="{$sitePrefix}/index/image/?imageId={$profile.picImageId}" border="0" />
        {/if}
        <div id="wsTitle">{$title}</div>
    </div>
	{if count($messages) != 0}
	    <div class="message">
	    {foreach from=$messages item=m}
	    {$m}<br />
	    {/foreach}
	    </div>
	{/if}    
    <b>MyClassMate</b> is your one-stop-shop for all the classes you are involved
    with.  Make sure to keep your user information up-to-date so that you get all
    the benefits of being a ClassMate member.    
    <div class="sectionBar">
        <p class="left"></p>
        <p class="content">My Reservations</p>
    </div>
    <div id="reservations" class="section">
	    <ul class="mootabs_title">
	        <li title="currentReservations">Current Reservations ({count source=$currentReservations})</li>
	        <li title="pastReservations">Past Reservations ({count source=$pastReservations})</li>
	        <li title="suggestedWorkshops">Suggested Workshops</li>
	    </ul>
        <div id="currentReservations" class="mootabs_panel">
	        {foreach from=$currentReservations item=e name=current}
	           {assign var=wc value=$e.workshop.workshopCategoryId}
               {if $smarty.foreach.current.index < count($currentReservations) && $smarty.foreach.current.index != 0}
               <div class="eventSpacer"></div>
               {/if}	           
	        <div class="event">
	            <div class="status">
	               <div class="{$e.status}">              
	                {if $e.status == 'attending'}
	                    You are attending this class.
	                {elseif $e.status == 'waitlist'}
	                    You are {$e.waitlistPosition|ordinal} on the waitlist.
	                {/if}
	                </div>
	                {if $e.cancelable}
	                <a href="{$sitePrefix}/workshop/signup/cancel/?eventId={$e.eventId}&amp;userId={$profile.userId}">Cancel Reservation...</a>
	                {/if}
		            {if $e.status == 'attending' && $e.evaluatable}
		            <a href="{$sitePrefix}/workshop/evaluate/?eventId={$e.eventId}">Evaluate Class</a>
		            {/if}
                    {if $e.status == 'attending' && $e.hasHandouts}
                    <a href="{$sitePrefix}/workshop/index/downloadHandouts?workshopId={$e.workshop.workshopId}">Download Handouts</a>
                    {/if}
                </div> 
	            <div class="workshopName" style="background-image:url({$sitePrefix}/index/image/?imageId={$categories.$wc.smallIconImageId});"><a href="{$sitePrefix}/workshop/index/details/?workshopId={$e.workshopId}">{$e.workshop.title}</a></div>
                <div class="dateTime">
                    <span class="date">{$e.date|date_format:$config.longDateFormat}</span>
                    <span class="time">{$e.startTime|date_format:$config.timeFormat} - {$e.endTime|date_format:$config.timeFormat}</span>
                    <span class="location"><b>Location:</b><a href="{$sitePrefix}/workshop/location/details/?locationId={$e.location.locationId}">{$e.location.name}</a></span> 
                </div>           
	        </div>
	        {foreachelse}
	        No Current Reservations Found.
	        {/foreach}        
        </div> 
        <div id="pastReservations" class="mootabs_panel">
	        {foreach from=$pastReservations item=e name=past}
	           {assign var=wc value=$e.workshop.workshopCategoryId}
	           {if $smarty.foreach.past.index < count($pastReservations) && $smarty.foreach.past.index != 0}
	           <div class="eventSpacer"></div>
	           {/if}
	        <div class="event">    
	            <div class="status">        
	                {if $e.attended == 1}
	                <div class="present">You attended this class</div>
	                {else}
	                <div class="absent">You were absent</div>
	                {/if}
                </div>
                 <div class="workshopName" style="background-image:url({$sitePrefix}/index/image/?imageId={$categories.$wc.smallIconImageId});"><a href="{$sitePrefix}/workshop/index/details/?workshopId={$e.workshopId}">{$e.workshop.title}</a></div>
	            <div class="dateTime">
	                <span class="date">{$e.date|date_format:$config.longDateFormat}</span>
	                <span class="time">{$e.startTime|date_format:$config.timeFormat} - {$e.endTime|date_format:$config.timeFormat}</span>
	                <span class="location"><b>Location:</b><a href="{$sitePrefix}/workshop/location/details/?locationId={$e.location.locationId}">{$e.location.name}</a></span> 
	            </div>                    
	        </div>
	        {foreachelse}
	        You have not taken any classes.
	        {/foreach}         
        </div>  
        <div id="suggestedWorkshops" class="workshops mootabs_panel">
            Based on the other workshops you have attended, you may be interested
            in these other workshops that we offer.<br />
            {foreach from=$relatedWorkshops item=w}
            {assign var=wc value=$w.workshopCategoryId}
            <div class="workshop">
                <div class="name" style="background-image:url({$sitePrefix}/index/image/?imageId={$categories.$wc.smallIconImageId});"><a href="{$sitePrefix}/workshop/index/details/?workshopId={$w.workshopId}">{$w.title}</a></div>
                <div class="description">{$w.description|strip_tags|truncate:150}</div>  
            </div>
            {foreachelse}
            <br />
            No related workshops found.
            {/foreach}
        </div>
    </div>
    {if count($currentTeaching) != 0 || count($pastTeaching) != 0}
    <br />
    <div class="sectionBar">
        <p class="left"></p>
        <p class="content">Classes I Am Teaching</p>
    </div>    
    <div id="teaching" class="section">
        <ul class="mootabs_title">
            <li title="currentTeaching">Current Classes ({count source=$currentTeaching})</li>
            <li title="historyTeaching">Past Classes ({count source=$pastTeaching})</li>
        </ul>
        <div id="currentTeaching" class="mootabs_panel">
            {foreach from=$currentTeaching item=e name=currentTeaching}
               {assign var=wc value=$e.workshop.workshopCategoryId}
               {if $smarty.foreach.currentTeaching.index < count($currentTeaching) && $smarty.foreach.currentTeaching.index != 0}
               <div class="eventSpacer"></div>
               {/if}               
            <div class="event">
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
                    
                    <a href="{$sitePrefix}/workshop/instructor/?eventId={$e.eventId}">Instructor Tools</a>
                    
                </div> 
                <div class="workshopName" style="background-image:url({$sitePrefix}/index/image/?imageId={$categories.$wc.smallIconImageId});"><a href="{$sitePrefix}/workshop/index/details/?workshopId={$e.workshopId}">{$e.workshop.title}</a></div>
                <div class="dateTime">
                    <span class="date">{$e.date|date_format:$config.longDateFormat}</span>
                    <span class="time">{$e.startTime|date_format:$config.timeFormat} - {$e.endTime|date_format:$config.timeFormat}</span>
                    <span class="location"><b>Location:</b><a href="{$sitePrefix}/workshop/location/details/?locationId={$e.location.locationId}">{$e.location.name}</a></span> 
                </div>           
            </div>
            {foreachelse}
            No Current Classes Found.
            {/foreach}         
        </div> 
        <div id="historyTeaching" class="mootabs_panel">
            {foreach from=$pastTeaching item=e name=pastTeaching}
               {assign var=wc value=$e.workshop.workshopCategoryId}
               {if $smarty.foreach.pastTeaching.index < count($pastTeaching) && $smarty.foreach.pastTeaching.index != 0}
               <div class="eventSpacer"></div>
               {/if}               
            <div class="event">
                <div class="status">             
                    <a href="{$sitePrefix}/workshop/instructor/?eventId={$e.eventId}">Instructor Tools</a>
                </div> 
                <div class="workshopName" style="background-image:url({$sitePrefix}/index/image/?imageId={$categories.$wc.smallIconImageId});"><a href="{$sitePrefix}/workshop/index/details/?workshopId={$e.workshopId}">{$e.workshop.title}</a></div>
                <div class="dateTime">
                    <span class="date">{$e.date|date_format:$config.longDateFormat}</span>
                    <span class="time">{$e.startTime|date_format:$config.timeFormat} - {$e.endTime|date_format:$config.timeFormat}</span>
                    <span class="location"><b>Location:</b><a href="{$sitePrefix}/workshop/location/details/?locationId={$e.location.locationId}">{$e.location.name}</a></span> 
                </div>           
            </div>
            {foreachelse}
            No Current Classes Found.
            {/foreach}           
        </div>
    </div>
    <br />
    {/if}     
</div>