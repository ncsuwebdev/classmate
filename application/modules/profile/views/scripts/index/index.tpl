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
    <div id="titleContainer">
        {if $profile.picImageId == 0}
        <img src="{$sitePrefix}/public/images/system-users.png" border="0" />
        {else}
        <img src="{$sitePrefix}/index/image/?imageId={$profile.picImageId}" border="0" />
        {/if}
        <div id="wsTitle">{$title}</div>
    </div>
    <b>MyClassMate</b> is your one-stop-shop for all the classes you are involved
    with.  Make sure to keep your user information up-to-date so that you get all
    the benefits of being a ClassMate member.    
    <div class="sectionBar">
        <p class="left"></p>
        <p class="content">My Classes</p>
    </div>
    <div id="classes" class="section">
	    <ul class="mootabs_title">
	        <li title="current">Current Reservations</li>
	        <li title="history">Past Reservations</li>
	    </ul>
        <div id="current" class="mootabs_panel">
	        {foreach from=$attendeeEvents item=e name=current}
	           {assign var=wc value=$e.workshop.workshopCategoryId}
               {if $smarty.foreach.current.index < count($attendeeEvents) && $smarty.foreach.current.index != 0}
               <div class="eventSpacer"></div>
               {/if}	           
	        <div class="event">         
	            <div class="dateTime">
	                <span class="date">{$e.date|date_format:$config.longDateFormat}</span>
	                <span class="time">{$e.startTime|date_format:$config.timeFormat} - {$e.endTime|date_format:$config.timeFormat}</span>
	            </div>          
	            <div class="workshopName" style="background-image:url({$sitePrefix}/index/image/?imageId={$categories.$wc.smallIconImageId});"><a href="{$sitePrefix}/workshop/index/details/?workshopId={$e.workshopId}">{$e.workshop.title}</a></div>
	            {if $e.status == 'instructor'}
	                <div class="instructor">Instructor Tools</div>
	            {elseif $e.status == 'attending'}
	                You are attending.  <a href="{$sitePrefix}/workshop/signup/reservation/?eventId={$e.eventId}&amp;userId={$profile.userId}">Click for reservation...</a>
	            {elseif $e.status == 'waitlist'}
	                You are on the waitlist.
	            {/if}           
	        </div>
	        {foreachelse}
	        No Current Reservations Found.
	        {/foreach}        
        </div> 
        <div id="history" class="mootabs_panel">
	        {foreach from=$pastEvents item=e name=past}
	           {assign var=wc value=$e.workshop.workshopCategoryId}
	           {if $smarty.foreach.past.index < count($pastEvents) && $smarty.foreach.past.index != 0}
	           <div class="eventSpacer"></div>
	           {/if}
	        <div class="event">         
	            <div class="dateTime">
	                <span class="date">{$e.date|date_format:$config.longDateFormat}</span>
	                <span class="time">{$e.startTime|date_format:$config.timeFormat} - {$e.endTime|date_format:$config.timeFormat}</span>
	            </div>          
	            <div class="workshopName" style="background-image:url({$sitePrefix}/index/image/?imageId={$categories.$wc.smallIconImageId});"><a href="{$sitePrefix}/workshop/index/details/?workshopId={$e.workshopId}">{$e.workshop.title}</a></div>
	            {if $e.status == 'instructor'}
	                <div class="instructor">Instructor Tools</div>
	            {elseif $e.status == 'attending'}
	                You attended this class.
	            {elseif $e.status == 'waitlist'}
	                You were on the waitlist, but didn't attend.
	            {/if}           
	        </div>
	        {foreachelse}
	        You have not taken any classes.
	        {/foreach}         
        </div>      
    </div>
    <br />
    <div class="sectionBar">
        <p class="left"></p>
        <p class="content">Other Workshops You May Be Interested In</p>
    </div>
    <div class="section">  
        <div class="workshops">
            Based on the other workshops you have attended, you may be interested
            in these other workshops that we offer.<br />
            {foreach from=$relatedWorkshops item=w}
            {assign var=wc value=$w.workshopCategoryId}
            <div class="workshop">
                <div class="name" style="background-image:url({$sitePrefix}/index/image/?imageId={$categories.$wc.smallIconImageId});"><a href="{$sitePrefix}/workshop/index/details/?workshopId={$w.workshopId}">{$w.title}</a></div>
                <div class="description">{$w.description|strip_tags|truncate:150}</div>  
            </div>
            {foreachelse}
            No related workshops found.
            {/foreach}
        </div>    
    </div>  
</div>