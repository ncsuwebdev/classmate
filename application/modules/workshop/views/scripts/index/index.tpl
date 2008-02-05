We regularly teach a wide variety of workshops.  Here is the complete list of
what we teach.  Click on the title to see when we are offering it next!
<br /><br />

    {if $acl.add}
    <a href="{$sitePrefix}/workshop/index/add/"><img src="{$sitePrefix}/public/images/add.png" alt="Add"></a>
    <a href="{$sitePrefix}/workshop/index/add/">Add New Workshop</a><br /><br />
    {/if}
    {foreach from=$workshops item=w name=workshops}
        <div class="category" style="background-image:url({$sitePrefix}/index/image/?imageId={$w.category.largeIconImageId});">
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
	                       <th width="250">Workshop</th>
	                       <th width="150">Next Scheduled Class</th>
	                   </tr>
	        {foreach from=$w.workshops item=s}
	            <tr>
	               <td>
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
	        {foreachelse}
	            <tr>
	               <td class="noResults">
	               No Workshops found 
	               </td>
	            </tr>
	        {/foreach}
	               </tbody>
	           </table>
	        </div>
	    </div>
    {/foreach}
