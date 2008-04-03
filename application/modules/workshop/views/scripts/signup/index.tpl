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
                <td><a href="{$sitePrefix}/workshop/location/details/?locationId={$location.locationId}">{$location.name}</a></td>
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
                    <a href="{$sitePrefix}/workshop/instructor/?eventId={$e.eventId}"></a>Instructor Tools
                {elseif $e.status == 'attending'}
                    <a href="{$sitePrefix}/workshop/signup/cancel/?eventId={$e.eventId}"></a>You are attending. Cancel...
                {elseif $e.status == 'waitlist'}
                    <a href="{$sitePrefix}/workshop/signup/cancel/?eventId={$e.eventId}"></a>You're on the waitlist.  Cancel...
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
    <div id="workshopTitleContainer">
        <img src="{$sitePrefix}/index/image/?imageId={$category.largeIconImageId}" alt="{$category.name}" />
        <div id="wsTitle">Signup for <a href="{$sitePrefix}/workshop/index/details/?workshopId={$workshop.workshopId}">{$workshop.title}</a></div>       
    </div>
	<div id="status">
	{if $status == 'instructor'}
	You are set as an instructor for this class, therefore you do not need to sign up.
	<br /><br />
	To access instructor options such as evaluation results, class roles, and notification options, 
	<a href="{$sitePrefix}/workshop/instructor/?eventId={$event.eventId}">click here</a> to access the Instructor Tools.
	{elseif $status == 'attending'}
	<h3 id="signup">You are already signed up for this class.</h3>
	You can access your reservation from the <b>MyClassMate</b> link in the navigation bar.<br /><br />
	{elseif $status == 'waitlist'}
	<h3 id="signup">You are on the waitlist for this class.</h3>
	You are on the waitlist for this class.  Once a spot becomes available, you will be automatically
	put into the class.  You can access your reservation from the <b>My Appointments</b> link in the navigation bar.
	<br /><br />
	{elseif $status == 'restricted'}
	<div id="restricted" class="banner">
        Access to this class is restricted.
    </div>
    This class has restricted its access to a certain list of people.  If you would 
    still like to attend this workshop, you can try one of the other times this workshop is
    offered:<br /><br />
	{else}
	    {if $event.roleSize < $event.maxSize}
            {math assign=openSeats equation="x-y" x=$event.maxSize y=$event.roleSize}
            <h3 id="signup">There {if $openSeats == 1}is{else}are{/if} <b>{$openSeats}</b> out of <b>{$event.maxSize}</b> seats remaining in this class.</h3>
            
            The following are the pre-requisites for this class.  Make sure you
            have these skills before proceeding.
		    <div class="sectionBar">
		        <p class="left"></p>
		        <p class="content">Workshop Pre-Requisites</p>
		    </div>            
	        <div id="prereqs">
	        {$workshop.prerequisites|empty_alt:"No Prerequisites were defined for this workshop"}
	        </div>
	        <input type="button" value="Sign-Up To Attend Now!" onclick="location.href='{$sitePrefix}/workshop/signup/reserve/?eventId={$event.eventId}'">
	        
            <input type="button" value="Cancel, Go Back" onclick="history.go(-1);" />
	    {else}
	        {if $event.waitlistSize != 0 && $event.waitlistSize > $event.waitlistTotal}
	               <div id="fullClass" class="banner">
				        Only a waitlist spot is currently available.
				    </div>
	             Currently, all {$event.maxSize} seats are taken in this class.  However, there are {math equation="x-y" x=$event.waitlistSize y=$event.waitlistTotal} waitlist
	             spots available.  If someone already enrolled cancels before the class starts, you 
	             will automatically be put into the class.             
	             <br /><br />
	                         
	            The following are the pre-requisites for this class.  Make sure you
	            have these skills before proceeding.
	            <div class="sectionBar">
	                <p class="left"></p>
	                <p class="content">Workshop Pre-Requisites</p>
	            </div>            
	            <div id="prereqs">
	            {$workshop.prerequisites|empty_alt:"No Prerequisites were defined for this workshop"}
	            </div>
	             <input type="button" value="Sign-Up for the Waitlist Now!" onclick="location.href='{$sitePrefix}/workshop/signup/reserve/?eventId={$event.eventId}'">
	             <input type="button" value="Cancel, Go Back" onclick="history.go(-1);" />
	        {else}  
	             <div id="fullClass" class="banner">
	             We're Sorry, this class is currently full.  If you would still like to attend this workshop, you can try one of the other times this workshop is
                     offered:<br /><br />
	             </div>
	             {assign var=showUpcoming value=true}
	        {/if}                  
	    {/if}
	{/if}
	</div>
</div>