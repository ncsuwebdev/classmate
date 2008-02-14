<a href="{$sitePrefix}/workshop/location/index/">&lt; &lt; Back to All Labs</a><br />
	<div id="response">&nbsp;</div> 
	<input type="hidden" name="locationId" id="locationId" value="{$location.locationId}" class="postArgs name description address capacity" />   
	<div id="titleContainer">
	    {if $acl.edit}
	        <input type="button" id="status" class="status_{$location.status}" value="{if $location.status == 'disabled'}Enable{else}Disable{/if} This Location" />
	    {/if}	    
	    {if $acl.edit}
		<div id="editName" class="inlineEdit" target="name"></div>
		{/if}
		<img id="statusImage" src="{$sitePrefix}/public/images/network-idle.png" alt="{$location.name}" />  
        {if $location.status == 'disabled'}
        <img id="disabledImage" src="{$sitePrefix}/public/images/cross.png" />
        <span id="disabled">DISABLED!</span>
        {/if}
		<span id="name" rel="type=input&size=40&url={$sitePrefix}/workshop/location/edit/&response=response">{$location.name}</span>
	</div>
	{if $acl.edit}
    <div id="editAddress" class="inlineEdit" target="address"></div>
    {/if}
	<div id="addressContainer">
	    {if $location.address != ''}
	    <div id="mapit"><a href="http://maps.google.com/maps?f=q&hl=en&geocode=&q={$location.address}&ie=UTF8&z=16&iwloc=addr" target="_blank">Map With Google Maps!</a></div>
	    {/if}    
	    <div id="address" rel="type=input&size=40&url={$sitePrefix}/workshop/location/edit/&response=response">{$location.address|empty_alt:"No Address Provided"}</div> 
	</div>   	 
	{if $acl.edit}
	<div id="editCapacity" class="inlineEdit" target="capacity"></div>
	{/if}
	<div id="capacityContainer">	
	   <b>Maximum Number of Students:</b> <span id="capacity" rel="type=input&size=5&url={$sitePrefix}/workshop/location/edit/&response=response">{$location.capacity}</span>
    </div>
    
	{if $acl.edit}
	<div id="editDescription" class="inlineEdit" target="description"></div>
	<div id="description" rel="type=textarea&cols=120&rows=20&url={$sitePrefix}/workshop/location/edit/&response=response">{$location.description|empty_alt:"No Description Provided"}</div>
	{else}
	<div id="description">{$location.description|empty_alt:"No Description Provided"}</div>
	{/if}