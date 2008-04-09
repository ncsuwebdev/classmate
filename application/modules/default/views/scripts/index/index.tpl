<div id="right">
    <div class="sectionTitle">
        <p class="left"></p>
        <p class="right"></p>
        <p class="content">ClassMate Workshops</p>
    </div>
    <div id="myTabs">
        <ul class="mootabs_title">
            <li title="events">Coming Soon</li>
            <li title="popular">Most Popular</li>
            <li title="searches">Top Searches</li>
        </ul>
	    <div id="events" class="mootabs_panel">
	       <div class="tabDesc">
              These are the upcoming workshops we are offering:
              
              {if count($upcoming) == 0}
              <br /><br />
              There are no upcoming workshops right now. 
              {/if}
           </div>
	        {foreach from=$upcoming item=e name=upcoming}
                {if $smarty.foreach.upcoming.index < 4}
        	        <div class="event">
        	           <span class="date">{$e.date|date_format:$config.medDateFormat}</span>
        	           <div class="workshopName"><a href="{$sitePrefix}/workshop/index/details/?workshopId={$e.workshopId}">{$e.workshop.title|truncate:40}</a></div> 
        	        </div>
                {/if}
	        {/foreach}
	        {if count($upcoming) != 0}
            <div class="event">
               <span class="date"></span>
               <div class="workshopName"><a href="{$sitePrefix}/workshop/schedule/all-events" title="Full Schedule">View more dates...</a></div> 
            </div>
            {/if}
	    </div>
        <div id="popular" class="mootabs_panel">
        Coming Soon
        </div>
        <div id="searches" class="mootabs_panel">
            <div class="tabDesc">These are the most popular search terms.</div>
        {foreach from=$popularSearchTerms item=t name=terms}
            <div class="searchTerm">
                <div class="count">({$t.count} time{if $t.count != 1}s{/if})</div>            
                <div class="rank">{$smarty.foreach.terms.iteration}</div>
                <div class="term"><a href="{$sitePrefix}/index/search/?search={$t.term}">{$t.term|truncate:50}</a></div>
            </div>
        {/foreach}
        </div>
    </div>    
</div>
<div id="left">
ClassMate is your one-stop shop for registering for Office of Information Technology 
(OIT) Workshops at North Carolina State University. For more information about 
our training program, please visit our <a href="http://www.ncsu.edu/it/education/" target="_blank">Technology Training and Engagement website</a>.
<br /><br />
Learn more about out new registration system on our <a href="http://www.ncsu.edu/it/education/faculty-staff/reg_overview.php" target="_blank">Classmate Overview page</a>.
</div>