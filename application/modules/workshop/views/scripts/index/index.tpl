We regularly teach a wide variety of workshops.  Here is the complete list of
what we teach.  Click on the title to see when we are offering it next!
<br /><br />

    {if $acl.add}
    <div class="add"><a href="{$sitePrefix}/workshop/index/add/">Add New Workshop</a></div>
    {/if}
    
    <label for="categorySelect">Select a Category: </label>
    <select id="categorySelect" name="categorySelect">
        <option style="background-image:url({$sitePrefix}/public/images/featured_badge_small.png);" value="category_0">Featured Workshops</option>
        {foreach from=$workshops item=w}
            <option style="background-image:url({$sitePrefix}/index/image/?imageId={$w.category.smallIconImageId});" value="category_{$w.category.workshopCategoryId}">{$w.category.name}</option>
        {/foreach}
    </select>
    <input type="button" value="Go" id="categorySelectButton" />
    <br /><br />
    
    <div id="category_0" class="catBox featured">
    <div class="featured_badge"></div>
    <div class="workshops">
   <table class="list">
       <tbody>
           <tr>
               <th width="450">Workshop</th>
               <th width="150">Next Scheduled Class</th>
           </tr>
           {foreach from=$featured item=s name=featured}
          
               {if ($s.status == 'disabled' && $acl.viewDisabled) || $s.status == 'enabled'}
               <tr class="workshop">
                   <td>
                       {if $s.status == 'disabled'}
                       <B>DISABLED!</B> &nbsp; 
                       {/if}
                       {if $acl.details}
                           <a href="{$sitePrefix}/workshop/index/details/?workshopId={$s.workshopId}">{$s.title}</a>
                       {else}
                           {$s.title}
                       {/if}
                   </td>
                   <td style="text-align:center">
                       {if is_null($s.nextEvent)}
                       Not Scheduled
                       {else}
                       {$s.nextEvent.date|date_format:$config.medDateFormat}
                       {/if}
                   </td>               
              </tr>
              {/if}
           {foreachelse}
               <tr>
                   <td colspan="2">No featured workshops found</td>
           {/foreach}
        </tbody>
       </table>
    </div>
    </div>
    
    {foreach from=$workshops item=w name=workshops}
        <div class="category catBox" id="category_{$w.category.workshopCategoryId}" style="background-image:url({$sitePrefix}/index/image/?imageId={$w.category.largeIconImageId});">
            <div class="name">
            {$w.category.name}
            </div>
            <div class="description">
            {$w.category.description}
            </div>
	        
	        <div class="workshops">
	           <table class="list">
	               <tbody>
	                   <tr>
	                       <th width="450">Workshop</th>
	                       <th width="150">Next Scheduled Class</th>
	                   </tr>
	        {foreach from=$w.workshops item=s}
	            {if ($s.status == 'disabled' && $acl.viewDisabled) || $s.status == 'enabled'}
	            <tr class="workshop">
	               <td>
	               {if $s.status == 'disabled'}
	               <B>DISABLED!</B> &nbsp; 
	               {/if}
		            {if $acl.details}
		                <a href="{$sitePrefix}/workshop/index/details/?workshopId={$s.workshopId}">{$s.title}</a>
		            {else}
		                {$s.title}
		            {/if}
	               </td>
	               <td style="text-align:center">
	               {if is_null($s.nextEvent)}
	               Not Scheduled
	               {else}
	               {$s.nextEvent.date|date_format:$config.medDateFormat}
	               {/if}
	               </td>               
	            </tr>
	            {/if}
	        {foreachelse}
	            <tr class="workshop">
	               <td>
	               No Workshops found 
	               </td>
                   <td>
                   &nbsp;
                   </td>
	            </tr>
	        {/foreach}
	               </tbody>
	           </table>
	        </div>
	    </div>
    {/foreach}
